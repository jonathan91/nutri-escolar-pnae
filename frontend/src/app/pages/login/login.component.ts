import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';
import { AuthService } from '../../services/auth.service';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule],
  template: `
    <div class="auth-container">
      <div class="auth-card">
        <h2>Login - NutriEscolar</h2>
        <p class="subtitle">Sistema de Calculo de Cardapios PNAE</p>

        @if (error) {
          <div class="error-msg">{{ error }}</div>
        }

        <form (ngSubmit)="onSubmit()">
          <div class="form-group">
            <label for="email">Email</label>
            <input id="email" type="email" [(ngModel)]="email" name="email" required placeholder="seu@email.com" />
          </div>
          <div class="form-group">
            <label for="password">Senha</label>
            <input id="password" type="password" [(ngModel)]="password" name="password" required placeholder="Sua senha" />
          </div>
          <button type="submit" class="btn btn-primary" [disabled]="loading">
            {{ loading ? 'Entrando...' : 'Entrar' }}
          </button>
        </form>
        <p class="register-link">Nao tem conta? <a routerLink="/register">Cadastre-se</a></p>
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
    input:focus { outline: none; border-color: #2e7d32; box-shadow: 0 0 0 2px rgba(46,125,50,0.2); }
    .btn { width: 100%; padding: 12px; border: none; border-radius: 6px; font-size: 1rem; cursor: pointer; margin-top: 8px; }
    .btn-primary { background: #2e7d32; color: white; }
    .btn-primary:hover { background: #1b5e20; }
    .btn-primary:disabled { background: #ccc; }
    .error-msg { background: #ffebee; color: #c62828; padding: 10px; border-radius: 6px; margin-bottom: 16px; font-size: 0.9rem; }
    .register-link { text-align: center; margin-top: 16px; font-size: 0.9rem; }
    .register-link a { color: #2e7d32; }
  `]
})
export class LoginComponent {
  email = '';
  password = '';
  loading = false;
  error = '';

  constructor(private authService: AuthService, private router: Router) {}

  onSubmit(): void {
    this.loading = true;
    this.error = '';
    this.authService.login({ username: this.email, password: this.password }).subscribe({
      next: () => {
        this.router.navigate(['/dashboard']);
      },
      error: () => {
        this.error = 'Email ou senha incorretos.';
        this.loading = false;
      }
    });
  }
}
