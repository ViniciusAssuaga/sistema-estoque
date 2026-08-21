# EstoqueSys

**EstoqueSys** é uma aplicação de gestão de estoque baseada no framework **Laravel** (versão 11/12+ sob PHP 8.3+) com a stack de autenticação **Laravel Breeze** (Blade). A arquitetura adota o padrão **MVC (Model-View-Controller)** integrado a uma camada híbrida de **SPA/API** acionada por chamadas assíncronas (AJAX) com processamento de tabelas no lado do servidor (**Server-Side Processing**) via **Yajra DataTables**.

## Arquitetura

O sistema adota **dois padrões de consumo** para suas rotas e controladores:

| Módulo | Tipo de Rota | Comportamento / Retorno |
| :--- | :--- | :--- |
| `/produtos`, `/clientes` | `routes/web.php` | Recursos completos (`Resource Controllers`) via `ProdutoController` e `ClienteController` |
| `/fornecedores`, `/categorias`, `/movimentacoes` | `routes/web.php` | Retornam views Blade estáticas; listagem e manipulação via AJAX consumindo a API |
| Fornecedores, Categorias, Movimentações | `routes/api.php` | Endpoints `apiResource` protegidos por `web`, `auth`, `verified` e `throttle:api` |

Mapeamento de arquivos de rotas:

* `routes/web.php` – Rota raiz `/` direcionada para login; rotas internas protegidas por `auth` e `verified`; painel `/dashboard` (`DashboardController`) e perfil `/profile`
* `routes/api.php` – Endpoints de API para operações de consulta e registro de fornecedores, categorias e movimentações
* `routes/auth.php` – Gerencia Login, Recuperação/Redefinição de Senha e Logout; rota `register` desabilitada para restringir criação pública de contas

Modelos e Entidades em `app/Models/`:

* `app/Models/User.php` – Atributos PHP 8.2+ (`#[Fillable(...)]` e `#[Hidden(...)]`) e RBAC base via campo `tipo` (`canCreateRecords()` >= 1, `canEditRecords()` >= 1, `canDeleteRecords()` >= 2 para Administrador)
* `app/Models/Produto.php` – Exclusão lógica (`SoftDeletes`) e relacionamento `belongsTo` com Categoria
* `app/Models/Categoria.php` – Mapeado para a tabela `categorias` com relacionamento `hasMany` com Produto
* `app/Models/Movimentacao.php` – Mapeado para a tabela `movimentacoes`, registra entradas/saídas com relacionamento `belongsTo` com Produto
* `app/Models/Fornecedor.php` – Mapeado explicitamente para a tabela `fornecedores`
* `app/Models/Cliente.php` – Dados cadastrais: E-mail, Telefone, Endereço e CPF/CNPJ

Estrutura de dados em `database/migrations/`:

* **Integridade Referencial:** Tabela `produtos` utiliza `softDeletes()`; migração `restrict_categoria_deletion.php` aplica FK que impede exclusão de categorias associadas a produtos ativos
* **Indexação de Performance:**
  * `produtos`: Índice composto em `['ativo', 'quantidade_estoque']` e índice em `nome`
  * `movimentacoes`: Índice composto em `['produto_id', 'created_at']`
  * `clientes`: Índice em `nome`
  * `fornecedores`: Índices em `razao_social` e `nome_fantasia`

## Regras de Negócio e Controladores

### Controladores Web (`app/Http/Controllers/`)

* **`DashboardController.php`:** Processa métricas em tempo real (Total de Produtos Ativos, Valor Financeiro do Estoque, Produtos com Estoque Baixo e Movimentações do Dia). Identifica o driver do banco (MySQL, PostgreSQL ou SQLite) e adapta a função SQL de extração de datas dinamicamente para agrupamentos (`diario`, `mensal`, `anual`). Estrutura dados para gráficos de linha (7 dias, 12 meses e 5 anos) e pizza (categorias).
* **`ClienteController.php`:** Integração com Yajra DataTables para ordenação e paginação Server-Side. Validações via `Validator::make` para unicidade de e-mail e CPF/CNPJ. Controle de autorização por método (`abort_unless($request->user()->can...(), 403)`).
* **`ProdutoController.php`:** Implementa CRUD via Form Requests injetados (`StoreProdutoRequest` e `UpdateProdutoRequest`). Endpoint `listarJson` disponível para autocompletar (*typeahead*).

### Controladores de API (`app/Http/Controllers/Api/`)

* **`CategoriaApiController.php` & `FornecedorApiController.php`:** Respostas JSON configuradas para consumo de DataTables AJAX ou REST externo.
* **`MovimentacaoController.php`:** Gerencia fluxo de entrada e saída. Utiliza transações (`DB::transaction`) com bloqueio pessimista (`lockForUpdate()`) no produto para evitar *Race Conditions* e saldo negativo. Valida disponibilidade prévia de saldo antes da saída.

## Configurações e Dependências

* **Ambiente Padrão:** MySQL 8.x (porta `3306`), banco de dados `estoque`
* **Drivers Globais:** Processamento de filas (`QUEUE_CONNECTION`) e cache (`CACHE_STORE`) no driver `database`
* **Pacotes Principais:** `yajra/laravel-datatables-oracle` (processamento Server-Side) e `laravel/sanctum` (tokens para API)