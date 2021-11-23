<?php

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\Behat\Context;

use Behat\Behat\Context\Context;
use BitBag\SyliusGraphqlPlugin\Behat\Client\GraphqlClientInterface;
use BitBag\SyliusGraphqlPlugin\Behat\Model\OperationRequestInterface;
use Exception;
use Sylius\Behat\Service\SharedStorageInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Webmozart\Assert\Assert;

/** Context for GraphQL. */
final class GraphqlApiPlatformContext implements Context
{
    private GraphqlClientInterface $client;

    private SharedStorageInterface $sharedStorage;

    public function __construct(GraphqlClientInterface $client, SharedStorageInterface $sharedStorage)
    {
        $this->client = $client;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Transform /^(true|false)$/
     */
    public function getBooleanFromString(string $boolean): bool
    {
        return (bool) $boolean;
    }

    /**
     * @When I send that GraphQL request
     */
    public function iSendThatGraphQlRequest(): void
    {
        $this->client->send();
    }

    /**
     * @When I send that GraphQL request as authorised user
     */
    public function iSendThatGraphQlRequestAsAuthorisedUser(): void
    {
        $this->client->addAuthorization();
        $this->client->send();
    }

    /**
     * @Then I should receive a JSON response
     *
     * @throws Exception
     */
    public function iShouldReceiveAJsonResponse(): void
    {
        $response = $this->client->getLastResponse();
        Assert::isInstanceOf($response, JsonResponse::class);

        $json = $this->client->getJsonFromResponse($response);
        if ($json === null) {
            throw new Exception('Return data is not Json format');
        }
    }

    /**
     * @param mixed $value
     * @When I set :key field to :value
     */
    public function iSetKeyFieldToValue(string $key, $value): void
    {
        $operation = $this->client->getLastOperationRequest();
        Assert::isInstanceOf($operation, OperationRequestInterface::class);
        $operation->addVariable($key, $value);
    }

    /**
     * @When I set :key field to integer :value
     */
    public function iSetKeyFieldToIntegerValue(string $key, int $value): void
    {
        $this->iSetKeyFieldToValue($key, $value);
    }

    /**
     * @When I set :key field to value :name
     * @When I set :key field to previously saved value :name
     * @When I set :key field to previously saved :type value :name
     */
    public function iSetKeyFieldToPreviouslySavedValue(string $key, string $name, string $type = null): void
    {
        $operation = $this->client->getLastOperationRequest();
        Assert::isInstanceOf($operation, OperationRequestInterface::class);
        $value = $this->sharedStorage->get($name);
        Assert::notEmpty($value);
        $value = $this->castToType($value, $type);
        $operation->addVariable($key, $value);
    }

    /**
     * @Then I set :sharedStorageKey object :propertyName property to :value
     *
     * @param mixed $value
     */
    public function iSetObjectPropertyToValue(string $sharedStorageKey, string $propertyName, $value): void
    {
        try {
            $storageValue = (array) $this->sharedStorage->get($sharedStorageKey);
        } catch (\InvalidArgumentException $e) {
            $storageValue = [];
        }
        /** @psalm-suppress MixedAssignment */
        $storageValue[$propertyName] = $value;
        $this->sharedStorage->set($sharedStorageKey, $storageValue);
    }

    /**
     * @Then This response should contain :key
     *
     * @throws Exception
     */
    public function thatResponseShouldContain(string $key): bool
    {
        $this->client->getValueAtKey($key);

        return true;
    }

    /**
     * @Then This response should contain empty :key
     *
     * @throws Exception
     */
    public function thatResponseShouldContainEmpty(string $key): bool
    {
        $value = $this->client->getValueAtKey($key);
        Assert::isEmpty($value);

        return true;
    }

    /**
     * @param mixed $value
     * @Then This response should contain :key equal to :value
     *
     * @throws Exception
     */
    public function thatResponseShouldContainKeyWithValue(string $key, $value): void
    {
        /** @psalm-suppress MixedAssignment */
        $responseValueAtKey = $this->client->getValueAtKey($key);
        Assert::same($value, $responseValueAtKey);
    }

    /**
     * @param mixed $value
     *
     * @return bool|float|int|string|array
     */
    private function castToType($value, string $type = null)
    {
        switch ($type) {
            case 'bool':
                $value = (bool) $value;

                break;
            case 'float':
                $value = (float) $value;

                break;
            case 'int':
                $value = (int) $value;

                break;
            case 'string':
                $value = (string) $value;

                break;
            default:
                $value = is_array($value) ? (array) $value: (string) $value;
                break;
        }

        return $value;
    }

    /**
     * @Then I should see following error message :message
     *
     * @throws Exception
     */
    public function iShouldSeeFollowingErrorMessage(string $message): bool
    {
        $arrayContent = $this->client->getLastResponseArrayContent();
        $flatLastResponse = $this->client->flattenArray($arrayContent);
        if (!array_key_exists('errors.0.message', $flatLastResponse)) {
            throw new Exception('No errors were produced.');
        }
        if ($flatLastResponse['errors.0.message'] !== $message) {
            throw new Exception('The error message is different then expected.');
        }

        return true;
    }

    /**
     * @Then I save key :key of this response as :name
     * @Then I save key :key of this response as :name as :type
     */
    public function iSaveValueAtKeyOfThisModelResponse(string $key, string $name, ?string $type = null): void
    {
        /** @psalm-suppress MixedAssignment */
        $value = $this->client->getValueAtKey($key);
        $value = $this->castToType($value, $type);
        $this->sharedStorage->set($name, $value);
    }

    private function getJsonFromResponse(string $response): ?array
    {
        $jsonData = [];

        try {
            /** @var array $jsonData */
            $jsonData = json_decode($response, true);
        } catch (Exception $exception) {
            print_r($exception->getMessage());
        }
        if (json_last_error() === \JSON_ERROR_NONE) {
            return $jsonData;
        }

        return null;
    }
}
