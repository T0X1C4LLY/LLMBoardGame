<?php

declare(strict_types=1);

namespace App\Game\Infrastructure\Symfony\Controller;

use App\Core\Infrastructure\Symfony\UuidV4;
use App\Game\Application\Query\ChatSessionQuery;
use App\Game\Application\Query\GlobalStatisticsQuery;
use App\Game\Application\Query\MapQuery;
use App\Game\Application\Query\SessionStatisticsQuery;
use App\Game\Application\UseCase\CreateMap;
use App\Game\Application\UseCase\CreateSession;
use App\Game\Application\UseCase\CreateTurn;
use App\Game\Domain\ChatClient;
use App\Game\Domain\Entity\Map;
use App\Game\Domain\Enum\Team;
use App\Game\Domain\Exception\ChatSessionNotFoundException;
use App\Game\Domain\Exception\InvalidChatResponseException;
use App\Game\Domain\Exception\InvalidCoordinatesException;
use App\Game\Domain\Exception\InvalidDirectionException;
use App\Game\Domain\Exception\ModelResponseException;
use App\Game\Domain\Exception\MonkMoveNotInResponseError;
use App\Game\Domain\Exception\MoveCannotBeDoneError;
use App\Game\Domain\MoveRater;
use App\Game\Domain\SessionStatisticsClient;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Exception\ParseException;
use Throwable;

class ChatController extends AbstractController
{
    public function __construct(
        private readonly ChatClient $chatClient,
        private readonly MessageBusInterface $messageBus,
        private readonly ClockInterface $clock,
        private readonly MapQuery $mapQuery,
        private readonly LoggerInterface $logger,
        private readonly MoveRater $moveRater,
        private readonly EntityManagerInterface $entityManager,
        private readonly SessionStatisticsClient $statisticsClient,
        private readonly ChatSessionQuery $chatSessionQuery,
        private readonly SessionStatisticsQuery $sessionStatisticsQuery,
        private readonly GlobalStatisticsQuery $globalStatisticsQuery,
    ) {
    }

    #[Route('/', name: 'main-menu', methods: ['GET'])]
    public function mainMenu(): Response
    {
        return $this->render('main-menu.html.twig');
    }

    #[Route('/game/new', name: 'new-game', methods: ['GET'])]
    public function newChatSession(): Response
    {
        $sessionId = UuidV4::generateNew();

        $this->messageBus->dispatch(new CreateSession\Command(
            $sessionId,
            $this->clock->now(),
        ));

        return $this->redirect(sprintf('/game/%s', $sessionId->toString()));
    }

    #[Route('/game/rules', name: 'rules', methods: ['GET'])]
    public function rules(): Response
    {
        return $this->render('rules.html.twig');
    }

    #[Route('/game/load', name: 'load', methods: ['GET'])]
    public function load(): Response
    {
        return $this->render(
            'load.html.twig',
            [
                'sessions' => $this->chatSessionQuery->allWithDates(),
            ]
        );
    }

    #[Route('/game/{sessionId}/map', name: 'map', methods: ['GET'])]
    public function getMap(string $sessionId): Response
    {
        if (!UuidV4::isValid($sessionId)) {
            return $this->redirect('/');
        }

        return $this->json($this->mapQuery->getNewestBySessionId(UuidV4::fromString($sessionId)));
    }

    #[Route('/model/{sessionId}/statistics', name: 'statistics', methods: ['GET'])]
    public function statistics(string $sessionId): Response
    {
        if (!UuidV4::isValid($sessionId)) {
            return $this->redirect('/');
        }

        try {
            return $this->render(
                'statistics.html.twig',
                [
                    'statistics' => $this->sessionStatisticsQuery->getById(UuidV4::fromString($sessionId)),
                ]
            );
        } catch (Throwable $e) {
            $this->logger->error(sprintf(
                '[ChatController] Endpoint: /model/{chatId}/statistics, Error message: %s',
                $e->getMessage()
            ));
        }

        return $this->json([], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/model/{sessionId}/endgame', name: 'endgame', methods: ['POST'])]
    public function endgame(Request $request, string $sessionId): Response
    {
        $data = $request->request->all();
        $sessionIdAsUuid = UuidV4::fromString($sessionId);

        $this->chatClient->sendInfoGameEnd(
            $sessionIdAsUuid,
            Team::from($data['teamThatWon']),
            Team::from($data['teamThatShouldHaveWon']),
        );

        return $this->json([], Response::HTTP_OK);
    }

    #[Route('/game/{sessionId}', name: 'getGame', methods: ['GET'])]
    public function game(string $sessionId): Response
    {
        if (!UuidV4::isValid($sessionId)) {
            return $this->redirect('/');
        }

        return $this->render('game.html.twig');
    }

    #[Route('/model/{sessionId}', name: 'model', methods: ['POST'])]
    public function model(Request $request, string $sessionId): Response
    {
        if (!UuidV4::isValid($sessionId)) {
            return $this->redirect('/');
        }

        $sessionIdAsUuid = UuidV4::fromString($sessionId);

        $data = $request->request->all();
        try {
            $this->entityManager->beginTransaction();
            $map = Map::fromArray(
                $mapId = UuidV4::generateNew(),
                $sessionIdAsUuid,
                $data['fields'],
                $createdAt = $this->clock->now(),
            );
            $moves = $map->prepareMovesForChat($data['winningTeam']);

            if ($this->moveRater->shouldTipBeSend($sessionIdAsUuid, Team::from($data['winningTeam']))) {
                $this->chatClient->sendTip($sessionIdAsUuid);
            }

            $decodedMoves = $this->chatClient->chat(
                $sessionIdAsUuid,
                $moves,
            );

            $movesFromChat = $map->prepareMoveList($decodedMoves);

            $this->messageBus->dispatch(new CreateMap\Command(
                $mapId,
                $data['fields'],
                $sessionIdAsUuid,
                $createdAt,
            ));

            $this->messageBus->dispatch(new CreateTurn\Command(
                UuidV4::generateNew(),
                $mapId,
                $sessionIdAsUuid,
                $movesFromChat,
                $data['numberOfTurns'],
                $data['numberOfGames'],
                false,
                $createdAt,
                $data['winningTeam'],
            ));

            $this->statisticsClient->setQuantityOfMonksIfNeeded($sessionIdAsUuid, $map->getNumberOfMonks());
            $this->statisticsClient->addCorrectAnswer($sessionIdAsUuid);

            $this->entityManager->commit();

            return $this->json($movesFromChat);
        }
        catch (MonkMoveNotInResponseError $e) {
            $this->chatClient->sendInfoAboutIncompleteData($sessionIdAsUuid);
            $this->statisticsClient->addAnswerWithMissingMove($sessionIdAsUuid);

            $this->entityManager->commit();

            $this->logger->error(sprintf(
                '[ChatController] Endpoint: /model/{sessionId}, Error message: %s',
                $e->getMessage()
            ));
        } catch (InvalidCoordinatesException|InvalidDirectionException $e) {
            $this->entityManager->rollback();

            $this->logger->error(sprintf(
                '[ChatController] Endpoint: /model/{sessionId}, Error message: %s',
                $e->getMessage()
            ));
        } catch (ChatSessionNotFoundException $e) {
            $this->entityManager->rollback();

            $this->logger->error(sprintf(
                '[ChatController] Endpoint: /model/{sessionId}, Error message: %s',
                $e->getMessage()
            ));

            return new Response('Session with given ID not found', Response::HTTP_NOT_FOUND);
        } catch (ModelResponseException $e) {
            $this->entityManager->rollback();

            $this->logger->error(sprintf(
                '[ChatController] Endpoint: /model/{sessionId}, Error message: %s',
                $e->getMessage()
            ));

            return new Response('ChatGPT response with error', Response::HTTP_BAD_REQUEST);
        } catch (InvalidChatResponseException|ParseException|JsonException $e) {
            $this->logger->error(sprintf(
                '[ChatController] Endpoint: /model/{sessionId}, Error message: %s',
                $e->getMessage()
            ));

            $this->chatClient->sendInfoAboutSemanticIssue($sessionIdAsUuid);
            $this->statisticsClient->addSemanticallyIncorrectAnswer($sessionIdAsUuid);

            $this->entityManager->commit();
        } catch (MoveCannotBeDoneError $e) {
            $this->logger->error(sprintf(
                '[ChatController] Endpoint: /model/{sessionId}, Error message: %s',
                $e->getMessage()
            ));

            $this->chatClient->sendInfoAboutIncorrectMove($sessionIdAsUuid);
            $this->statisticsClient->addAnswerWithIncorrectMove($sessionIdAsUuid);

            $this->entityManager->commit();
        }

        return $this->json([], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/save/{sessionId}', name: 'save', methods: ['POST'])]
    public function save(Request $request, string $sessionId): Response
    {
        $filename = sprintf("%s.csv", $sessionId);
        $filePath = sprintf("/var/www/chatgpt/stats/%s", $filename);

        shell_exec(sprintf('cd touch /var/www/chatgpt/stats && touch %s && chmod 777 %s', $filename, $filename));

        $file = fopen($filePath, "wb");

        $data = $request->request->all();

        foreach ($data as $line) {
            fputcsv($file, $line, ';');
        }

        fclose($file);

        return $this->json([]);
    }

    #[Route('/global-statistics', name: 'global_statistics', methods: ['GET'])]
    public function globalStatistics(): Response
    {
        return $this->render(
            'global-statistics.html.twig',
            [
                'statistics' => $this->globalStatisticsQuery->getAll(),
            ]
        );
    }
}
