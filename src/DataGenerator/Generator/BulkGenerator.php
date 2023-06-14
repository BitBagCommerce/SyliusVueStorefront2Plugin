<?php
/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\BulkContextInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class BulkGenerator implements BulkGeneratorInterface
{
    private EntityManagerInterface $entityManager;

    private SymfonyStyle $io;

    private GeneratorInterface $generator;

    private BulkContextInterface $bulkContext;

    public function __construct(
        EntityManagerInterface $entityManager,
        SymfonyStyle $io,
        GeneratorInterface $generator,
        BulkContextInterface $bulkContext,
    ) {
        $this->entityManager = $entityManager;
        $this->generator = $generator;
        $this->bulkContext = $bulkContext;
        $this->io = $io;
    }

    public function generate(): void
    {
        $this->io->info(sprintf(
            '%s Generating %ss',
            (new \DateTime())->format('Y-m-d H:i:s'),
            $this->bulkContext->getContext()::entityName(),
        ));

        $quantity = $this->bulkContext->getQuantity();

        $this->io->progressStart($quantity);

        for ($i = 1; $i <= $quantity; $i++) {
            $object = $this->generator->generate($this->bulkContext->getContext());

            $this->entityManager->persist($object);
            $this->io->progressAdvance();

            if ($i % self::FLUSH_AFTER === 0) {
                $this->entityManager->flush();
            }
        }

        $this->entityManager->flush();

        $this->io->progressFinish();
    }
}
