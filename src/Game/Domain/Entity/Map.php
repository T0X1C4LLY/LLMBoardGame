<?php

declare(strict_types=1);

namespace App\Game\Domain\Entity;

use App\Core\Domain\Uuid;
use App\Game\Domain\ChatClient\Move;
use App\Game\Domain\Enum\Direction;
use App\Game\Domain\Enum\Team;
use App\Game\Domain\Exception\InvalidChatResponseException;
use App\Game\Domain\Exception\InvalidCoordinatesException;
use App\Game\Domain\Exception\InvalidDirectionException;
use App\Game\Domain\Exception\MonkMoveNotInResponseError;
use App\Game\Domain\Exception\MoveCannotBeDoneError;
use App\Game\Domain\Exception\NeighbourNotFoundException;
use App\Game\Domain\MapElement\Field;
use App\Game\Domain\MapElement\Monk;
use App\Game\Domain\ValueObject\MapCoordinates;
use DateTimeImmutable;
use JsonSerializable;
use Throwable;

readonly class Map implements JsonSerializable
{
    /**
     * @param Field[] $fields
     */
    private function __construct(
        public Uuid $id,
        public Uuid $sessionId,
        private array $fields,
        public DateTimeImmutable $createdAt,
    ) {
    }

    /**
     * @param array<array{
     *     row: int,
     *     column: int,
     *     monk: array{
     *          id: int,
     *          team: string,
     *     }|null,
     *     color: string,
     * }|null> $fields
     *
     * @throws InvalidCoordinatesException
     */
    public static function fromArray(
        Uuid $mapId,
        Uuid $sessionId,
        array $fields,
        DateTimeImmutable $createdAt,
    ): self {
        $arrayOfFields = [];

        foreach ($fields as $field) {
            if ($field) {
                $arrayOfFields[] = new Field(
                    new MapCoordinates(
                        $field['row'],
                        $field['column'],
                    ),
                    $field['monk'] ? new Monk(
                        $field['monk']['id'],
                        Team::from($field['monk']['team']),
                    ) : null,
                    $field['color'],
                );
            }
        }

        return new self(
            $mapId,
            $sessionId,
            $arrayOfFields,
            $createdAt,
        );
    }

    /**
     * @return array{
     *     team: Team,
     *     moves: Direction[],
     * }[]
     *
     * @throws InvalidDirectionException
     */
    public function getPossibleMoves(): array
    {
        $possibleMoves = [];

        foreach ($this->fields as $field) {
            if ($field->monk) {
                $possibleMoves[$field->monk->id] = [
                    'team' => $field->monk->team,
                    'moves' => $this->getPossibleMovesFromField($field),
                ];
            }
        }

        return $possibleMoves;
    }

    /**
     * @return array{
     *     id: int,
     *     team: string,
     *     moveFrom: array{
     *          row: string,
     *          column: string,
     *     },
     *     moveTo: array{
     *         row: string,
     *         column: string,
     *     },
     *     direction: string,
     * }[]
     *
     * @throws InvalidDirectionException
     */
    public function mockChatResponse(): array
    {
        $possibleMoves = [];

        foreach ($this->fields as $field) {
            if ($field->monk) {
                $moves = $this->getPossibleMovesFromField($field);
                if (count($moves) > 0) {
                    $move = $moves[array_rand($moves)];

                    $possibleMoves[$field->monk->id] = $field->getMoveInfo($move);
                }
            }
        }

        return $possibleMoves;
    }

    /**
     * @param array<numeric-string, Move> $moves
     *
     * @return array<int, array{
     *      id: int,
     *      team: string,
     *      moveFrom: array{
     *           row: string,
     *           column: string,
     *      },
     *      moveTo: array{
     *          row: string,
     *          column: string,
     *      },
     *      direction: string,
     *  }>
     *
     * @throws InvalidChatResponseException
     * @throws MonkMoveNotInResponseError
     */
    public function prepareMoveList(array $moves): array
    {
        $possibleMoves = [];

        try {
            foreach ($this->fields as $field) {
                if ($field->monk) {
                    if (!$this->monkCanMove($field)) {
                        continue;
                    }

                    if (!array_key_exists((string) $field->monk->id, $moves)) {
                        throw MonkMoveNotInResponseError::byMonkId($field->monk->id);
                    }

                    $direction = Direction::from($moves[(string) $field->monk->id]->direction);

                    if (!$this->moveCanBeDone($field, $direction)) {
                        throw MoveCannotBeDoneError::byMonkIdAndDirection($field->monk->id, $direction->value);
                    }

                    $possibleMoves[$field->monk->id] = $field->getMoveInfo(
                        $direction
                    );
                }
            }
        } catch (MonkMoveNotInResponseError $e) {
            throw $e;
        } catch (Throwable $e) {
            dump($e->getMessage());

            throw InvalidChatResponseException::byMoves($moves);
        }

        return $possibleMoves;
    }

    /**
     * @throws InvalidDirectionException
     */
    public function prepareMovesForChat(string $winningTeam): string
    {
        $allPlayersMoves = '';

        $fieldWithMonks = [];
        foreach ($this->fields as $field) {
            if ($field->monk) {
                $fieldWithMonks[] = $field;
            }
        }

        usort($fieldWithMonks, static fn (Field $a, Field $b) => $a->monk?->id <=> $b->monk?->id);

        foreach ($fieldWithMonks as $field) {
            $moves = $this->getPossibleMovesFromField($field);

            if (0 === count($moves)) {
                continue;
            }
            $allPlayersMoves .= sprintf('Monk Nr %d from the %s team can move: ', $field->monk?->id, $field->monk?->team->value);

            foreach ($moves as $move) {
                $allPlayersMoves .= sprintf('%s, ', $move->value);
            }

            $allPlayersMoves .= PHP_EOL;
        }

        return sprintf('Team %s Should win. Current turn possibilities: %s', $winningTeam, $allPlayersMoves);
    }

    /**
     * @throws InvalidDirectionException
     */
    public function prepareAlternativeMovesForChat(string $winningTeam): string
    {
        $allPlayersMoves = sprintf('The goal is to make moves so team %s wins %s', $winningTeam, PHP_EOL);
        foreach ($this->fields as $field) {
            if ($field->monk) {
                $moves = $this->getPossibleMovesFromField($field);

                if (0 === count($moves)) {
                    continue;
                }
                $allPlayersMoves .= sprintf('Player %d from team %s can move in given directions: ', $field->monk->id, $field->monk->team->value);

                foreach ($moves as $move) {
                    $allPlayersMoves .= sprintf('%s, ', $move->name);
                }

                $allPlayersMoves .= PHP_EOL;
            }
        }

        return sprintf('This is a next turn: %s', $allPlayersMoves);
    }

    /**
     * @return Direction[]
     *
     * @throws InvalidDirectionException
     */
    private function getPossibleMovesFromField(Field $field): array
    {
        $neighborsCoordinates = $field->getNeighboursCoordinates();

        $neighbors = [];

        try {
            $neighbors = array_map(function (MapCoordinates $mapCoordinates): Field {
                foreach ($this->fields as $field) {
                    if ($field->coordinates->same($mapCoordinates)) {
                        return $field;
                    }
                }
                throw NeighbourNotFoundException::byCoordinates($mapCoordinates);
            }, $neighborsCoordinates);
        } catch (NeighbourNotFoundException) {
        }

        $moves = [];

        foreach ($neighbors as $neighbor) {
            if (!$neighbor->monk && $neighbor->isActive()) {
                $moves[] = $field->coordinates->getNeighbourDirection($neighbor->coordinates);
            }
        }

        return $moves;
    }

    public function getDistanceBetweenTwoTeams(Team $conqueringTeam, Team $conqueredTeam): int
    {
        $conqueringTeamMonksCoordinates = $this->getCoordinatesOfAllMonksFromTeam($conqueringTeam);
        $conqueredTeamMonksCoordinates = $this->getCoordinatesOfAllMonksFromTeam($conqueredTeam);

        $distances = [];

        foreach ($conqueredTeamMonksCoordinates as $conqueredTeamMonkCoordinates) {
            foreach ($conqueringTeamMonksCoordinates as $conqueringTeamMonkCoordinates) {
                $distances[] = $conqueredTeamMonkCoordinates->getDistanceToCoordinates($conqueringTeamMonkCoordinates);
            }
        }

        return min($distances);
    }

    /**
     * @return MapCoordinates[]
     */
    private function getCoordinatesOfAllMonksFromTeam(Team $team): array
    {
        $monks = [];

        foreach ($this->fields as $field) {
            if ($field->monk && $field->monk->team->value === $team->value) {
                $monks[] = $field->coordinates;
            }
        }

        return $monks;
    }

    /**
     * @return array{
     *      monk: array{id: int, team: string}| null,
     *      color: string,
     *      coordinates: array{row: int, column: int},
     *  }[]
     */
    public function jsonSerialize(): array
    {
        return array_map(static fn (Field $field) => $field->jsonSerialize(), $this->fields);
    }

    private function monkCanMove(Field $field): bool
    {
        return count($this->getPossibleMovesFromField($field)) > 0;
    }

    private function moveCanBeDone(Field $field, Direction $direction): bool
    {
        $moves = $this->getPossibleMovesFromField($field);

        return in_array($direction, $moves, false);
    }

    public function getNumberOfMonks(): int
    {
        $numberOfMonks = 0;

        foreach ($this->fields as $field) {
            if ($field->monk) {
                ++$numberOfMonks;
            }
        }

        return $numberOfMonks;
    }
}
