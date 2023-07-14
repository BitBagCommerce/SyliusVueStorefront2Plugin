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

        # todo: make it an array of keys in case of duplicated names;
        # todo: the array should be nested according to the query/model structure, so the keys could be recognized in ->supports
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
        $filteredAttributes = $this->filterAttributes($attributes);

        foreach ($filteredAttributes as $attribute => $fields) {
            $isCollection = is_array($fields['collection'] ?? null);

            if ($isCollection) {
                $nestedAttributes = $this->gatherAttributesToPreFetch($fields['collection']);
            } else {
                $nestedAttributes = $this->gatherAttributesToPreFetch($fields['edges']['node']);
            }

            $this->removeNestedAttributes($filteredAttributes, $nestedAttributes, $attribute, $isCollection);
            $filteredAttributes = array_merge($filteredAttributes, $nestedAttributes);
        }

        return $filteredAttributes;
    }

    private function filterAttributes(array $attributes): array
    {
        return array_filter(
            $attributes,
            static fn($attr) => is_array($attr['collection'] ?? $attr['edges'] ?? null)
        );
    }

    private function removeNestedAttributes(
        array &$attributes,
        array $nestedAttributes,
        string $attribute,
        bool $isCollection,
    ): void {
        if (count($nestedAttributes) > 0) {
            foreach (array_keys($nestedAttributes) as $nestedAttribute) {
                if ($isCollection) {
                    unset($attributes[$attribute]['collection'][$nestedAttribute]);
                } else {
                    unset($attributes[$attribute]['edges']['node'][$nestedAttribute]);
                }
            }
        }
    }
}
