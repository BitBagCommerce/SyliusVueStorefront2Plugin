<?php
/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\BulkGenerator;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\BulkContext\BulkContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\EntityContext\ProductContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\BulkGenerator\ProductBulkGenerator;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\EntityGenerator\GeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductInterface;

class ProductBulkGeneratorSpec extends ObjectBehavior
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
        BulkContextInterface $context,
        ProductContextInterface $entityContext,
        GeneratorInterface $generator,
        ProductInterface $product,
        EntityManagerInterface $entityManager,
//        \DateTime $dateTime,
    ): void {
        $entityName = 'Product';
        $quantity = 100;
        $dateTimeFormat = 'Y-m-d H:i:s';
        $dateTimeString = new \DateTime();
        $flushAfter = 10;

        $context->getIO()->shouldBeCalled();
        $context->getEntityContext()->willReturn($entityContext->getWrappedObject());
        $entityContext->entityName()->willReturn($entityName);
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
}
