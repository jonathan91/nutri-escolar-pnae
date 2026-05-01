import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, RouterModule } from '@angular/router';
import { ApiService } from '../../services/api.service';
import { School, StudentGroup, AGE_GROUP_LABELS, MEAL_PERIOD_LABELS } from '../../models/interfaces';

@Component({
  selector: 'app-school-detail',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule],
  template: `
    @if (school) {
      <h1>{{ school.name }}</h1>
      <p class="info">{{ school.city }}/{{ school.state }} | INEP: {{ school.inepCode || 'N/A' }}</p>

      <div class="form-card">
        <h3>Nova Turma</h3>
        <form (ngSubmit)="addGroup()">
          <div class="form-row">
            <div class="form-group">
              <label>Nome da Turma</label>
              <input [(ngModel)]="newGroup.name" name="name" required placeholder="Ex: Turma A" />
            </div>
            <div class="form-group">
              <label>Faixa Etaria</label>
              <select [(ngModel)]="newGroup.ageGroup" name="ageGroup" required>
                @for (ag of ageGroups; track ag.key) {
                  <option [value]="ag.key">{{ ag.label }}</option>
                }
              </select>
            </div>
            <div class="form-group">
              <label>Periodo de Refeicao</label>
              <select [(ngModel)]="newGroup.mealPeriod" name="mealPeriod" required>
                @for (mp of mealPeriods; track mp.key) {
                  <option [value]="mp.key">{{ mp.label }}</option>
                }
              </select>
            </div>
            <div class="form-group">
              <label>Num. Alunos</label>
              <input type="number" [(ngModel)]="newGroup.studentCount" name="studentCount" min="1" />
            </div>
          </div>
          <button type="submit" class="btn btn-primary">Adicionar Turma</button>
        </form>
      </div>

      <h3>Turmas</h3>
      @for (group of school.studentGroups; track group.id) {
        <div class="list-item">
          <div>
            <strong>{{ group.name }}</strong>
            <span class="item-info">{{ getAgeGroupLabel(group.ageGroup) }} | {{ getMealPeriodLabel(group.mealPeriod) }} | {{ group.studentCount }} alunos</span>
          </div>
          <button class="btn btn-danger btn-sm" (click)="deleteGroup(group.id)">Excluir</button>
        </div>
      }
      @if (!school.studentGroups || school.studentGroups.length === 0) {
        <p class="empty">Nenhuma turma cadastrada.</p>
      }

      <a routerLink="/schools" class="back-link">Voltar para Escolas</a>
    }
  `,
  styles: [`
    h1 { color: #2e7d32; }
    .info { color: #666; margin-bottom: 24px; }
    .form-card { background: white; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 24px; }
    .form-card h3 { margin-top: 0; }
    .form-row { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 12px; }
    .form-group { flex: 1; min-width: 150px; }
    label { display: block; margin-bottom: 4px; font-size: 0.85rem; font-weight: 500; }
    input, select { width: 100%; padding: 8px 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.95rem; box-sizing: border-box; }
    .btn { padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer; }
    .btn-primary { background: #2e7d32; color: white; }
    .btn-danger { background: #c62828; color: white; }
    .btn-sm { padding: 4px 10px; font-size: 0.8rem; }
    .list-item { display: flex; justify-content: space-between; align-items: center; padding: 14px; background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); margin-bottom: 8px; }
    .item-info { display: block; font-size: 0.85rem; color: #888; margin-top: 4px; }
    .empty { color: #999; text-align: center; padding: 40px; }
    .back-link { display: inline-block; margin-top: 16px; color: #2e7d32; }
  `]
})
export class SchoolDetailComponent implements OnInit {
  school: School | null = null;
  newGroup: Partial<StudentGroup> = { ageGroup: 'fundamental_6_10', mealPeriod: 'parcial_20', studentCount: 30 };
  ageGroups = Object.entries(AGE_GROUP_LABELS).map(([key, label]) => ({ key, label }));
  mealPeriods = Object.entries(MEAL_PERIOD_LABELS).map(([key, label]) => ({ key, label }));

  constructor(private api: ApiService, private route: ActivatedRoute) {}

  ngOnInit(): void { this.loadSchool(); }

  loadSchool(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    this.api.getSchool(id).subscribe(s => this.school = s);
  }

  addGroup(): void {
    if (!this.school) return;
    this.api.createStudentGroup(this.school.id, this.newGroup).subscribe(() => {
      this.newGroup = { ageGroup: 'fundamental_6_10', mealPeriod: 'parcial_20', studentCount: 30 };
      this.loadSchool();
    });
  }

  deleteGroup(groupId: number): void {
    if (!this.school || !confirm('Excluir esta turma?')) return;
    this.api.deleteStudentGroup(this.school.id, groupId).subscribe(() => this.loadSchool());
  }

  getAgeGroupLabel(key: string): string { return AGE_GROUP_LABELS[key] || key; }
  getMealPeriodLabel(key: string): string { return MEAL_PERIOD_LABELS[key] || key; }
}
