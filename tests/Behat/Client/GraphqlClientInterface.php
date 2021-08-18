<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
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

    function request(RequestInterface $request): Response;

    /**
     * @return mixed|null
     */
    function getJsonFromResponse(string $response);

}
