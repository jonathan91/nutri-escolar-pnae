import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { ApiService } from '../../services/api.service';
import { Menu, MEAL_TYPE_LABELS } from '../../models/interfaces';

@Component({
  selector: 'app-menus',
  standalone: true,
  imports: [CommonModule, RouterModule],
  template: `
    <div class="header">
      <h1>Cardapios</h1>
      <a routerLink="/menus/new" class="btn btn-primary">Novo Cardapio</a>
    </div>

    @for (menu of menus; track menu.id) {
      <div class="list-item">
        <div>
          <a [routerLink]="['/menus', menu.id]" class="item-title">{{ menu.name }}</a>
          <span class="item-info">{{ menu.menuDate }} | {{ getMealTypeLabel(menu.mealType) }} | {{ menu.schoolName }} | {{ menu.itemCount }} itens</span>
        </div>
        <button class="btn btn-danger btn-sm" (click)="deleteMenu(menu.id)">Excluir</button>
      </div>
    }
    @if (menus.length === 0) {
      <p class="empty">Nenhum cardapio cadastrado. <a routerLink="/menus/new">Crie o primeiro!</a></p>
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
export class MenusComponent implements OnInit {
  menus: Menu[] = [];

  constructor(private api: ApiService) {}
  ngOnInit(): void { this.loadMenus(); }

  loadMenus(): void {
    this.api.getMenus().subscribe(m => this.menus = m);
  }

  deleteMenu(id: number): void {
    if (confirm('Excluir este cardapio?')) {
      this.api.deleteMenu(id).subscribe(() => this.loadMenus());
    }
  }

  getMealTypeLabel(key: string): string { return MEAL_TYPE_LABELS[key] || key; }
}
