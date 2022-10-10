<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\Filter;

use ApiPlatform\Core\Api\IdentifiersExtractorInterface;
use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
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

    protected EntityManagerInterface $entityManager;

    protected ChannelContextInterface $channelContext;

    protected const PROPERTY_NAME = 'attributes';

    public function __construct(
        EntityRepository $productAttributeRepository,
        EntityManagerInterface $entityManager,
        ChannelContextInterface $channelContext,
        ManagerRegistry $managerRegistry,
        ?RequestStack $requestStack,
        IriConverterInterface $iriConverter,
        PropertyAccessorInterface $propertyAccessor = null,
        LoggerInterface $logger = null,
        array $properties = null,
        IdentifiersExtractorInterface $identifiersExtractor = null,
        NameConverterInterface $nameConverter = null,
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
        $this->entityManager = $entityManager;
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
        string $operationName = null,
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

        $localeCode = $this->channelContext->getChannel()->getDefaultLocale()->getCode();

        $storageTypes = $this->getAttributesStorageTypes(array_keys($value));
        $productIds = $this->findAttributedProductIds(
            $value,
            $storageTypes,
            $localeCode,
        );

        $alias = $queryBuilder->getRootAliases()[0];

        $i = 0;
        foreach ($value as $attributeCode => $attributeValues) {
            if (!isset($storageTypes[$attributeCode])) {
                continue;
            }

            $storage = $storageTypes[$attributeCode];
            $supportedStorages = [
                AttributeValueInterface::STORAGE_INTEGER,
                AttributeValueInterface::STORAGE_FLOAT,
                AttributeValueInterface::STORAGE_TEXT,
            ];

            if (!in_array($storage, $supportedStorages)) {
                continue;
            }

            $queryBuilder->andWhere($alias . ".id IN(:productIds$i)");
            $queryBuilder->setParameter("productIds$i", $productIds[$attributeCode]);

            ++$i;
        }
    }

    private function getAttributesStorageTypes(array $attributeCodes): array
    {
        $attributes = $this->productAttributeRepository->findBy([
            'code' => $attributeCodes,
        ]);

        $storageTypes = [];

        /** @var ProductAttributeInterface $attribute */
        foreach ($attributes as $attribute) {
            $storageTypes[$attribute->getCode()] = $attribute->getStorageType();
        }

        return $storageTypes;
    }

    private function findAttributedProductIds(
        array $attributes,
        array $storageTypes,
        string $localeCode,
    ): array {
        $results = [];

        foreach ($attributes as $attributeCode => $attributeValues) {
            $qb = $this->entityManager->createQueryBuilder();

            $qb->select('s.id')
                ->from(ProductAttributeValueInterface::class, 'av')
                ->join('av.attribute', 'a')
                ->join('av.subject', 's')
            ;

            if (!isset($storageTypes[$attributeCode])) {
                continue;
            }

            $storage = $storageTypes[$attributeCode];

            switch ($storage) {
                case AttributeValueInterface::STORAGE_INTEGER:
                    $qb->andWhere('av.integer >= :value1');
                    $qb->andWhere('av.integer <= :value2');
                    $qb->setParameter('value1', reset($attributeValues));
                    $qb->setParameter('value2', end($attributeValues));

                    break;
                case AttributeValueInterface::STORAGE_FLOAT:
                    $qb->andWhere('av.float >= :value1');
                    $qb->andWhere('av.float <= :value2');
                    $qb->setParameter('value1', reset($attributeValues));
                    $qb->setParameter('value2', end($attributeValues));

                    break;
                case AttributeValueInterface::STORAGE_TEXT:
                    $qb->where('av.text IN(:attributeValues)')
                        ->setParameter('attributeValues', $attributeValues)
                    ;

                    break;
                default:
                    continue 2;
            }

            $results[$attributeCode] = $qb->andWhere('a.code = :attributeCode')
                ->andWhere('av.localeCode = :localeCode')
                ->setParameter('localeCode', $localeCode)
                ->setParameter('attributeCode', $attributeCode)
                ->getQuery()
                ->getSingleColumnResult()
            ;
        }

        return $results;
    }

    public function getDescription(
        string $resourceClass,
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
