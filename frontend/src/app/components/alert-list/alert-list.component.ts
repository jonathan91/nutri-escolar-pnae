import { Component, Input } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ComplianceAlert } from '../../models/interfaces';

@Component({
  selector: 'app-alert-list',
  standalone: true,
  imports: [CommonModule],
  template: `
    @if (alerts && alerts.length > 0) {
      <div class="alerts">
        <h4>Alertas de Conformidade PNAE</h4>
        @for (alert of alerts; track alert.code) {
          <div class="alert" [class]="'alert-' + alert.type">
            <strong>{{ getAlertIcon(alert.type) }} {{ alert.code }}</strong>
            <p>{{ alert.message }}</p>
          </div>
        }
      </div>
    }
  `,
  styles: [`
    .alerts { margin: 16px 0; }
    .alerts h4 { margin-bottom: 8px; color: #333; }
    .alert { padding: 12px 16px; border-radius: 6px; margin-bottom: 8px; border-left: 4px solid; }
    .alert-error { background: #ffebee; border-color: #c62828; color: #b71c1c; }
    .alert-warning { background: #fff8e1; border-color: #f9a825; color: #e65100; }
    .alert-info { background: #e3f2fd; border-color: #1565c0; color: #0d47a1; }
    .alert p { margin: 4px 0 0; font-size: 0.9rem; }
  `]
})
export class AlertListComponent {
  @Input() alerts: ComplianceAlert[] = [];

  getAlertIcon(type: string): string {
    switch (type) {
      case 'error': return '[ERRO]';
      case 'warning': return '[AVISO]';
      case 'info': return '[INFO]';
      default: return '';
    }
  }
}
