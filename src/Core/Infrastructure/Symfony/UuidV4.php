<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Symfony;

use App\Core\Domain\Uuid as UuidInterface;
use Symfony\Component\Uid\UuidV4 as SymfonyUuidV4;

readonly class UuidV4 implements UuidInterface
{
    private function __construct(
        private SymfonyUuidV4 $uuid,
    ) {
    }

    public static function fromString(string $uuid): self
    {
        return new self(SymfonyUuidV4::fromString($uuid));
    }

    public static function generateNew(): self
    {
        return new self(SymfonyUuidV4::v4());
    }

    public static function isValid(string $uuid): bool
    {
        return SymfonyUuidV4::isValid($uuid);
    }

    public function toString(): string
    {
        return $this->uuid->toRfc4122();
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
