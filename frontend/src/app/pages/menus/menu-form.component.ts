import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';
import { ApiService } from '../../services/api.service';
import { School, StudentGroup, Food, Recipe, MEAL_TYPE_LABELS } from '../../models/interfaces';

interface MenuItemRow {
  type: 'food' | 'recipe';
  foodId?: number;
  recipeId?: number;
  label: string;
  portionSize: number;
  servings: number;
}

@Component({
  selector: 'app-menu-form',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule],
  template: `
    <h1>Novo Cardapio</h1>

    <div class="form-card">
      <div class="form-row">
        <div class="form-group">
          <label>Nome do Cardapio</label>
          <input [(ngModel)]="name" placeholder="Ex: Cardapio Semana 1" />
        </div>
        <div class="form-group">
          <label>Data</label>
          <input type="date" [(ngModel)]="menuDate" />
        </div>
        <div class="form-group">
          <label>Tipo de Refeicao</label>
          <select [(ngModel)]="mealType">
            @for (mt of mealTypes; track mt.key) {
              <option [value]="mt.key">{{ mt.label }}</option>
            }
          </select>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Escola</label>
          <select [(ngModel)]="schoolId" (change)="loadStudentGroups()">
            <option [ngValue]="null">-- Selecione --</option>
            @for (school of schools; track school.id) {
              <option [ngValue]="school.id">{{ school.name }}</option>
            }
          </select>
        </div>
        <div class="form-group">
          <label>Turma</label>
          <select [(ngModel)]="studentGroupId">
            <option [ngValue]="null">-- Selecione --</option>
            @for (group of studentGroups; track group.id) {
              <option [ngValue]="group.id">{{ group.name }}</option>
            }
          </select>
        </div>
      </div>

      <h3>Itens do Cardapio</h3>

      <div class="add-item-row">
        <div class="form-group" style="flex:2">
          <label>Buscar Alimento</label>
          <input [(ngModel)]="foodSearch" (input)="searchFoods()" placeholder="Buscar alimento..." />
          @if (foodResults.length > 0) {
            <div class="search-dropdown">
              @for (food of foodResults; track food.id) {
                <div class="dropdown-item" (click)="addFoodItem(food)">{{ food.name }}</div>
              }
            </div>
          }
        </div>
        <div class="form-group" style="flex:2">
          <label>Ou selecione uma Receita</label>
          <select (change)="addRecipeItem($event)">
            <option value="">-- Receita --</option>
            @for (recipe of recipes; track recipe.id) {
              <option [value]="recipe.id">{{ recipe.name }}</option>
            }
          </select>
        </div>
      </div>

      @for (item of items; track $index) {
        <div class="item-row">
          <span class="item-label">[{{ item.type === 'food' ? 'Alimento' : 'Receita' }}] {{ item.label }}</span>
          <div class="item-fields">
            <div>
              <label>Porcao (g)</label>
              <input type="number" [(ngModel)]="item.portionSize" />
            </div>
            <div>
              <label>Servicos</label>
              <input type="number" [(ngModel)]="item.servings" min="1" />
            </div>
          </div>
          <button class="btn btn-danger btn-sm" (click)="removeItem($index)">X</button>
        </div>
      }

      <div class="actions">
        <button class="btn btn-primary" (click)="save()" [disabled]="saving">{{ saving ? 'Salvando...' : 'Salvar Cardapio' }}</button>
        <a routerLink="/menus" class="btn btn-secondary">Cancelar</a>
      </div>
    </div>
  `,
  styles: [`
    h1 { color: #2e7d32; }
    .form-card { background: white; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
    .form-row { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 16px; }
    .form-group { flex: 1; min-width: 150px; position: relative; }
    label { display: block; margin-bottom: 4px; font-size: 0.85rem; font-weight: 500; }
    input, select, textarea { width: 100%; padding: 8px 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.95rem; box-sizing: border-box; }
    .add-item-row { display: flex; gap: 16px; margin-bottom: 12px; flex-wrap: wrap; }
    .search-dropdown { position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #ddd; border-radius: 6px; max-height: 200px; overflow-y: auto; z-index: 10; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
    .dropdown-item { padding: 10px; cursor: pointer; }
    .dropdown-item:hover { background: #f5f5f5; }
    .item-row { display: flex; align-items: center; gap: 12px; padding: 10px; background: #f9f9f9; border-radius: 6px; margin-bottom: 8px; flex-wrap: wrap; }
    .item-label { font-weight: 500; flex: 2; min-width: 150px; }
    .item-fields { display: flex; gap: 8px; }
    .item-fields div { min-width: 80px; }
    .item-fields label { font-size: 0.75rem; }
    .item-fields input { padding: 6px; }
    .actions { display: flex; gap: 12px; margin-top: 20px; }
    .btn { padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; text-decoration: none; font-size: 0.9rem; }
    .btn-primary { background: #2e7d32; color: white; }
    .btn-secondary { background: #757575; color: white; }
    .btn-danger { background: #c62828; color: white; }
    .btn-sm { padding: 4px 10px; font-size: 0.8rem; }
  `]
})
export class MenuFormComponent implements OnInit {
  name = '';
  menuDate = new Date().toISOString().split('T')[0];
  mealType = 'almoco';
  schoolId: number | null = null;
  studentGroupId: number | null = null;
  schools: School[] = [];
  studentGroups: StudentGroup[] = [];
  recipes: Recipe[] = [];
  items: MenuItemRow[] = [];
  foodSearch = '';
  foodResults: Food[] = [];
  saving = false;

  mealTypes = Object.entries(MEAL_TYPE_LABELS).map(([key, label]) => ({ key, label }));

  constructor(private api: ApiService, private router: Router) {}

  ngOnInit(): void {
    this.api.getSchools().subscribe(s => this.schools = s);
    this.api.getRecipes().subscribe(r => this.recipes = r);
  }

  loadStudentGroups(): void {
    if (!this.schoolId) return;
    this.api.getSchool(this.schoolId).subscribe(s => this.studentGroups = s.studentGroups || []);
  }

  searchFoods(): void {
    if (this.foodSearch.length < 2) { this.foodResults = []; return; }
    this.api.searchFoods(this.foodSearch).subscribe(f => this.foodResults = f);
  }

  addFoodItem(food: Food): void {
    this.items.push({ type: 'food', foodId: food.id, label: food.name, portionSize: 100, servings: 1 });
    this.foodSearch = '';
    this.foodResults = [];
  }

  addRecipeItem(event: Event): void {
    const select = event.target as HTMLSelectElement;
    const recipeId = Number(select.value);
    if (!recipeId) return;
    const recipe = this.recipes.find(r => r.id === recipeId);
    if (recipe) {
      this.items.push({ type: 'recipe', recipeId: recipe.id, label: recipe.name, portionSize: 100, servings: 1 });
    }
    select.value = '';
  }

  removeItem(index: number): void { this.items.splice(index, 1); }

  save(): void {
    this.saving = true;
    const data = {
      name: this.name,
      menuDate: this.menuDate,
      mealType: this.mealType,
      schoolId: this.schoolId,
      studentGroupId: this.studentGroupId,
      items: this.items.map(i => ({
        foodId: i.foodId || null,
        recipeId: i.recipeId || null,
        portionSize: i.portionSize,
        servings: i.servings,
      })),
    };
    this.api.createMenu(data).subscribe({
      next: (menu) => this.router.navigate(['/menus', menu.id]),
      error: () => this.saving = false,
    });
  }
}
