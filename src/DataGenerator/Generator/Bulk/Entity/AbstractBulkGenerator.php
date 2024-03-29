<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Bulk\Entity;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\ContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Generator\GeneratorContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Exception\InvalidContextException;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Bulk\BulkGeneratorInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Entity\GeneratorInterface;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractBulkGenerator implements BulkGeneratorInterface
{
    private EntityManagerInterface $entityManager;

    private GeneratorInterface $generator;

    public function __construct(
        EntityManagerInterface $entityManager,
        GeneratorInterface $generator,
    ) {
        $this->entityManager = $entityManager;
        $this->generator = $generator;
    }

    public function generate(ContextInterface $context): void
    {
        if (!$context instanceof GeneratorContextInterface) {
            throw new InvalidContextException();
        }

        $io = $context->getIO();
        $quantity = $context->getQuantity();

        if ($quantity === 0) {
            return;
        }

        $io->info(sprintf(
            '%s Generating %ss',
            (new DateTime())->format('Y-m-d H:i:s'),
            self::resolveEntityName($context->entityName()),
        ));

        $io->progressStart($quantity);

        for ($i = 1; $i <= $quantity; $i++) {
            $object = $this->generator->generate($context);

            $this->entityManager->persist($object);
            $io->progressAdvance();

            if ($i % self::FLUSH_AFTER === 0) {
                $this->entityManager->flush();
            }
        }

        $this->entityManager->flush();

        $io->progressFinish();
    }

    private static function resolveEntityName(string $entityName): string
    {
        $parts = explode('\\', $entityName);

        return end($parts);
    }
}
