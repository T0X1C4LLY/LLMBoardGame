<?php

declare(strict_types=1);

namespace App\Game\Infrastructure\Doctrine\Repository;

use App\Core\Domain\Uuid;
use App\Game\Domain\Entity\Map;
use App\Game\Domain\Exception\NotEnoughMapsToCompareException;
use App\Game\Domain\Repository\MapRepository as MapRepositoryInterface;
use App\Game\Infrastructure\Doctrine\Query\MapQuery;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;
use JsonException;

readonly class MapRepository implements MapRepositoryInterface
{
    public function __construct(
        private ManagerRegistry $managerRegistry,
        private MapQuery $mapQuery,
    ) {
    }

    public function add(Map $map): void
    {
        $entityManager = $this->managerRegistry->getManager();
        $entityManager->persist($map);
        $entityManager->flush();
    }

    /**
     * @return array{firstMap: Map, secondMap: Map}
     *
     * @throws Exception
     * @throws NotEnoughMapsToCompareException
     * @throws JsonException
     */
    public function findForCompare(Uuid $sessionId): array
    {
        $entityManager = $this->managerRegistry->getManager();
        $repository = $entityManager->getRepository(Map::class);

        $maps = $repository->findBy(['sessionId' => $sessionId], ['createdAt' => 'ASC']);

        if (!$this->hasEnoughMapsToCompare($maps)) {
            throw new NotEnoughMapsToCompareException();
        }

        return [
            'firstMap' => $maps[0],
            'secondMap' => $maps[3],
        ];
    }

    /**
     * @param Map[] $maps
     *
     * @throws Exception
     * @throws JsonException
     */
    private function hasEnoughMapsToCompare(array $maps): bool
    {
        if (count($maps) < 4) {
            return false;
        }

        $firstMap = $this->mapQuery->getById($maps[0]->id);
        $secondMap = $this->mapQuery->getById($maps[3]->id);

        return $firstMap->numberOfGames === $secondMap->numberOfGames;
    }
}
