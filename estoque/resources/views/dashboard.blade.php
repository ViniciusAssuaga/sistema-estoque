<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistema de Estoque - Dashboard</title>
    
    <!-- CSS das bibliotecas -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- JS das bibliotecas -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
        }

        /* SIDEBAR */
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

        /* CONTENT WRAPPER */
        #content-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .card {
            background-color: var(--bg-card-dark) !important;
            border: 1px solid var(--border-dark) !important;
            border-radius: 10px;
        }

        .text-laravel {
            color: var(--laravel-red) !important;
        }

        /* STAT CARDS */
        .stat-card {
            border-left: 4px solid var(--laravel-red) !important;
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-3px);
        }
        .stat-icon {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(214, 51, 39, 0.15);
            color: var(--laravel-red);
            border-radius: 8px;
            font-size: 1.5rem;
        }

        /* TABLES */
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
    </style>
</head>
<body>

    <!-- SIDEBAR / MENU LATERAL -->
    <nav id="sidebar">
        <a href="{{ route('dashboard') }}" class="sidebar-brand">
            <i class="bi bi-box-seam text-laravel"></i>
            <span>Estoque<span class="text-laravel">Sys</span></span>
        </a>
        
        <ul class="sidebar-menu nav flex-column">
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link active">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('produtos.index') }}" class="nav-link">
                    <i class="bi bi-boxes"></i>
                    <span>Produtos</span>
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
                    <i class="bi bi-people"></i>
                    <span>Fornecedores</span>
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
        <div class="container-fluid p-4">
            <!-- CABEÇALHO DA DASHBOARD -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 fw-bold mb-0">Visão Geral do <span class="text-laravel">Estoque</span></h1>
                    <small class="text-secondary">Bem-vindo de volta, Administrador. Aqui está o resumo atual do sistema.</small>
                </div>
            </div>

            <!-- CARDS DE ESTATÍSTICAS (KPIs) -->
            <div class="row g-4 mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card shadow-sm h-100 p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-secondary small fw-semibold text-uppercase">Total de Produtos</span>
                                <h2 class="h3 fw-bold text-white mb-0 mt-1">1,248</h2>
                                <small class="text-success"><i class="bi bi-arrow-up"></i> +12% este mês</small>
                            </div>
                            <div class="stat-icon">
                                <i class="bi bi-boxes"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card shadow-sm h-100 p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-secondary small fw-semibold text-uppercase">Valor em Estoque</span>
                                <h2 class="h3 fw-bold text-white mb-0 mt-1">R$ 148.230</h2>
                                <small class="text-success"><i class="bi bi-arrow-up"></i> +4.5% vs. ontem</small>
                            </div>
                            <div class="stat-icon">
                                <i class="bi bi-currency-dollar"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card shadow-sm h-100 p-3" style="border-left-color: #ffc107 !important;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-secondary small fw-semibold text-uppercase">Estoque Baixo</span>
                                <h2 class="h3 fw-bold text-warning mb-0 mt-1">14</h2>
                                <small class="text-warning">Requer atenção</small>
                            </div>
                            <div class="stat-icon" style="background-color: rgba(255, 193, 7, 0.15); color: #ffc107;">
                                <i class="bi bi-exclamation-triangle"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card shadow-sm h-100 p-3" style="border-left-color: #0dcaf0 !important;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-secondary small fw-semibold text-uppercase">Movimentações Hoje</span>
                                <h2 class="h3 fw-bold text-info mb-0 mt-1">38</h2>
                                <small class="text-info">Entradas e saídas</small>
                            </div>
                            <div class="stat-icon" style="background-color: rgba(13, 202, 240, 0.15); color: #0dcaf0;">
                                <i class="bi bi-arrow-left-right"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SEÇÃO DE GRÁFICOS -->
            <div class="row g-4 mb-4">
                <div class="col-lg-8">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-transparent border-bottom border-dark py-3 d-flex justify-content-between align-items-center">
                            <h5 class="card-title fw-bold mb-0 text-white"><i class="bi bi-graph-up text-laravel me-2"></i> Fluxo de Movimentações (Últimos 7 Dias)</h5>
                            <span class="badge bg-dark border border-secondary text-secondary">Semanal</span>
                        </div>
                        <div class="card-body">
                            <canvas id="graficoMovimentacoes" height="130"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-transparent border-bottom border-dark py-3">
                            <h5 class="card-title fw-bold mb-0 text-white"><i class="bi bi-pie-chart text-laravel me-2"></i> Categorias Populares</h5>
                        </div>
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <div style="width: 100%; max-width: 250px;">
                                <canvas id="graficoCategorias"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABELA INFERIOR: ÚLTIMAS MOVIMENTAÇÕES E ALERTAS -->
            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="card shadow-sm">
                        <div class="card-header bg-transparent border-bottom border-dark py-3 d-flex justify-content-between align-items-center">
                            <h5 class="card-title fw-bold mb-0 text-white"><i class="bi bi-clock-history text-laravel me-2"></i> Últimas Movimentações</h5>
                            <a href="#" class="small text-laravel text-decoration-none fw-semibold">Ver todas</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-dark-custom">
                                        <tr>
                                            <th>Produto</th>
                                            <th>Tipo</th>
                                            <th>Qtd</th>
                                            <th>Data/Hora</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><span class="fw-semibold text-white">Cadeira Ergonômica Executiva</span></td>
                                            <td><span class="badge bg-success bg-opacity-15 text-success border border-success border-opacity-25">Entrada</span></td>
                                            <td class="text-success fw-bold">+15</td>
                                            <td class="text-secondary small">Hoje, 10:42</td>
                                        </tr>
                                        <tr>
                                            <td><span class="fw-semibold text-white">Mouse Gamer RGB Wireless</span></td>
                                            <td><span class="badge bg-danger bg-opacity-15 text-danger border border-danger border-opacity-25">Saída</span></td>
                                            <td class="text-danger fw-bold">-4</td>
                                            <td class="text-secondary small">Hoje, 09:15</td>
                                        </tr>
                                        <tr>
                                            <td><span class="fw-semibold text-white">Teclado Mecânico Switch Red</span></td>
                                            <td><span class="badge bg-danger bg-opacity-15 text-danger border border-danger border-opacity-25">Saída</span></td>
                                            <td class="text-danger fw-bold">-2</td>
                                            <td class="text-secondary small">Ontem, 16:30</td>
                                        </tr>
                                        <tr>
                                            <td><span class="fw-semibold text-white">Monitor Ultrawide 29" IPS</span></td>
                                            <td><span class="badge bg-success bg-opacity-15 text-success border border-success border-opacity-25">Entrada</span></td>
                                            <td class="text-success fw-bold">+10</td>
                                            <td class="text-secondary small">Ontem, 14:10</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card shadow-sm">
                        <div class="card-header bg-transparent border-bottom border-dark py-3">
                            <h5 class="card-title fw-bold mb-0 text-warning"><i class="bi bi-exclamation-triangle me-2"></i> Produtos com Estoque Mínimo</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-dark-custom">
                                        <tr>
                                            <th>Produto</th>
                                            <th>Atual</th>
                                            <th>Mín</th>
                                            <th class="text-end">Ação</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><span class="fw-semibold text-white">Headset Gamer 7.1</span></td>
                                            <td><span class="text-danger fw-bold">1</span></td>
                                            <td>5</td>
                                            <td class="text-end">
                                                <a href="{{ route('produtos.index') }}" class="btn btn-sm btn-outline-laravel py-0 px-2">Repor</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><span class="fw-semibold text-white">Cabo HDMI 2.0 4K 2m</span></td>
                                            <td><span class="text-danger fw-bold">2</span></td>
                                            <td>10</td>
                                            <td class="text-end">
                                                <a href="{{ route('produtos.index') }}" class="btn btn-sm btn-outline-laravel py-0 px-2">Repor</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><span class="fw-semibold text-white">Webcam Full HD 1080p</span></td>
                                            <td><span class="text-warning fw-bold">3</span></td>
                                            <td>5</td>
                                            <td class="text-end">
                                                <a href="{{ route('produtos.index') }}" class="btn btn-sm btn-outline-laravel py-0 px-2">Repor</a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- SCRIPTS PARA OS GRÁFICOS CHART.JS -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Gráfico de Linha (Movimentações)
            const ctxMov = document.getElementById('graficoMovimentacoes').getContext('2d');
            new Chart(ctxMov, {
                type: 'line',
                data: {
                    labels: ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
                    datasets: [{
                        label: 'Entradas',
                        data: [12, 19, 8, 15, 22, 10, 14],
                        borderColor: '#198754',
                        backgroundColor: 'rgba(25, 135, 84, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    }, {
                        label: 'Saídas',
                        data: [8, 12, 15, 9, 18, 6, 11],
                        borderColor: '#D63327',
                        backgroundColor: 'rgba(214, 51, 39, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: {
                                color: '#E0E0E0',
                                font: { size: 12 }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { color: 'rgba(255, 255, 255, 0.05)' },
                            ticks: { color: '#A0A0A0' }
                        },
                        y: {
                            grid: { color: 'rgba(255, 255, 255, 0.05)' },
                            ticks: { color: '#A0A0A0' }
                        }
                    }
                }
            });

            // Gráfico de Rosca (Categorias)
            const ctxCat = document.getElementById('graficoCategorias').getContext('2d');
            new Chart(ctxCat, {
                type: 'doughnut',
                data: {
                    labels: ['Periféricos', 'Hardware', 'Monitores', 'Acessórios'],
                    datasets: [{
                        data: [45, 25, 20, 10],
                        backgroundColor: [
                            '#D63327',
                            '#fd7e14',
                            '#ffc107',
                            '#0dcaf0'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#E0E0E0',
                                font: { size: 11 },
                                boxWidth: 12
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
