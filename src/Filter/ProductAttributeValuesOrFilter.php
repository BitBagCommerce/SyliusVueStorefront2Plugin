<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\Filter;

use ApiPlatform\Core\Api\IdentifiersExtractorInterface;
use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

final class ProductAttributeValuesOrFilter extends AbstractContextAwareFilter
{
    protected IriConverterInterface $iriConverter;

    protected PropertyAccessorInterface $propertyAccessor;

    protected ?IdentifiersExtractorInterface $identifiersExtractor;

    protected EntityRepository $productAttributeRepository;

    protected ChannelContextInterface $channelContext;

    private const PROPERTY_NAME = 'attributes';

    private array $storageTypes;

    private array $attributesIds;

    public function __construct(
        EntityRepository $productAttributeRepository,
        ChannelContextInterface $channelContext,
        ManagerRegistry $managerRegistry,
        ?RequestStack $requestStack,
        IriConverterInterface $iriConverter,
        PropertyAccessorInterface $propertyAccessor = null,
        LoggerInterface $logger = null,
        array $properties = null,
        IdentifiersExtractorInterface $identifiersExtractor = null,
        NameConverterInterface $nameConverter = null
    ) {
        parent::__construct($managerRegistry, $requestStack, $logger, $properties, $nameConverter);

        if (null === $identifiersExtractor) {
            @trigger_error('Not injecting ItemIdentifiersExtractor is deprecated since API Platform 2.5 and can lead to unexpected behaviors, it will not be possible anymore in API Platform 3.0.', \E_USER_DEPRECATED);
        }

        $this->iriConverter = $iriConverter;
        $this->identifiersExtractor = $identifiersExtractor;
        $this->propertyAccessor = $propertyAccessor ?: PropertyAccess::createPropertyAccessor();
        $this->channelContext = $channelContext;
        $this->productAttributeRepository = $productAttributeRepository;
    }

    protected function getIriConverter(): IriConverterInterface
    {
        return $this->iriConverter;
    }

    protected function getPropertyAccessor(): PropertyAccessorInterface
    {
        return $this->propertyAccessor;
    }

    protected function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null
    ) {
        if ($property !== self::PROPERTY_NAME) {
            return;
        }

        if (!is_array($value) || count($value) === 0) {
            return;
        }

        if (!is_a($resourceClass, ProductInterface::class, true)) {
            return;
        }

        $this->cacheAttributes(array_keys($value));

        $alias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->setParameter("localeCode", $this->channelContext->getChannel()->getDefaultLocale()->getCode());

        $i=0;
        foreach ($value as $attributeCode => $attributeValues) {
            if (!isset($this->storageTypes[$attributeCode])) {
                continue;
            }

            $storage = $this->storageTypes[$attributeCode];
            $supportedStorages = [
                AttributeValueInterface::STORAGE_INTEGER,
                AttributeValueInterface::STORAGE_FLOAT,
                AttributeValueInterface::STORAGE_TEXT
            ];

            if (!in_array($storage, $supportedStorages)) {
                continue;
            }

            $queryBuilder->join("$alias.attributes", "cav$i", Join::WITH, "cav$i.localeCode = :localeCode");

            $queryBuilder->andWhere("cav$i.attribute = :attributeId$i");
            $queryBuilder->setParameter(":attributeId$i", $this->attributesIds[$attributeCode]);

            switch ($storage) {
                case AttributeValueInterface::STORAGE_INTEGER: {
                    $queryBuilder->andWhere("cav$i.integer >= :value1$i");
                    $queryBuilder->andWhere("cav$i.integer <= :value2$i");
                    $queryBuilder->setParameter("value1$i", reset($attributeValues));
                    $queryBuilder->setParameter("value2$i", end($attributeValues));
                } break;
                case AttributeValueInterface::STORAGE_FLOAT: {
                    $queryBuilder->andWhere("cav$i.float >= :value1$i");
                    $queryBuilder->andWhere("cav$i.float <= :value2$i");
                    $queryBuilder->setParameter("value1$i", reset($attributeValues));
                    $queryBuilder->setParameter("value2$i", end($attributeValues));
                } break;
                case AttributeValueInterface::STORAGE_TEXT: {
                    $queryBuilder->andWhere("cav$i.text IN(:values$i)");
                    $queryBuilder->setParameter("values$i", $attributeValues);
                } break;
                default: {
                    continue 2;
                }
            }

            $i++;
        }
    }

    private function cacheAttributes(array $attributeCodes): void
    {
        $attributes = $this->productAttributeRepository->findBy([
            'code' => $attributeCodes
        ]);

        $this->storageTypes = [];
        $this->attributesIds = [];

        /** @var ProductAttributeInterface $attribute */
        foreach ($attributes as $attribute) {
            $this->storageTypes[$attribute->getCode()] = $attribute->getStorageType();
            $this->attributesIds[$attribute->getCode()] = $attribute->getId();
        }
    }

    private function getAttributeIds(array $attributeCodes): array
    {

    }

    public function getDescription(
        string $resourceClass
    ): array {
        return [
            'attributes' => [
                'type' => 'array',
                'required' => false,
                'property' => null,
                'swagger' => [
                    'name' => 'Product attributes filter',
                    'description' => 'Get a collection of product with chosen attributes',
                ],
            ],
        ];
    }

}
