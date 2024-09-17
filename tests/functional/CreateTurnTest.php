<?php

namespace App\Tests\functional;

use App\Game\Application\UseCase\CreateTurn;
use App\Core\Infrastructure\Symfony\UuidV4;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\Messenger\MessageBusInterface;

class CreateTurnTest extends KernelTestCase
{
    private MessageBusInterface $messageBus;
    private MockClock $clock;
    private Connection $connection;

    protected function setUp(): void
    {
        parent::setUp();

        $purger = new ORMPurger(static::getContainer()->get('doctrine.orm.default_entity_manager'));
        $purger->purge();

        $this->messageBus = static::getContainer()->get('messenger.bus.default');
        $this->clock = new MockClock('2023-12-02 19:32:00');
        $this->connection = static::getContainer()->get(Connection::class);
    }

    public function testAddingNewSession(): void
    {
        // arrange
        $turnId = UuidV4::generateNew();
        $mapId = UuidV4::generateNew();
        $sessionId = UuidV4::generateNew();

        $command = new CreateTurn\Command(
            $turnId,
            $mapId,
            $sessionId,
            [],
            1,
            0,
            false,
            $this->clock->now(),
            'red',
        );

        // act
        $this->messageBus->dispatch($command);

        $turnIdFromDataBase = $this->connection->fetchOne('
            SELECT id
            FROM turns
            WHERE id = :id;
        ', [
            'id' => $turnId->toString(),
        ]);

        // assert
        self::assertSame($turnId->toString(), $turnIdFromDataBase);
    }
}
