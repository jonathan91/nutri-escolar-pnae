<?php

namespace App\Entity;

use App\Repository\MenuItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MenuItemRepository::class)]
class MenuItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Menu $menu = null;

    #[ORM\ManyToOne]
    private ?Food $food = null;

    #[ORM\ManyToOne]
    private ?Recipe $recipe = null;

    #[ORM\Column(type: 'float')]
    #[Assert\Positive]
    private float $portionSize = 0;

    #[ORM\Column]
    #[Assert\Positive]
    private int $servings = 1;

    public function getId(): ?int { return $this->id; }

    public function getMenu(): ?Menu { return $this->menu; }
    public function setMenu(?Menu $menu): static { $this->menu = $menu; return $this; }

    public function getFood(): ?Food { return $this->food; }
    public function setFood(?Food $food): static { $this->food = $food; return $this; }

    public function getRecipe(): ?Recipe { return $this->recipe; }
    public function setRecipe(?Recipe $recipe): static { $this->recipe = $recipe; return $this; }

    public function getPortionSize(): float { return $this->portionSize; }
    public function setPortionSize(float $portionSize): static { $this->portionSize = $portionSize; return $this; }

    public function getServings(): int { return $this->servings; }
    public function setServings(int $servings): static { $this->servings = $servings; return $this; }
}
