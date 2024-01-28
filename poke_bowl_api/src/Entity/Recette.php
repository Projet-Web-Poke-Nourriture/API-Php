<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\IngredientRecette;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\RecetteRepository;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use App\State\RecetteDeleteProcessor;
use App\State\RecetteProcessor;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RecetteRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            processor: RecetteProcessor::class,
            security: "is_granted('ROLE_USER')"
        ),
        new Delete(
            processor: RecetteDeleteProcessor::class,
            security: "is_granted('ROLE_ADMIN')"
        ),
        new GetCollection(
            uriTemplate: '/recettes/{idUtilisateur}/utilisateur',
            uriVariables: [
                'idUtilisateur' => new Link(
                    fromProperty: 'recettes',
                    fromClass: Utilisateur::class
                )
            ]
        ),
    ],
    normalizationContext: ["groups" => ["recette:read"]]
)]
class Recette
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["recette:read"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Groups(["recette:read"])]
    private ?string $nom = null;

    #[ORM\OneToMany(mappedBy: 'recette', targetEntity: IngredientRecette::class, orphanRemoval: true, fetch: 'EAGER')]
    #[Groups(["ingredientRecette:read", "recette:read"])]
    private Collection $ingredients;

    #[ORM\Column]
    #[ApiProperty(writable: false)]
    #[Groups(["recette:read"])]
    private bool $recommande = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[ApiProperty(writable: false)]
    #[Groups(["recette:read"])]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(inversedBy: 'recettes', fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    #[ApiProperty(writable: false)]
    #[Groups(["ingredientRecette:read", "recette:read"])]
    private ?Utilisateur $auteur = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Groups(["recette:read"])]
    private ?string $etapes = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Groups(["recette:read"])]
    private ?int $tempsPreparation = null;

    public function __construct()
    {
        $this->ingredients = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection<int, IngredientRecette>
     */
    public function getIngredients(): Collection
    {
        return $this->ingredients;
    }

    public function addIngredient(IngredientRecette $ingredient): static
    {
        if (!$this->ingredients->contains($ingredient)) {
            $this->ingredients->add($ingredient);
            $ingredient->setRecette($this);
        }

        return $this;
    }

    public function removeIngredient(IngredientRecette $ingredient): static
    {
        if ($this->ingredients->removeElement($ingredient)) {
            // set the owning side to null (unless already changed)
            if ($ingredient->getRecette() === $this) {
                $ingredient->setRecette(null);
            }
        }

        return $this;
    }

    public function isRecommande(): ?bool
    {
        return $this->recommande;
    }

    public function setRecommande(bool $recommande): static
    {
        $this->recommande = $recommande;

        return $this;
    }

    #[ORM\PrePersist]
    public function prePersistDate(): void
    {
        $this->date = new \DateTime();
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getAuteur(): ?Utilisateur
    {
        return $this->auteur;
    }

    public function setAuteur(?Utilisateur $auteur): static
    {
        $this->auteur = $auteur;

        return $this;
    }

    public function getEtapes(): ?string
    {
        return $this->etapes;
    }

    public function setEtapes(string $etapes): static
    {
        $this->etapes = $etapes;

        return $this;
    }

    public function getTempsPreparation(): ?int
    {
        return $this->tempsPreparation;
    }

    public function setTempsPreparation(int $tempsPreparation): static
    {
        $this->tempsPreparation = $tempsPreparation;

        return $this;
    }
}
