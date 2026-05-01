import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, RouterModule } from '@angular/router';
import { ApiService } from '../../services/api.service';
import { Menu, MEAL_TYPE_LABELS, AGE_GROUP_LABELS, MEAL_PERIOD_LABELS } from '../../models/interfaces';
import { NutritionChartComponent } from '../../components/nutrition-chart/nutrition-chart.component';
import { AlertListComponent } from '../../components/alert-list/alert-list.component';

@Component({
  selector: 'app-menu-detail',
  standalone: true,
  imports: [CommonModule, RouterModule, NutritionChartComponent, AlertListComponent],
  template: `
    @if (menu) {
      <h1>{{ menu.name }}</h1>
      <p class="info">
        {{ menu.menuDate }} | {{ getMealTypeLabel(menu.mealType) }} |
        {{ menu.school?.name }} | {{ menu.studentGroup?.name }}
        ({{ getAgeGroupLabel(menu.studentGroup?.ageGroup || '') }} - {{ getMealPeriodLabel(menu.studentGroup?.mealPeriod || '') }})
      </p>

      <app-alert-list [alerts]="menu.alerts || []" />

      <div class="section">
        <h3>Itens do Cardapio</h3>
        <table class="table">
          <thead>
            <tr><th>Item</th><th>Tipo</th><th>Porcao (g)</th><th>Servicos</th></tr>
          </thead>
          <tbody>
            @for (item of menu.items; track item.id) {
              <tr>
                <td>{{ item.food?.name || item.recipe?.name }}</td>
                <td>{{ item.food ? 'Alimento' : 'Receita' }}</td>
                <td>{{ item.portionSize }}</td>
                <td>{{ item.servings }}</td>
              </tr>
            }
          </tbody>
        </table>
      </div>

      @if (menu.nutrition) {
        <app-nutrition-chart [comparison]="menu.nutrition" />

        <div class="section">
          <h3>Detalhes Nutricionais</h3>
          <div class="nutrition-grid">
            <div class="nutrient"><span>Energia</span><strong>{{ menu.nutrition.nutrition.energy }} kcal</strong><small>Meta: {{ menu.nutrition.reference['energy'] }} kcal</small></div>
            <div class="nutrient"><span>Proteina</span><strong>{{ menu.nutrition.nutrition.protein }} g</strong><small>Adequacao: {{ menu.nutrition.adequacy['protein'] }}%</small></div>
            <div class="nutrient"><span>Fibra</span><strong>{{ menu.nutrition.nutrition.fiber }} g</strong><small>Adequacao: {{ menu.nutrition.adequacy['fiber'] }}%</small></div>
            <div class="nutrient"><span>Calcio</span><strong>{{ menu.nutrition.nutrition.calcium }} mg</strong><small>Adequacao: {{ menu.nutrition.adequacy['calcium'] }}%</small></div>
            <div class="nutrient"><span>Ferro</span><strong>{{ menu.nutrition.nutrition.iron }} mg</strong><small>Adequacao: {{ menu.nutrition.adequacy['iron'] }}%</small></div>
            <div class="nutrient"><span>Vit. A</span><strong>{{ menu.nutrition.nutrition.vitamin_a }} mcg</strong><small>Adequacao: {{ menu.nutrition.adequacy['vitamin_a'] }}%</small></div>
            <div class="nutrient"><span>Vit. C</span><strong>{{ menu.nutrition.nutrition.vitamin_c }} mg</strong><small>Adequacao: {{ menu.nutrition.adequacy['vitamin_c'] }}%</small></div>
            <div class="nutrient"><span>Sodio</span><strong>{{ menu.nutrition.nutrition.sodium }} mg</strong><small>Max: {{ menu.nutrition.reference['sodium_max'] }} mg</small></div>
          </div>
        </div>
      }

      <div class="actions">
        <button class="btn btn-secondary" (click)="exportPdf()">Exportar PDF</button>
        <a routerLink="/menus" class="btn btn-link">Voltar</a>
      </div>
    }
  `,
  styles: [`
    h1 { color: #2e7d32; }
    .info { color: #666; margin-bottom: 16px; }
    .section { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin: 16px 0; }
    h3 { color: #333; margin-top: 0; }
    .table { width: 100%; border-collapse: collapse; }
    .table th { background: #f5f5f5; padding: 8px; text-align: left; font-size: 0.85rem; }
    .table td { padding: 8px; border-top: 1px solid #eee; }
    .nutrition-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 12px; }
    .nutrient { background: #f9f9f9; padding: 12px; border-radius: 6px; text-align: center; }
    .nutrient span { display: block; font-size: 0.8rem; color: #888; }
    .nutrient strong { font-size: 1.1rem; color: #333; display: block; }
    .nutrient small { font-size: 0.75rem; color: #aaa; }
    .actions { display: flex; gap: 12px; margin-top: 20px; }
    .btn { padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; text-decoration: none; font-size: 0.9rem; }
    .btn-secondary { background: #1565c0; color: white; }
    .btn-link { color: #2e7d32; }
  `]
})
export class MenuDetailComponent implements OnInit {
  menu: Menu | null = null;

  constructor(private api: ApiService, private route: ActivatedRoute) {}

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    this.api.getMenu(id).subscribe(m => this.menu = m);
  }

  getMealTypeLabel(key: string): string { return MEAL_TYPE_LABELS[key] || key; }
  getAgeGroupLabel(key: string): string { return AGE_GROUP_LABELS[key] || key; }
  getMealPeriodLabel(key: string): string { return MEAL_PERIOD_LABELS[key] || key; }

  exportPdf(): void {
    if (!this.menu) return;
    import('jspdf').then(({ jsPDF }) => {
      import('jspdf-autotable').then(() => {
        const doc = new jsPDF() as any;
        doc.setFontSize(16);
        doc.text('Cardapio Escolar - PNAE', 14, 20);
        doc.setFontSize(11);
        doc.text(`Cardapio: ${this.menu!.name}`, 14, 30);
        doc.text(`Data: ${this.menu!.menuDate} | Refeicao: ${this.getMealTypeLabel(this.menu!.mealType)}`, 14, 37);
        doc.text(`Escola: ${this.menu!.school?.name} | Turma: ${this.menu!.studentGroup?.name}`, 14, 44);

        const items = this.menu!.items?.map(i => [
          i.food?.name || i.recipe?.name || '', i.food ? 'Alimento' : 'Receita',
          `${i.portionSize}g`, String(i.servings),
        ]) || [];

        doc.autoTable({
          startY: 52,
          head: [['Item', 'Tipo', 'Porcao', 'Servicos']],
          body: items,
        });

        let y = doc.lastAutoTable.finalY + 10;
        const n = this.menu!.nutrition?.nutrition;
        if (n) {
          doc.text('Resumo Nutricional:', 14, y);
          doc.autoTable({
            startY: y + 5,
            head: [['Nutriente', 'Valor', 'Adequacao']],
            body: [
              ['Energia', `${n.energy} kcal`, `${this.menu!.nutrition!.adequacy['energy']}%`],
              ['Proteina', `${n.protein} g`, `${this.menu!.nutrition!.adequacy['protein']}%`],
              ['Fibra', `${n.fiber} g`, `${this.menu!.nutrition!.adequacy['fiber']}%`],
              ['Calcio', `${n.calcium} mg`, `${this.menu!.nutrition!.adequacy['calcium']}%`],
              ['Ferro', `${n.iron} mg`, `${this.menu!.nutrition!.adequacy['iron']}%`],
              ['Vit. A', `${n.vitamin_a} mcg`, `${this.menu!.nutrition!.adequacy['vitamin_a']}%`],
              ['Vit. C', `${n.vitamin_c} mg`, `${this.menu!.nutrition!.adequacy['vitamin_c']}%`],
              ['Sodio', `${n.sodium} mg`, '-'],
            ],
          });
        }

        y = doc.lastAutoTable.finalY + 10;
        if (this.menu!.alerts && this.menu!.alerts.length > 0) {
          doc.text('Alertas de Conformidade:', 14, y);
          const alertData = this.menu!.alerts.map(a => [a.type.toUpperCase(), a.message]);
          doc.autoTable({ startY: y + 5, head: [['Tipo', 'Mensagem']], body: alertData });
        }

        doc.save(`cardapio-${this.menu!.name.replace(/\s+/g, '-')}.pdf`);
      });
    });
  }
}
