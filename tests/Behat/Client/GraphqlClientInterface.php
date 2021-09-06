<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusGraphqlPlugin\Behat\Client;

use Sylius\Behat\Client\RequestInterface;
use Symfony\Component\HttpFoundation\Response;

interface GraphqlClientInterface
{
    public function post(?RequestInterface $request = null): Response;

    public function put(): Response;

    public function patch(): Response;

    public function delete(string $id): Response;

    /** @param string|int $value */
    public function setRequestInput(string $key, $value): void;

    public function setRequestData(array $content): void;

    public function clearParameters(): void;

    /** @param string|int|array $value */
    public function addRequestData(string $key, $value): void;

    public function updateRequestData(array $data): void;

    public function getLastResponse(): Response;

    public function getToken(): ?string;

    public function request(RequestInterface $request): Response;

    /**
     * @return mixed|null
     */
    public function getJsonFromResponse(string $response);
}
