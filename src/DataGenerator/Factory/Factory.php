<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory;

use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory as FakerFactory;
use Faker\Generator;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class Factory implements FactoryInterface, BulkableInterface
{
    private EntityManagerInterface $entityManager;
    private SymfonyStyle $io;
    protected Generator $faker;
    private int $quantity;

    public function __construct(
        EntityManagerInterface $entityManager,
        InputInterface $input,
        OutputInterface $output,
    ) {
        $this->entityManager = $entityManager;
        $this->io = new SymfonyStyle($input, $output);
        $this->faker = FakerFactory::create();
        $this->quantity = 0;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function bulkCreate(): void
    {
        $this->io->info(sprintf(
            '%s Generating %ss',
            (new \DateTime())->format('Y-m-d H:i:s'),
            $this->entityName(),
        ));

        $this->io->progressStart($this->quantity);

        for ($i = 1; $i <= $this->quantity; $i++) {
            $object = $this->create();

            $this->entityManager->persist($object);
            $this->io->progressAdvance();

            if ($i % self::FLUSH_AFTER === 0) {
                $this->entityManager->flush();
            }
        }

        $this->entityManager->flush();

        $this->io->progressFinish();
    }

    abstract public function create(): ResourceInterface;

    abstract public function entityName(): string;
}
