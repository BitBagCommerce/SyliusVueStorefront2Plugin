<?php
/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\BulkContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\ProductContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\BulkGenerator;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\GeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Mockery\Matcher\Any;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class BulkGeneratorSpec extends ObjectBehavior
{
    public function let(
        EntityManagerInterface $entityManager,
        SymfonyStyle $io,
        GeneratorInterface $generator,
        BulkContextInterface $bulkContext,
    ): void {
        $this->beConstructedWith($entityManager, $io, $generator, $bulkContext);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(BulkGenerator::class);
    }

    public function it_generates(
        EntityManagerInterface $entityManager,
        GeneratorInterface $generator,
        BulkContextInterface $bulkContext,
        \DateTime $dateTime,
        ProductContextInterface $context,
        ProductInterface $product,
    ): void {
        $dateTimeFormat = 'Y-m-d H:i:s';
        $dateTimeString = new \DateTime();
        $entityName = 'Product';
        $quantity = 100;
        $flushAfter = 10;

        $dateTime->format($dateTimeFormat)->willReturn($dateTimeString);
        $context->entityName()->willReturn($entityName);
        $bulkContext->getContext()->willReturn($context->getWrappedObject());

        $bulkContext->getQuantity()->willReturn($quantity);


        for ($i = 1; $i <= $quantity; $i++) {
            $bulkContext->getContext()->willReturn($context->getWrappedObject());
            $generator->generate($context->getWrappedObject())->willReturn($product);

            $entityManager->persist($product->getWrappedObject())->shouldBeCalled();

            if ($i % $flushAfter === 0) {
                $entityManager->flush()->shouldBeCalled();
            }
        }

        $entityManager->flush()->shouldBeCalled();

        $this->generate();
    }
}
