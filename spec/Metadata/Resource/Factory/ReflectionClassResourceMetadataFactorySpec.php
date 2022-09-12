<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusGraphqlPlugin\Metadata\Resource\Factory;

use ApiPlatform\Core\Exception\ResourceClassNotFoundException;
use ApiPlatform\Core\Metadata\Resource\ResourceMetadata;
use PhpSpec\ObjectBehavior;

final class ReflectionClassResourceMetadataFactorySpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith(null);
    }

    public function it_throws_exception_when_class_not_exist(): void
    {
        $resourceClass = 'This/Class/Does/NotExist';

        $this->shouldThrow(ResourceClassNotFoundException::class)
            ->during('create', [$resourceClass])
        ;
    }

    public function it_creates_metadata(): void
    {
        $resourceClass = ObjectBehavior::class;
        $reflectionClass = new \ReflectionClass($resourceClass);

        $resourceMetadata = new ResourceMetadata();
        $resourceMetadata = $resourceMetadata->withShortName($reflectionClass->getShortName());

        $this->create($resourceClass)->shouldBeLike($resourceMetadata);
    }
}
