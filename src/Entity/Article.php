<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'articles')]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Le titre est obligatoire')]
    #[Assert\Length(min: 3, max: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: 'Le contenu est obligatoire')]
    private ?string $contenu = null;

    #[ORM\Column(type: 'string', length: 100)]
    #[Assert\NotBlank]
    private ?string $auteur = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $datePublication = null;

    public function __construct()
    {
        $this->datePublication = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getTitre(): ?string { return $this->titre; }
    public function setTitre(string $titre): self { $this->titre = $titre; return $this; }
    public function getContenu(): ?string { return $this->contenu; }
    public function setContenu(string $contenu): self { $this->contenu = $contenu; return $this; }
    public function getAuteur(): ?string { return $this->auteur; }
    public function setAuteur(string $auteur): self { $this->auteur = $auteur; return $this; }
    public function getDatePublication(): ?\DateTimeInterface { return $this->datePublication; }
    public function setDatePublication(\DateTimeInterface $date): self { $this->datePublication = $date; return $this; }
}



