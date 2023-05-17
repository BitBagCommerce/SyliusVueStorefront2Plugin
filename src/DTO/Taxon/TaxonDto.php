<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DTO\Taxon;

use Doctrine\Common\Collections\Collection;

class TaxonDto
{
    public function __construct(
        private int $id,
        private ?string $name,
        private ?string $code,
        private ?int $position,
        private ?string $slug,
        private ?string $description,
        private ?bool $enabled,
        private ?int $level,
        private Collection $translations,
        private ?TaxonParentDto $parent,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function getParent(): ?TaxonParentDto
    {
        return $this->parent;
    }
}
