<?php

namespace App\Tests\Game\Application\Query\ChatSessionQuery;

use App\Game\Application\Query\ChatSessionQuery\ChatSession;
use PHPUnit\Framework\TestCase;

class ChatSessionTest extends TestCase
{
    public static function chatSessionProvider(): array
    {
        return [
            [
                [
                    'id' => '9277b76a-454f-4b84-b7b9-e9bdec8a7770',
                    'messages' => '[{"user": "foo", "system": "bar"}]',
                    'created_at' => '2023-12-02 14:57:00',
                    'updated_at' => '2023-12-02 14:57:00',
                ]
            ]
        ];
    }

    public static function chatSessionInvalidProvider(): array
    {
        return [
            [
                [
                    'id' => '9277b76a-454f-4b84-b7b9-e9bdec8a7770',
                    'messages' => '["user": "foo", "system": "bar"]',
                    'created_at' => '2023-12-02 14:57:00',
                    'updated_at' => '2023-12-02 14:57:00',
                ]
            ]
        ];
    }

    /** @dataProvider chatSessionProvider */
    public function testFromArray(array $session): void
    {
        // act
        $chatSession = ChatSession::fromArray($session);

        // assert
        self::assertSame($session['id'], $chatSession->id->toString());
        self::assertSame(json_decode($session['messages'], true), $chatSession->messages);
        self::assertSame($session['created_at'], $chatSession->createdAt->format('Y-m-d H:i:s'));
        self::assertSame($session['updated_at'], $chatSession->updatedAt->format('Y-m-d H:i:s'));
    }

    /** @dataProvider chatSessionInvalidProvider */
    public function testJsonExceptionWillBeThrownInFromArray(array $session): void
    {
        // assert
        $this->expectException(\JsonException::class);

        // act
        ChatSession::fromArray($session);
    }
}
