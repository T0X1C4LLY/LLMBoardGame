<?php

declare(strict_types=1);

namespace App\Ollama\Domain;

interface Ollama
{
    /**
     * @param array{role: string, content: string}[] $messages
     */
    public function chat(array $messages): string;
}
