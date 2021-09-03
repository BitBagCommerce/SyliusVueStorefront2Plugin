<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusGraphqlPlugin\Behat\Model;


interface OperationRequestInterface
{

    public function __construct(string $name, string $query, array $variables = []);

    public function getVariables(): array;

    public function setVariables(array $variables): void;

    /** @param mixed $variable */
    public function addVariable($variable): void;

    public function getFormatted(): array;

}
