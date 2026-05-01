<?php

namespace App\Entity;

use App\Repository\StudentGroupRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: StudentGroupRepository::class)]
class StudentGroup
{
    public const AGE_CRECHE_0_5 = 'creche_0_5';
    public const AGE_CRECHE_6_11 = 'creche_6_11';
    public const AGE_CRECHE_1_3 = 'creche_1_3';
    public const AGE_PRE_ESCOLA = 'pre_escola';
    public const AGE_FUNDAMENTAL_6_10 = 'fundamental_6_10';
    public const AGE_FUNDAMENTAL_11_15 = 'fundamental_11_15';
    public const AGE_MEDIO = 'medio';
    public const AGE_EJA = 'eja';

    public const PERIOD_PARTIAL_20 = 'parcial_20';
    public const PERIOD_PARTIAL_30 = 'parcial_30';
    public const PERIOD_INTEGRAL = 'integral';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank]
    private ?string $ageGroup = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank]
    private ?string $mealPeriod = null;

    #[ORM\Column]
    #[Assert\Positive]
    private int $studentCount = 0;

    #[ORM\ManyToOne(inversedBy: 'studentGroups')]
    #[ORM\JoinColumn(nullable: false)]
    private ?School $school = null;

    public function getId(): ?int { return $this->id; }

    public function getName(): ?string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }

    public function getAgeGroup(): ?string { return $this->ageGroup; }
    public function setAgeGroup(string $ageGroup): static { $this->ageGroup = $ageGroup; return $this; }

    public function getMealPeriod(): ?string { return $this->mealPeriod; }
    public function setMealPeriod(string $mealPeriod): static { $this->mealPeriod = $mealPeriod; return $this; }

    public function getStudentCount(): int { return $this->studentCount; }
    public function setStudentCount(int $studentCount): static { $this->studentCount = $studentCount; return $this; }

    public function getSchool(): ?School { return $this->school; }
    public function setSchool(?School $school): static { $this->school = $school; return $this; }
}
