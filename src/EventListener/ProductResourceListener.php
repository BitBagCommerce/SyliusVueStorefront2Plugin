<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\EventListener;

use Imagine\Exception\RuntimeException;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Imagine\Data\DataManager;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class ProductResourceListener
{
    private CacheManager $cacheManager;

    private DataManager $dataManager;

    private FilterManager $filterManager;

    public function __construct(
        CacheManager $cacheManager,
        DataManager $dataManager,
        FilterManager $filterManager
    ) {
        $this->cacheManager = $cacheManager;
        $this->dataManager = $dataManager;
        $this->filterManager = $filterManager;
    }

    public function resolveThumbnails(GenericEvent $event): void
    {
        $product = $event->getSubject();

        if ($product instanceof ProductInterface === false) {
            return;
        }

        $this->addThumbnailsForFixtures($product);
    }

    public function resolveThumbnailsForFixtures(ProductInterface $product): void
    {
        $this->addThumbnailsForFixtures($product);
    }

    private function addThumbnailsForFixtures(ProductInterface $product): void
    {
        $filters = [
            'sylius_shop_product_large_thumbnail',
            'sylius_shop_product_thumbnail',
        ];

        /** @var ProductImageInterface $image */
        foreach ($product->getImages() as $image) {
            try {
                /** @var string $filter */
                foreach ($filters as $filter) {
                    if (null !== $image->getPath()) {
                        $binary = $this->dataManager->find($filter, $image->getPath());
                        $binary = $this->filterManager->applyFilter($binary, $filter);

                        $this->cacheManager->store($binary, $image->getPath(), $filter);
                    }
                }
            } catch (RuntimeException $exception) {

            }
        }
    }
}
