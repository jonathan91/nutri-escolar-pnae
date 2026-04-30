# NutriEscolar PNAE - Calculadora de Cardapios

Aplicacao web para nutricionistas RT (Responsaveis Tecnicos) que automatiza o calculo nutricional de cardapios escolares, garantindo conformidade com a Resolucao CD/FNDE n. 06/2020.

## Stack Tecnologica

- **Frontend**: Angular 18 (standalone components, lazy loading)
- **Backend**: PHP 8.2 + Symfony 6.4
- **Banco de Dados**: PostgreSQL 16
- **Autenticacao**: JWT (lexik/jwt-authentication-bundle)
- **Orquestracao**: Docker Compose

## Funcionalidades

### Core
- Cadastro de escolas e turmas por faixa etaria (Creche, Pre-escola, Fundamental, Medio, EJA)
- Banco de dados de alimentos integrado com tabela TACO (60+ alimentos)
- Calculo automatico de VET e micronutrientes (energia, proteinas, carboidratos, lipidios, fibras, calcio, ferro, magnesio, zinco, vitamina A, vitamina C)
- Diferenciacao por periodo (Parcial 20%, 30% e Integral 70%)
- Ficha tecnica de preparo com custo por porcao e valor nutricional

### Compliance PNAE
- Alerta para acucar adicionado em criancas ate 3 anos (proibido)
- Alerta para sodio acima dos limites por faixa etaria
- Alerta para excesso de ultraprocessados (>20%)
- Alerta para gordura saturada acima de 10% do VET

### Dashboard e Relatorios
- Graficos de adequacao nutricional vs metas FNDE
- Exportacao PDF de cardapios e fichas tecnicas
- Identificacao de alergenos (gluten, lactose)
- Filtro de alimentos sazonais (agricultura familiar)

## Como Executar

### Com Docker (Recomendado)

```bash
docker compose up -d --build
```

Acesse:
- **Frontend**: http://localhost:4200
- **Backend API**: http://localhost:8000/api

### Setup Inicial (apos primeiro start)

```bash
# Executar migracoes
docker compose exec backend php bin/console doctrine:migrations:migrate --no-interaction

# Importar dados da tabela TACO
docker compose exec backend php bin/console app:import-taco

# Gerar chaves JWT
docker compose exec backend php bin/console lexik:jwt:generate-keypair --skip-if-exists
```

### Desenvolvimento Local

**Backend:**
```bash
cd backend
composer install
php bin/console doctrine:migrations:migrate
php bin/console app:import-taco
php -S localhost:8000 -t public
```

**Frontend:**
```bash
cd frontend
npm install
ng serve
```

## API Endpoints

| Metodo | Endpoint | Descricao |
|--------|----------|-----------|
| POST | /api/register | Cadastro de usuario |
| POST | /api/login | Login (retorna JWT) |
| GET | /api/me | Dados do usuario logado |
| GET/POST | /api/schools | Listar/criar escolas |
| GET/PUT/DELETE | /api/schools/{id} | Detalhes/atualizar/excluir escola |
| POST | /api/schools/{id}/student-groups | Criar turma |
| GET | /api/foods?q=&category= | Buscar alimentos |
| GET | /api/foods/categories | Categorias de alimentos |
| GET | /api/foods/seasonal?month= | Alimentos sazonais |
| GET/POST | /api/recipes | Listar/criar receitas |
| GET/PUT/DELETE | /api/recipes/{id} | Detalhes/atualizar/excluir receita |
| GET/POST | /api/menus | Listar/criar cardapios |
| GET/DELETE | /api/menus/{id} | Detalhes/excluir cardapio |
| GET | /api/dashboard | Estatisticas gerais |
| GET | /api/dashboard/menu-analysis/{id} | Analise nutricional do cardapio |
| GET | /api/dashboard/references | Referencias nutricionais FNDE |

## Arquitetura Modular

As constantes de referencia nutricional estao centralizadas em `NutritionalReferenceService.php`, permitindo atualizacao facil quando o FNDE publicar novas diretrizes sem necessidade de reescrever a logica principal.

## Licenca

MIT
