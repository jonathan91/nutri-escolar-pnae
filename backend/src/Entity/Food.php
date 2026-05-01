<?php

namespace App\Entity;

use App\Repository\FoodRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FoodRepository::class)]
#[ORM\Table(name: 'food')]
#[ORM\Index(columns: ['name'], name: 'idx_food_name')]
class Food
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $tacoId = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $category = null;

    #[ORM\Column(length: 20)]
    private string $source = 'taco';

    // Per 100g values
    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $energy = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $protein = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $carbohydrate = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $lipid = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $fiber = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $calcium = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $iron = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $magnesium = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $zinc = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $vitaminA = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $vitaminC = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $sodium = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $saturatedFat = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $addedSugar = null;

    #[ORM\Column(type: 'boolean')]
    private bool $ultraProcessed = false;

    #[ORM\Column(type: 'boolean')]
    private bool $containsGluten = false;

    #[ORM\Column(type: 'boolean')]
    private bool $containsLactose = false;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $allergens = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $seasonMonths = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $correctionFactor = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $cookingFactor = null;

    public function getId(): ?int { return $this->id; }

    public function getTacoId(): ?string { return $this->tacoId; }
    public function setTacoId(?string $tacoId): static { $this->tacoId = $tacoId; return $this; }

    public function getName(): ?string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }

    public function getCategory(): ?string { return $this->category; }
    public function setCategory(?string $category): static { $this->category = $category; return $this; }

    public function getSource(): string { return $this->source; }
    public function setSource(string $source): static { $this->source = $source; return $this; }

    public function getEnergy(): ?float { return $this->energy; }
    public function setEnergy(?float $energy): static { $this->energy = $energy; return $this; }

    public function getProtein(): ?float { return $this->protein; }
    public function setProtein(?float $protein): static { $this->protein = $protein; return $this; }

    public function getCarbohydrate(): ?float { return $this->carbohydrate; }
    public function setCarbohydrate(?float $carbohydrate): static { $this->carbohydrate = $carbohydrate; return $this; }

    public function getLipid(): ?float { return $this->lipid; }
    public function setLipid(?float $lipid): static { $this->lipid = $lipid; return $this; }

    public function getFiber(): ?float { return $this->fiber; }
    public function setFiber(?float $fiber): static { $this->fiber = $fiber; return $this; }

    public function getCalcium(): ?float { return $this->calcium; }
    public function setCalcium(?float $calcium): static { $this->calcium = $calcium; return $this; }

    public function getIron(): ?float { return $this->iron; }
    public function setIron(?float $iron): static { $this->iron = $iron; return $this; }

    public function getMagnesium(): ?float { return $this->magnesium; }
    public function setMagnesium(?float $magnesium): static { $this->magnesium = $magnesium; return $this; }

    public function getZinc(): ?float { return $this->zinc; }
    public function setZinc(?float $zinc): static { $this->zinc = $zinc; return $this; }

    public function getVitaminA(): ?float { return $this->vitaminA; }
    public function setVitaminA(?float $vitaminA): static { $this->vitaminA = $vitaminA; return $this; }

    public function getVitaminC(): ?float { return $this->vitaminC; }
    public function setVitaminC(?float $vitaminC): static { $this->vitaminC = $vitaminC; return $this; }

    public function getSodium(): ?float { return $this->sodium; }
    public function setSodium(?float $sodium): static { $this->sodium = $sodium; return $this; }

    public function getSaturatedFat(): ?float { return $this->saturatedFat; }
    public function setSaturatedFat(?float $saturatedFat): static { $this->saturatedFat = $saturatedFat; return $this; }

    public function getAddedSugar(): ?float { return $this->addedSugar; }
    public function setAddedSugar(?float $addedSugar): static { $this->addedSugar = $addedSugar; return $this; }

    public function isUltraProcessed(): bool { return $this->ultraProcessed; }
    public function setUltraProcessed(bool $ultraProcessed): static { $this->ultraProcessed = $ultraProcessed; return $this; }

    public function isContainsGluten(): bool { return $this->containsGluten; }
    public function setContainsGluten(bool $containsGluten): static { $this->containsGluten = $containsGluten; return $this; }

    public function isContainsLactose(): bool { return $this->containsLactose; }
    public function setContainsLactose(bool $containsLactose): static { $this->containsLactose = $containsLactose; return $this; }

    public function getAllergens(): ?array { return $this->allergens; }
    public function setAllergens(?array $allergens): static { $this->allergens = $allergens; return $this; }

    public function getSeasonMonths(): ?array { return $this->seasonMonths; }
    public function setSeasonMonths(?array $seasonMonths): static { $this->seasonMonths = $seasonMonths; return $this; }

    public function getCorrectionFactor(): ?float { return $this->correctionFactor; }
    public function setCorrectionFactor(?float $correctionFactor): static { $this->correctionFactor = $correctionFactor; return $this; }

    public function getCookingFactor(): ?float { return $this->cookingFactor; }
    public function setCookingFactor(?float $cookingFactor): static { $this->cookingFactor = $cookingFactor; return $this; }
}
