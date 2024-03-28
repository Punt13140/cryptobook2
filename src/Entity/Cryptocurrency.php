<?php

namespace App\Entity;

use App\Repository\CryptocurrencyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CryptocurrencyRepository::class)]
class Cryptocurrency
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelleCoingecko = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column]
    private ?float $priceUsd = null;

    #[ORM\Column]
    private ?float $mcapUsd = null;

    #[ORM\Column(length: 255)]
    private ?string $urlImgThumb = null;

    #[ORM\Column(length: 8)]
    private ?string $symbol = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $color = null;

    #[ORM\Column]
    private ?bool $isStable = null;

    #[ORM\OneToMany(targetEntity: Blockchain::class, mappedBy: 'cryptocurrency')]
    private Collection $blockchains;

    public function __construct()
    {
        $this->blockchains = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelleCoingecko(): ?string
    {
        return $this->libelleCoingecko;
    }

    public function setLibelleCoingecko(string $libelleCoingecko): static
    {
        $this->libelleCoingecko = $libelleCoingecko;

        return $this;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getPriceUsd(): ?float
    {
        return $this->priceUsd;
    }

    public function setPriceUsd(float $priceUsd): static
    {
        $this->priceUsd = $priceUsd;

        return $this;
    }

    public function getMcapUsd(): ?float
    {
        return $this->mcapUsd;
    }

    public function setMcapUsd(float $mcapUsd): static
    {
        $this->mcapUsd = $mcapUsd;

        return $this;
    }

    public function getUrlImgThumb(): ?string
    {
        return $this->urlImgThumb;
    }

    public function setUrlImgThumb(string $urlImgThumb): static
    {
        $this->urlImgThumb = $urlImgThumb;

        return $this;
    }

    public function getSymbol(): ?string
    {
        return $this->symbol;
    }

    public function setSymbol(string $symbol): static
    {
        $this->symbol = $symbol;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function isIsStable(): ?bool
    {
        return $this->isStable;
    }

    public function setIsStable(bool $isStable): static
    {
        $this->isStable = $isStable;

        return $this;
    }

    /**
     * @return Collection<int, Blockchain>
     */
    public function getBlockchains(): Collection
    {
        return $this->blockchains;
    }

    public function addBlockchain(Blockchain $blockchain): static
    {
        if (!$this->blockchains->contains($blockchain)) {
            $this->blockchains->add($blockchain);
            $blockchain->setCryptocurrency($this);
        }

        return $this;
    }

    public function removeBlockchain(Blockchain $blockchain): static
    {
        if ($this->blockchains->removeElement($blockchain)) {
            // set the owning side to null (unless already changed)
            if ($blockchain->getCryptocurrency() === $this) {
                $blockchain->setCryptocurrency(null);
            }
        }

        return $this;
    }
}
