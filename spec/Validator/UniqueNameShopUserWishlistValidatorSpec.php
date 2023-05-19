<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\Validator;

use BitBag\SyliusVueStorefront2Plugin\Validator\UniqueNameShopUserWishlist;
use BitBag\SyliusVueStorefront2Plugin\Validator\UniqueNameShopUserWishlistValidator;
use BitBag\SyliusWishlistPlugin\Checker\WishlistNameCheckerInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class UniqueNameShopUserWishlistValidatorSpec extends ObjectBehavior
{
    public function let(
        Security $security,
        WishlistRepositoryInterface $wishlistRepository,
        WishlistNameCheckerInterface $wishlistNameChecker,
    ): void {
        $this->beConstructedWith(
            $security,
            $wishlistRepository,
            $wishlistNameChecker,
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(UniqueNameShopUserWishlistValidator::class);
        $this->shouldHaveType(ConstraintValidator::class);
    }

    public function it_extends_constraint_validator_class(): void
    {
        $this->shouldHaveType(ConstraintValidator::class);
    }

    public function it_blocks_when_exist_wishlist_with_the_same_name(
        Security $security,
        ShopUserInterface $shopUser,
        WishlistRepositoryInterface $wishlistRepository,
        WishlistInterface $wishlist,
        WishlistNameCheckerInterface $wishlistNameChecker,
        ExecutionContextInterface $context,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
    ): void {
        $newName = 'Wishlist';
        $uniqueNameShopUserWishlist = new UniqueNameShopUserWishlist();

        $userId = 1;
        $security->getUser()->willReturn($shopUser);
        $shopUser->getId()->willReturn($userId);

        $name = 'Wishlist';
        $wishlist->getName()->willReturn($name);
        $wishlistRepository->findAllByShopUser($userId)->willReturn([$wishlist]);

        $wishlistNameChecker->check($name, $newName)->willReturn(true);

        $constraintViolationBuilder->addViolation()->shouldBeCalled();
        $context->buildViolation('validator.message.wishlist.name.unique')->willReturn($constraintViolationBuilder);

        $this->initialize($context);
        $this->validate($newName, $uniqueNameShopUserWishlist);
    }

    public function it_does_not_blocks_when_not_exist_wishlist_with_the_same_name(
        Security $security,
        ShopUserInterface $shopUser,
        WishlistRepositoryInterface $wishlistRepository,
        WishlistInterface $wishlist,
        WishlistNameCheckerInterface $wishlistNameChecker,
    ): void {
        $newName = 'Wishlist2';
        $uniqueNameShopUserWishlist = new UniqueNameShopUserWishlist();

        $userId = 1;
        $security->getUser()->willReturn($shopUser);
        $shopUser->getId()->willReturn($userId);

        $name = 'Wishlist';
        $wishlist->getName()->willReturn($name);
        $wishlistRepository->findAllByShopUser($userId)->willReturn([$wishlist]);

        $wishlistNameChecker->check($name, $newName)->willReturn(false);

        $this->validate($newName, $uniqueNameShopUserWishlist);
    }
}
