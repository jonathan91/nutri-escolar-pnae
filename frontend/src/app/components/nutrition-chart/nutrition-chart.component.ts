import { Component, Input, OnChanges } from '@angular/core';
import { CommonModule } from '@angular/common';
import { BaseChartDirective } from 'ng2-charts';
import { ChartConfiguration, ChartData } from 'chart.js';
import { NutritionComparison } from '../../models/interfaces';

@Component({
  selector: 'app-nutrition-chart',
  standalone: true,
  imports: [CommonModule, BaseChartDirective],
  template: `
    @if (comparison) {
      <div class="chart-container">
        <h4>Adequacao Nutricional vs Metas FNDE</h4>
        <div class="chart-wrapper">
          <canvas baseChart
            [data]="chartData"
            [options]="chartOptions"
            [type]="'bar'">
          </canvas>
        </div>
        <div class="legend">
          <span class="legend-item"><span class="dot achieved"></span> Atingido</span>
          <span class="legend-item"><span class="dot target"></span> Meta FNDE</span>
        </div>
      </div>
    }
  `,
  styles: [`
    .chart-container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
    .chart-wrapper { max-height: 400px; position: relative; }
    h4 { text-align: center; margin-bottom: 16px; color: #333; }
    .legend { display: flex; justify-content: center; gap: 24px; margin-top: 12px; }
    .legend-item { display: flex; align-items: center; gap: 6px; font-size: 0.85rem; }
    .dot { width: 12px; height: 12px; border-radius: 50%; }
    .dot.achieved { background: #4caf50; }
    .dot.target { background: #ff9800; }
  `]
})
export class NutritionChartComponent implements OnChanges {
  @Input() comparison: NutritionComparison | null = null;

  chartData: ChartData<'bar'> = { labels: [], datasets: [] };
  chartOptions: ChartConfiguration<'bar'>['options'] = {
    responsive: true,
    maintainAspectRatio: true,
    plugins: {
      legend: { display: false },
    },
    scales: {
      y: { beginAtZero: true, title: { display: true, text: '% Adequacao' } },
    },
  };

  ngOnChanges(): void {
    if (!this.comparison) return;

    const adequacy = this.comparison.adequacy;
    const labels = [
      'Energia', 'Proteina', 'Fibra', 'Calcio', 'Ferro',
      'Magnesio', 'Zinco', 'Vit. A', 'Vit. C',
    ];
    const values = [
      adequacy['energy'], adequacy['protein'], adequacy['fiber'],
      adequacy['calcium'], adequacy['iron'], adequacy['magnesium'],
      adequacy['zinc'], adequacy['vitamin_a'], adequacy['vitamin_c'],
    ];

    this.chartData = {
      labels,
      datasets: [
        {
          label: 'Atingido (%)',
          data: values,
          backgroundColor: values.map(v =>
            v >= 90 && v <= 110 ? '#4caf50' : v < 90 ? '#f44336' : '#ff9800'
          ),
          borderRadius: 4,
        },
        {
          label: 'Meta (100%)',
          data: labels.map(() => 100),
          backgroundColor: 'rgba(255, 152, 0, 0.2)',
          borderColor: '#ff9800',
          borderWidth: 1,
          borderRadius: 4,
        },
      ],
    };
  }
}
