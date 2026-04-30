import { Routes } from '@angular/router';
import { authGuard } from './guards/auth.guard';

export const routes: Routes = [
  { path: 'login', loadComponent: () => import('./pages/login/login.component').then(m => m.LoginComponent) },
  { path: 'register', loadComponent: () => import('./pages/register/register.component').then(m => m.RegisterComponent) },
  { path: 'dashboard', loadComponent: () => import('./pages/dashboard/dashboard.component').then(m => m.DashboardComponent), canActivate: [authGuard] },
  { path: 'schools', loadComponent: () => import('./pages/schools/schools.component').then(m => m.SchoolsComponent), canActivate: [authGuard] },
  { path: 'schools/:id', loadComponent: () => import('./pages/schools/school-detail.component').then(m => m.SchoolDetailComponent), canActivate: [authGuard] },
  { path: 'foods', loadComponent: () => import('./pages/foods/foods.component').then(m => m.FoodsComponent), canActivate: [authGuard] },
  { path: 'recipes', loadComponent: () => import('./pages/recipes/recipes.component').then(m => m.RecipesComponent), canActivate: [authGuard] },
  { path: 'recipes/new', loadComponent: () => import('./pages/recipes/recipe-form.component').then(m => m.RecipeFormComponent), canActivate: [authGuard] },
  { path: 'recipes/:id', loadComponent: () => import('./pages/recipes/recipe-detail.component').then(m => m.RecipeDetailComponent), canActivate: [authGuard] },
  { path: 'menus', loadComponent: () => import('./pages/menus/menus.component').then(m => m.MenusComponent), canActivate: [authGuard] },
  { path: 'menus/new', loadComponent: () => import('./pages/menus/menu-form.component').then(m => m.MenuFormComponent), canActivate: [authGuard] },
  { path: 'menus/:id', loadComponent: () => import('./pages/menus/menu-detail.component').then(m => m.MenuDetailComponent), canActivate: [authGuard] },
  { path: '', redirectTo: '/dashboard', pathMatch: 'full' },
  { path: '**', redirectTo: '/dashboard' },
];
