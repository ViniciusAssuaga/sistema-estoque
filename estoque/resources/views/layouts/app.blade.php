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

    <!-- Asset Bundler Oficial do Laravel (Vite) teste alteração -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .form-switch .form-check-input {
            background-color: #495057 !important;
            background-image: radial-gradient(circle, #FFFFFF 0 0.36em, transparent 0.39em) !important;
            background-size: 1em 1em !important;
            background-position: left center !important;
            background-repeat: no-repeat !important;
        }

        .form-switch .form-check-input:checked {
            background-color: var(--laravel-red) !important;
            background-position: right center !important;
        }

        #sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1030;
        }

        /* Ajuste do overlay quando o menu está aberto */
        body.sidebar-open #sidebar-overlay {
            display: block;
        }

        /* Ajustes do Menu Superior Mobile */
        .navbar-mobile-top {
            background-color: #18181b !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
        }

        /* Cor padrão: BRANCO */
        #sidebarToggle {
            color: #ffffff !important;
            background-color: transparent !important;
            border-color: transparent !important;
            box-shadow: none !important;
            outline: none !important;
            transition: color 0.15s ease-in-out;
        }

        /* Cor ao passar o mouse ou no momento exato do clique: LARANJA LARAVEL */
        #sidebarToggle:hover,
        #sidebarToggle:active {
            color: #ff2d20 !important;
        }

        /* Regra específica para desktop: força o menu lateral a usar Flexbox de ponta a ponta */
        @media (min-width: 992px) {
            #sidebar {
                display: flex;
                flex-direction: column;
            }

            .sidebar-menu {
                flex: 1;
                display: flex;
                flex-direction: column;
            }

            .user-section-container {
                margin-top: auto !important;
            }
        }

        @media (max-width: 991.98px) {
            .navbar-mobile-top {
                z-index: 1050 !important;
            }

            #sidebar {
                position: fixed;
                top: 0;
                left: -260px;
                height: 100vh;
                z-index: 1040;
                padding-top: 56px;
                transition: left 0.3s ease-in-out;
                overflow-y: auto;
            }

            #sidebar.show {
                left: 0;
            }

            /* Ocupar ~95% da largura da tela no celular */
            #content-wrapper {
                margin-left: 0 !important;
                padding-top: 66px !important;
                padding-left: 10px !important;
                padding-right: 10px !important;
                width: 100% !important;
            }

            #content-wrapper .container,
            #content-wrapper .container-fluid {
                padding-left: 5px !important;
                padding-right: 5px !important;
            }
        }
    </style>
</head>

<body>

    <!-- NAVBAR SUPERIOR PARA DISPOSITIVOS MÓVEIS -->
    <nav class="navbar navbar-dark d-lg-none fixed-top px-3 navbar-mobile-top">
        <button class="btn border-0 fs-4 p-0" type="button" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>
        <span class="navbar-brand mb-0 h1 fs-5">
            <i class="bi bi-box-seam text-laravel"></i> Estoque<span class="text-laravel">Sys</span>
        </span>
    </nav>

    <!-- OVERLAY ESCURO AO ABRIR MENU NO CELULAR -->
    <div id="sidebar-overlay"></div>

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
                <a href="{!! route('movimentacoes.index') !!}" class="nav-link {{ request()->routeIs('movimentacoes*') ? 'active' : '' }}">
                    <i class="bi bi-arrow-left-right"></i>
                    <span>Movimentações</span>
                </a>
            </li>

            <!-- CONTAINER DO USUÁRIO E LOGOUT (Empurrado para baixo apenas no Desktop via CSS) -->
            <div class="user-section-container mt-3 mt-lg-0">
                <li class="nav-item border-top border-dark pt-2">
                    <div class="nav-link text-white-50 cursor-default px-3 py-2 d-flex align-items-center gap-2">
                        <i class="bi bi-person-circle fs-5 text-laravel"></i>
                        <span class="fw-semibold text-truncate" style="max-width: 170px;" title="{{ auth()->user()->name ?? 'Usuário' }}">
                            {{ auth()->user()->name ?? 'Usuário' }}
                        </span>
                    </div>
                </li>

                <li class="nav-item mb-3 mb-lg-0">
                    <form method="POST" action="{{ route('logout') }}" id="logout-form">
                        @csrf
                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="nav-link text-danger">
                            <i class="bi bi-box-arrow-left"></i>
                            <span>Sair</span>
                        </a>
                    </form>
                </li>
            </div>
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

    <script>
        // SweetAlert Padrão
        window.swalPadrao = Swal.mixin({
            background: '#1E1E1E',
            color: '#E0E0E0'
        });

        // Permissões do Usuário
        window.userPermissions = {
            canCreate: "{{ auth()->user()?->canCreateRecords() ? 1 : 0 }}" === "1",
            canEdit: "{{ auth()->user()?->canEditRecords() ? 1 : 0 }}" === "1",
            canDelete: "{{ auth()->user()?->canDeleteRecords() ? 1 : 0 }}" === "1"
        };

        // Lógica da Sidebar
        $(document).ready(function() {
            const $sidebar = $('#sidebar');

            function toggleSidebar() {
                $sidebar.toggleClass('show');
                $('body').toggleClass('sidebar-open');
            }

            $('#sidebarToggle, #sidebar-overlay').on('click', function(e) {
                e.stopPropagation();
                toggleSidebar();
            });

            // Remove o foco do botão logo após o clique
            $('#sidebarToggle').on('mouseup touchend keyup', function() {
                $(this).blur();
            });
        });
    </script>

    @stack('scripts')
</body>

</html>