<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\CommandHandler\Cart;

use ApiPlatform\Core\Api\IriConverterInterface;
use BitBag\SyliusVueStorefront2Plugin\Command\Cart\AddItemToCart;
use BitBag\SyliusVueStorefront2Plugin\CommandHandler\Trait\AddProductVariantToCartTrait;
use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class AddItemToCartHandler implements MessageHandlerInterface
{
    use AddProductVariantToCartTrait;

    private OrderRepositoryInterface $orderRepository;

    private IriConverterInterface $iriConverter;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        OrderModifierInterface $orderModifier,
        CartItemFactoryInterface $cartItemFactory,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        AvailabilityCheckerInterface $availabilityChecker,
        IriConverterInterface $iriConverter,
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderModifier = $orderModifier;
        $this->cartItemFactory = $cartItemFactory;
        $this->orderItemQuantityModifier = $orderItemQuantityModifier;
        $this->availabilityChecker = $availabilityChecker;
        $this->iriConverter = $iriConverter;
    }

    public function __invoke(AddItemToCart $addItemToCart): OrderInterface
    {
        /** @var ProductVariantInterface|null $productVariant */
        $productVariant = $this->iriConverter->getItemFromIri($addItemToCart->productVariant);

        Assert::notNull($productVariant);
        Assert::notNull($addItemToCart->orderTokenValue);

        /** @var OrderInterface|null $cart */
        $cart = $this->orderRepository->findCartByTokenValue($addItemToCart->orderTokenValue);
        Assert::notNull($cart);

        $this->addProductVariantToCart($productVariant, $addItemToCart->quantity, $cart);

        return $cart;
    }
}
