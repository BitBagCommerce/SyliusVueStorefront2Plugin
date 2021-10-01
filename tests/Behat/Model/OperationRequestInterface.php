<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusGraphqlPlugin\Behat\Model;


use Symfony\Component\HttpFoundation\Request;

interface OperationRequestInterface
{
    public const OPERATION_MUTATION = "mutation";
    public const OPERATION_QUERY = "query";

    public function __construct(string $name, string $query, array $variables = [], string $method = Request::METHOD_POST);

    public function getVariables(): array;

    public function setVariables(array $variables): void;

    /** @param mixed $value */
    public function addVariable(string $key, $value): void;

    public function getFormatted(): array;

    public function getMethod(): string;

    public function setMethod(string $method): void;

    public function getOperationType(): string;

    public function setOperationType(string $operationType): void;

    public function getFilters(): array;

    public function setFilters(array $filters): void;

    /** @param mixed $value */
    public function addFilter(string $key, $value): void;
}
