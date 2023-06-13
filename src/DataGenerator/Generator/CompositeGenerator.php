<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class CompositeGenerator implements GeneratorInterface
{
    /** @var GeneratorInterface[] */
    private array $generators;

    private EntityManagerInterface $entityManager;

    private SymfonyStyle $io;

    public function __construct(
        array $generators,
        EntityManagerInterface $entityManager
    ) {
        $this->generators = $generators;
        $this->entityManager = $entityManager;
    }

    public function setIOConsoleContext(SymfonyStyle $io): void
    {
        $this->io = $io;
    }

    public function generate(): void
    {
        foreach ($this->generators as $generator) {
            $this->io->info(sprintf(
                '%s Generating %ss',
                (new \DateTime())->format('Y-m-d H:i:s'),
                $this->entityName(),
            ));

            $this->io->progressStart($this->quantity);

            for ($i = 1; $i <= $this->quantity; $i++) {
                $object = $generator->generate();

                $this->entityManager->persist($object);
                $this->io->progressAdvance();

                if ($i % self::FLUSH_AFTER === 0) {
                    $this->entityManager->flush();
                }
            }

            $this->entityManager->flush();

            $io->progressFinish();
        }
    }
}
