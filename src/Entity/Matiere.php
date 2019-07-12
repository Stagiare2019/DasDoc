<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity; 

/**
 * @ORM\Entity(repositoryClass="App\Repository\MatiereRepository")
 * @UniqueEntity("libelle")
 */
class Matiere
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=8, unique=true)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=120, unique=true)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=127, unique=true)
     */
    private $libelle;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\FamilleMatiere", inversedBy="matieres")
     * @ORM\JoinColumn(nullable=false)
     */
    private $fkFamille;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Acte", mappedBy="fkMatiere")
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

    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;
        return $this;
    }

    public function getFkFamille(): ?FamilleMatiere
    {
        return $this->fkFamille;
    }

    public function setFkFamille(?FamilleMatiere $fkFamille): self
    {
        $this->fkFamille = $fkFamille;
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
            $acte->setFkMatiere($this);
        }

        return $this;
    }

    public function removeActe(Acte $acte): self
    {
        if ($this->actes->contains($acte)) {
            $this->actes->removeElement($acte);
            // set the owning side to null (unless already changed)
            if ($acte->getFkMatiere() === $this) {
                $acte->setFkMatiere(null);
            }
        }

        return $this;
    }

    public function __toString(): string 
    {
        return $this->libelle;
    }
}
