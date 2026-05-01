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

## Configuracao e Credenciais

### Banco de Dados (PostgreSQL)

| Parametro | Valor |
|-----------|-------|
| Host | `database` (interno Docker) / `localhost:5432` (externo) |
| Database | `nutri_pnae` |
| Usuario | `nutri_user` |
| Senha | `nutri_secret` |

### JWT (Autenticacao)

| Parametro | Valor |
|-----------|-------|
| Passphrase | `nutri_jwt_passphrase` |
| Chave Privada | `config/jwt/private.pem` (gerada automaticamente no build) |
| Chave Publica | `config/jwt/public.pem` (gerada automaticamente no build) |

### Aplicacao (Symfony)

| Parametro | Valor |
|-----------|-------|
| APP_ENV | `dev` |
| APP_SECRET | `a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4` |
| CORS_ALLOW_ORIGIN | `localhost` e `127.0.0.1` (qualquer porta) |

### Portas dos Servicos

| Servico | Porta | Descricao |
|---------|-------|-----------|
| Nginx Gateway | `4200` | Ponto de entrada unico (frontend + API) |
| Backend (Nginx + PHP-FPM) | `8000` (interna) | API backend self-contained |
| PHP-FPM | `9000` (localhost no container) | Processa requisicoes PHP |
| PostgreSQL | `5432` | Banco de dados |

### Arquitetura dos Containers

```
Cliente --> Nginx Gateway (:4200) --> Backend Nginx (:8000) --> PHP-FPM (:9000 localhost)
                |                          |
                |-- Frontend Angular       |-- API Symfony
                    (arquivos estaticos)       (via FastCGI)
                                           |
                                           --> PostgreSQL (:5432)
```

## Como Executar

### Com Docker (Recomendado)

```bash
docker compose up -d --build
```

Acesse:
- **Aplicacao**: http://localhost:4200
- **API**: http://localhost:4200/api

### Setup Inicial (apos primeiro start)

```bash
# Executar migracoes
docker compose exec backend php bin/console doctrine:migrations:migrate --no-interaction

# Importar dados da tabela TACO
docker compose exec backend php bin/console app:import-taco

# Gerar chaves JWT (caso nao tenham sido geradas no build)
docker compose exec backend php bin/console lexik:jwt:generate-keypair --skip-if-exists
```

### Criando um Usuario

```bash
curl -X POST http://localhost:4200/api/register \
  -H "Content-Type: application/json" \
  -d '{"email":"seu@email.com", "password":"suaSenha", "name":"Seu Nome"}'
```

### Autenticando (Login)

```bash
curl -X POST http://localhost:4200/api/login \
  -H "Content-Type: application/json" \
  -d '{"username":"seu@email.com", "password":"suaSenha"}'
```

O login retorna um token JWT que deve ser enviado no header `Authorization: Bearer <token>` em todas as requisicoes autenticadas.

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
