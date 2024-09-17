<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Doctrine\Type;

use App\Core\Domain\Uuid;
use App\Core\Infrastructure\Symfony\UuidV4;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;

class UuidType extends GuidType
{
    private const NAME = 'uuid_type';

    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * @param Uuid|null $value
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        return $value?->toString();
    }

    /**
     * @param string|null $value
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?Uuid
    {
        return $value ? UuidV4::fromString($value) : null;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
