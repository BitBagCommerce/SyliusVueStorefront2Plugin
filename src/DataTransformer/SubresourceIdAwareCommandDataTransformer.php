<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\DataTransformer;

use Sylius\Bundle\ApiBundle\Command\SubresourceIdAwareInterface;
use Sylius\Bundle\ApiBundle\DataTransformer\CommandDataTransformerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\Assert;

/** @experimental */
final class SubresourceIdAwareCommandDataTransformer implements CommandDataTransformerInterface
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function transform($object, string $to, array $context = [])
    {
        if (null !== $object->getSubresourceId()) {
            return $object;
        }

        $attributes = $this->requestStack->getCurrentRequest()->attributes;

        $attributeKey = $object->getSubresourceIdAttributeKey();

        Assert::true($attributes->has($attributeKey), 'Path does not have subresource id');

        /** @var string $subresourceId */
        $subresourceId = $attributes->get($object->getSubresourceIdAttributeKey());

        $object->setSubresourceId($subresourceId);

        return $object;
    }

    public function supportsTransformation($object): bool
    {
        return $object instanceof SubresourceIdAwareInterface;
    }
}
