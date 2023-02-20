<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\Security\Voter;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

final class WishlistVoter extends Voter
{
    public const PREFIX = 'BITBAG_SYLIUS_VUE_STOREFRONT2_PLUGIN_WISHLIST_';

    public const VIEW = self::PREFIX . 'VIEW';

    public const UPDATE = self::PREFIX . 'UPDATE';

    public const CLEAR = self::PREFIX . 'CLEAR';

    public const DELETE = self::PREFIX . 'DELETE';

    public const ADD_ITEM = self::PREFIX . 'ADD_ITEM';

    public const REMOVE_ITEM = self::PREFIX . 'REMOVE_ITEM';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function supportsAttribute(string $attribute): bool
    {
        return str_starts_with($attribute, self::PREFIX);
    }

    public function supportsType(string $subjectType): bool
    {
        return is_a($subjectType, WishlistInterface::class, true);
    }

    protected function supports(string $attribute, $subject): bool
    {
        if (!in_array($attribute, [self::VIEW, self::UPDATE, self::CLEAR, self::DELETE, self::ADD_ITEM, self::REMOVE_ITEM], true)) {
            return false;
        }

        if (!$subject instanceof WishlistInterface) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof ShopUserInterface || !$this->security->isGranted('ROLE_USER')) {
            return false;
        }

        return match ($attribute) {
            self::VIEW,
            self::UPDATE,
            self::CLEAR,
            self::DELETE,
            self::ADD_ITEM,
            self::REMOVE_ITEM => $this->isOwnerWishlist($user, $subject),
            default => throw new \LogicException('WishlistVoter: This code should not be reached!')
        };
    }

    protected function isOwnerWishlist(ShopUserInterface $shopUser, WishlistInterface $wishlist): bool
    {
        $wishlistShopUser = $wishlist->getShopUser();

        return null !== $wishlistShopUser && $shopUser->getId() === $wishlistShopUser->getId();
    }
}
