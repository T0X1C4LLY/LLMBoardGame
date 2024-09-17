<?php

namespace App\Tests\functional;

use App\Game\Application\UseCase\CreateSession;
use App\Game\Application\UseCase\UpdateSession;
use App\Core\Infrastructure\Symfony\UuidV4;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\Messenger\MessageBusInterface;

class UpdateSessionTest extends KernelTestCase
{
    private MessageBusInterface $messageBus;
    private MockClock $clock;
    private Connection $connection;
    private UuidV4 $sessionId;

    protected function setUp(): void
    {
        parent::setUp();

        $purger = new ORMPurger(static::getContainer()->get('doctrine.orm.default_entity_manager'));
        $purger->purge();

        $this->messageBus = static::getContainer()->get('messenger.bus.default');
        $this->clock = new MockClock('2023-12-02 19:32:00');
        $this->connection = static::getContainer()->get(Connection::class);

        $this->sessionId = UuidV4::generateNew();

        $this->messageBus->dispatch(new CreateSession\Command(
            $this->sessionId,
            $this->clock->now(),
        ));
    }

    public function testAddingNewSession(): void
    {
        // arrange
        $messages = [['user' => 'new message']];

        $command = new UpdateSession\Command(
            $this->sessionId,
            $messages,
            $this->clock->now(),
        );

        // act
        $this->messageBus->dispatch($command);

        $sessionIdFromDataBase = $this->connection->fetchOne('
            SELECT id
            FROM chat_sessions
            WHERE id = :id;
        ', [
            'id' => $this->sessionId->toString(),
        ]);

        // assert
        self::assertSame($this->sessionId->toString(), $sessionIdFromDataBase);
    }
}
