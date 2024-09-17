<?php

declare(strict_types=1);

namespace App\Game\Domain\Exception;

use App\Core\Domain\Uuid;
use Exception;

class ChatSessionNotFoundException extends Exception
{
    public static function byId(Uuid $id): self
    {
        return new self(sprintf('Chat session with id %s not found', $id->toString()));
    }
}
