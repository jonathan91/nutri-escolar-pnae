import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';
import { AuthService } from '../../services/auth.service';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule],
  template: `
    <div class="auth-container">
      <div class="auth-card">
        <h2>Cadastro</h2>
        <p class="subtitle">Crie sua conta de nutricionista RT</p>

        @if (error) { <div class="error-msg">{{ error }}</div> }
        @if (success) { <div class="success-msg">{{ success }}</div> }

        <form (ngSubmit)="onSubmit()">
          <div class="form-group">
            <label for="name">Nome completo</label>
            <input id="name" type="text" [(ngModel)]="name" name="name" required />
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input id="email" type="email" [(ngModel)]="email" name="email" required />
          </div>
          <div class="form-group">
            <label for="crn">CRN (opcional)</label>
            <input id="crn" type="text" [(ngModel)]="crn" name="crn" placeholder="CRN-X XXXXX" />
          </div>
          <div class="form-group">
            <label for="password">Senha</label>
            <input id="password" type="password" [(ngModel)]="password" name="password" required />
          </div>
          <button type="submit" class="btn btn-primary" [disabled]="loading">
            {{ loading ? 'Cadastrando...' : 'Cadastrar' }}
          </button>
        </form>
        <p class="login-link">Ja tem conta? <a routerLink="/login">Entrar</a></p>
      </div>
    </div>
  `,
  styles: [`
    .auth-container { display: flex; justify-content: center; align-items: center; min-height: 80vh; }
    .auth-card { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
    h2 { text-align: center; color: #2e7d32; margin-bottom: 4px; }
    .subtitle { text-align: center; color: #666; margin-bottom: 24px; font-size: 0.9rem; }
    .form-group { margin-bottom: 16px; }
    label { display: block; margin-bottom: 4px; font-weight: 500; color: #333; font-size: 0.9rem; }
    input { width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem; box-sizing: border-box; }
    input:focus { outline: none; border-color: #2e7d32; }
    .btn { width: 100%; padding: 12px; border: none; border-radius: 6px; font-size: 1rem; cursor: pointer; margin-top: 8px; }
    .btn-primary { background: #2e7d32; color: white; }
    .btn-primary:hover { background: #1b5e20; }
    .error-msg { background: #ffebee; color: #c62828; padding: 10px; border-radius: 6px; margin-bottom: 16px; font-size: 0.9rem; }
    .success-msg { background: #e8f5e9; color: #2e7d32; padding: 10px; border-radius: 6px; margin-bottom: 16px; font-size: 0.9rem; }
    .login-link { text-align: center; margin-top: 16px; font-size: 0.9rem; }
    .login-link a { color: #2e7d32; }
  `]
})
export class RegisterComponent {
  name = '';
  email = '';
  crn = '';
  password = '';
  loading = false;
  error = '';
  success = '';

  constructor(private authService: AuthService, private router: Router) {}

  onSubmit(): void {
    this.loading = true;
    this.error = '';
    this.authService.register({ name: this.name, email: this.email, password: this.password, crn: this.crn || undefined }).subscribe({
      next: () => {
        this.success = 'Conta criada com sucesso! Redirecionando...';
        setTimeout(() => this.router.navigate(['/login']), 1500);
      },
      error: (err) => {
        this.error = err.error?.error || 'Erro ao cadastrar.';
        this.loading = false;
      }
    });
  }
}
