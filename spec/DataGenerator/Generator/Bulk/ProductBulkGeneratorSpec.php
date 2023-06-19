<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Bulk;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Bulk\BulkGeneratorContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\DataGeneratorCommandContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Entity\ProductContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Exception\InvalidContextException;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Bulk\ProductBulkGenerator;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Entity\GeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductInterface;

final class ProductBulkGeneratorSpec extends ObjectBehavior
{
    public function let(
        EntityManagerInterface $entityManager,
        GeneratorInterface $generator,
    ): void {
        $this->beConstructedWith($entityManager, $generator);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ProductBulkGenerator::class);
    }

    public function it_generates(
        BulkGeneratorContextInterface $context,
        ProductContextInterface $entityContext,
        GeneratorInterface $generator,
        ProductInterface $product,
        EntityManagerInterface $entityManager,
    ): void {
        $className = Product::class;
        $quantity = 100;
        $flushAfter = 10;

        $context->getIO()->shouldBeCalled();
        $context->getEntityContext()->willReturn($entityContext->getWrappedObject());
        $entityContext->className()->willReturn($className);
        $context->getQuantity()->willReturn($quantity);

        for ($i = 1; $i <= $quantity; $i++) {
            $generator->generate($entityContext->getWrappedObject())->willReturn($product->getWrappedObject());

            $entityManager->persist($product->getWrappedObject())->shouldBeCalled();

            if ($i % $flushAfter === 0) {
                $entityManager->flush()->shouldBeCalled();
            }
        }

        $entityManager->flush()->shouldBeCalled();

        $this->generate($context);
    }

    public function it_throws_exception_on_invalid_context(DataGeneratorCommandContextInterface $context): void
    {
        $this->shouldThrow(InvalidContextException::class)
            ->during('generate', [$context]);
    }
}
