<?php

namespace App\Service;

use App\Entity\StudentGroup;

/**
 * Modular reference values from Resolucao CD/FNDE n 06/2020.
 * Update these constants when FNDE publishes new guidelines.
 */
class NutritionalReferenceService
{
    private const DAILY_REFERENCES = [
        StudentGroup::AGE_CRECHE_0_5 => [
            'energy' => 500,
            'protein_pct_min' => 10, 'protein_pct_max' => 15,
            'lipid_pct_min' => 25, 'lipid_pct_max' => 35,
            'carb_pct_min' => 50, 'carb_pct_max' => 65,
            'fiber' => 5.0,
            'calcium' => 260, 'iron' => 0.27, 'magnesium' => 30,
            'zinc' => 2.0, 'vitamin_a' => 400, 'vitamin_c' => 40,
            'sodium_max' => 120,
            'saturated_fat_pct_max' => 10,
            'added_sugar_allowed' => false,
        ],
        StudentGroup::AGE_CRECHE_6_11 => [
            'energy' => 600,
            'protein_pct_min' => 10, 'protein_pct_max' => 15,
            'lipid_pct_min' => 25, 'lipid_pct_max' => 35,
            'carb_pct_min' => 50, 'carb_pct_max' => 65,
            'fiber' => 9.0,
            'calcium' => 260, 'iron' => 11.0, 'magnesium' => 75,
            'zinc' => 3.0, 'vitamin_a' => 500, 'vitamin_c' => 50,
            'sodium_max' => 370,
            'saturated_fat_pct_max' => 10,
            'added_sugar_allowed' => false,
        ],
        StudentGroup::AGE_CRECHE_1_3 => [
            'energy' => 1000,
            'protein_pct_min' => 10, 'protein_pct_max' => 15,
            'lipid_pct_min' => 25, 'lipid_pct_max' => 35,
            'carb_pct_min' => 50, 'carb_pct_max' => 65,
            'fiber' => 19.0,
            'calcium' => 700, 'iron' => 7.0, 'magnesium' => 80,
            'zinc' => 3.0, 'vitamin_a' => 300, 'vitamin_c' => 15,
            'sodium_max' => 800,
            'saturated_fat_pct_max' => 10,
            'added_sugar_allowed' => false,
        ],
        StudentGroup::AGE_PRE_ESCOLA => [
            'energy' => 1400,
            'protein_pct_min' => 10, 'protein_pct_max' => 15,
            'lipid_pct_min' => 25, 'lipid_pct_max' => 35,
            'carb_pct_min' => 50, 'carb_pct_max' => 65,
            'fiber' => 25.0,
            'calcium' => 1000, 'iron' => 10.0, 'magnesium' => 130,
            'zinc' => 5.0, 'vitamin_a' => 400, 'vitamin_c' => 25,
            'sodium_max' => 1200,
            'saturated_fat_pct_max' => 10,
            'added_sugar_allowed' => true,
        ],
        StudentGroup::AGE_FUNDAMENTAL_6_10 => [
            'energy' => 1600,
            'protein_pct_min' => 10, 'protein_pct_max' => 15,
            'lipid_pct_min' => 25, 'lipid_pct_max' => 35,
            'carb_pct_min' => 50, 'carb_pct_max' => 65,
            'fiber' => 26.0,
            'calcium' => 1000, 'iron' => 8.0, 'magnesium' => 200,
            'zinc' => 5.0, 'vitamin_a' => 600, 'vitamin_c' => 45,
            'sodium_max' => 1500,
            'saturated_fat_pct_max' => 10,
            'added_sugar_allowed' => true,
        ],
        StudentGroup::AGE_FUNDAMENTAL_11_15 => [
            'energy' => 2000,
            'protein_pct_min' => 10, 'protein_pct_max' => 15,
            'lipid_pct_min' => 25, 'lipid_pct_max' => 35,
            'carb_pct_min' => 50, 'carb_pct_max' => 65,
            'fiber' => 32.0,
            'calcium' => 1300, 'iron' => 11.0, 'magnesium' => 360,
            'zinc' => 9.0, 'vitamin_a' => 900, 'vitamin_c' => 75,
            'sodium_max' => 2300,
            'saturated_fat_pct_max' => 10,
            'added_sugar_allowed' => true,
        ],
        StudentGroup::AGE_MEDIO => [
            'energy' => 2200,
            'protein_pct_min' => 10, 'protein_pct_max' => 15,
            'lipid_pct_min' => 25, 'lipid_pct_max' => 35,
            'carb_pct_min' => 50, 'carb_pct_max' => 65,
            'fiber' => 34.0,
            'calcium' => 1300, 'iron' => 15.0, 'magnesium' => 410,
            'zinc' => 11.0, 'vitamin_a' => 900, 'vitamin_c' => 90,
            'sodium_max' => 2300,
            'saturated_fat_pct_max' => 10,
            'added_sugar_allowed' => true,
        ],
        StudentGroup::AGE_EJA => [
            'energy' => 2000,
            'protein_pct_min' => 10, 'protein_pct_max' => 15,
            'lipid_pct_min' => 25, 'lipid_pct_max' => 35,
            'carb_pct_min' => 50, 'carb_pct_max' => 65,
            'fiber' => 32.0,
            'calcium' => 1000, 'iron' => 18.0, 'magnesium' => 320,
            'zinc' => 8.0, 'vitamin_a' => 700, 'vitamin_c' => 75,
            'sodium_max' => 2300,
            'saturated_fat_pct_max' => 10,
            'added_sugar_allowed' => true,
        ],
    ];

    private const PERIOD_MULTIPLIERS = [
        StudentGroup::PERIOD_PARTIAL_20 => 0.20,
        StudentGroup::PERIOD_PARTIAL_30 => 0.30,
        StudentGroup::PERIOD_INTEGRAL   => 0.70,
    ];

    public function getDailyReference(string $ageGroup): array
    {
        return self::DAILY_REFERENCES[$ageGroup] ?? self::DAILY_REFERENCES[StudentGroup::AGE_FUNDAMENTAL_6_10];
    }

    public function getMealReference(string $ageGroup, string $mealPeriod): array
    {
        $daily = $this->getDailyReference($ageGroup);
        $multiplier = self::PERIOD_MULTIPLIERS[$mealPeriod] ?? 0.20;

        return [
            'energy' => round($daily['energy'] * $multiplier, 1),
            'protein_min' => round(($daily['energy'] * $multiplier * $daily['protein_pct_min'] / 100) / 4, 1),
            'protein_max' => round(($daily['energy'] * $multiplier * $daily['protein_pct_max'] / 100) / 4, 1),
            'lipid_min' => round(($daily['energy'] * $multiplier * $daily['lipid_pct_min'] / 100) / 9, 1),
            'lipid_max' => round(($daily['energy'] * $multiplier * $daily['lipid_pct_max'] / 100) / 9, 1),
            'carb_min' => round(($daily['energy'] * $multiplier * $daily['carb_pct_min'] / 100) / 4, 1),
            'carb_max' => round(($daily['energy'] * $multiplier * $daily['carb_pct_max'] / 100) / 4, 1),
            'fiber' => round($daily['fiber'] * $multiplier, 1),
            'calcium' => round($daily['calcium'] * $multiplier, 1),
            'iron' => round($daily['iron'] * $multiplier, 2),
            'magnesium' => round($daily['magnesium'] * $multiplier, 1),
            'zinc' => round($daily['zinc'] * $multiplier, 2),
            'vitamin_a' => round($daily['vitamin_a'] * $multiplier, 1),
            'vitamin_c' => round($daily['vitamin_c'] * $multiplier, 1),
            'sodium_max' => round($daily['sodium_max'] * $multiplier, 1),
            'saturated_fat_pct_max' => $daily['saturated_fat_pct_max'],
            'added_sugar_allowed' => $daily['added_sugar_allowed'],
        ];
    }

    public function getPeriodMultiplier(string $mealPeriod): float
    {
        return self::PERIOD_MULTIPLIERS[$mealPeriod] ?? 0.20;
    }

    public function getAllAgeGroups(): array
    {
        return array_keys(self::DAILY_REFERENCES);
    }

    public function getAgeGroupLabel(string $ageGroup): string
    {
        $labels = [
            StudentGroup::AGE_CRECHE_0_5 => 'Creche (0 a 5 meses)',
            StudentGroup::AGE_CRECHE_6_11 => 'Creche (6 a 11 meses)',
            StudentGroup::AGE_CRECHE_1_3 => 'Creche (1 a 3 anos)',
            StudentGroup::AGE_PRE_ESCOLA => 'Pre-escola (4 a 5 anos)',
            StudentGroup::AGE_FUNDAMENTAL_6_10 => 'Fundamental (6 a 10 anos)',
            StudentGroup::AGE_FUNDAMENTAL_11_15 => 'Fundamental (11 a 15 anos)',
            StudentGroup::AGE_MEDIO => 'Ensino Medio',
            StudentGroup::AGE_EJA => 'EJA',
        ];
        return $labels[$ageGroup] ?? $ageGroup;
    }
}
