<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\Doctrine\Orm\Filter;

use ApiPlatform\Core\Api\FilterInterface;
use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\PropertyHelperTrait;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Sylius\Component\Attribute\AttributeType\CheckboxAttributeType;
use Sylius\Component\Attribute\AttributeType\DateAttributeType;
use Sylius\Component\Attribute\AttributeType\DatetimeAttributeType;
use Sylius\Component\Attribute\AttributeType\IntegerAttributeType;
use Sylius\Component\Attribute\AttributeType\PercentAttributeType;
use Sylius\Component\Attribute\AttributeType\SelectAttributeType;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Webmozart\Assert\Assert;

/**
 * Filters the collection by value of given attribute IRI
 */
class AttributeFilterSpec extends AbstractContextAwareFilter implements FilterInterface
{
    use PropertyHelperTrait;

    private IriConverterInterface $iriConverter;

    public const OPERATOR_EXACT = 'exact';

    public const OPERATOR_PARTIAL = 'partial';

    public const ATTRIBUTE_ID = 'attribute_id';

    public const VALUE = 'value';

    public function __construct(
        ManagerRegistry $managerRegistry,
        IriConverterInterface $iriConverter,
        ?RequestStack $requestStack = null,
        LoggerInterface $logger = null,
        array $properties = null,
        NameConverterInterface $nameConverter = null
    ) {
        parent::__construct(
            $managerRegistry,
            $requestStack,
            $logger,
            $properties,
            $nameConverter
        );

        $this->iriConverter = $iriConverter;
    }

    /**
     * @param mixed $value
     */
    protected function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null
    ): void {
        if (
            !\is_array($value) ||
            !$this->isPropertyEnabled($property, $resourceClass)
        ) {
            return;
        }

        $attributeId = $this->getAttributeId($value, $property);
        $extractedValue = $this->extractValue($value, $property);
        if (null === $attributeId || null === $extractedValue) {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];

        if ($this->isPropertyNested($property . '.id', $resourceClass)) {
            [$alias] = $this->addJoinsForNestedProperty($property . '.id', $alias, $queryBuilder, $queryNameGenerator, $resourceClass);
        }

        Assert::string($alias);

        $this->addWhere(
            $queryBuilder,
            $queryNameGenerator,
            $alias,
            $property,
            $attributeId,
            $extractedValue
        );
    }

    /** @param mixed $value */
    protected function addWhere(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $alias,
        string $field,
        string $attributeId,
        $value,
        string $operator = self::OPERATOR_EXACT
    ): void {
        $valueParameter = $queryNameGenerator->generateParameterName($field);

        /** @var AttributeInterface $attribute */
        $attribute = $this->iriConverter->getItemFromIri($attributeId);
        $attributeType = $attribute->getType();
        Assert::notNull($attributeType);
        $value = $this->normalizeValue($value, $attributeType);

        switch ($operator) {
            case self::OPERATOR_EXACT:
                $queryBuilder
                    ->andWhere(
                        sprintf(
                            '%s.%s = :%s',
                            $alias,
                            $attributeType,
                            $valueParameter
                        )
                    )
                    ->setParameter($valueParameter, $value);

                break;
            case self::OPERATOR_PARTIAL:
                if (null === $value) {
                    return;
                }

                $queryBuilder
                    ->andWhere(sprintf('%s.%s > :%s', $alias, $field, $valueParameter))
                    ->setParameter($valueParameter, $value);

                break;
        }
    }

    public function getDescription(string $resourceClass): array
    {
        $description = [];

        $properties = $this->getProperties();
        if (null === $properties) {
            $properties = array_fill_keys($this->getClassMetadata($resourceClass)->getFieldNames(), null);
        }

        /** @var string $property */
        foreach (array_keys($properties) as $property) {
            $description += $this->getFilterDescription($property, self::ATTRIBUTE_ID);
            $description += $this->getFilterDescription($property, self::VALUE);
        }

        return $description;
    }

    protected function getFilterDescription(string $fieldName, string $operator): array
    {
        /** @var string $propertyName */
        $propertyName = $this->normalizePropertyName($fieldName);

        return [
            sprintf('%s[%s]', $propertyName, $operator) => [
                'property' => $propertyName,
                'type' => 'string',
                'required' => false,
            ],
        ];
    }

    private function getAttributeId(array $values, string $property): ?string
    {
        if (array_key_exists(self::ATTRIBUTE_ID, $values)) {
            /** @var string $attributeId */
            $attributeId = $values[self::ATTRIBUTE_ID];
        } else {
            $this->getLogger()->notice('Invalid filter ignored', [
                'exception' => new InvalidArgumentException(
                    sprintf(
                        '%s is required for "%s" property',
                        self::ATTRIBUTE_ID,
                        $property
                    )
                ),
            ]);

            return null;
        }

        return $attributeId;
    }

    private function extractValue(array $values, string $property): ?string
    {
        if (array_key_exists(self::VALUE, $values)) {
            /** @var string $value */
            $value = $values[self::VALUE];
        } else {
            $this->getLogger()->notice('Invalid filter ignored', [
                'exception' => new InvalidArgumentException(
                    sprintf(
                        '%s is required for "%s" property',
                        self::VALUE,
                        $property
                    )
                ),
            ]);

            return null;
        }

        return $value;
    }

    /**
     * @param ?mixed $value
     *
     * @return int|float|string|bool|\DateTime|null
     *
     * @throws \Exception
     */
    private function normalizeValue($value, string $type)
    {
        switch ($type) {
            case CheckboxAttributeType::TYPE:
                $value = (bool) $value;

                break;
            case DateAttributeType::TYPE:
            case DatetimeAttributeType::TYPE:
                Assert::string($value);
                $value = new \DateTime($value);

                break;
            case IntegerAttributeType::TYPE:
                $value = (int) $value;

                break;
            case PercentAttributeType::TYPE:
                $value = (float) $value;

                break;
            case SelectAttributeType::TYPE:
                //assume IRI ?
                //TODO::
                $value = (bool) $value;

                break;
            default:
                $value = (null !== $value) ? (string) $value : null;
        }

        return $value;
    }
}
