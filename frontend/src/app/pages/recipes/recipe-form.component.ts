import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';
import { ApiService } from '../../services/api.service';
import { Food } from '../../models/interfaces';

interface IngredientRow {
  foodId: number | null;
  foodName: string;
  grossWeight: number;
  netWeight: number;
  costPerKg: number | null;
}

@Component({
  selector: 'app-recipe-form',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule],
  template: `
    <h1>Nova Receita (Ficha Tecnica)</h1>

    <div class="form-card">
      <div class="form-group">
        <label>Nome da Receita</label>
        <input [(ngModel)]="name" placeholder="Ex: Arroz com feijao" />
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Numero de Porcoes</label>
          <input type="number" [(ngModel)]="portions" min="1" />
        </div>
      </div>
      <div class="form-group">
        <label>Modo de Preparo</label>
        <textarea [(ngModel)]="preparationMethod" rows="4" placeholder="Descreva o modo de preparo..."></textarea>
      </div>

      <h3>Ingredientes</h3>
      <div class="search-group">
        <input [(ngModel)]="foodSearch" (input)="searchFoods()" placeholder="Buscar alimento para adicionar..." />
        @if (searchResults.length > 0) {
          <div class="search-dropdown">
            @for (food of searchResults; track food.id) {
              <div class="dropdown-item" (click)="addIngredient(food)">
                {{ food.name }} <span class="food-energy">({{ food.energy }} kcal/100g)</span>
              </div>
            }
          </div>
        }
      </div>

      @for (ing of ingredients; track $index) {
        <div class="ingredient-row">
          <span class="ing-name">{{ ing.foodName }}</span>
          <div class="ing-field">
            <label>Bruto (g)</label>
            <input type="number" [(ngModel)]="ing.grossWeight" (change)="updateNet($index)" />
          </div>
          <div class="ing-field">
            <label>Liquido (g)</label>
            <input type="number" [(ngModel)]="ing.netWeight" />
          </div>
          <div class="ing-field">
            <label>R$/kg</label>
            <input type="number" [(ngModel)]="ing.costPerKg" step="0.01" />
          </div>
          <button class="btn btn-danger btn-sm" (click)="removeIngredient($index)">X</button>
        </div>
      }

      <div class="actions">
        <button class="btn btn-primary" (click)="save()" [disabled]="saving">{{ saving ? 'Salvando...' : 'Salvar Receita' }}</button>
        <a routerLink="/recipes" class="btn btn-secondary">Cancelar</a>
      </div>
    </div>
  `,
  styles: [`
    h1 { color: #2e7d32; }
    .form-card { background: white; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
    .form-group { margin-bottom: 16px; }
    .form-row { display: flex; gap: 12px; }
    label { display: block; margin-bottom: 4px; font-size: 0.85rem; font-weight: 500; }
    input, select, textarea { width: 100%; padding: 8px 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.95rem; box-sizing: border-box; }
    textarea { resize: vertical; }
    .search-group { position: relative; margin-bottom: 12px; }
    .search-dropdown { position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #ddd; border-radius: 6px; max-height: 200px; overflow-y: auto; z-index: 10; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
    .dropdown-item { padding: 10px; cursor: pointer; border-bottom: 1px solid #f0f0f0; }
    .dropdown-item:hover { background: #f5f5f5; }
    .food-energy { color: #888; font-size: 0.85rem; }
    .ingredient-row { display: flex; align-items: center; gap: 12px; padding: 10px; background: #f9f9f9; border-radius: 6px; margin-bottom: 8px; flex-wrap: wrap; }
    .ing-name { font-weight: 500; min-width: 150px; flex: 2; }
    .ing-field { flex: 1; min-width: 80px; }
    .ing-field label { font-size: 0.75rem; }
    .ing-field input { padding: 6px; }
    .actions { display: flex; gap: 12px; margin-top: 20px; }
    .btn { padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; text-decoration: none; font-size: 0.9rem; }
    .btn-primary { background: #2e7d32; color: white; }
    .btn-secondary { background: #757575; color: white; }
    .btn-danger { background: #c62828; color: white; }
    .btn-sm { padding: 4px 10px; font-size: 0.8rem; }
  `]
})
export class RecipeFormComponent {
  name = '';
  portions = 1;
  preparationMethod = '';
  ingredients: IngredientRow[] = [];
  foodSearch = '';
  searchResults: Food[] = [];
  saving = false;

  constructor(private api: ApiService, private router: Router) {}

  searchFoods(): void {
    if (this.foodSearch.length < 2) {
      this.searchResults = [];
      return;
    }
    this.api.searchFoods(this.foodSearch).subscribe(f => this.searchResults = f);
  }

  addIngredient(food: Food): void {
    this.ingredients.push({
      foodId: food.id,
      foodName: food.name,
      grossWeight: 100,
      netWeight: 100,
      costPerKg: null,
    });
    this.foodSearch = '';
    this.searchResults = [];
  }

  removeIngredient(index: number): void {
    this.ingredients.splice(index, 1);
  }

  updateNet(index: number): void {
    this.ingredients[index].netWeight = this.ingredients[index].grossWeight;
  }

  save(): void {
    this.saving = true;
    const data = {
      name: this.name,
      portions: this.portions,
      preparationMethod: this.preparationMethod,
      ingredients: this.ingredients.map(i => ({
        foodId: i.foodId,
        grossWeight: i.grossWeight,
        netWeight: i.netWeight,
        costPerKg: i.costPerKg,
      })),
    };
    this.api.createRecipe(data).subscribe({
      next: (recipe) => this.router.navigate(['/recipes', recipe.id]),
      error: () => this.saving = false,
    });
  }
}
