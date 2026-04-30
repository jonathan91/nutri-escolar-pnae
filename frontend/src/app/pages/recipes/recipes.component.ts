import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { ApiService } from '../../services/api.service';
import { Recipe } from '../../models/interfaces';

@Component({
  selector: 'app-recipes',
  standalone: true,
  imports: [CommonModule, RouterModule],
  template: `
    <div class="header">
      <h1>Fichas Tecnicas (Receitas)</h1>
      <a routerLink="/recipes/new" class="btn btn-primary">Nova Receita</a>
    </div>

    @for (recipe of recipes; track recipe.id) {
      <div class="list-item">
        <div>
          <a [routerLink]="['/recipes', recipe.id]" class="item-title">{{ recipe.name }}</a>
          <span class="item-info">{{ recipe.ingredientCount }} ingredientes | {{ recipe.portions }} porcoes | R$ {{ recipe.costPerPortion?.toFixed(2) || '0.00' }}/porcao</span>
        </div>
        <button class="btn btn-danger btn-sm" (click)="deleteRecipe(recipe.id)">Excluir</button>
      </div>
    }
    @if (recipes.length === 0) {
      <p class="empty">Nenhuma receita cadastrada. <a routerLink="/recipes/new">Crie a primeira!</a></p>
    }
  `,
  styles: [`
    .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
    h1 { color: #2e7d32; }
    .btn { padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; text-decoration: none; font-size: 0.9rem; }
    .btn-primary { background: #2e7d32; color: white; }
    .btn-danger { background: #c62828; color: white; }
    .btn-sm { padding: 4px 10px; font-size: 0.8rem; }
    .list-item { display: flex; justify-content: space-between; align-items: center; padding: 16px; background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); margin-bottom: 8px; }
    .item-title { font-weight: 600; color: #2e7d32; text-decoration: none; font-size: 1.05rem; }
    .item-info { display: block; font-size: 0.85rem; color: #888; margin-top: 4px; }
    .empty { color: #999; text-align: center; padding: 40px; }
    .empty a { color: #2e7d32; }
  `]
})
export class RecipesComponent implements OnInit {
  recipes: Recipe[] = [];

  constructor(private api: ApiService) {}
  ngOnInit(): void { this.loadRecipes(); }

  loadRecipes(): void {
    this.api.getRecipes().subscribe(r => this.recipes = r);
  }

  deleteRecipe(id: number): void {
    if (confirm('Excluir esta receita?')) {
      this.api.deleteRecipe(id).subscribe(() => this.loadRecipes());
    }
  }
}
