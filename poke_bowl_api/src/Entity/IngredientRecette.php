<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use App\Repository\IngredientRecetteRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\Post;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: IngredientRecetteRepository::class)]
#[ApiResource(
    operations: [
        new Post(),
        new Delete(),
    ],
    normalizationContext: ["groups" => ["ingredientRecette:read"]]
)]
class IngredientRecette
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(["ingredientRecette:read"])]
    #[ORM\ManyToOne(fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Recette $recette = null;

    #[Groups(["ingredientRecette:read", "recette:read"])]
    #[ORM\ManyToOne(fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ingredient $ingredient = null;

    #[ORM\Column]
    #[Groups(["ingredientRecette:read"])]
    private ?int $quantite = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRecette(): ?Recette
    {
        return $this->recette;
    }

    public function setRecette(?Recette $recette): static
    {
        $this->recette = $recette;

        return $this;
    }

    public function getIngredient(): ?Ingredient
    {
        return $this->ingredient;
    }

    public function setIngredient(?Ingredient $ingredient): static
    {
        $this->ingredient = $ingredient;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }
}
