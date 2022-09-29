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
use ApiPlatform\Metadata\Extractor\ResourceExtractorInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ShopUser;
use Sylius\Component\Core\Model\ShopUserInterface;

final class InterfaceExtractorResourceMetadataFactorySpec extends ObjectBehavior
{
    public function let(
        ResourceExtractorInterface $extractor
    ): void {
        $this->beConstructedWith($extractor, null);
    }

    public function it_creates_metadata(
        ResourceExtractorInterface $extractor
    ): void {
        $resourceClass = ShopUserInterface::class;
        $resources = [
            ShopUser::class => [
                'shortName' => 'ShopUser',
            ],
        ];

        $extractor->getResources()->willReturn($resources);

        $baseResourceClass = (string) preg_replace('/Interface/', '', $resourceClass);
        $metadata = $resources[$baseResourceClass];

        $newResourceMetadata = new ResourceMetadata();
        $resourceMetadata = $newResourceMetadata->withShortName($metadata['shortName']);

        $this->create($resourceClass)->shouldBeLike($resourceMetadata);
    }

    public function it_throws_exception_when_class_not_exist(): void
    {
        $resourceClass = 'This/Class/Does/NotExist';

        $this->shouldThrow(ResourceClassNotFoundException::class)
            ->during('create', [$resourceClass]);
    }
}
