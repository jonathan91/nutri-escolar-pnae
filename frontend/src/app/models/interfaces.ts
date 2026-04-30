export interface User {
  id: number;
  email: string;
  name: string;
  crn?: string;
  roles: string[];
}

export interface LoginRequest {
  username: string;
  password: string;
}

export interface RegisterRequest {
  email: string;
  name: string;
  password: string;
  crn?: string;
}

export interface School {
  id: number;
  name: string;
  city?: string;
  state?: string;
  inepCode?: string;
  studentGroupCount?: number;
  studentGroups?: StudentGroup[];
}

export interface StudentGroup {
  id: number;
  name: string;
  ageGroup: string;
  mealPeriod: string;
  studentCount: number;
}

export interface Food {
  id: number;
  tacoId?: string;
  name: string;
  category?: string;
  source: string;
  energy?: number;
  protein?: number;
  carbohydrate?: number;
  lipid?: number;
  fiber?: number;
  calcium?: number;
  iron?: number;
  magnesium?: number;
  zinc?: number;
  vitaminA?: number;
  vitaminC?: number;
  sodium?: number;
  saturatedFat?: number;
  ultraProcessed: boolean;
  containsGluten: boolean;
  containsLactose: boolean;
  allergens?: string[];
  seasonMonths?: number[];
}

export interface Recipe {
  id: number;
  name: string;
  preparationMethod?: string;
  portions: number;
  costPerPortion?: number;
  ingredientCount?: number;
  ingredients?: RecipeIngredient[];
  nutritionPerPortion?: NutritionData;
  nutritionTotal?: NutritionData;
  cost?: { total_cost: number; cost_per_portion: number };
  createdAt?: string;
}

export interface RecipeIngredient {
  id?: number;
  food: { id: number; name: string };
  foodId?: number;
  grossWeight: number;
  netWeight: number;
  costPerKg?: number;
}

export interface Menu {
  id: number;
  name: string;
  menuDate: string;
  mealType: string;
  schoolName?: string;
  studentGroupName?: string;
  school?: { id: number; name: string };
  studentGroup?: { id: number; name: string; ageGroup: string; mealPeriod: string };
  items?: MenuItemData[];
  itemCount?: number;
  nutrition?: NutritionComparison;
  alerts?: ComplianceAlert[];
}

export interface MenuItemData {
  id?: number;
  food?: { id: number; name: string } | null;
  recipe?: { id: number; name: string } | null;
  foodId?: number;
  recipeId?: number;
  portionSize: number;
  servings: number;
}

export interface NutritionData {
  energy: number;
  protein: number;
  carbohydrate: number;
  lipid: number;
  fiber: number;
  calcium: number;
  iron: number;
  magnesium: number;
  zinc: number;
  vitamin_a: number;
  vitamin_c: number;
  sodium: number;
  saturated_fat: number;
  added_sugar: number;
}

export interface NutritionComparison {
  nutrition: NutritionData;
  reference: Record<string, number | boolean>;
  adequacy: Record<string, number>;
}

export interface ComplianceAlert {
  type: 'error' | 'warning' | 'info';
  code: string;
  message: string;
}

export interface DashboardStats {
  schools: number;
  menus: number;
  recipes: number;
}

export const AGE_GROUP_LABELS: Record<string, string> = {
  creche_0_5: 'Creche (0 a 5 meses)',
  creche_6_11: 'Creche (6 a 11 meses)',
  creche_1_3: 'Creche (1 a 3 anos)',
  pre_escola: 'Pre-escola (4 a 5 anos)',
  fundamental_6_10: 'Fundamental (6 a 10 anos)',
  fundamental_11_15: 'Fundamental (11 a 15 anos)',
  medio: 'Ensino Medio',
  eja: 'EJA',
};

export const MEAL_PERIOD_LABELS: Record<string, string> = {
  parcial_20: 'Parcial - 20%',
  parcial_30: 'Parcial - 30%',
  integral: 'Integral - 70%',
};

export const MEAL_TYPE_LABELS: Record<string, string> = {
  cafe_manha: 'Cafe da Manha',
  lanche_manha: 'Lanche da Manha',
  almoco: 'Almoco',
  lanche_tarde: 'Lanche da Tarde',
  jantar: 'Jantar',
  ceia: 'Ceia',
};
