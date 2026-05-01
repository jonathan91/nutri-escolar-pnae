import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import {
  School, StudentGroup, Food, Recipe, Menu,
  DashboardStats, NutritionComparison, ComplianceAlert,
} from '../models/interfaces';

@Injectable({ providedIn: 'root' })
export class ApiService {
  private readonly apiUrl = '/api';

  constructor(private http: HttpClient) {}

  // Schools
  getSchools(): Observable<School[]> {
    return this.http.get<School[]>(`${this.apiUrl}/schools`);
  }

  getSchool(id: number): Observable<School> {
    return this.http.get<School>(`${this.apiUrl}/schools/${id}`);
  }

  createSchool(data: Partial<School>): Observable<School> {
    return this.http.post<School>(`${this.apiUrl}/schools`, data);
  }

  updateSchool(id: number, data: Partial<School>): Observable<School> {
    return this.http.put<School>(`${this.apiUrl}/schools/${id}`, data);
  }

  deleteSchool(id: number): Observable<void> {
    return this.http.delete<void>(`${this.apiUrl}/schools/${id}`);
  }

  createStudentGroup(schoolId: number, data: Partial<StudentGroup>): Observable<StudentGroup> {
    return this.http.post<StudentGroup>(`${this.apiUrl}/schools/${schoolId}/student-groups`, data);
  }

  deleteStudentGroup(schoolId: number, groupId: number): Observable<void> {
    return this.http.delete<void>(`${this.apiUrl}/schools/${schoolId}/student-groups/${groupId}`);
  }

  // Foods
  searchFoods(query: string, category?: string, month?: number): Observable<Food[]> {
    let params = new HttpParams().set('q', query);
    if (category) params = params.set('category', category);
    if (month) params = params.set('month', month.toString());
    return this.http.get<Food[]>(`${this.apiUrl}/foods`, { params });
  }

  getFoodCategories(): Observable<string[]> {
    return this.http.get<string[]>(`${this.apiUrl}/foods/categories`);
  }

  getSeasonalFoods(month?: number): Observable<Food[]> {
    let params = new HttpParams();
    if (month) params = params.set('month', month.toString());
    return this.http.get<Food[]>(`${this.apiUrl}/foods/seasonal`, { params });
  }

  // Recipes
  getRecipes(): Observable<Recipe[]> {
    return this.http.get<Recipe[]>(`${this.apiUrl}/recipes`);
  }

  getRecipe(id: number): Observable<Recipe> {
    return this.http.get<Recipe>(`${this.apiUrl}/recipes/${id}`);
  }

  createRecipe(data: Record<string, unknown>): Observable<Recipe> {
    return this.http.post<Recipe>(`${this.apiUrl}/recipes`, data);
  }

  updateRecipe(id: number, data: Record<string, unknown>): Observable<Recipe> {
    return this.http.put<Recipe>(`${this.apiUrl}/recipes/${id}`, data);
  }

  deleteRecipe(id: number): Observable<void> {
    return this.http.delete<void>(`${this.apiUrl}/recipes/${id}`);
  }

  // Menus
  getMenus(): Observable<Menu[]> {
    return this.http.get<Menu[]>(`${this.apiUrl}/menus`);
  }

  getMenu(id: number): Observable<Menu> {
    return this.http.get<Menu>(`${this.apiUrl}/menus/${id}`);
  }

  createMenu(data: Record<string, unknown>): Observable<Menu> {
    return this.http.post<Menu>(`${this.apiUrl}/menus`, data);
  }

  deleteMenu(id: number): Observable<void> {
    return this.http.delete<void>(`${this.apiUrl}/menus/${id}`);
  }

  // Dashboard
  getDashboardStats(): Observable<DashboardStats> {
    return this.http.get<DashboardStats>(`${this.apiUrl}/dashboard`);
  }

  getMenuAnalysis(menuId: number): Observable<{
    menu: { id: number; name: string; menuDate: string };
    analysis: NutritionComparison;
    alerts: ComplianceAlert[];
  }> {
    return this.http.get<{
      menu: { id: number; name: string; menuDate: string };
      analysis: NutritionComparison;
      alerts: ComplianceAlert[];
    }>(`${this.apiUrl}/dashboard/menu-analysis/${menuId}`);
  }

  getNutritionalReferences(ageGroup: string, mealPeriod: string): Observable<Record<string, unknown>> {
    const params = new HttpParams().set('ageGroup', ageGroup).set('mealPeriod', mealPeriod);
    return this.http.get<Record<string, unknown>>(`${this.apiUrl}/dashboard/references`, { params });
  }
}
