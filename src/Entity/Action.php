<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ActionRepository")
 */
class Action
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TypeAction", inversedBy="actions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $fkType;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Acte", inversedBy="actions")
     */
    private $fkActe;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Utilisateur", inversedBy="actions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $fkUtilisateur;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFkType(): ?TypeAction
    {
        return $this->fkType;
    }

    public function setFkType(?TypeAction $fkType): self
    {
        $this->fkType = $fkType;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getFkActe(): ?Acte
    {
        return $this->fkActe;
    }

    public function setFkActe(?Acte $fkActe): self
    {
        $this->fkActe = $fkActe;

        return $this;
    }

    public function getFkUtilisateur(): ?Utilisateur
    {
        return $this->fkUtilisateur;
    }

    public function setFkUtilisateur(?Utilisateur $fkUtilisateur): self
    {
        $this->fkUtilisateur = $fkUtilisateur;

        return $this;
    }
}
