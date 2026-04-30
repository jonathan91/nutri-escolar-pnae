import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';
import { ApiService } from '../../services/api.service';
import { School } from '../../models/interfaces';

@Component({
  selector: 'app-schools',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule],
  template: `
    <h1>Escolas</h1>

    <div class="form-card">
      <h3>Nova Escola</h3>
      <form (ngSubmit)="createSchool()">
        <div class="form-row">
          <div class="form-group">
            <label>Nome</label>
            <input [(ngModel)]="newSchool.name" name="name" required />
          </div>
          <div class="form-group">
            <label>Cidade</label>
            <input [(ngModel)]="newSchool.city" name="city" />
          </div>
          <div class="form-group">
            <label>UF</label>
            <input [(ngModel)]="newSchool.state" name="state" maxlength="2" />
          </div>
          <div class="form-group">
            <label>Codigo INEP</label>
            <input [(ngModel)]="newSchool.inepCode" name="inepCode" />
          </div>
        </div>
        <button type="submit" class="btn btn-primary">Cadastrar</button>
      </form>
    </div>

    <div class="list">
      @for (school of schools; track school.id) {
        <div class="list-item">
          <div>
            <a [routerLink]="['/schools', school.id]" class="item-title">{{ school.name }}</a>
            <span class="item-info">{{ school.city }}/{{ school.state }} | {{ school.studentGroupCount }} turma(s)</span>
          </div>
          <button class="btn btn-danger btn-sm" (click)="deleteSchool(school.id)">Excluir</button>
        </div>
      }
      @if (schools.length === 0) {
        <p class="empty">Nenhuma escola cadastrada.</p>
      }
    </div>
  `,
  styles: [`
    h1 { color: #2e7d32; }
    .form-card { background: white; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 24px; }
    .form-card h3 { margin-top: 0; color: #333; }
    .form-row { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 12px; }
    .form-group { flex: 1; min-width: 150px; }
    label { display: block; margin-bottom: 4px; font-size: 0.85rem; font-weight: 500; }
    input, select { width: 100%; padding: 8px 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.95rem; box-sizing: border-box; }
    .btn { padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer; font-size: 0.9rem; }
    .btn-primary { background: #2e7d32; color: white; }
    .btn-danger { background: #c62828; color: white; }
    .btn-sm { padding: 4px 10px; font-size: 0.8rem; }
    .list-item { display: flex; justify-content: space-between; align-items: center; padding: 16px; background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); margin-bottom: 8px; }
    .item-title { font-weight: 600; color: #2e7d32; text-decoration: none; }
    .item-info { display: block; font-size: 0.85rem; color: #888; margin-top: 4px; }
    .empty { color: #999; text-align: center; padding: 40px; }
  `]
})
export class SchoolsComponent implements OnInit {
  schools: School[] = [];
  newSchool: Partial<School> = {};

  constructor(private api: ApiService) {}

  ngOnInit(): void { this.loadSchools(); }

  loadSchools(): void {
    this.api.getSchools().subscribe(s => this.schools = s);
  }

  createSchool(): void {
    this.api.createSchool(this.newSchool).subscribe(() => {
      this.newSchool = {};
      this.loadSchools();
    });
  }

  deleteSchool(id: number): void {
    if (confirm('Excluir esta escola?')) {
      this.api.deleteSchool(id).subscribe(() => this.loadSchools());
    }
  }
}
