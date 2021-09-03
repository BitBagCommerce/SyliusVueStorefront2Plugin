<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusGraphqlPlugin\Behat\Client;

use Sylius\Behat\Client\RequestInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\BitBag\SyliusGraphqlPlugin\Behat\Model\OperationRequestInterface;

interface GraphqlClientInterface
{

    public function prepareRequest(
        OperationRequestInterface $request,
        string $method = Request::METHOD_POST,
        ?bool $auth = false
    ): Request;

    public function getLastResponse(): Response;

    public function getToken(): ?string;

    public function request(RequestInterface $request): Response;

    /**
     * @return mixed|null
     */
    public function getJsonFromResponse(string $response);
}
