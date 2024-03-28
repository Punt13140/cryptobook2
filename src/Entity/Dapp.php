<?php

namespace App\Entity;

use App\Repository\DappRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DappRepository::class)]
class Dapp
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(length: 255)]
    private ?string $url = null;

    #[ORM\ManyToMany(targetEntity: Blockchain::class, inversedBy: 'dapps')]
    private Collection $blockchains;

    public function __construct()
    {
        $this->blockchains = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

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
        }

        return $this;
    }

    public function removeBlockchain(Blockchain $blockchain): static
    {
        $this->blockchains->removeElement($blockchain);

        return $this;
    }
}
