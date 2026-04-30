<?php

namespace App\Command;

use App\Entity\Food;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:import-taco', description: 'Import TACO food database')]
class ImportTacoCommand extends Command
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Importando dados da Tabela TACO');

        $foods = $this->getTacoData();
        $count = 0;

        foreach ($foods as $foodData) {
            $existing = $this->em->getRepository(Food::class)->findOneBy(['tacoId' => $foodData['tacoId']]);
            if ($existing) continue;

            $food = new Food();
            $food->setTacoId($foodData['tacoId']);
            $food->setName($foodData['name']);
            $food->setCategory($foodData['category']);
            $food->setSource('taco');
            $food->setEnergy($foodData['energy'] ?? null);
            $food->setProtein($foodData['protein'] ?? null);
            $food->setCarbohydrate($foodData['carbohydrate'] ?? null);
            $food->setLipid($foodData['lipid'] ?? null);
            $food->setFiber($foodData['fiber'] ?? null);
            $food->setCalcium($foodData['calcium'] ?? null);
            $food->setIron($foodData['iron'] ?? null);
            $food->setMagnesium($foodData['magnesium'] ?? null);
            $food->setZinc($foodData['zinc'] ?? null);
            $food->setVitaminA($foodData['vitaminA'] ?? null);
            $food->setVitaminC($foodData['vitaminC'] ?? null);
            $food->setSodium($foodData['sodium'] ?? null);
            $food->setSaturatedFat($foodData['saturatedFat'] ?? null);
            $food->setContainsGluten($foodData['containsGluten'] ?? false);
            $food->setContainsLactose($foodData['containsLactose'] ?? false);
            $food->setUltraProcessed($foodData['ultraProcessed'] ?? false);
            $food->setSeasonMonths($foodData['seasonMonths'] ?? null);

            $this->em->persist($food);
            $count++;

            if ($count % 50 === 0) {
                $this->em->flush();
            }
        }

        $this->em->flush();
        $io->success("Importados {$count} alimentos da tabela TACO.");

        return Command::SUCCESS;
    }

    private function getTacoData(): array
    {
        return [
            // Cereais e derivados
            ['tacoId' => 'T001', 'name' => 'Arroz integral cozido', 'category' => 'Cereais e derivados', 'energy' => 124, 'protein' => 2.6, 'carbohydrate' => 25.8, 'lipid' => 1.0, 'fiber' => 2.7, 'calcium' => 5, 'iron' => 0.3, 'magnesium' => 59, 'zinc' => 0.8, 'vitaminA' => 0, 'vitaminC' => 0, 'sodium' => 1, 'saturatedFat' => 0.2, 'containsGluten' => false, 'containsLactose' => false],
            ['tacoId' => 'T002', 'name' => 'Arroz branco polido cozido', 'category' => 'Cereais e derivados', 'energy' => 128, 'protein' => 2.5, 'carbohydrate' => 28.1, 'lipid' => 0.2, 'fiber' => 1.6, 'calcium' => 4, 'iron' => 0.1, 'magnesium' => 3, 'zinc' => 0.5, 'vitaminA' => 0, 'vitaminC' => 0, 'sodium' => 1, 'saturatedFat' => 0.1, 'containsGluten' => false, 'containsLactose' => false],
            ['tacoId' => 'T003', 'name' => 'Aveia em flocos crua', 'category' => 'Cereais e derivados', 'energy' => 394, 'protein' => 13.9, 'carbohydrate' => 66.6, 'lipid' => 8.5, 'fiber' => 9.1, 'calcium' => 48, 'iron' => 4.4, 'magnesium' => 119, 'zinc' => 2.6, 'vitaminA' => 0, 'vitaminC' => 0, 'sodium' => 4, 'saturatedFat' => 1.5, 'containsGluten' => true, 'containsLactose' => false],
            ['tacoId' => 'T004', 'name' => 'Farinha de mandioca crua', 'category' => 'Cereais e derivados', 'energy' => 361, 'protein' => 1.6, 'carbohydrate' => 87.9, 'lipid' => 0.3, 'fiber' => 6.5, 'calcium' => 46, 'iron' => 0.9, 'magnesium' => 35, 'zinc' => 0.5, 'vitaminA' => 0, 'vitaminC' => 0, 'sodium' => 2, 'saturatedFat' => 0.1, 'containsGluten' => false, 'containsLactose' => false],
            ['tacoId' => 'T005', 'name' => 'Farinha de trigo', 'category' => 'Cereais e derivados', 'energy' => 360, 'protein' => 9.8, 'carbohydrate' => 75.1, 'lipid' => 1.4, 'fiber' => 2.3, 'calcium' => 17, 'iron' => 1.0, 'magnesium' => 28, 'zinc' => 0.8, 'vitaminA' => 0, 'vitaminC' => 0, 'sodium' => 1, 'saturatedFat' => 0.2, 'containsGluten' => true, 'containsLactose' => false],
            ['tacoId' => 'T006', 'name' => 'Macarrao cozido', 'category' => 'Cereais e derivados', 'energy' => 102, 'protein' => 3.4, 'carbohydrate' => 19.9, 'lipid' => 0.5, 'fiber' => 1.5, 'calcium' => 5, 'iron' => 0.3, 'magnesium' => 14, 'zinc' => 0.3, 'vitaminA' => 0, 'vitaminC' => 0, 'sodium' => 1, 'saturatedFat' => 0.1, 'containsGluten' => true, 'containsLactose' => false],
            ['tacoId' => 'T007', 'name' => 'Milho verde cozido', 'category' => 'Cereais e derivados', 'energy' => 138, 'protein' => 3.6, 'carbohydrate' => 28.6, 'lipid' => 1.5, 'fiber' => 3.9, 'calcium' => 3, 'iron' => 0.5, 'magnesium' => 33, 'zinc' => 0.5, 'vitaminA' => 28, 'vitaminC' => 5.1, 'sodium' => 1, 'saturatedFat' => 0.2, 'containsGluten' => false, 'containsLactose' => false],
            ['tacoId' => 'T008', 'name' => 'Pao frances', 'category' => 'Cereais e derivados', 'energy' => 300, 'protein' => 8.0, 'carbohydrate' => 58.6, 'lipid' => 3.1, 'fiber' => 2.3, 'calcium' => 22, 'iron' => 1.0, 'magnesium' => 22, 'zinc' => 0.7, 'vitaminA' => 0, 'vitaminC' => 0, 'sodium' => 648, 'saturatedFat' => 0.8, 'containsGluten' => true, 'containsLactose' => false],
            ['tacoId' => 'T009', 'name' => 'Polenta cozida', 'category' => 'Cereais e derivados', 'energy' => 101, 'protein' => 2.4, 'carbohydrate' => 20.1, 'lipid' => 0.7, 'fiber' => 1.4, 'calcium' => 2, 'iron' => 0.3, 'magnesium' => 12, 'zinc' => 0.2, 'vitaminA' => 0, 'vitaminC' => 0, 'sodium' => 171, 'saturatedFat' => 0.1, 'containsGluten' => false, 'containsLactose' => false],

            // Verduras, hortalicas e derivados
            ['tacoId' => 'T010', 'name' => 'Abobora cozida', 'category' => 'Verduras e hortalicas', 'energy' => 18, 'protein' => 0.7, 'carbohydrate' => 4.2, 'lipid' => 0.1, 'fiber' => 1.6, 'calcium' => 13, 'iron' => 0.2, 'magnesium' => 7, 'zinc' => 0.1, 'vitaminA' => 540, 'vitaminC' => 3.0, 'sodium' => 1, 'saturatedFat' => 0, 'containsGluten' => false, 'containsLactose' => false, 'seasonMonths' => [3,4,5,6,7,8]],
            ['tacoId' => 'T011', 'name' => 'Alface lisa crua', 'category' => 'Verduras e hortalicas', 'energy' => 11, 'protein' => 1.3, 'carbohydrate' => 1.7, 'lipid' => 0.2, 'fiber' => 1.0, 'calcium' => 38, 'iron' => 0.4, 'magnesium' => 11, 'zinc' => 0.3, 'vitaminA' => 230, 'vitaminC' => 15.6, 'sodium' => 2, 'saturatedFat' => 0, 'containsGluten' => false, 'containsLactose' => false],
            ['tacoId' => 'T012', 'name' => 'Batata doce cozida', 'category' => 'Verduras e hortalicas', 'energy' => 77, 'protein' => 0.6, 'carbohydrate' => 18.4, 'lipid' => 0.1, 'fiber' => 2.2, 'calcium' => 17, 'iron' => 0.2, 'magnesium' => 11, 'zinc' => 0.1, 'vitaminA' => 11, 'vitaminC' => 23.8, 'sodium' => 2, 'saturatedFat' => 0, 'containsGluten' => false, 'containsLactose' => false, 'seasonMonths' => [1,2,3,4,5,6]],
            ['tacoId' => 'T013', 'name' => 'Beterraba cozida', 'category' => 'Verduras e hortalicas', 'energy' => 32, 'protein' => 1.3, 'carbohydrate' => 7.2, 'lipid' => 0.1, 'fiber' => 1.9, 'calcium' => 11, 'iron' => 0.2, 'magnesium' => 14, 'zinc' => 0.2, 'vitaminA' => 2, 'vitaminC' => 1.0, 'sodium' => 45, 'saturatedFat' => 0, 'containsGluten' => false, 'containsLactose' => false],
            ['tacoId' => 'T014', 'name' => 'Cenoura crua', 'category' => 'Verduras e hortalicas', 'energy' => 34, 'protein' => 1.3, 'carbohydrate' => 7.7, 'lipid' => 0.2, 'fiber' => 3.2, 'calcium' => 23, 'iron' => 0.2, 'magnesium' => 11, 'zinc' => 0.2, 'vitaminA' => 933, 'vitaminC' => 5.1, 'sodium' => 3, 'saturatedFat' => 0, 'containsGluten' => false, 'containsLactose' => false, 'seasonMonths' => [6,7,8,9,10]],
            ['tacoId' => 'T015', 'name' => 'Chuchu cozido', 'category' => 'Verduras e hortalicas', 'energy' => 22, 'protein' => 0.4, 'carbohydrate' => 5.1, 'lipid' => 0.1, 'fiber' => 1.6, 'calcium' => 8, 'iron' => 0.1, 'magnesium' => 6, 'zinc' => 0.2, 'vitaminA' => 2, 'vitaminC' => 7.5, 'sodium' => 1, 'saturatedFat' => 0, 'containsGluten' => false, 'containsLactose' => false, 'seasonMonths' => [5,6,7,8,9,10]],
            ['tacoId' => 'T016', 'name' => 'Couve manteiga refogada', 'category' => 'Verduras e hortalicas', 'energy' => 90, 'protein' => 2.9, 'carbohydrate' => 7.9, 'lipid' => 5.4, 'fiber' => 4.2, 'calcium' => 177, 'iron' => 0.6, 'magnesium' => 26, 'zinc' => 0.2, 'vitaminA' => 280, 'vitaminC' => 76.7, 'sodium' => 29, 'saturatedFat' => 0.8, 'containsGluten' => false, 'containsLactose' => false, 'seasonMonths' => [4,5,6,7,8,9]],
            ['tacoId' => 'T017', 'name' => 'Mandioca cozida', 'category' => 'Verduras e hortalicas', 'energy' => 125, 'protein' => 0.6, 'carbohydrate' => 30.1, 'lipid' => 0.3, 'fiber' => 1.6, 'calcium' => 15, 'iron' => 0.3, 'magnesium' => 16, 'zinc' => 0.2, 'vitaminA' => 0, 'vitaminC' => 14.4, 'sodium' => 3, 'saturatedFat' => 0.1, 'containsGluten' => false, 'containsLactose' => false],
            ['tacoId' => 'T018', 'name' => 'Tomate cru', 'category' => 'Verduras e hortalicas', 'energy' => 15, 'protein' => 1.1, 'carbohydrate' => 3.1, 'lipid' => 0.2, 'fiber' => 1.2, 'calcium' => 7, 'iron' => 0.2, 'magnesium' => 10, 'zinc' => 0.1, 'vitaminA' => 54, 'vitaminC' => 21.2, 'sodium' => 1, 'saturatedFat' => 0, 'containsGluten' => false, 'containsLactose' => false],

            // Frutas e derivados
            ['tacoId' => 'T019', 'name' => 'Banana prata crua', 'category' => 'Frutas e derivados', 'energy' => 98, 'protein' => 1.3, 'carbohydrate' => 26.0, 'lipid' => 0.1, 'fiber' => 2.0, 'calcium' => 8, 'iron' => 0.4, 'magnesium' => 26, 'zinc' => 0.2, 'vitaminA' => 6, 'vitaminC' => 21.6, 'sodium' => 0, 'saturatedFat' => 0, 'containsGluten' => false, 'containsLactose' => false],
            ['tacoId' => 'T020', 'name' => 'Laranja pera crua', 'category' => 'Frutas e derivados', 'energy' => 37, 'protein' => 1.0, 'carbohydrate' => 8.9, 'lipid' => 0.1, 'fiber' => 0.8, 'calcium' => 22, 'iron' => 0.1, 'magnesium' => 12, 'zinc' => 0.1, 'vitaminA' => 13, 'vitaminC' => 53.7, 'sodium' => 0, 'saturatedFat' => 0, 'containsGluten' => false, 'containsLactose' => false, 'seasonMonths' => [5,6,7,8,9,10]],
            ['tacoId' => 'T021', 'name' => 'Maca fuji crua', 'category' => 'Frutas e derivados', 'energy' => 56, 'protein' => 0.3, 'carbohydrate' => 15.2, 'lipid' => 0.0, 'fiber' => 1.3, 'calcium' => 2, 'iron' => 0.1, 'magnesium' => 2, 'zinc' => 0.0, 'vitaminA' => 3, 'vitaminC' => 2.4, 'sodium' => 0, 'saturatedFat' => 0, 'containsGluten' => false, 'containsLactose' => false, 'seasonMonths' => [1,2,3,4]],
            ['tacoId' => 'T022', 'name' => 'Mamao papaia cru', 'category' => 'Frutas e derivados', 'energy' => 40, 'protein' => 0.5, 'carbohydrate' => 10.4, 'lipid' => 0.1, 'fiber' => 1.0, 'calcium' => 25, 'iron' => 0.2, 'magnesium' => 11, 'zinc' => 0.1, 'vitaminA' => 37, 'vitaminC' => 82.2, 'sodium' => 3, 'saturatedFat' => 0, 'containsGluten' => false, 'containsLactose' => false],
            ['tacoId' => 'T023', 'name' => 'Manga crua', 'category' => 'Frutas e derivados', 'energy' => 64, 'protein' => 0.4, 'carbohydrate' => 16.7, 'lipid' => 0.3, 'fiber' => 1.6, 'calcium' => 12, 'iron' => 0.2, 'magnesium' => 10, 'zinc' => 0.0, 'vitaminA' => 210, 'vitaminC' => 17.4, 'sodium' => 2, 'saturatedFat' => 0.1, 'containsGluten' => false, 'containsLactose' => false, 'seasonMonths' => [10,11,12,1,2]],
            ['tacoId' => 'T024', 'name' => 'Melancia crua', 'category' => 'Frutas e derivados', 'energy' => 33, 'protein' => 0.9, 'carbohydrate' => 8.1, 'lipid' => 0.0, 'fiber' => 0.1, 'calcium' => 8, 'iron' => 0.2, 'magnesium' => 7, 'zinc' => 0.1, 'vitaminA' => 36, 'vitaminC' => 6.1, 'sodium' => 0, 'saturatedFat' => 0, 'containsGluten' => false, 'containsLactose' => false, 'seasonMonths' => [11,12,1,2,3]],
            ['tacoId' => 'T025', 'name' => 'Abacaxi cru', 'category' => 'Frutas e derivados', 'energy' => 48, 'protein' => 0.9, 'carbohydrate' => 12.3, 'lipid' => 0.1, 'fiber' => 1.0, 'calcium' => 22, 'iron' => 0.3, 'magnesium' => 18, 'zinc' => 0.1, 'vitaminA' => 3, 'vitaminC' => 34.6, 'sodium' => 1, 'saturatedFat' => 0, 'containsGluten' => false, 'containsLactose' => false, 'seasonMonths' => [11,12,1,2,3]],

            // Leguminosas e derivados
            ['tacoId' => 'T026', 'name' => 'Feijao carioca cozido', 'category' => 'Leguminosas e derivados', 'energy' => 76, 'protein' => 4.8, 'carbohydrate' => 13.6, 'lipid' => 0.5, 'fiber' => 8.5, 'calcium' => 27, 'iron' => 1.3, 'magnesium' => 42, 'zinc' => 0.8, 'vitaminA' => 0, 'vitaminC' => 0, 'sodium' => 2, 'saturatedFat' => 0.1, 'containsGluten' => false, 'containsLactose' => false],
            ['tacoId' => 'T027', 'name' => 'Feijao preto cozido', 'category' => 'Leguminosas e derivados', 'energy' => 77, 'protein' => 4.5, 'carbohydrate' => 14.0, 'lipid' => 0.5, 'fiber' => 8.4, 'calcium' => 29, 'iron' => 1.5, 'magnesium' => 50, 'zinc' => 0.6, 'vitaminA' => 0, 'vitaminC' => 0, 'sodium' => 2, 'saturatedFat' => 0.1, 'containsGluten' => false, 'containsLactose' => false],
            ['tacoId' => 'T028', 'name' => 'Lentilha cozida', 'category' => 'Leguminosas e derivados', 'energy' => 93, 'protein' => 6.3, 'carbohydrate' => 16.3, 'lipid' => 0.5, 'fiber' => 7.9, 'calcium' => 18, 'iron' => 1.5, 'magnesium' => 22, 'zinc' => 0.9, 'vitaminA' => 0, 'vitaminC' => 0, 'sodium' => 2, 'saturatedFat' => 0.1, 'containsGluten' => false, 'containsLactose' => false],
            ['tacoId' => 'T029', 'name' => 'Soja cozida', 'category' => 'Leguminosas e derivados', 'energy' => 151, 'protein' => 14.0, 'carbohydrate' => 7.1, 'lipid' => 7.5, 'fiber' => 5.6, 'calcium' => 78, 'iron' => 2.5, 'magnesium' => 63, 'zinc' => 1.1, 'vitaminA' => 1, 'vitaminC' => 0, 'sodium' => 1, 'saturatedFat' => 1.1, 'containsGluten' => false, 'containsLactose' => false],
            ['tacoId' => 'T030', 'name' => 'Grao de bico cozido', 'category' => 'Leguminosas e derivados', 'energy' => 130, 'protein' => 6.7, 'carbohydrate' => 22.9, 'lipid' => 1.8, 'fiber' => 9.9, 'calcium' => 46, 'iron' => 2.1, 'magnesium' => 36, 'zinc' => 1.3, 'vitaminA' => 3, 'vitaminC' => 0, 'sodium' => 5, 'saturatedFat' => 0.2, 'containsGluten' => false, 'containsLactose' => false],

            // Carnes e derivados
            ['tacoId' => 'T031', 'name' => 'Carne bovina acem cozido', 'category' => 'Carnes e derivados', 'energy' => 215, 'protein' => 26.7, 'carbohydrate' => 0, 'lipid' => 11.5, 'fiber' => 0, 'calcium' => 4, 'iron' => 2.3, 'magnesium' => 18, 'zinc' => 7.7, 'vitaminA' => 0, 'vitaminC' => 0, 'sodium' => 42, 'saturatedFat' => 4.3, 'containsGluten' => false, 'containsLactose' => false],
            ['tacoId' => 'T032', 'name' => 'Frango peito sem pele cozido', 'category' => 'Carnes e derivados', 'energy' => 159, 'protein' => 32.0, 'carbohydrate' => 0, 'lipid' => 2.5, 'fiber' => 0, 'calcium' => 4, 'iron' => 0.4, 'magnesium' => 30, 'zinc' => 1.0, 'vitaminA' => 3, 'vitaminC' => 0, 'sodium' => 50, 'saturatedFat' => 0.7, 'containsGluten' => false, 'containsLactose' => false],
            ['tacoId' => 'T033', 'name' => 'Frango coxa sem pele cozida', 'category' => 'Carnes e derivados', 'energy' => 194, 'protein' => 27.2, 'carbohydrate' => 0, 'lipid' => 8.8, 'fiber' => 0, 'calcium' => 11, 'iron' => 0.8, 'magnesium' => 21, 'zinc' => 2.6, 'vitaminA' => 11, 'vitaminC' => 0, 'sodium' => 67, 'saturatedFat' => 2.5, 'containsGluten' => false, 'containsLactose' => false],
            ['tacoId' => 'T034', 'name' => 'Carne bovina moida refogada', 'category' => 'Carnes e derivados', 'energy' => 212, 'protein' => 26.3, 'carbohydrate' => 0, 'lipid' => 11.4, 'fiber' => 0, 'calcium' => 6, 'iron' => 2.5, 'magnesium' => 19, 'zinc' => 7.0, 'vitaminA' => 0, 'vitaminC' => 0, 'sodium' => 52, 'saturatedFat' => 4.0, 'containsGluten' => false, 'containsLactose' => false],
            ['tacoId' => 'T035', 'name' => 'Ovo de galinha cozido', 'category' => 'Carnes e derivados', 'energy' => 146, 'protein' => 13.3, 'carbohydrate' => 0.6, 'lipid' => 9.5, 'fiber' => 0, 'calcium' => 49, 'iron' => 1.5, 'magnesium' => 10, 'zinc' => 1.1, 'vitaminA' => 220, 'vitaminC' => 0, 'sodium' => 142, 'saturatedFat' => 3.1, 'containsGluten' => false, 'containsLactose' => false],

            // Pescados e frutos do mar
            ['tacoId' => 'T036', 'name' => 'Sardinha assada', 'category' => 'Pescados e frutos do mar', 'energy' => 164, 'protein' => 32.2, 'carbohydrate' => 0, 'lipid' => 5.0, 'fiber' => 0, 'calcium' => 437, 'iron' => 2.5, 'magnesium' => 40, 'zinc' => 1.5, 'vitaminA' => 14, 'vitaminC' => 0, 'sodium' => 100, 'saturatedFat' => 1.5, 'containsGluten' => false, 'containsLactose' => false],
            ['tacoId' => 'T037', 'name' => 'Tilapia cozida', 'category' => 'Pescados e frutos do mar', 'energy' => 124, 'protein' => 24.4, 'carbohydrate' => 0, 'lipid' => 2.7, 'fiber' => 0, 'calcium' => 10, 'iron' => 0.6, 'magnesium' => 28, 'zinc' => 0.5, 'vitaminA' => 8, 'vitaminC' => 0, 'sodium' => 50, 'saturatedFat' => 0.8, 'containsGluten' => false, 'containsLactose' => false],

            // Leite e derivados
            ['tacoId' => 'T038', 'name' => 'Leite integral', 'category' => 'Leite e derivados', 'energy' => 58, 'protein' => 3.0, 'carbohydrate' => 4.5, 'lipid' => 3.2, 'fiber' => 0, 'calcium' => 113, 'iron' => 0.1, 'magnesium' => 10, 'zinc' => 0.4, 'vitaminA' => 46, 'vitaminC' => 1.0, 'sodium' => 50, 'saturatedFat' => 1.9, 'containsGluten' => false, 'containsLactose' => true],
            ['tacoId' => 'T039', 'name' => 'Iogurte natural', 'category' => 'Leite e derivados', 'energy' => 51, 'protein' => 4.1, 'carbohydrate' => 5.5, 'lipid' => 1.4, 'fiber' => 0, 'calcium' => 143, 'iron' => 0.1, 'magnesium' => 12, 'zinc' => 0.6, 'vitaminA' => 17, 'vitaminC' => 0.5, 'sodium' => 51, 'saturatedFat' => 0.9, 'containsGluten' => false, 'containsLactose' => true],
            ['tacoId' => 'T040', 'name' => 'Queijo minas frescal', 'category' => 'Leite e derivados', 'energy' => 264, 'protein' => 17.4, 'carbohydrate' => 3.2, 'lipid' => 20.2, 'fiber' => 0, 'calcium' => 579, 'iron' => 0.3, 'magnesium' => 17, 'zinc' => 1.6, 'vitaminA' => 178, 'vitaminC' => 0, 'sodium' => 440, 'saturatedFat' => 12.0, 'containsGluten' => false, 'containsLactose' => true],

            // Oleos e gorduras
            ['tacoId' => 'T041', 'name' => 'Oleo de soja', 'category' => 'Oleos e gorduras', 'energy' => 884, 'protein' => 0, 'carbohydrate' => 0, 'lipid' => 100.0, 'fiber' => 0, 'calcium' => 0, 'iron' => 0, 'magnesium' => 0, 'zinc' => 0, 'vitaminA' => 0, 'vitaminC' => 0, 'sodium' => 0, 'saturatedFat' => 15.6, 'containsGluten' => false, 'containsLactose' => false],
            ['tacoId' => 'T042', 'name' => 'Azeite de oliva', 'category' => 'Oleos e gorduras', 'energy' => 884, 'protein' => 0, 'carbohydrate' => 0, 'lipid' => 100.0, 'fiber' => 0, 'calcium' => 0, 'iron' => 0, 'magnesium' => 0, 'zinc' => 0, 'vitaminA' => 0, 'vitaminC' => 0, 'sodium' => 0, 'saturatedFat' => 14.0, 'containsGluten' => false, 'containsLactose' => false],
            ['tacoId' => 'T043', 'name' => 'Margarina com sal', 'category' => 'Oleos e gorduras', 'energy' => 540, 'protein' => 0.4, 'carbohydrate' => 0.3, 'lipid' => 60.0, 'fiber' => 0, 'calcium' => 4, 'iron' => 0, 'magnesium' => 2, 'zinc' => 0, 'vitaminA' => 606, 'vitaminC' => 0, 'sodium' => 818, 'saturatedFat' => 16.2, 'containsGluten' => false, 'containsLactose' => false, 'ultraProcessed' => true],

            // Acucares e doces
            ['tacoId' => 'T044', 'name' => 'Acucar refinado', 'category' => 'Acucares e doces', 'energy' => 387, 'protein' => 0, 'carbohydrate' => 99.6, 'lipid' => 0, 'fiber' => 0, 'calcium' => 1, 'iron' => 0.1, 'magnesium' => 0, 'zinc' => 0, 'vitaminA' => 0, 'vitaminC' => 0, 'sodium' => 1, 'saturatedFat' => 0, 'addedSugar' => 99.6, 'containsGluten' => false, 'containsLactose' => false],
            ['tacoId' => 'T045', 'name' => 'Mel', 'category' => 'Acucares e doces', 'energy' => 309, 'protein' => 0.3, 'carbohydrate' => 84.0, 'lipid' => 0, 'fiber' => 0, 'calcium' => 5, 'iron' => 0.3, 'magnesium' => 1, 'zinc' => 0, 'vitaminA' => 0, 'vitaminC' => 0.5, 'sodium' => 4, 'saturatedFat' => 0, 'addedSugar' => 84.0, 'containsGluten' => false, 'containsLactose' => false],

            // Industrializados / Ultraprocessados
            ['tacoId' => 'T046', 'name' => 'Salsicha', 'category' => 'Carnes e derivados', 'energy' => 243, 'protein' => 12.3, 'carbohydrate' => 4.0, 'lipid' => 19.7, 'fiber' => 0, 'calcium' => 67, 'iron' => 1.2, 'magnesium' => 10, 'zinc' => 1.3, 'vitaminA' => 0, 'vitaminC' => 0, 'sodium' => 1077, 'saturatedFat' => 7.4, 'containsGluten' => false, 'containsLactose' => false, 'ultraProcessed' => true],
            ['tacoId' => 'T047', 'name' => 'Biscoito recheado chocolate', 'category' => 'Cereais e derivados', 'energy' => 472, 'protein' => 5.6, 'carbohydrate' => 69.0, 'lipid' => 19.6, 'fiber' => 2.1, 'calcium' => 66, 'iron' => 2.1, 'magnesium' => 43, 'zinc' => 0.5, 'vitaminA' => 0, 'vitaminC' => 0, 'sodium' => 440, 'saturatedFat' => 7.0, 'addedSugar' => 30.0, 'containsGluten' => true, 'containsLactose' => true, 'ultraProcessed' => true],
            ['tacoId' => 'T048', 'name' => 'Refrigerante cola', 'category' => 'Bebidas', 'energy' => 39, 'protein' => 0, 'carbohydrate' => 10.0, 'lipid' => 0, 'fiber' => 0, 'calcium' => 3, 'iron' => 0, 'magnesium' => 1, 'zinc' => 0, 'vitaminA' => 0, 'vitaminC' => 0, 'sodium' => 7, 'saturatedFat' => 0, 'addedSugar' => 10.0, 'containsGluten' => false, 'containsLactose' => false, 'ultraProcessed' => true],

            // Temperos e condimentos
            ['tacoId' => 'T049', 'name' => 'Alho cru', 'category' => 'Verduras e hortalicas', 'energy' => 113, 'protein' => 7.0, 'carbohydrate' => 23.9, 'lipid' => 0.2, 'fiber' => 4.3, 'calcium' => 14, 'iron' => 0.7, 'magnesium' => 21, 'zinc' => 0.7, 'vitaminA' => 0, 'vitaminC' => 17.1, 'sodium' => 6, 'saturatedFat' => 0, 'containsGluten' => false, 'containsLactose' => false],
            ['tacoId' => 'T050', 'name' => 'Cebola crua', 'category' => 'Verduras e hortalicas', 'energy' => 39, 'protein' => 1.7, 'carbohydrate' => 8.9, 'lipid' => 0.1, 'fiber' => 2.2, 'calcium' => 15, 'iron' => 0.2, 'magnesium' => 9, 'zinc' => 0.1, 'vitaminA' => 0, 'vitaminC' => 4.7, 'sodium' => 1, 'saturatedFat' => 0, 'containsGluten' => false, 'containsLactose' => false],

            // Batata
            ['tacoId' => 'T051', 'name' => 'Batata inglesa cozida', 'category' => 'Verduras e hortalicas', 'energy' => 52, 'protein' => 1.2, 'carbohydrate' => 11.9, 'lipid' => 0.1, 'fiber' => 1.3, 'calcium' => 4, 'iron' => 0.3, 'magnesium' => 10, 'zinc' => 0.2, 'vitaminA' => 0, 'vitaminC' => 13.3, 'sodium' => 2, 'saturatedFat' => 0, 'containsGluten' => false, 'containsLactose' => false],
            ['tacoId' => 'T052', 'name' => 'Repolho cru', 'category' => 'Verduras e hortalicas', 'energy' => 17, 'protein' => 0.9, 'carbohydrate' => 3.9, 'lipid' => 0.1, 'fiber' => 1.9, 'calcium' => 37, 'iron' => 0.1, 'magnesium' => 9, 'zinc' => 0.1, 'vitaminA' => 1, 'vitaminC' => 24.7, 'sodium' => 2, 'saturatedFat' => 0, 'containsGluten' => false, 'containsLactose' => false, 'seasonMonths' => [5,6,7,8,9]],

            // Sucos
            ['tacoId' => 'T053', 'name' => 'Suco de laranja natural', 'category' => 'Bebidas', 'energy' => 40, 'protein' => 0.6, 'carbohydrate' => 10.0, 'lipid' => 0.1, 'fiber' => 0.1, 'calcium' => 8, 'iron' => 0.1, 'magnesium' => 8, 'zinc' => 0.0, 'vitaminA' => 5, 'vitaminC' => 40.0, 'sodium' => 1, 'saturatedFat' => 0, 'containsGluten' => false, 'containsLactose' => false],

            // Sal
            ['tacoId' => 'T054', 'name' => 'Sal refinado', 'category' => 'Outros', 'energy' => 0, 'protein' => 0, 'carbohydrate' => 0, 'lipid' => 0, 'fiber' => 0, 'calcium' => 6, 'iron' => 0, 'magnesium' => 2, 'zinc' => 0, 'vitaminA' => 0, 'vitaminC' => 0, 'sodium' => 38758, 'saturatedFat' => 0, 'containsGluten' => false, 'containsLactose' => false],

            // Mais alimentos da Agricultura Familiar
            ['tacoId' => 'T055', 'name' => 'Inhame cozido', 'category' => 'Verduras e hortalicas', 'energy' => 97, 'protein' => 2.1, 'carbohydrate' => 23.2, 'lipid' => 0.1, 'fiber' => 1.7, 'calcium' => 8, 'iron' => 0.3, 'magnesium' => 17, 'zinc' => 0.4, 'vitaminA' => 0, 'vitaminC' => 2.1, 'sodium' => 3, 'saturatedFat' => 0, 'containsGluten' => false, 'containsLactose' => false, 'seasonMonths' => [4,5,6,7,8]],
            ['tacoId' => 'T056', 'name' => 'Espinafre refogado', 'category' => 'Verduras e hortalicas', 'energy' => 68, 'protein' => 2.5, 'carbohydrate' => 4.4, 'lipid' => 4.5, 'fiber' => 3.1, 'calcium' => 160, 'iron' => 1.1, 'magnesium' => 30, 'zinc' => 0.3, 'vitaminA' => 570, 'vitaminC' => 3.0, 'sodium' => 180, 'saturatedFat' => 0.7, 'containsGluten' => false, 'containsLactose' => false, 'seasonMonths' => [4,5,6,7,8]],
            ['tacoId' => 'T057', 'name' => 'Abobora moranga cozida', 'category' => 'Verduras e hortalicas', 'energy' => 22, 'protein' => 0.8, 'carbohydrate' => 4.8, 'lipid' => 0.1, 'fiber' => 1.4, 'calcium' => 16, 'iron' => 0.3, 'magnesium' => 6, 'zinc' => 0.1, 'vitaminA' => 700, 'vitaminC' => 5.0, 'sodium' => 1, 'saturatedFat' => 0, 'containsGluten' => false, 'containsLactose' => false, 'seasonMonths' => [3,4,5,6,7,8]],
            ['tacoId' => 'T058', 'name' => 'Carne suina lombo assado', 'category' => 'Carnes e derivados', 'energy' => 210, 'protein' => 30.2, 'carbohydrate' => 0, 'lipid' => 9.8, 'fiber' => 0, 'calcium' => 9, 'iron' => 0.8, 'magnesium' => 24, 'zinc' => 2.3, 'vitaminA' => 3, 'vitaminC' => 0, 'sodium' => 56, 'saturatedFat' => 3.4, 'containsGluten' => false, 'containsLactose' => false],
            ['tacoId' => 'T059', 'name' => 'Feijao branco cozido', 'category' => 'Leguminosas e derivados', 'energy' => 99, 'protein' => 6.3, 'carbohydrate' => 17.6, 'lipid' => 0.5, 'fiber' => 12.0, 'calcium' => 50, 'iron' => 1.4, 'magnesium' => 40, 'zinc' => 0.9, 'vitaminA' => 0, 'vitaminC' => 0, 'sodium' => 2, 'saturatedFat' => 0.1, 'containsGluten' => false, 'containsLactose' => false],
            ['tacoId' => 'T060', 'name' => 'Ervilha cozida', 'category' => 'Leguminosas e derivados', 'energy' => 72, 'protein' => 5.0, 'carbohydrate' => 12.6, 'lipid' => 0.3, 'fiber' => 7.5, 'calcium' => 18, 'iron' => 1.1, 'magnesium' => 25, 'zinc' => 0.8, 'vitaminA' => 12, 'vitaminC' => 1.0, 'sodium' => 2, 'saturatedFat' => 0.1, 'containsGluten' => false, 'containsLactose' => false],
        ];
    }
}
