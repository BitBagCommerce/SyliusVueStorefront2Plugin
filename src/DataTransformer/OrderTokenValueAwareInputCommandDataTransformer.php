<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\DataTransformer;

use Exception;
use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;
use Sylius\Bundle\ApiBundle\DataTransformer\CommandDataTransformerInterface;
use Sylius\Component\Core\Model\OrderInterface;

/** @experimental */
final class OrderTokenValueAwareInputCommandDataTransformer implements CommandDataTransformerInterface
{
    public function transform($object, string $to, array $context = [])
    {
        if (array_key_exists('object_to_populate', $context)) {
            /** @var OrderInterface $cart */
            $cart = $context['object_to_populate'];
            $tokenValue = $cart->getTokenValue();
        } elseif (property_exists($object, 'orderTokenValue')) {
            $tokenValue = $object->orderTokenValue;
        } else {
            throw new Exception('Token value could not be found');
        }

        $object->setOrderTokenValue($tokenValue);

        return $object;
    }

    public function supportsTransformation($object): bool
    {
        return $object instanceof OrderTokenValueAwareInterface;
    }
}
