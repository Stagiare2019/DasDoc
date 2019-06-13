<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PieceJointeRepository")
 * @UniqueEntity("nomPDF")
 */
class PieceJointe
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Acte", inversedBy="pieceJointes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $fkActe;

    /**
     * @Assert\File(
     *     maxSize = "40M",
     *     mimeTypes = {"application/pdf", "application/x-pdf"},
     *     mimeTypesMessage = "Sélectionnez un format pdf valide."
     * )
     */
    private $file;

    public function getFile()
    {
        return $this->file;
    }

    public function setFile(File $file): self
    {
        $this->file = $file;
        return $this;
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

    public function getFkActe(): ?Acte
    {
        return $this->fkActe;
    }

    public function setFkActe(?Acte $fkActe): self
    {
        $this->fkActe = $fkActe;
        return $this;
    }
}
