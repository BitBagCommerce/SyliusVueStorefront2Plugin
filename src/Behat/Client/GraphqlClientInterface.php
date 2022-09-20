<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\Behat\Client;

use BitBag\SyliusGraphqlPlugin\Behat\Model\OperationRequestInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface GraphqlClientInterface
{
    public function prepareOperation(
        string $name,
        string $formattedExpectedData,
        string $method = Request::METHOD_POST
    ): OperationRequestInterface;

    public function prepareQuery(
        string $name,
        string $formattedExpectedData,
        string $method = Request::METHOD_POST
    ): OperationRequestInterface;

    public function getToken(): ?string;

    public function addAuthorization(): void;

    public function send(): Response;

    public function getJsonFromResponse(Response $response): ?array;

    public function getLastResponseArrayContent(): array;

    public function flattenArray(array $responseArray): array;

    public function getLastOperationRequest(): ?OperationRequestInterface;

    public function saveLastResponse(Response $response): void;

    public function getLastResponse(): ?JsonResponse;

    /** @return mixed */
    public function getValueAtKey(string $key);
}
