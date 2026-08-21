@extends('layouts.app')

@section('title', 'Sistema de Estoque')

@push('styles')
<!-- Carrega o Chart.js especificamente para a dashboard -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    /* Estilização dos botões do gráfico */
    .btn-filtro-grafico {
        background-color: transparent;
        color: #6c757d;
        border: 1px solid #495057;
        transition: all 0.2s ease-in-out;
    }
    .btn-filtro-grafico:hover {
        color: #fff;
        border-color: #6c757d;
    }
    .btn-filtro-grafico.active {
        background-color: transparent !important;
        color: var(--laravel-red, #d63327) !important;
        border-color: var(--laravel-red, #d63327) !important;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="container-fluid p-4">
    <!-- CABEÇALHO DA DASHBOARD -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-0 text-white">Visão Geral do <span class="text-laravel">Estoque</span></h1>
            <small class="text-secondary">Bem-vindo(a), {{ trim(auth()->user()->name) }}. Aqui está o resumo atual do sistema.</small>
        </div>
    </div>

    <!-- CARDS DE ESTATÍSTICAS (KPIs) -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card shadow-sm h-100 p-3" style="border-left: 4px solid var(--laravel-red) !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary small fw-semibold text-uppercase">Total de Produtos</span>
                        <h2 class="h3 fw-bold text-white mb-0 mt-1">{{ number_format($totalProdutos ?? 0, 0, ',', '.') }}</h2>
                        <small class="text-success"><i class="bi bi-arrow-up"></i> Cadastrados no sistema</small>
                    </div>
                    <div class="stat-icon" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; background-color: rgba(214, 51, 39, 0.15); color: var(--laravel-red); border-radius: 8px; font-size: 1.5rem;">
                        <i class="bi bi-boxes"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card shadow-sm h-100 p-3" style="border-left: 4px solid var(--laravel-red) !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary small fw-semibold text-uppercase">Valor em Estoque</span>
                        <h2 class="h3 fw-bold text-white mb-0 mt-1">R$ {{ number_format($valorTotalEstoque ?? 0, 2, ',', '.') }}</h2>
                        <small class="text-success"><i class="bi bi-shield-check"></i> Custo total atual</small>
                    </div>
                    <div class="stat-icon" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; background-color: rgba(214, 51, 39, 0.15); color: var(--laravel-red); border-radius: 8px; font-size: 1.5rem;">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card shadow-sm h-100 p-3" style="border-left: 4px solid #ffc107 !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary small fw-semibold text-uppercase">Estoque Baixo</span>
                        <h2 class="h3 fw-bold text-warning mb-0 mt-1">{{ $totalEstoqueBaixo ?? 0 }}</h2>
                        <small class="text-warning">Requer atenção</small>
                    </div>
                    <div class="stat-icon" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; background-color: rgba(255, 193, 7, 0.15); color: #ffc107; border-radius: 8px; font-size: 1.5rem;">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card shadow-sm h-100 p-3" style="border-left: 4px solid #0dcaf0 !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary small fw-semibold text-uppercase">Movimentações Hoje</span>
                        <h2 class="h3 fw-bold text-info mb-0 mt-1">{{ $movimentacoesHoje ?? 0 }}</h2>
                        <small class="text-info">Entradas e saídas</small>
                    </div>
                    <div class="stat-icon" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; background-color: rgba(13, 202, 240, 0.15); color: #0dcaf0; border-radius: 8px; font-size: 1.5rem;">
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
                    <h5 class="card-title fw-bold mb-0 text-white" id="tituloGraficoFluxo">
                        <i class="bi bi-graph-up text-laravel me-2"></i> Fluxo de Movimentações (Últimos 7 Dias)
                    </h5>
                    <div class="btn-group btn-group-sm" role="group" aria-label="Filtro de período">
                        <button type="button" class="btn btn-filtro-grafico active" id="btnFiltroSemanal">Semanal</button>
                        <button type="button" class="btn btn-filtro-grafico" id="btnFiltroMensal">Mensal</button>
                        <button type="button" class="btn btn-filtro-grafico" id="btnFiltroAnual">Anual</button>
                    </div>
                </div>
                <div class="card-body" style="position: relative; height: 300px;">
                    <canvas id="graficoMovimentacoes"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom border-dark py-3">
                    <h5 class="card-title fw-bold mb-0 text-white"><i class="bi bi-pie-chart text-laravel me-2"></i> Categorias Populares</h5>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center" style="position: relative; height: 300px;">
                    <div style="width: 100%; max-width: 220px;">
                        <canvas id="graficoCategorias"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TABELA INFERIOR -->
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent border-bottom border-dark py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title fw-bold mb-0 text-white"><i class="bi bi-clock-history text-laravel me-2"></i> Últimas Movimentações</h5>
                    <a href="{{ route('movimentacoes.index') }}" class="small text-laravel text-decoration-none fw-semibold">Ver todas</a>
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
                                @forelse($ultimasMovimentacoes ?? [] as $mov)
                                    <tr>
                                        <td><span class="fw-semibold text-white">{{ $mov->produto->nome ?? 'Produto Removido' }}</span></td>
                                        <td>
                                            @if($mov->tipo === 'entrada')
                                                <span class="badge bg-success text-white">Entrada</span>
                                            @else
                                                <span class="badge bg-danger text-white">Saída</span>
                                            @endif
                                        </td>
                                        <td class="{{ $mov->tipo === 'entrada' ? 'text-success' : 'text-danger' }} fw-bold">
                                            {{ $mov->tipo === 'entrada' ? '+' : '-' }}{{ $mov->quantidade }}
                                        </td>
                                        <td class="text-secondary small">{{ $mov->created_at ? $mov->created_at->format('d/m/Y H:i') : '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-secondary py-3">Nenhuma movimentação registrada.</td>
                                    </tr>
                                @endforelse
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
                                @forelse($produtosEstoqueBaixo ?? [] as $prod)
                                    <tr>
                                        <td><span class="fw-semibold text-white">{{ $prod->nome }}</span></td>
                                        <td><span class="text-danger fw-bold">{{ $prod->quantidade_estoque }}</span></td>
                                        <td>{{ $prod->estoque_minimo }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('produtos.index') }}?q={{ urlencode($prod->nome) }}" class="btn btn-sm btn-outline-warning py-0 px-2">Repor</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-secondary py-3">Nenhum produto com estoque crítico.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Função auxiliar para garantir Array numérico simples
        function paraArray(dados) {
            if (!dados) return [];
            if (Array.isArray(dados)) return dados;
            if (typeof dados === 'object') return Object.values(dados);
            return [];
        }

        // Recupera os dados injetados pelo Blade
        const rawSemanalLabels = @json($diasLabels ?? []);
        const rawSemanalEntradas = @json($dadosEntradas ?? []);
        const rawSemanalSaidas = @json($dadosSaidas ?? []);

        const rawMensalLabels = @json($mesesLabels ?? []);
        const rawMensalEntradas = @json($dadosEntradasMensal ?? []);
        const rawMensalSaidas = @json($dadosSaidasMensal ?? []);

        const rawAnualLabels = @json($anosLabels ?? []);
        const rawAnualEntradas = @json($dadosEntradasAnual ?? []);
        const rawAnualSaidas = @json($dadosSaidasAnual ?? []);

        // Trata os dados semanais
        let dadosSemanalLabels = paraArray(rawSemanalLabels);
        let dadosSemanalEntradas = paraArray(rawSemanalEntradas);
        let dadosSemanalSaidas = paraArray(rawSemanalSaidas);

        // Trata os dados mensais (Gera fallback caso esteja vazio na Controller)
        let dadosMensalLabels = paraArray(rawMensalLabels);
        let dadosMensalEntradas = paraArray(rawMensalEntradas);
        let dadosMensalSaidas = paraArray(rawMensalSaidas);

        let dadosAnualLabels = paraArray(rawAnualLabels);
        let dadosAnualEntradas = paraArray(rawAnualEntradas);
        let dadosAnualSaidas = paraArray(rawAnualSaidas);

        if (dadosMensalLabels.length === 0) {
            const mesesNomes = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
            const dataAtual = new Date();
            dadosMensalLabels = [];
            dadosMensalEntradas = new Array(12).fill(0);
            dadosMensalSaidas = new Array(12).fill(0);

            for (let i = 11; i >= 0; i--) {
                const d = new Date(dataAtual.getFullYear(), dataAtual.getMonth() - i, 1);
                dadosMensalLabels.push(mesesNomes[d.getMonth()] + '/' + String(d.getFullYear()).slice(-2));
            }
        }

        if (dadosAnualLabels.length === 0) {
            const anoAtual = new Date().getFullYear();
            dadosAnualLabels = [];
            dadosAnualEntradas = new Array(5).fill(0);
            dadosAnualSaidas = new Array(5).fill(0);

            for (let i = 4; i >= 0; i--) {
                dadosAnualLabels.push(String(anoAtual - i));
            }
        }

        const dadosGrafico = {
            semanal: {
                titulo: '<i class="bi bi-graph-up text-laravel me-2"></i> Fluxo de Movimentações (Últimos 7 Dias)',
                labels: dadosSemanalLabels,
                entradas: dadosSemanalEntradas,
                saidas: dadosSemanalSaidas
            },
            mensal: {
                titulo: '<i class="bi bi-graph-up text-laravel me-2"></i> Fluxo de Movimentações (Últimos 12 Meses)',
                labels: dadosMensalLabels,
                entradas: dadosMensalEntradas,
                saidas: dadosMensalSaidas
            },
            anual: {
                titulo: '<i class="bi bi-graph-up text-laravel me-2"></i> Fluxo de Movimentações (Últimos 5 Anos)',
                labels: dadosAnualLabels,
                entradas: dadosAnualEntradas,
                saidas: dadosAnualSaidas
            }
        };

        // Inicialização do Gráfico de Movimentações
        const ctxMov = document.getElementById('graficoMovimentacoes').getContext('2d');
        const chartMov = new Chart(ctxMov, {
            type: 'line',
            data: {
                labels: dadosGrafico.semanal.labels,
                datasets: [{
                    label: 'Entradas',
                    data: dadosGrafico.semanal.entradas,
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }, {
                    label: 'Saídas',
                    data: dadosGrafico.semanal.saidas,
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
                        labels: { color: '#E0E0E0', font: { size: 12 } }
                    }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(255, 255, 255, 0.05)' },
                        ticks: { color: '#A0A0A0' }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(255, 255, 255, 0.05)' },
                        ticks: { color: '#A0A0A0', precision: 0 }
                    }
                }
            }
        });

        // Eventos dos Botões de Filtro
        const btnSemanal = document.getElementById('btnFiltroSemanal');
        const btnMensal = document.getElementById('btnFiltroMensal');
        const btnAnual = document.getElementById('btnFiltroAnual');
        const tituloGrafico = document.getElementById('tituloGraficoFluxo');

        const botoesFiltro = [btnSemanal, btnMensal, btnAnual];

        function atualizarGrafico(periodo) {
            const config = dadosGrafico[periodo];

            tituloGrafico.innerHTML = config.titulo;
            chartMov.data.labels = config.labels;
            chartMov.data.datasets[0].data = config.entradas;
            chartMov.data.datasets[1].data = config.saidas;
            chartMov.update();
        }

        function selecionarPeriodo(periodo, botaoSelecionado) {
            if (!botaoSelecionado.classList.contains('active')) {
                botoesFiltro.forEach(botao => botao.classList.remove('active'));
                botaoSelecionado.classList.add('active');
                atualizarGrafico(periodo);
            }
        }

        btnSemanal.addEventListener('click', function() {
            selecionarPeriodo('semanal', this);
        });

        btnMensal.addEventListener('click', function() {
            selecionarPeriodo('mensal', this);
        });

        btnAnual.addEventListener('click', function() {
            selecionarPeriodo('anual', this);
        });

        // Gráfico de Categorias Populares
        const ctxCat = document.getElementById('graficoCategorias').getContext('2d');
        const rawCatLabels = @json($categoriasLabels ?? []);
        const rawCatTotais = @json($categoriasTotais ?? []);

        new Chart(ctxCat, {
            type: 'doughnut',
            data: {
                labels: paraArray(rawCatLabels),
                datasets: [{
                    data: paraArray(rawCatTotais),
                    backgroundColor: ['#D63327', '#fd7e14', '#ffc107', '#0dcaf0', '#6610f2', '#20c997'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#E0E0E0', font: { size: 11 }, boxWidth: 12 }
                    }
                }
            }
        });
    });
</script>
@endpush
