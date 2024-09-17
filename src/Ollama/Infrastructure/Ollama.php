<?php

declare(strict_types=1);

namespace App\Ollama\Infrastructure;

use App\Ollama\Domain\Ollama as OllamaInterface;
use JsonException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Process;

readonly class Ollama implements OllamaInterface
{
    public function __construct(
        private LoggerInterface $logger,
        private string $ollamaHost,
        private string $ollamaPort,
        private string $modelName,
    ) {
    }

    /**
     * @throws JsonException
     */
    public function chat(array $messages): string
    {
        $process = new Process(
            [
                'curl',
                sprintf('http://%s:%s/api/chat', $this->ollamaHost, $this->ollamaPort),
                '-d',
                sprintf(
                    '{"model": "%s", "messages": %s, "raw": true, "stream": false}',
                    $this->modelName,
                    json_encode($messages, JSON_THROW_ON_ERROR),
                ),
            ],
            timeout: 3000
        );

        $process->run();

        $result = $process->getOutput();

        try {
            /** @var array{
             *     model: string,
             *     created_at: string,
             *     message: array{
             *          role: string,
             *          content: string,
             *     },
             *     done: bool,
             *     total_duration: int,
             *     load_duration: int,
             *     prompt_eval_count: int,
             *     prompt_eval_duration: int,
             *     eval_count: int,
             *     eval_duration: int,
             * } $decodedResult
             */
            $decodedResult = json_decode($result, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            $this->logger->error(sprintf(
                '[Ollama] Unable to decode ollama response: %s',
                $result
            ));

            throw $e;
        }

        return trim($decodedResult['message']['content']);
    }
}
