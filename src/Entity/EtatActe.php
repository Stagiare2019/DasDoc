<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EtatActeRepository")
 * @UniqueEntity("libelle")
 */
class EtatActe
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=63, unique=true)
     */
    private $libelle;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Acte", mappedBy="fkEtat")
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
            $acte->setFkEtat($this);
        }

        return $this;
    }

    public function removeActe(Acte $acte): self
    {
        if ($this->actes->contains($acte)) {
            $this->actes->removeElement($acte);
            // set the owning side to null (unless already changed)
            if ($acte->getFkEtat() === $this) {
                $acte->setFkEtat(null);
            }
        }

        return $this;
    }

    public function __toString(): string 
    {
        return $this->libelle;
    }
}
