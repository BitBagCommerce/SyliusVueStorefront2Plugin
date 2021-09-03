<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusGraphqlPlugin\Behat\Model;


class OperationRequest implements OperationRequestInterface
{

    private string $operationName;

    private string $query;

    private array $variables;

    public function __construct(string $name, string $query, array $variables = [])
    {
        $this->operationName = $name;
        $this->query = $query;
        $this->variables = $variables;
    }

    public function getVariables(): array
    {
        return $this->variables;
    }

    public function setVariables(array $variables): void
    {
        $this->variables = $variables;
    }

    /** @param mixed $variable */
    public function addVariable($variable): void
    {
        $this->variables[] = $variable;
    }

    public function getFormatted(): array
    {
        return [
            "operationName" => $this->operationName,
            "query" => $this->query,
            "variables" => [
                "input" => json_encode($this->variables)
            ]
        ];
    }
}
