<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusGraphqlPlugin\Behat\Client;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Tests\BitBag\SyliusGraphqlPlugin\Behat\Model\OperationRequest;
use Tests\BitBag\SyliusGraphqlPlugin\Behat\Model\OperationRequestInterface;
use const JSON_ERROR_NONE;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use Sylius\Behat\Client\RequestInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\HttpFoundation\Response;

final class GraphqlClient implements GraphqlClientInterface
{

    public static string $LAST_GRAPHQL_RESPONSE = "lastGraphqlResponse";

    private AbstractBrowser $client;

    private SharedStorageInterface $sharedStorage;

    private string $authorizationHeader;

    private RequestInterface $request;

    public function __construct(
        AbstractBrowser $client,
        SharedStorageInterface $sharedStorage,
        string $authorizationHeader
    ) {
        $this->client = $client;
        $this->sharedStorage = $sharedStorage;
        $this->authorizationHeader = $authorizationHeader;
    }

    public function prepareOperation(string $mutationName, string $formattedExpectedData): OperationRequestInterface
    {
        $mutation = '
        mutation <name> ($input: <name>Input!) {
            <name>(input: $input){
            <expectedData>
          }
        }';

        $mutation = str_replace("<name>", $mutationName, $mutation);
        $mutation = str_replace("<expectedData>", $formattedExpectedData, $mutation);

        return new OperationRequest($mutationName, $mutation);
    }

    /**
     * @throws Exception
     */
    public function prepareRequest(
        OperationRequestInterface $request,
        string $method = Request::METHOD_POST,
        ?bool $auth = false
    ): Request
    {
        if(!in_array($method, [
            Request::METHOD_POST, Request::METHOD_PATCH, Request::METHOD_DELETE, Request::METHOD_PUT
        ])){
            throw new Exception(sprintf("Provided method %s does not match available ones.", $method));
        }

        $requestData = Request::create("",$method, $request->getFormatted());
        $token = $this->getToken();

        if($auth && null !== $token ){
            $requestData->server->add([
                "Authorization" => sprintf('%s %s',$this->authorizationHeader, $token)
            ]);
        }
        return $requestData;
    }

    public function getToken(): ?string
    {
        return $this->sharedStorage->has('token') ? (string)$this->sharedStorage->get('token') : null;
    }

    public function send(Request $request): Response
    {
        if ($this->sharedStorage->has('hostname')) {
            $this->client->setServerParameter('HTTP_HOST', $this->sharedStorage->get('hostname'));
        }

        $this->client->request(
            $request->method(),
            '/api/v2/graphql',
            [],
            [],
            $request->headers(),
            $request->content() ?? null
        );

        $response = $this->getLastResponse();

        $this->saveLastResponse($response);

        return $response;
    }

    public function saveLastResponse($response): void
    {
        $this->sharedStorage->set(self::$LAST_GRAPHQL_RESPONSE,$response);
    }

    public function getLastResponse(): Response
    {
        return $this->client->getResponse();
    }

    /**
     * @return mixed|null
     */
    public function getJsonFromResponse(string $response)
    {
        $jsonData = json_decode($response, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $jsonData;
        }

        return null;
    }

    public static function diff($arr1, $arr2): array
    {
        $diff = [];

        // Check the similarities
        foreach ($arr1 as $k1 => $v1) {
            if (isset($arr2[$k1])) {
                $v2 = $arr2[$k1];
                if (is_array($v1) && is_array($v2)) {
                    // 2 arrays: just go further...
                    // .. and explain it's an update!
                    $changes = self::diff($v1, $v2);
                    if (count($changes) > 0) {
                        // If we have no change, simply ignore
                        $diff[$k1] = ['upd' => $changes];
                    }
                    unset($arr2[$k1]); // don't forget
                } elseif ($v2 === $v1) {
                    // unset the value on the second array
                    // for the "surplus"
                    unset($arr2[$k1]);
                } else {
                    // Don't mind if arrays or not.
                    $diff[$k1] = ['old' => $v1, 'new' => $v2];
                    unset($arr2[$k1]);
                }
            } else {
                // remove information
                $diff[$k1] = ['old' => $v1];
            }
        }

        // Now, check for new stuff in $arr2
        reset($arr2); // Don't argue it's unnecessary (even I believe you)
        foreach ($arr2 as $k => $v) {
            // OK, it is quite stupid my friend
            $diff[$k] = ['new' => $v];
        }

        return $diff;
    }

    public function flattenArray(array $array): array
    {
        $array = reset($array['data']);
        $ritit = new RecursiveIteratorIterator(new RecursiveArrayIterator($array));
        $result = [];
        foreach ($ritit as $leafValue) {
            $keys = [];
            foreach (range(0, $ritit->getDepth()) as $depth) {
                $keys[] = $ritit->getSubIterator($depth)->key();
            }
            $result[implode('.', $keys)] = $leafValue;
        }

        return $result;
    }
}
