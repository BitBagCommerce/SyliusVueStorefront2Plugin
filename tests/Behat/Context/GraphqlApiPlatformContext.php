<?php

declare(strict_types=1);

namespace Tests\BitBag\SyliusGraphqlPlugin\Behat\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Exception;
use Sylius\Behat\Service\SharedStorageInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Tests\BitBag\SyliusGraphqlPlugin\Behat\Client\GraphqlClient;
use Tests\BitBag\SyliusGraphqlPlugin\Behat\Client\GraphqlClientInterface;
use Tests\BitBag\SyliusGraphqlPlugin\Behat\Model\OperationRequestInterface;
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
    public function IShouldReceiveAJsonResponse(): void
    {
        $response = $this->client->getLastResponse();
        Assert::isInstanceOf($response, JsonResponse::class);

        $json = $this->client->getJsonFromResponse($response);
        if ($json === null) {
            throw new Exception('Return data is not Json format');
        }
    }

    /**
     * @When I should see following response:
     *
     * @throws Exception
     */
    public function iShouldSeeFollowingResponse(PyStringNode $json): bool
    {
        /** @var array $expected */
        $expected = json_decode($json->getRaw(), true);
        /** @var array $lastResponse */
        $lastResponse = $this->sharedStorage->get(GraphqlClient::LAST_GRAPHQL_RESPONSE);
        $result_array = $this->differ->diff($expected, $lastResponse);
        if (empty($result_array)) {
            return true;
        }

        throw new Exception('Expected response doest match last one');
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
     * @var mixed
     * @Then I set :sharedStorageKey object :propertyName property to :value
     */
    public function iSetObjectPropertyToValue(string $sharedStorageKey, string $propertyName, $value): void
    {
        try {
            $storageValue = (array) $this->sharedStorage->get($sharedStorageKey);
        } catch (\InvalidArgumentException $e) {
            $storageValue = [];
        }
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
     * @return mixed
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
                return $value;
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
     */
    public function iSaveValueAtKeyOfThisModelResponse(string $key, string $name): void
    {
        $value = $this->client->getValueAtKey($key);
        $this->sharedStorage->set($name, $value);
    }

    private function getJsonFromResponse(string $response): ?array
    {
        /** @var array $jsonData */
        try {
            $jsonData = json_decode($response, true);
        }catch (Exception $exception){
            print_r($exception->getMessage());
        }
        if (json_last_error() === \JSON_ERROR_NONE) {
            return $jsonData;
        }

        return null;
    }
}
