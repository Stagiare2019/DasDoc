<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MotcleRepository")
 * @UniqueEntity("libelle")
 */
class Motcle
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TypeMotcle", inversedBy="motcles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $fkType;

    /**
     * @ORM\Column(type="string", length=63, unique=true)
     */
    private $libelle;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Acte", inversedBy="motcles")
     */
    private $actes;

    public function __construct()
    {
        $this->actes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFkType(): ?TypeMotcle
    {
        return $this->fkType;
    }

    public function setFkType(?TypeMotcle $fkType): self
    {
        $this->fkType = $fkType;
        return $this;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;
        return $this;
    }

    /**
     * @return Collection|Acte[]
     */
    public function getActes(): Collection
    {
        return $this->actes;
    }

    public function addActe(Acte $acte): self
    {
        if (!$this->actes->contains($acte)) {
            $this->actes[] = $acte;
        }

        return $this;
    }

    public function removeActe(Acte $acte): self
    {
        if ($this->actes->contains($acte)) {
            $this->actes->removeElement($acte);
        }

        return $this;
    }

    public function __toString(): string 
    {
        return $this->libelle;
    }
}
