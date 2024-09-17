<?php

declare(strict_types=1);

namespace App\Game\Application\Query;

use App\Core\Domain\Uuid;
use App\Game\Application\Query\ChatSessionQuery\ChatSession;
use App\Game\Domain\Exception\ChatSessionNotFoundException;

interface ChatSessionQuery
{
    /**
     * @throws ChatSessionNotFoundException
     */
    public function getSession(Uuid $id): ChatSession;

    /**
     * @return array{
     *     id: string,
     *     created_at: string,
     *     updated_at: string,
     * }[]
     */
    public function allWithDates(): array;
}
