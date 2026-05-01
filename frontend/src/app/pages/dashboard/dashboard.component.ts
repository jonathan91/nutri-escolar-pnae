import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { BaseChartDirective } from 'ng2-charts';
import { ChartData } from 'chart.js';
import { ApiService } from '../../services/api.service';
import { AuthService } from '../../services/auth.service';
import { NutritionChartComponent } from '../../components/nutrition-chart/nutrition-chart.component';
import { AlertListComponent } from '../../components/alert-list/alert-list.component';
import { DashboardStats, Menu, NutritionComparison, ComplianceAlert } from '../../models/interfaces';

@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [CommonModule, RouterModule, FormsModule, BaseChartDirective, NutritionChartComponent, AlertListComponent],
  template: `
    <div class="dashboard">
      <h1>Dashboard</h1>
      <p class="welcome">Bem-vindo(a), {{ userName }}!</p>

      <div class="stats-grid">
        <div class="stat-card">
          <h3>{{ stats?.schools ?? 0 }}</h3>
          <p>Escolas</p>
          <a routerLink="/schools">Gerenciar</a>
        </div>
        <div class="stat-card">
          <h3>{{ stats?.recipes ?? 0 }}</h3>
          <p>Receitas</p>
          <a routerLink="/recipes">Gerenciar</a>
        </div>
        <div class="stat-card">
          <h3>{{ stats?.menus ?? 0 }}</h3>
          <p>Cardapios</p>
          <a routerLink="/menus">Gerenciar</a>
        </div>
      </div>

      @if (menus.length > 0) {
        <div class="analysis-section">
          <h2>Analise Nutricional</h2>
          <div class="form-group">
            <label>Selecione um cardapio para analisar:</label>
            <select [(ngModel)]="selectedMenuId" (change)="loadAnalysis()">
              <option [ngValue]="null">-- Selecione --</option>
              @for (menu of menus; track menu.id) {
                <option [ngValue]="menu.id">{{ menu.name }} ({{ menu.menuDate }})</option>
              }
            </select>
          </div>

          @if (comparison) {
            <app-nutrition-chart [comparison]="comparison" />
            <app-alert-list [alerts]="alerts" />
          }
        </div>
      }
    </div>
  `,
  styles: [`
    .dashboard h1 { color: #2e7d32; }
    .welcome { color: #666; margin-bottom: 24px; }
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 32px; }
    .stat-card { background: white; padding: 24px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); text-align: center; }
    .stat-card h3 { font-size: 2.5rem; color: #2e7d32; margin: 0; }
    .stat-card p { color: #666; margin: 4px 0 12px; }
    .stat-card a { color: #2e7d32; font-size: 0.9rem; }
    .analysis-section { margin-top: 24px; }
    .analysis-section h2 { color: #333; margin-bottom: 16px; }
    .form-group { margin-bottom: 16px; }
    label { display: block; margin-bottom: 6px; font-weight: 500; }
    select { width: 100%; max-width: 400px; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem; }
  `]
})
export class DashboardComponent implements OnInit {
  stats: DashboardStats | null = null;
  menus: Menu[] = [];
  selectedMenuId: number | null = null;
  comparison: NutritionComparison | null = null;
  alerts: ComplianceAlert[] = [];
  userName = '';

  constructor(private api: ApiService, private auth: AuthService) {}

  ngOnInit(): void {
    this.auth.currentUser$.subscribe(u => { if (u) this.userName = u.name; });
    this.api.getDashboardStats().subscribe(s => this.stats = s);
    this.api.getMenus().subscribe(m => this.menus = m);
  }

  loadAnalysis(): void {
    if (!this.selectedMenuId) {
      this.comparison = null;
      this.alerts = [];
      return;
    }
    this.api.getMenuAnalysis(this.selectedMenuId).subscribe(data => {
      this.comparison = data.analysis;
      this.alerts = data.alerts;
    });
  }
}
