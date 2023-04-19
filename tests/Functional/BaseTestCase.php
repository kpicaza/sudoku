<?php

declare(strict_types=1);

namespace Test\Kpicaza\Sudoku\Functional;

use Doctrine\DBAL\Connection;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class BaseTestCase extends KernelTestCase
{
    protected KernelInterface $symfonyKernel;
    protected ContainerInterface $container;
    protected Connection $connection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->symfonyKernel = self::bootKernel();
        $this->container = $this->symfonyKernel->getContainer()->get('test.service_container');
        $this->connection = $this->container->get(Connection::class);
        $this->connection->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->connection->rollback();
        parent::tearDown();
    }
}
