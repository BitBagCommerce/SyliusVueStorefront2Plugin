<?php

declare(strict_types=1);

namespace Tests\BitBag\SyliusGraphqlPlugin\Behat\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Mink\Element\DocumentElement;
use Exception;
use Sylius\Behat\Service\SharedStorageInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Tests\BitBag\SyliusGraphqlPlugin\Behat\Client\GraphqlClient;
use Tests\BitBag\SyliusGraphqlPlugin\Behat\Client\GraphqlClientInterface;
use Tests\BitBag\SyliusGraphqlPlugin\Behat\Model\OperationRequestInterface;

/**
 * Context for GraphQL.
 */
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
     * @When I create a GraphQL request
     */
    public function iCreateAGraphQlRequest(): void
    {
        /** @var OperationRequestInterface $operation */
        $operation = $this->sharedStorage->get(GraphqlClient::$GRAPHQL_OPERATION);
        $request = $this->client->prepareRequest($operation);
        $this->sharedStorage->set(GraphqlClient::$GRAPHQL_VARIABLES, new ParameterBag());
        $this->sharedStorage->set(GraphqlClient::$GRAPHQL_REQUEST, $request);
    }

    /**
     * @When I send that GraphQL request
     */
    public function iSendThatGraphQlRequest(): void
    {
        $this->client->send();
    }

    /**
     * @Then I should receive a JSON response
     */
    public function IShouldReceiveAJsonResponse(): void
    {
        $content = (string)$this->client->getLastResponse();
        $json = $this->client->getJsonFromResponse($content);
        if ($json === null) {
            throw new Exception('Return data is not Json format');
        }
        $this->sharedStorage->set(GraphqlClient::$LAST_GRAPHQL_RESPONSE,$json);
    }

    /**
     * @param DocumentElement $response
     * @throws Exception
     */
    private function saveLastResponse(DocumentElement $response): void
    {
        $content = $response->getContent();
        $json = $this->getJsonFromResponse($content);
        if ($json === null) {
            throw new Exception('Return data is not Json format');
        }
        $this->sharedStorage->set(GraphqlClient::$LAST_GRAPHQL_RESPONSE, $json);
    }

    /**
     * @When I should see following response:
     * @throws Exception
     */
    public function iShouldSeeFollowingResponse(PyStringNode $json): bool
    {
        /** @var array $expected */
        $expected = json_decode($json->getRaw(), true);
        /** @var array $lastResponse */
        $lastResponse = $this->sharedStorage->get(GraphqlClient::$LAST_GRAPHQL_RESPONSE);
        $result_array = GraphqlClient::diff($expected, $lastResponse);
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
        /** @var ParameterBag $variables */
        $variables = $this->sharedStorage->get(GraphqlClient::$GRAPHQL_VARIABLES);
        $variables->set($key,$value);
        $this->sharedStorage->set(GraphqlClient::$GRAPHQL_VARIABLES, $variables);
    }

    /**
     * @Then This response should contain :key
     * @throws Exception
     */
    public function thatResponseShouldContain(string $key): bool
    {
        $this->getValueAtKey($key);
        return true;
    }

    /**
     * @param mixed $value
     * @Then This response should contain :key equal to :value
     * @throws Exception
     */
    public function thatResponseShouldContainKeyWithValue(string $key, $value): bool
    {
        /** @psalm-suppress MixedAssignment */
        $responseValueAtKey = $this->getValueAtKey($key);
        return ($value === $responseValueAtKey);
    }

    /**
     * @return mixed
     * @throws Exception
     */
    private function getValueAtKey(string $key)
    {
        /** @var array $lastResponse */
        $lastResponse = $this->sharedStorage->get(GraphqlClient::$LAST_GRAPHQL_RESPONSE);
        $flatResponse = $this->client->flattenArray($lastResponse);
        if (!array_key_exists($key, $flatResponse)) {
            throw new Exception(sprintf('Last response did not have any key named %s', $key));
        }
        return $flatResponse[$key];
    }

    /**
     * @Then I should see following error message :message
     * @throws Exception
     */
    public function iShouldSeeFollowingErrorMessage(string $message): bool
    {
        /** @var  array $lastResponse */
        $lastResponse = $this->sharedStorage->get(GraphqlClient::$LAST_GRAPHQL_RESPONSE);
        $flatLastResponse = $this->client->flattenArray($lastResponse);
        if (!array_key_exists('errors.0.message', $flatLastResponse)) {
            throw new Exception('No errors were produced.');
        }
        if ($flatLastResponse['errors.0.message'] !== $message) {
            throw new Exception('The error message is different then expected.');
        }

        return true;
    }

    /**
     * @param string $response
     * @return array|null
     */
    private function getJsonFromResponse(string $response): ?array
    {
        /** @var array $jsonData */
        $jsonData = json_decode($response, true);
        if (json_last_error() === \JSON_ERROR_NONE) {
            return $jsonData;
        }

        return null;
    }

}
