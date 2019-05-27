<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TypeMotcleRepository")
 * @UniqueEntity("libelle")
 */
class TypeMotcle
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
     * @ORM\OneToMany(targetEntity="App\Entity\Motcle", mappedBy="fkType")
     */
    private $motcles;

    public function __construct()
    {
        $this->motcles = new ArrayCollection();
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
     * @return Collection|Motcle[]
     */
    public function getMotcles(): Collection
    {
        return $this->motcles;
    }

    public function addMotcle(Motcle $motcle): self
    {
        if (!$this->motcles->contains($motcle)) {
            $this->motcles[] = $motcle;
            $motcle->setFkType($this);
        }

        return $this;
    }

    public function removeMotcle(Motcle $motcle): self
    {
        if ($this->motcles->contains($motcle)) {
            $this->motcles->removeElement($motcle);
            // set the owning side to null (unless already changed)
            if ($motcle->getFkType() === $this) {
                $motcle->setFkType(null);
            }
        }

        return $this;
    }
}
