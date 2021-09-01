<?php

declare(strict_types=1);

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

namespace BitBag\SyliusGraphqlPlugin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class InvoiceUrl
{
    private ?int $id;

    private ?Collection $urls;

    public function __construct()
    {
        $this->urls = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getUrls(): ?Collection
    {
        return $this->urls;
    }

    public function setUrls(Collection $urls): void
    {
        $this->urls = $urls;
    }

    public function addUrl(string $url): void
    {
        $this->urls->add($url);
    }
}
