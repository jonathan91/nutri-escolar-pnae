import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, Router } from '@angular/router';
import { AuthService } from '../../services/auth.service';

@Component({
  selector: 'app-navbar',
  standalone: true,
  imports: [CommonModule, RouterModule],
  template: `
    <nav class="navbar">
      <div class="navbar-brand">
        <a routerLink="/dashboard" class="brand-link">NutriEscolar PNAE</a>
      </div>
      @if (authService.isAuthenticated()) {
        <div class="navbar-links">
          <a routerLink="/dashboard" routerLinkActive="active">Dashboard</a>
          <a routerLink="/schools" routerLinkActive="active">Escolas</a>
          <a routerLink="/foods" routerLinkActive="active">Alimentos</a>
          <a routerLink="/recipes" routerLinkActive="active">Receitas</a>
          <a routerLink="/menus" routerLinkActive="active">Cardapios</a>
          <button class="btn-logout" (click)="logout()">Sair</button>
        </div>
      }
    </nav>
  `,
  styles: [`
    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: #2e7d32;
      color: white;
      padding: 0 24px;
      height: 56px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    .brand-link {
      color: white;
      text-decoration: none;
      font-size: 1.3rem;
      font-weight: bold;
    }
    .navbar-links {
      display: flex;
      align-items: center;
      gap: 16px;
    }
    .navbar-links a {
      color: rgba(255,255,255,0.85);
      text-decoration: none;
      padding: 6px 12px;
      border-radius: 4px;
      font-size: 0.9rem;
      transition: background 0.2s;
    }
    .navbar-links a:hover, .navbar-links a.active {
      background: rgba(255,255,255,0.15);
      color: white;
    }
    .btn-logout {
      background: rgba(255,255,255,0.15);
      border: 1px solid rgba(255,255,255,0.3);
      color: white;
      padding: 6px 14px;
      border-radius: 4px;
      cursor: pointer;
      font-size: 0.9rem;
    }
    .btn-logout:hover {
      background: rgba(255,255,255,0.25);
    }
  `]
})
export class NavbarComponent {
  constructor(public authService: AuthService, private router: Router) {}

  logout(): void {
    this.authService.logout();
    this.router.navigate(['/login']);
  }
}
