<?php


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

    public function getUrls(): Collection
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
