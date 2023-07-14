<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataProvider\PreFetcher;

use Webmozart\Assert\Assert;

class CompositePreFetcher implements PreFetcherInterface
{
    private iterable $preFetchers = [];

    public function __construct(iterable $preFetchers = [])
    {
        Assert::allIsInstanceOf($preFetchers, RestrictedPreFetcherInterface::class);
        $this->preFetchers = $preFetchers;
    }

    public function preFetchData(
        array $parentIds,
        array $context,
    ): void {
        $attributes = $context['attributes'] ?? null;
        if ($attributes === null) {
            return;
        }
        $attributes = $this->gatherAttributesToPreFetch($attributes);

        foreach (array_keys($attributes) as $attribute) {
            /** @var RestrictedPreFetcherInterface $preFetcher */
            foreach ($this->preFetchers as $preFetcher) {
                if ($preFetcher->supports($context, $attribute)) {
                    $preFetcher->preFetchData($parentIds, $context);
                }
            }
        }
    }

    public function getPreFetchedData(
        string $identifier,
        ?array $context = [],
    ): array {
        foreach ($this->preFetchers as $preFetcher) {
            if ($preFetcher->supports($context)) {
                return $preFetcher->getPreFetchedData($identifier);
            }
        }

        return [];
    }

    private function gatherAttributesToPreFetch(array $attributes): array
    {
        $attributes = array_filter(
            $attributes,
            static fn($attr) => is_array($attr['collection'] ?? null)
        );

        foreach ($attributes as $attribute => $fields) {
            $nestedAttributes = $this->gatherAttributesToPreFetch($fields['collection']);
            if (count($nestedAttributes) > 0) {
                foreach (array_keys($nestedAttributes) as $nestedAttribute) {
                    unset($attributes[$attribute]['collection'][$nestedAttribute]);
                }
            }

            $attributes = array_merge($attributes, $nestedAttributes);
        }

        return $attributes;
    }
}
