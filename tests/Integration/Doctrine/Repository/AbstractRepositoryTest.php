<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusVueStorefront2Plugin\Integration\Doctrine\Repository;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class AbstractRepositoryTest extends KernelTestCase
{
    protected EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();

        /** @var EntityManagerInterface $em */
        $em = $this->getService('doctrine.orm.default_entity_manager');
        $this->entityManager = $em;

        $this->purgeDatabase();
    }

    protected function purgeDatabase(): void
    {
        $purger = new ORMPurger($this->entityManager);
        $purger->purge();
    }

    protected function getService(string $id): ?object
    {
        return self::$kernel->getContainer()
            ->get($id);
    }
}
