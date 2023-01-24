<?php

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\Validator;

use Symfony\Component\Validator\Constraint;

final class UniqueNameShopUserWishlist extends Constraint
{
    public string $message = 'validator.message.wishlist.name.unique';
}
