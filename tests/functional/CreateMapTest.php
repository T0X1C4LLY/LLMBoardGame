<?php

namespace App\Tests\functional;

use App\Game\Application\UseCase\CreateMap;
use App\Core\Infrastructure\Symfony\UuidV4;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\Messenger\MessageBusInterface;

class CreateMapTest extends KernelTestCase
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

    public function testAddingNewMap(): void
    {
        // arrange
        $mapId = UuidV4::generateNew();
        $sessionId = UuidV4::generateNew();

        $command = new CreateMap\Command(
            $mapId,
            [null],
            $sessionId,
            $this->clock->now(),
        );

        // act
        $this->messageBus->dispatch($command);

        $mapIdFromDataBase = $this->connection->fetchOne('
            SELECT id
            FROM maps
            WHERE id = :id;
        ', [
            'id' => $mapId->toString(),
        ]);

        // assert
        self::assertSame($mapId->toString(), $mapIdFromDataBase);
    }
}
