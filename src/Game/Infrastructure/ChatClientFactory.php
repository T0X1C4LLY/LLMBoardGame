<?php

declare(strict_types=1);

namespace App\Game\Infrastructure;

use App\Game\Domain\ChatClient;
use App\Game\Infrastructure\Google\ChatClient as GoogleClient;
use App\Game\Infrastructure\Ollama\ChatClient as OllamaClient;
use App\Game\Infrastructure\OpenAi\ChatClient as OpenAIClient;
use LogicException;

readonly class ChatClientFactory
{
    public function __construct(
        private string $modelName,
        private OllamaClient $ollamaClient,
        private OpenAIClient $openAIClient,
        private GoogleClient $googleClient,
    ) {
    }

    public function getChatClient(): ChatClient
    {
        return match ($this->modelName) {
            'phi', 'phi3', 'llama2:13b', 'llama3' => $this->ollamaClient,
            'gpt-3.5-turbo' => $this->openAIClient,
            'bard' => $this->googleClient,
            default => throw new LogicException('Unsupported model name'),
        };
    }
}
