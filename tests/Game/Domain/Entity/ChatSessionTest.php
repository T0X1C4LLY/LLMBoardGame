<?php

namespace App\Tests\Game\Domain\Entity;

use App\Game\Domain\Entity\ChatSession;
use App\Game\Domain\Enum\ChatRole;
use App\Core\Infrastructure\Symfony\UuidV4;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\MockClock;

class ChatSessionTest extends TestCase
{
    private readonly ChatSession $chatSession;
    private DateTimeImmutable $now;

    protected function setUp(): void
    {
        parent::setUp();

        $clock = new MockClock();

        $this->now = $clock->now();

        $this->chatSession = new ChatSession(
            UuidV4::generateNew(),
            [
                [
                    'role' => ChatRole::ASSISTANT->value,
                    'some message',
                ]
            ],
            $this->now,
            $this->now,
        );
    }

    public static function messagesProvider(): array
    {
        return [
            [
                [
                    [
                        'role' => ChatRole::USER->value,
                        'content' =>'test message',
                    ],
                ]
            ],
            [
                [
                    [
                        'role' => ChatRole::USER->value,
                        'content' =>'first message',
                    ],
                    [
                        'role' => ChatRole::SYSTEM->value,
                        'content' =>'first response',
                    ],
                    [
                        'role' => ChatRole::USER->value,
                        'content' =>'second message',
                    ],
                    [
                        'role' => ChatRole::SYSTEM->value,
                        'content' =>'second response',
                    ],
                ]
            ],
        ];
    }

    /** @dataProvider messagesProvider */
    public function testAddMessage(array $messages): void
    {
        // arrange
        $oldMessages = $this->chatSession->getMessages();

        // act
        foreach ($messages as $message) {
            $this->chatSession->addMessage(
                ChatRole::from($message['role']),
                $message['content'],
                $this->now,
            );
        }

        // assert
        $newMessages = $oldMessages;

        foreach ($messages as $message) {
            $newMessages[] = $message;
        }

        self::assertSame($newMessages, $this->chatSession->getMessages());
    }
}
