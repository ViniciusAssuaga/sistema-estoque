**EstoqueSys** é um sistema de estoque em **Laravel 13** (PHP 8.3), no Laragon, com MySQL no banco `estoque`. A tela logada usa Bootstrap 5 (tema escuro) + DataTables + modais AJAX. Auth vem do **Laravel Breeze**; o cadastro público está desligado.

## Arquitetura

Há **dois padrões de CRUD** no mesmo app:

| Módulo | Página web | Dados / CRUD |
| :--- | :--- | :--- |
| Produtos, clientes | Controller web + Blade | JSON no mesmo controller (`ajax` / `JsonResponse`) |
| Fornecedores, categorias, movimentações | Blade só lista a tela | REST em `routes/api.php` (`/api/...`) |

Arquivos que definem isso:

* `routes/web.php` — rotas autenticadas e o mapa das telas
* `routes/api.php` — API de fornecedores, categorias e movimentações
* `routes/auth.php` — login, senha e logout (register comentado)
* `bootstrap/app.php` — registra web + API
* `resources/views/layouts/app.blade.php` — shell da UI (sidebar EstoqueSys)

O núcleo de domínio está em:

* `app/Models/Produto.php` — SKU, preços, estoque, `softDeletes`, `categoria`
* `app/Models/Movimentacao.php` — entrada/saída ligada ao produto
* `app/Models/Categoria.php`, `Cliente.php`, `Fornecedor.php`, `User.php`

Migrations em `database/migrations/` (produtos, clientes, fornecedores, categorias, movimentações). Seeders em `database/seeders/DatabaseSeeder.php` geram volume grande de teste (incluindo 10 mil produtos).

## Fluxos Principais

1. **Entrar no sistema**  
   `/` redireciona para login. Depois do Breeze, o grupo `auth` + `verified` libera o resto.

2. **Dashboard**  
   `DashboardController` calcula KPIs (total de produtos, valor em estoque, estoque baixo, movimentações do dia) e dados dos gráficos; a view é `resources/views/dashboard.blade.php`.

3. **Cadastros (produtos / clientes)**  
   A página Blade abre a tabela. O DataTables pede JSON no mesmo `index` quando `ajax()`. Criar/editar/excluir vai para `store` / `update` / `destroy` e volta JSON para o modal. O exemplo mais completo é `app/Http/Controllers/ProdutoController.php` + `resources/views/produtos/index.blade.php`.

4. **Movimentação de estoque (fluxo de negócio)**  
   A tela `resources/views/movimentacoes/index.blade.php` busca produtos em `produtos/listar-json` e grava em `POST /api/movimentacoes`. O controller da API atualiza `quantidade_estoque` e grava a movimentação.