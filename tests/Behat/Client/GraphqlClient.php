<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusGraphqlPlugin\Behat\Client;

use Exception;
use const JSON_ERROR_NONE;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use Sylius\Behat\Service\SharedStorageInterface;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\BitBag\SyliusGraphqlPlugin\Behat\Model\OperationRequest;
use Tests\BitBag\SyliusGraphqlPlugin\Behat\Model\OperationRequestInterface;
use Webmozart\Assert\Assert;

final class GraphqlClient implements GraphqlClientInterface
{
    public const LAST_GRAPHQL_RESPONSE = 'lastGraphqlResponse';

    public const GRAPHQL_OPERATION = 'graphqlOperation';

    private AbstractBrowser $client;

    private SharedStorageInterface $sharedStorage;

    private string $authorizationHeader;

    private string $uri;

    private ParameterBag $headers;

    public function __construct(
        AbstractBrowser $client,
        SharedStorageInterface $sharedStorage,
        string $authorizationHeader,
        string $uri
    ) {
        $this->client = $client;
        $this->sharedStorage = $sharedStorage;
        $this->authorizationHeader = $authorizationHeader;
        $this->uri = $uri;
        $this->headers = new ParameterBag();
    }

    public function prepareOperation(
        string $name,
        string $formattedExpectedData,
        string $method = Request::METHOD_POST
    ): OperationRequestInterface {
        $operation = '
        mutation <name> ($input: <name>Input!) {
            <name>(input: $input){
            <expectedData>
          }
        }';

        $this->headers->add([
            'CONTENT_TYPE' => 'application/json',
        ]);
        $operation = str_replace('<name>', $name, $operation);
        $operation = str_replace('<expectedData>', $formattedExpectedData, $operation);

        return new OperationRequest($name, $operation, [], $method);
    }

    public function prepareQuery(
        string $name,
        string $formattedExpectedData,
        string $method = Request::METHOD_POST
    ): OperationRequestInterface {
        $operation = '
        query <name> {
            <name><filters>{
            <expectedData>
          }
        }';

        $this->headers->add([
            'CONTENT_TYPE' => 'application/json',
        ]);
        $operation = str_replace('<name>', $name, $operation);
        $operation = str_replace('<expectedData>', $formattedExpectedData, $operation);

        $operationRequest = new OperationRequest($name, $operation, [], $method);
        $operationRequest->setOperationType(OperationRequestInterface::OPERATION_QUERY);

        return $operationRequest;
    }

    public function getLastOperationRequest(): ?OperationRequestInterface
    {
        return $this->sharedStorage->get(self::GRAPHQL_OPERATION);
    }

    public function addAuthorization(): void
    {
        $token = $this->getToken();
        if (null !== $token) {
            $this->headers->add([
                'HTTP_AUTHORIZATION' => sprintf('%s %s', $this->authorizationHeader, $token),
            ]);
        }
    }

    public function getToken(): ?string
    {
        return $this->sharedStorage->has('token') ? (string) $this->sharedStorage->get('token') : null;
    }

    public function send(): Response
    {
        if ($this->sharedStorage->has('hostname')) {
            $this->client->setServerParameter('HTTP_HOST', (string) $this->sharedStorage->get('hostname'));
        }

        $operation = $this->getLastOperationRequest();

        $this->sendJsonRequest(
            $operation->getMethod(),
            $this->uri,
            $operation->getFormatted(),
            $this->headers->all()
        );

        /** @var Response $response */
        $response = $this->client->getResponse();
        $this->saveLastResponse($response);
        $this->headers = new ParameterBag();

        return $response;
    }

    public function saveLastResponse(Response $response): void
    {
        $this->sharedStorage->set(self::LAST_GRAPHQL_RESPONSE, $response);
    }

    public function getLastResponse(): ?JsonResponse
    {
        return $this->sharedStorage->get(self::LAST_GRAPHQL_RESPONSE);
    }

    /**
     * @throws Exception
     */
    public function getLastResponseArrayContent(): array
    {
        $response = $this->getLastResponse();
        $json = $this->getJsonFromResponse($response);
        if ($json === null) {
            throw new Exception('Return data is not Json format');
        }

        return $json;
    }

    public function getJsonFromResponse(Response $response): ?array
    {
        $content = $response->getContent();
        Assert::string($content);

        try {
            /** @var array $jsonData */
            $jsonData = json_decode($content, true);
        } catch (Exception $exception) {
            print_r($exception->getMessage());
        }

        if (json_last_error() === JSON_ERROR_NONE) {
            return $jsonData;
        }

        return null;
    }

    /**
     * @throws Exception
     */
    public function flattenArray(array $responseArray): array
    {
        $this->checkIfResponseProperlyFormatted($responseArray);
        $array = [];

        if ($this->isDataSectionPresentInResponse($responseArray)) {
            /** @var array $array */
            $array = reset($responseArray['data']);
        }

        if ($this->isErrorSectionPresentInResponse($responseArray)) {
            /** @var array $array */
            $array = reset($responseArray['errors']);
        }
        $recursiveIteratorIterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($array), RecursiveIteratorIterator::CHILD_FIRST);
        $result = [];
        foreach ($recursiveIteratorIterator as $value) {
            $keys = [];
            foreach (range(0, $recursiveIteratorIterator->getDepth()) as $depth) {
                $keys[] = $recursiveIteratorIterator->getSubIterator($depth)->key();
            }
            $result[implode('.', $keys)] = $value;
        }

        return $result;
    }

    /**
     * Converts the request parameters into a JSON string and uses it as request content.
     */
    private function sendJsonRequest(
        string $method,
        string $uri,
        array $parameters = [],
        array $server = [],
        bool $changeHistory = true
    ): Crawler {
        $content = json_encode($parameters);

        $this->client->setServerParameter('CONTENT_TYPE', 'application/json');
        $this->client->setServerParameter('HTTP_ACCEPT', 'application/json');

        return $this->client->request($method, $uri, [], [], $server, $content, $changeHistory);
    }

    /**
     * @return mixed
     *
     * @throws Exception
     */
    public function getValueAtKey(string $key)
    {
        $arrayContent = $this->getLastResponseArrayContent();
        $flatResponse = $this->flattenArray($arrayContent);

        if (!array_key_exists($key, $flatResponse)) {
            $message = array_key_exists('debugMessage', $flatResponse) ? $flatResponse['debugMessage'] : $flatResponse;

            throw new Exception(
                sprintf(
                    "Last response did not have any key named %s \n%s",
                    $key,
                    print_r($message, true)
                )
            );
        }

        return $flatResponse[$key];
    }

    /**
     * @throws Exception
     */
    private function checkIfResponseProperlyFormatted(array $responseArray): void
    {
        if (!array_key_exists('data', $responseArray) && !array_key_exists('errors', $responseArray)) {
            throw new Exception('Malformed response, no data or error key.');
        }
    }

    private function isDataSectionPresentInResponse(array $responseArray): bool
    {
        return array_key_exists('data', $responseArray) && is_array($responseArray['data']);
    }

    private function isErrorSectionPresentInResponse(array $responseArray): bool
    {
        return array_key_exists('errors', $responseArray) && is_array($responseArray['errors']);
    }
}
