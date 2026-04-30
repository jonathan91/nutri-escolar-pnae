import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ApiService } from '../../services/api.service';
import { Food } from '../../models/interfaces';

@Component({
  selector: 'app-foods',
  standalone: true,
  imports: [CommonModule, FormsModule],
  template: `
    <h1>Banco de Alimentos (TACO)</h1>

    <div class="search-bar">
      <input type="text" [(ngModel)]="searchQuery" (input)="search()" placeholder="Buscar alimento..." class="search-input" />
      <select [(ngModel)]="selectedCategory" (change)="search()">
        <option value="">Todas as categorias</option>
        @for (cat of categories; track cat) {
          <option [value]="cat">{{ cat }}</option>
        }
      </select>
      <button class="btn btn-secondary" (click)="showSeasonal()">Alimentos da Epoca</button>
    </div>

    @if (showingSeasonalLabel) {
      <p class="season-label">Alimentos da epoca (mes {{ currentMonth }})</p>
    }

    <div class="food-table-wrapper">
      <table class="food-table">
        <thead>
          <tr>
            <th>Alimento</th>
            <th>Categoria</th>
            <th>Energia</th>
            <th>Prot.</th>
            <th>Carb.</th>
            <th>Lip.</th>
            <th>Fibra</th>
            <th>Ca</th>
            <th>Fe</th>
            <th>Na</th>
            <th>Vit.A</th>
            <th>Vit.C</th>
            <th>Flags</th>
          </tr>
        </thead>
        <tbody>
          @for (food of foods; track food.id) {
            <tr>
              <td><strong>{{ food.name }}</strong></td>
              <td>{{ food.category }}</td>
              <td>{{ food.energy }}</td>
              <td>{{ food.protein }}</td>
              <td>{{ food.carbohydrate }}</td>
              <td>{{ food.lipid }}</td>
              <td>{{ food.fiber }}</td>
              <td>{{ food.calcium }}</td>
              <td>{{ food.iron }}</td>
              <td>{{ food.sodium }}</td>
              <td>{{ food.vitaminA }}</td>
              <td>{{ food.vitaminC }}</td>
              <td>
                @if (food.containsGluten) { <span class="tag gluten">G</span> }
                @if (food.containsLactose) { <span class="tag lactose">L</span> }
                @if (food.ultraProcessed) { <span class="tag ultra">UP</span> }
              </td>
            </tr>
          }
        </tbody>
      </table>
    </div>
    @if (foods.length === 0) {
      <p class="empty">Nenhum alimento encontrado.</p>
    }
  `,
  styles: [`
    h1 { color: #2e7d32; }
    .search-bar { display: flex; gap: 12px; margin-bottom: 16px; flex-wrap: wrap; }
    .search-input { flex: 1; min-width: 200px; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem; }
    select { padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.95rem; }
    .btn { padding: 10px 16px; border: none; border-radius: 6px; cursor: pointer; }
    .btn-secondary { background: #ff9800; color: white; }
    .season-label { color: #ff9800; font-weight: 500; }
    .food-table-wrapper { overflow-x: auto; }
    .food-table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
    .food-table th { background: #f5f5f5; padding: 10px 8px; font-size: 0.8rem; text-align: left; white-space: nowrap; }
    .food-table td { padding: 8px; font-size: 0.85rem; border-top: 1px solid #eee; }
    .tag { display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 0.7rem; font-weight: bold; margin-right: 2px; }
    .tag.gluten { background: #fff3e0; color: #e65100; }
    .tag.lactose { background: #e3f2fd; color: #0d47a1; }
    .tag.ultra { background: #ffebee; color: #c62828; }
    .empty { color: #999; text-align: center; padding: 40px; }
  `]
})
export class FoodsComponent implements OnInit {
  foods: Food[] = [];
  categories: string[] = [];
  searchQuery = '';
  selectedCategory = '';
  currentMonth = new Date().getMonth() + 1;
  showingSeasonalLabel = false;

  constructor(private api: ApiService) {}

  ngOnInit(): void {
    this.api.getFoodCategories().subscribe(c => this.categories = c);
    this.search();
  }

  search(): void {
    this.showingSeasonalLabel = false;
    this.api.searchFoods(this.searchQuery, this.selectedCategory || undefined).subscribe(f => this.foods = f);
  }

  showSeasonal(): void {
    this.showingSeasonalLabel = true;
    this.api.getSeasonalFoods(this.currentMonth).subscribe(f => this.foods = f);
  }
}
