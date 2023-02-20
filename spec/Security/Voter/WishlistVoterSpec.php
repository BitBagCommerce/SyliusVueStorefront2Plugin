<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\Security\Voter;

use BitBag\SyliusVueStorefront2Plugin\Security\Voter\WishlistVoter;
use BitBag\SyliusWishlistPlugin\Entity\Wishlist;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProduct;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Security;

final class WishlistVoterSpec extends ObjectBehavior
{
    public function let(Security $security): void
    {
        $this->beConstructedWith($security);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(WishlistVoter::class);
    }

    public function it_supports_expected_attributes(): void
    {
        foreach ($this->getSupportsAttributes() as $attribute) {
            $this->supportsAttribute($attribute)->shouldReturn(true);
        }
    }

    public function it_does_not_support_unexpected_attributes(): void
    {
        $this->supportsAttribute('TEST')->shouldReturn(false);
        $this->supportsAttribute('BITBAG_SYLIUS_VUE_STOREFRONT2_PLUGIN_WISHLIST')->shouldReturn(false);
        $this->supportsAttribute('UPDATE')->shouldReturn(false);
    }

    public function it_supports_expected_class(): void
    {
        $this->supportsType(Wishlist::class)->shouldReturn(true);
    }

    public function it_does_not_support_unexpected_class(): void
    {
        $this->supportsType(WishlistProduct::class)->shouldReturn(false);
    }

    public function it_abstains_if_does_not_support_attribute(
        TokenInterface $token,
        WishlistInterface $wishlist,
    ): void {
        $this->vote($token, $wishlist, ['unsupported'])->shouldReturn(VoterInterface::ACCESS_ABSTAIN);
    }

    public function it_abstains_if_does_not_support_class(
        TokenInterface $token,
        WishlistProductInterface $wishlistProduct,
    ): void {
        $this->vote($token, $wishlistProduct, [WishlistVoter::VIEW])->shouldReturn(VoterInterface::ACCESS_ABSTAIN);
    }

    public function it_denied_if_anonymous_user(
        TokenInterface $token,
        WishlistInterface $wishlist,
    ): void {
        $token->getUser()->willReturn(null);
        foreach ($this->getSupportsAttributes() as $attribute) {
            $this->vote($token, $wishlist, [$attribute])->shouldReturn(VoterInterface::ACCESS_DENIED);
        }
    }

    public function it_denied_if_user_has_not_role(
        TokenInterface $token,
        ShopUserInterface $user,
        WishlistInterface $wishlist,
        Security $security,
    ): void {
        $token->getUser()->willReturn($user);
        $security->isGranted('ROLE_USER')->willReturn(false);

        foreach ($this->getSupportsAttributes() as $attribute) {
            $this->vote($token, $wishlist, [$attribute])->shouldReturn(VoterInterface::ACCESS_DENIED);
        }
    }

    public function it_denied_if_user_does_not_owner_wishlist(
        TokenInterface $token,
        ShopUserInterface $user,
        WishlistInterface $wishlist,
        ShopUserInterface $wishlistShopUser,
        Security $security,
    ): void {
        $token->getUser()->willReturn($user);
        $security->isGranted('ROLE_USER')->willReturn(true);
        $user->getId()->willReturn(1);

        $wishlistShopUser->getId()->willReturn(2);
        $wishlist->getShopUser()->willReturn($wishlistShopUser);

        foreach ($this->getSupportsAttributes() as $attribute) {
            $this->vote($token, $wishlist, [$attribute])->shouldReturn(VoterInterface::ACCESS_DENIED);
        }
    }

    public function it_granted_if_user_is_owner_wishlist(
        TokenInterface $token,
        ShopUserInterface $user,
        WishlistInterface $wishlist,
        ShopUserInterface $wishlistShopUser,
        Security $security,
    ): void {
        $token->getUser()->willReturn($user);
        $security->isGranted('ROLE_USER')->willReturn(true);
        $user->getId()->willReturn(2);

        $wishlistShopUser->getId()->willReturn(2);
        $wishlist->getShopUser()->willReturn($wishlistShopUser);

        foreach ($this->getSupportsAttributes() as $attribute) {
            $this->vote($token, $wishlist, [$attribute])->shouldReturn(VoterInterface::ACCESS_GRANTED);
        }
    }

    private function getSupportsAttributes(): array
    {
        return [
            'BITBAG_SYLIUS_VUE_STOREFRONT2_PLUGIN_WISHLIST_VIEW',
            'BITBAG_SYLIUS_VUE_STOREFRONT2_PLUGIN_WISHLIST_UPDATE',
            'BITBAG_SYLIUS_VUE_STOREFRONT2_PLUGIN_WISHLIST_CLEAR',
            'BITBAG_SYLIUS_VUE_STOREFRONT2_PLUGIN_WISHLIST_DELETE',
            'BITBAG_SYLIUS_VUE_STOREFRONT2_PLUGIN_WISHLIST_ADD_ITEM',
            'BITBAG_SYLIUS_VUE_STOREFRONT2_PLUGIN_WISHLIST_REMOVE_ITEM',
        ];
    }
}
