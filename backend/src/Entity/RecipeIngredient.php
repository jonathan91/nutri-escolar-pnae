<?php

namespace App\Entity;

use App\Repository\RecipeIngredientRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RecipeIngredientRepository::class)]
class RecipeIngredient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'ingredients')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Recipe $recipe = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Food $food = null;

    #[ORM\Column(type: 'float')]
    #[Assert\Positive]
    private float $grossWeight = 0;

    #[ORM\Column(type: 'float')]
    #[Assert\PositiveOrZero]
    private float $netWeight = 0;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $costPerKg = null;

    public function getId(): ?int { return $this->id; }

    public function getRecipe(): ?Recipe { return $this->recipe; }
    public function setRecipe(?Recipe $recipe): static { $this->recipe = $recipe; return $this; }

    public function getFood(): ?Food { return $this->food; }
    public function setFood(?Food $food): static { $this->food = $food; return $this; }

    public function getGrossWeight(): float { return $this->grossWeight; }
    public function setGrossWeight(float $grossWeight): static { $this->grossWeight = $grossWeight; return $this; }

    public function getNetWeight(): float { return $this->netWeight; }
    public function setNetWeight(float $netWeight): static { $this->netWeight = $netWeight; return $this; }

    public function getCostPerKg(): ?float { return $this->costPerKg; }
    public function setCostPerKg(?float $costPerKg): static { $this->costPerKg = $costPerKg; return $this; }
}
