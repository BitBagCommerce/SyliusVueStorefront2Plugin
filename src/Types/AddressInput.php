<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\Types;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;

class AddressInput extends InputObjectType
{
    public function getName(): string
    {
        return $this->name;
    }

    public function __construct()
    {
        $this->name = 'AddressInput';
        $this->description = 'The `Address` object type.';
        $config = [
            'fields' => [
                'id' => Type::id(),
                'firstName' => Type::string(),
                'lastName' => Type::string(),
                'countryCode' => Type::string(),
                'provinceCode' => Type::string(),
                'phoneNumber' => Type::string(),
                'street' => Type::string(),
                'city' => Type::string(),
                'company' => Type::string(),
                'postcode' => Type::string(),
                'provinceName' => Type::string(),
            ],
        ];
        parent::__construct($config);
    }
}
