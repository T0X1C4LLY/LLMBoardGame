<?php

declare(strict_types=1);

namespace App\Game\Infrastructure\Doctrine\Type;

use App\Game\Domain\Enum\Team;
use App\Game\Domain\MapElement\Field;
use App\Game\Domain\MapElement\Monk;
use App\Game\Domain\ValueObject\MapCoordinates;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;
use JsonException;

final class FieldType extends JsonType
{
    public function getName(): string
    {
        return 'field_type';
    }

    /**
     * @param Field[] $value
     *
     * @throws JsonException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        return json_encode($value, JSON_THROW_ON_ERROR);
    }

    /**
     * @param string $value
     *
     * @return Field[]
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): array
    {
        /** @var array{
         *     monk: array{id: int, team: string}|null,
         *     color: string,
         *     coordinates: array{row: int, column: int},
         * }[] $decodedValues
         */
        $decodedValues = json_decode($value, true, 512, JSON_THROW_ON_ERROR);

        return array_map(static fn (array $field): Field => new Field(
            new MapCoordinates($field['coordinates']['row'], $field['coordinates']['column']),
            $field['monk'] ? new Monk($field['monk']['id'], Team::from($field['monk']['team'])) : null,
            $field['color']
        ), $decodedValues);
    }
}
