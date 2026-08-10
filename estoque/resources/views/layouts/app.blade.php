<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistema de Estoque')</title>
    
    <!-- CSS das bibliotecas -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    @stack('styles')

    <style>
        :root {
            --bs-primary: #D63327 !important;
            --bs-primary-rgb: 214, 51, 39 !important;
            --laravel-red: #D63327;
            --laravel-red-hover: #B5281D;
            --bg-black-main: #121212;
            --bg-card-dark: #1E1E1E;
            --bg-input-dark: #2A2A2A;
            --border-dark: #333333;
            --sidebar-width: 260px;
        }

        body {
            background-color: var(--bg-black-main) !important;
            color: #E0E0E0;
            overflow-x: hidden;
        }

        #sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background-color: var(--bg-card-dark);
            border-right: 1px solid var(--border-dark);
            z-index: 1000;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        .sidebar-brand {
            padding: 1.5rem 1.25rem;
            font-size: 1.2rem;
            font-weight: 700;
            color: #FFFFFF;
            border-bottom: 1px solid var(--border-dark);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .sidebar-menu {
            list-style: none;
            padding: 1rem 0.75rem;
            margin: 0;
            flex-grow: 1;
            overflow-y: auto;
        }

        .sidebar-menu .nav-item {
            margin-bottom: 0.3rem;
        }

        .sidebar-menu .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: #A0A0A0;
            font-weight: 500;
            border-radius: 6px;
            transition: all 0.2s ease-in-out;
            text-decoration: none;
        }

        .sidebar-menu .nav-link:hover {
            color: #FFFFFF;
            background-color: rgba(214, 51, 39, 0.1);
        }

        .sidebar-menu .nav-link.active {
            color: #FFFFFF;
            background-color: var(--laravel-red);
            font-weight: 600;
        }

        .sidebar-menu .nav-link i {
            font-size: 1.1rem;
        }

        #content-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .card {
            background-color: var(--bg-card-dark) !important;
            border: 1px solid var(--border-dark) !important;
        }

        .btn-laravel {
            background-color: var(--laravel-red);
            color: #FFFFFF;
            border: none;
            font-weight: 600;
        }
        .btn-laravel:hover {
            background-color: var(--laravel-red-hover);
            color: #FFFFFF;
        }

        .btn-outline-laravel {
            color: var(--laravel-red);
            border-color: var(--laravel-red);
        }
        .btn-outline-laravel:hover {
            background-color: var(--laravel-red);
            color: #FFFFFF;
        }

        .form-control, .form-select {
            background-color: var(--bg-input-dark) !important;
            border-color: var(--border-dark) !important;
            color: #FFFFFF !important;
        }

        .form-control:focus, .form-select:focus, .form-check-input:focus {
            border-color: var(--laravel-red) !important;
            box-shadow: 0 0 0 0.25rem rgba(214, 51, 39, 0.2) !important;
        }

        .form-check-input:checked {
            background-color: var(--laravel-red) !important;
            border-color: var(--laravel-red) !important;
        }

        .table {
            --bs-table-bg: transparent;
            --bs-table-color: #E0E0E0;
            border-color: var(--border-dark);
        }
        
        thead.table-dark-custom {
            background-color: #181818;
            color: var(--laravel-red);
            border-bottom: 2px solid var(--laravel-red);
        }

        .page-item.active .page-link {
            background-color: var(--laravel-red) !important;
            border-color: var(--laravel-red) !important;
            color: #FFFFFF !important;
        }

        .page-link {
            color: #A0A0A0;
            background-color: #181818;
            border-color: var(--border-dark);
        }
        .page-link:hover {
            color: var(--laravel-red);
            background-color: #252525;
            border-color: var(--border-dark);
        }

        .text-laravel {
            color: var(--laravel-red) !important;
        }

        .modal-content {
            background-color: var(--bg-card-dark) !important;
            border-color: var(--border-dark) !important;
        }

        .modal-header, .modal-footer {
            border-color: var(--border-dark) !important;
        }

        /* Caixa de processamento com o tema escuro */
        #tabela-produtos_processing.dataTables_processing {
            background-color: var(--bg-card-dark) !important;
            border: 1px solid var(--border-dark) !important;
            border-radius: 8px !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.7) !important;
            padding: 12px !important;
            
            /* Zera o conteúdo de texto nativo que o DataTables injeta por cima */
            font-size: 0 !important;
        }

        /* Oculta qualquer elemento gerado nativamente pelo DataTables dentro do loading de qualquer tabela */
        .dataTables_processing > div:not(.spinner-custom) {
            display: none !important;
        }

        /* Spinner customizado elegante */
        .spinner-custom {
            width: 1.2rem;
            height: 1.2rem;
            border: 3px solid rgba(214, 51, 39, 0.25);
            border-top-color: var(--laravel-red);
            border-radius: 50%;
            animation: spin 0.75s linear infinite;
            display: inline-block !important;
            vertical-align: middle;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

    </style>
</head>
<body>

    <!-- MENU LATERAL FIXO -->
    <nav id="sidebar">
        <a href="{{ route('dashboard') }}" class="sidebar-brand">
            <i class="bi bi-box-seam text-laravel"></i>
            <span>Estoque<span class="text-laravel">Sys</span></span>
        </a>
        
        <ul class="sidebar-menu nav flex-column">
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('produtos.index') }}" class="nav-link {{ request()->routeIs('produtos*') ? 'active' : '' }}">
                    <i class="bi bi-boxes"></i>
                    <span>Produtos</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('clientes.index') }}" class="nav-link {{ request()->routeIs('clientes*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i>
                    <span>Clientes</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-truck"></i>
                    <span>Fornecedores</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-tags"></i>
                    <span>Categorias</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-arrow-left-right"></i>
                    <span>Movimentações</span>
                </a>
            </li>
            <li class="nav-item mt-auto">
                <a href="#" class="nav-link text-danger">
                    <i class="bi bi-box-arrow-left"></i>
                    <span>Sair</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- WRAPPER DO CONTEÚDO PRINCIPAL -->
    <div id="content-wrapper">
        @yield('content')
    </div>

    <!-- JS Globais -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    @stack('scripts')
</body>
</html>
