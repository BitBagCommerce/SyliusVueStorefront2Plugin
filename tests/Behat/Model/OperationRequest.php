<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusGraphqlPlugin\Behat\Model;

use Symfony\Component\HttpFoundation\Request;

class OperationRequest implements OperationRequestInterface
{
    private string $operationName;

    private string $operationType = OperationRequestInterface::OPERATION_MUTATION;

    private string $method;

    private string $query;

    private array $variables;

    private array $filters = [];

    public function __construct(string $name, string $query, array $variables = [], string $method = Request::METHOD_POST)
    {
        $this->operationName = $name;
        $this->query = $query;
        $this->variables = $variables;
        $this->method = $method;
    }

    public function getVariables(): array
    {
        return $this->variables;
    }

    public function setVariables(array $variables): void
    {
        $this->variables = $variables;
    }

    /** @param mixed $value */
    public function addVariable(string $key, $value): void
    {
        $this->variables[$key] = $value;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    public function getOperationType(): string
    {
        return $this->operationType;
    }

    public function setOperationType(string $operationType): void
    {
        $this->operationType = $operationType;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function setFilters(array $filters): void
    {
        $this->filters = $filters;
    }

    /** @param mixed $value */
    public function addFilter(string $key, $value): void
    {
        $this->filters[$key] = $value;
    }


    public function getFormatted(): array
    {
        if ($this->operationType === OperationRequestInterface::OPERATION_QUERY) {
            $this->addFiltersToQuery();
        }

        return [
            'operationName' => $this->operationName,
            'query' => $this->query,
            'variables' => json_encode([
                'input' => $this->variables,
            ], \JSON_FORCE_OBJECT),
        ];
    }

    private function formatFilters(): string
    {
        $output = '';
        foreach ($this->filters as $filter) {
            //TODO:: Implement in later stages of development
        }

        return $output;
    }

    private function addFiltersToQuery(): void
    {
        $filters = $this->formatFilters();
        $this->query = str_replace('<filters>', $filters, $this->query);
    }

}
