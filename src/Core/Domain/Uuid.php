<?php

declare(strict_types=1);

namespace App\Core\Domain;

interface Uuid
{
    public static function fromString(string $uuid): self;

    public static function generateNew(): self;

    public static function isValid(string $uuid): bool;

    public function toString(): string;
}
