import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, RouterModule } from '@angular/router';
import { ApiService } from '../../services/api.service';
import { Recipe, NutritionData } from '../../models/interfaces';

declare let jspdf: { jsPDF: new (...args: unknown[]) => unknown };

@Component({
  selector: 'app-recipe-detail',
  standalone: true,
  imports: [CommonModule, RouterModule],
  template: `
    @if (recipe) {
      <h1>{{ recipe.name }}</h1>
      <p class="info">{{ recipe.portions }} porcoes | Custo por porcao: R$ {{ recipe.cost?.cost_per_portion?.toFixed(2) }}</p>

      <div class="section">
        <h3>Ingredientes</h3>
        <table class="table">
          <thead>
            <tr><th>Alimento</th><th>Bruto (g)</th><th>Liquido (g)</th><th>R$/kg</th></tr>
          </thead>
          <tbody>
            @for (ing of recipe.ingredients; track ing.id) {
              <tr>
                <td>{{ ing.food.name }}</td>
                <td>{{ ing.grossWeight }}</td>
                <td>{{ ing.netWeight }}</td>
                <td>{{ ing.costPerKg?.toFixed(2) || '-' }}</td>
              </tr>
            }
          </tbody>
        </table>
      </div>

      @if (recipe.preparationMethod) {
        <div class="section">
          <h3>Modo de Preparo</h3>
          <p class="prep-method">{{ recipe.preparationMethod }}</p>
        </div>
      }

      <div class="section">
        <h3>Informacao Nutricional por Porcao</h3>
        <div class="nutrition-grid">
          @if (recipe.nutritionPerPortion) {
            <div class="nutrient"><span>Energia</span><strong>{{ recipe.nutritionPerPortion.energy }} kcal</strong></div>
            <div class="nutrient"><span>Proteina</span><strong>{{ recipe.nutritionPerPortion.protein }} g</strong></div>
            <div class="nutrient"><span>Carboidrato</span><strong>{{ recipe.nutritionPerPortion.carbohydrate }} g</strong></div>
            <div class="nutrient"><span>Lipidio</span><strong>{{ recipe.nutritionPerPortion.lipid }} g</strong></div>
            <div class="nutrient"><span>Fibra</span><strong>{{ recipe.nutritionPerPortion.fiber }} g</strong></div>
            <div class="nutrient"><span>Calcio</span><strong>{{ recipe.nutritionPerPortion.calcium }} mg</strong></div>
            <div class="nutrient"><span>Ferro</span><strong>{{ recipe.nutritionPerPortion.iron }} mg</strong></div>
            <div class="nutrient"><span>Magnesio</span><strong>{{ recipe.nutritionPerPortion.magnesium }} mg</strong></div>
            <div class="nutrient"><span>Zinco</span><strong>{{ recipe.nutritionPerPortion.zinc }} mg</strong></div>
            <div class="nutrient"><span>Vit. A</span><strong>{{ recipe.nutritionPerPortion.vitamin_a }} mcg</strong></div>
            <div class="nutrient"><span>Vit. C</span><strong>{{ recipe.nutritionPerPortion.vitamin_c }} mg</strong></div>
            <div class="nutrient"><span>Sodio</span><strong>{{ recipe.nutritionPerPortion.sodium }} mg</strong></div>
          }
        </div>
      </div>

      <div class="actions">
        <button class="btn btn-secondary" (click)="exportPdf()">Exportar PDF</button>
        <a routerLink="/recipes" class="btn btn-link">Voltar</a>
      </div>
    }
  `,
  styles: [`
    h1 { color: #2e7d32; }
    .info { color: #666; }
    .section { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin: 16px 0; }
    h3 { color: #333; margin-top: 0; }
    .table { width: 100%; border-collapse: collapse; }
    .table th { background: #f5f5f5; padding: 8px; text-align: left; font-size: 0.85rem; }
    .table td { padding: 8px; border-top: 1px solid #eee; font-size: 0.9rem; }
    .prep-method { white-space: pre-line; color: #555; }
    .nutrition-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 12px; }
    .nutrient { background: #f9f9f9; padding: 12px; border-radius: 6px; text-align: center; }
    .nutrient span { display: block; font-size: 0.8rem; color: #888; }
    .nutrient strong { font-size: 1.1rem; color: #333; }
    .actions { display: flex; gap: 12px; margin-top: 20px; }
    .btn { padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; text-decoration: none; font-size: 0.9rem; }
    .btn-secondary { background: #1565c0; color: white; }
    .btn-link { color: #2e7d32; }
  `]
})
export class RecipeDetailComponent implements OnInit {
  recipe: Recipe | null = null;

  constructor(private api: ApiService, private route: ActivatedRoute) {}

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    this.api.getRecipe(id).subscribe(r => this.recipe = r);
  }

  exportPdf(): void {
    if (!this.recipe) return;
    import('jspdf').then(({ jsPDF }) => {
      import('jspdf-autotable').then(() => {
        const doc = new jsPDF() as any;
        doc.setFontSize(16);
        doc.text('Ficha Tecnica de Preparo', 14, 20);
        doc.setFontSize(12);
        doc.text(`Receita: ${this.recipe!.name}`, 14, 30);
        doc.text(`Porcoes: ${this.recipe!.portions}`, 14, 38);
        doc.text(`Custo/porcao: R$ ${this.recipe!.cost?.cost_per_portion?.toFixed(2) || '0.00'}`, 14, 46);

        const ingredients = this.recipe!.ingredients?.map(i => [
          i.food.name, `${i.grossWeight}g`, `${i.netWeight}g`, `R$ ${i.costPerKg?.toFixed(2) || '-'}`
        ]) || [];

        doc.autoTable({
          startY: 55,
          head: [['Alimento', 'Peso Bruto', 'Peso Liquido', 'R$/kg']],
          body: ingredients,
        });

        let y = doc.lastAutoTable.finalY + 10;
        if (this.recipe!.preparationMethod) {
          doc.text('Modo de Preparo:', 14, y);
          const lines = doc.splitTextToSize(this.recipe!.preparationMethod, 180);
          doc.text(lines, 14, y + 8);
          y += 8 + lines.length * 6;
        }

        const np = this.recipe!.nutritionPerPortion;
        if (np) {
          y += 5;
          doc.text('Informacao Nutricional por Porcao:', 14, y);
          doc.autoTable({
            startY: y + 5,
            head: [['Nutriente', 'Valor']],
            body: [
              ['Energia', `${np.energy} kcal`],
              ['Proteina', `${np.protein} g`],
              ['Carboidrato', `${np.carbohydrate} g`],
              ['Lipidio', `${np.lipid} g`],
              ['Fibra', `${np.fiber} g`],
              ['Calcio', `${np.calcium} mg`],
              ['Ferro', `${np.iron} mg`],
              ['Sodio', `${np.sodium} mg`],
              ['Vit. A', `${np.vitamin_a} mcg`],
              ['Vit. C', `${np.vitamin_c} mg`],
            ],
          });
        }

        doc.save(`ficha-tecnica-${this.recipe!.name.replace(/\s+/g, '-')}.pdf`);
      });
    });
  }
}
