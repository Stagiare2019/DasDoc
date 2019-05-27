<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ActeRepository")
 * @UniqueEntity(fields={"nomPDF","numero"})
 */
class Acte
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $nomPDF;

    /**
     * @ORM\Column(type="string", length=63, unique=true)
     */
    private $numero;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\EtatActe", inversedBy="actes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $fkEtat;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\NatureActe", inversedBy="actes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $fkNature;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Matiere", inversedBy="actes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $fkMatiere;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $objet;

    /**
     * @ORM\Column(type="date")
     */
    private $dateDecision;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Service", inversedBy="actes")
     */
    private $fkService;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateEffectiviteDebut;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateEffectiviteFin;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PieceJointe", mappedBy="fkActe")
     */
    private $pieceJointes;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Motcle", mappedBy="actes")
     */
    private $motcles;

    /**
     * @Assert\NotBlank(message="Déposez le pdf de l'acte ici.")
     * @Assert\File(
     *     maxSize = "40M",
     *     mimeTypes = {"application/pdf", "application/x-pdf"},
     *     mimeTypesMessage = "Sélectionnez un format pdf valide."
     * )
     */
    private $file;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Action", mappedBy="fkActe")
     */
    private $actions;

    public function getFile()
    {
        return $this->file;
    }

    public function setFile(File $file): self
    {
        $this->file = $file;
        return $this;
    }

    public function __construct()
    {
        $this->pieceJointes = new ArrayCollection();
        $this->motcles = new ArrayCollection();
        $this->actions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomPDF(): ?string
    {
        return $this->nomPDF;
    }

    public function setNomPDF(string $nomPDF): self
    {
        $this->nomPDF = $nomPDF;
        return $this;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): self
    {
        $this->numero = $numero;
        return $this;
    }

    public function getFkEtat(): ?EtatActe
    {
        return $this->fkEtat;
    }

    public function setFkEtat(?EtatActe $fkEtat): self
    {
        $this->fkEtat = $fkEtat;
        return $this;
    }

    public function getFkNature(): ?NatureActe
    {
        return $this->fkNature;
    }

    public function setFkNature(?NatureActe $fkNature): self
    {
        $this->fkNature = $fkNature;
        return $this;
    }

    public function getFkMatiere(): ?Matiere
    {
        return $this->fkMatiere;
    }

    public function setFkMatiere(?Matiere $fkMatiere): self
    {
        $this->fkMatiere = $fkMatiere;
        return $this;
    }

    public function getObjet(): ?string
    {
        return $this->objet;
    }

    public function setObjet(string $objet): self
    {
        $this->objet = $objet;
        return $this;
    }

    public function getDateDecision(): ?\DateTimeInterface
    {
        return $this->dateDecision;
    }

    public function setDateDecision(\DateTimeInterface $dateDecision): self
    {
        $this->dateDecision = $dateDecision;
        return $this;
    }

    public function getFkService(): ?Service
    {
        return $this->fkService;
    }

    public function setFkService(?Service $fkService): self
    {
        $this->fkService = $fkService;
        return $this;
    }

    public function getDateEffectiviteDebut(): ?\DateTimeInterface
    {
        return $this->dateEffectiviteDebut;
    }

    public function setDateEffectiviteDebut(?\DateTimeInterface $dateEffectiviteDebut): self
    {
        $this->dateEffectiviteDebut = $dateEffectiviteDebut;
        return $this;
    }

    public function getDateEffectiviteFin(): ?\DateTimeInterface
    {
        return $this->dateEffectiviteFin;
    }

    public function setDateEffectiviteFin(?\DateTimeInterface $dateEffectiviteFin): self
    {
        $this->dateEffectiviteFin = $dateEffectiviteFin;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return Collection|PieceJointe[]
     */
    public function getPieceJointes(): Collection
    {
        return $this->pieceJointes;
    }

    public function addPieceJointe(PieceJointe $pieceJointe): self
    {
        if (!$this->pieceJointes->contains($pieceJointe)) {
            $this->pieceJointes[] = $pieceJointe;
            $pieceJointe->setFkActe($this);
        }

        return $this;
    }

    public function removePieceJointe(PieceJointe $pieceJointe): self
    {
        if ($this->pieceJointes->contains($pieceJointe)) {
            $this->pieceJointes->removeElement($pieceJointe);
            // set the owning side to null (unless already changed)
            if ($pieceJointe->getFkActe() === $this) {
                $pieceJointe->setFkActe(null);
            }
        }

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
            $motcle->addActe($this);
        }

        return $this;
    }

    public function removeMotcle(Motcle $motcle): self
    {
        if ($this->motcles->contains($motcle)) {
            $this->motcles->removeElement($motcle);
            $motcle->removeActe($this);
        }

        return $this;
    }

    /**
     * @return Collection|Action[]
     */
    public function getActions(): Collection
    {
        return $this->actions;
    }

    public function addAction(Action $action): self
    {
        if (!$this->actions->contains($action)) {
            $this->actions[] = $action;
            $action->setFkActe($this);
        }

        return $this;
    }

    public function removeAction(Action $action): self
    {
        if ($this->actions->contains($action)) {
            $this->actions->removeElement($action);
            // set the owning side to null (unless already changed)
            if ($action->getFkActe() === $this) {
                $action->setFkActe(null);
            }
        }

        return $this;
    }
}
