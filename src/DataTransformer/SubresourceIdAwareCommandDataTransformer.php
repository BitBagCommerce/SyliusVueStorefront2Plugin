<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataTransformer;

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

    /** @param SubresourceIdAwareInterface|mixed $object */
    public function transform(
        $object,
        string $to,
        array $context = [],
    ): SubresourceIdAwareInterface {
        Assert::isInstanceOf($object, SubresourceIdAwareInterface::class);
        if (null !== $object->getSubresourceId()) {
            return $object;
        }

        $request = $this->requestStack->getCurrentRequest();
        Assert::notNull($request);
        $attributes = $request->attributes;

        $attributeKey = $object->getSubresourceIdAttributeKey();

        Assert::true($attributes->has($attributeKey), 'Path does not have subresource id');

        /** @var string $subresourceId */
        $subresourceId = $attributes->get($object->getSubresourceIdAttributeKey());

        $object->setSubresourceId($subresourceId);

        return $object;
    }

    /** @param mixed $object */
    public function supportsTransformation($object): bool
    {
        return $object instanceof SubresourceIdAwareInterface;
    }
}
