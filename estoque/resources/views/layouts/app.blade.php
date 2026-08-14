<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistema de Estoque')</title>
    
    <!-- Favicon Global -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('box-seam.svg') }}">
    
    <!-- CSS das bibliotecas -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    @stack('styles')

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ time() }}">
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
                <a href="{{ route('fornecedores.index') }}" class="nav-link {{ request()->routeIs('fornecedores*') ? 'active' : '' }}">
                    <i class="bi bi-truck"></i>
                    <span>Fornecedores</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('categorias.index') }}" class="nav-link {{ request()->routeIs('categorias*') ? 'active' : '' }}">
                    <i class="bi bi-tags"></i>
                    <span>Categorias</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('movimentacoes.index') }}" class="nav-link {{ request()->routeIs('movimentacoes*') ? 'active' : '' }}">
                    <i class="bi bi-arrow-left-right"></i>
                    <span>Movimentações</span>
                </a>
            </li>
            <li class="nav-item mt-auto">
                <a href="https://www.google.com" class="nav-link text-danger">
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
