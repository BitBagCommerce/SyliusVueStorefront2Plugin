<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusGraphqlPlugin\Behat\Client;

use const JSON_ERROR_NONE;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use Sylius\Behat\Client\RequestInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\HttpFoundation\Response;

final class GraphqlClient implements GraphqlClientInterface
{
    /** @var AbstractBrowser */
    private $client;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var string */
    private $authorizationHeader;

    /** @var RequestInterface */
    private $request;

    public function __construct(
        AbstractBrowser $client,
        SharedStorageInterface $sharedStorage,
        string $authorizationHeader
    ) {
        $this->client = $client;
        $this->sharedStorage = $sharedStorage;
        $this->authorizationHeader = $authorizationHeader;
    }

    public function post(?RequestInterface $request = null): Response
    {
        return $this->request($request ?? $this->request);
    }

    public function put(): Response
    {
        return $this->request($this->request);
    }

    public function patch(): Response
    {
        return $this->request($this->request);
    }

    public function delete(string $id): Response
    {
        return $this->request(Request::delete(
            $this->section,
            $this->resource,
            $id,
            $this->authorizationHeader,
            $this->getToken()
        ));
    }

    public function buildCreateRequest(): void
    {
        $this->request = Request::create($this->section, $this->resource, $this->authorizationHeader);
        $this->request->authorize($this->getToken(), $this->authorizationHeader);
    }

    public function buildUpdateRequest(string $id): void
    {
        $this->show($id);

        $this->request = Request::update(
            $this->section,
            $this->resource,
            $id,
            $this->authorizationHeader,
            $this->getToken()
        );
        $this->request->setContent(json_decode($this->client->getResponse()->getContent(), true));
    }

    public function buildCustomUpdateRequest(string $id, string $customSuffix): void
    {
        $this->request = Request::update(
            $this->section,
            $this->resource,
            sprintf('%s/%s', $id, $customSuffix),
            $this->authorizationHeader,
            $this->getToken()
        );
    }

    /** @param string|int $value */
    public function setRequestInput(string $key, $value): void
    {
        $this->request->updateParameters([$key => $value]);
    }

    public function setRequestData(array $content): void
    {
        $this->request->setContent($content);
    }

    public function clearParameters(): void
    {
        $this->request->clearParameters();
    }

    /** @param string|int|array $value */
    public function addRequestData(string $key, $value): void
    {
        $this->request->updateContent([$key => $value]);
    }

    public function updateRequestData(array $data): void
    {
        $this->request->updateContent($data);
    }

    public function getLastResponse(): Response
    {
        return $this->client->getResponse();
    }

    public function getToken(): ?string
    {
        return $this->sharedStorage->has('token') ? $this->sharedStorage->get('token') : null;
    }

    public function request(RequestInterface $request): Response
    {
        if ($this->sharedStorage->has('hostname')) {
            $this->client->setServerParameter('HTTP_HOST', $this->sharedStorage->get('hostname'));
        }

        $this->client->request(
            $request->method(),
            '/api/v2/graphql',
            $request->parameters(),
            $request->files(),
            $request->headers(),
            $request->content() ?? null
        );

        $response = $this->getLastResponse();

        $this->saveLastResponse($response);
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
