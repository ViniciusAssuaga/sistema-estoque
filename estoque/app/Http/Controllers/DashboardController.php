<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Movimentacao;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. KPIs dos Cards Superiores
        $totalProdutos = Produto::where('ativo', true)->count();
        
        // Valor total em estoque calculado direto na Query do banco de dados
        $valorTotalEstoque = Produto::where('ativo', true)
            ->selectRaw('SUM(preco_custo * quantidade_estoque) as total')
            ->value('total') ?? 0;

        $totalEstoqueBaixo = Produto::where('ativo', true)
            ->whereColumn('quantidade_estoque', '<=', 'estoque_minimo')
            ->count();
        
        $movimentacoesHoje = Movimentacao::whereDate('created_at', Carbon::today())->count();

        // 2. Tabelas Inferiores
        $ultimasMovimentacoes = Movimentacao::with('produto')->latest()->take(5)->get();
        
        $produtosEstoqueBaixo = Produto::where('ativo', true)
            ->whereColumn('quantidade_estoque', '<=', 'estoque_minimo')
            ->orderByRaw('(estoque_minimo - quantidade_estoque) DESC')
            ->take(5)
            ->get();

        // 3. Gráfico de Movimentações (Últimos 7 Dias - Semanal)
        $diasLabels = [];
        $dadosEntradas = [];
        $dadosSaidas = [];
        $hoje = Carbon::today();
        $totaisDiarios = $this->totaisMovimentacoesPorPeriodo(
            $hoje->copy()->subDays(6)->startOfDay(),
            $hoje->copy()->endOfDay(),
            'diario'
        );

        for ($i = 6; $i >= 0; $i--) {
            $data = $hoje->copy()->subDays($i);
            $totais = $totaisDiarios[$data->format('Y-m-d')] ?? [];
            $diasLabels[] = ucfirst($data->translatedFormat('D'));
            $dadosEntradas[] = (int) ($totais['entrada'] ?? 0);
            $dadosSaidas[] = (int) ($totais['saida'] ?? 0);
        }

        // 4. Gráfico de Movimentações (Últimos 12 Meses - Mensal)
        $mesesLabels = [];
        $dadosEntradasMensal = [];
        $dadosSaidasMensal = [];
        $totaisMensais = $this->totaisMovimentacoesPorPeriodo(
            Carbon::now()->subMonths(11)->startOfMonth(),
            Carbon::now()->endOfMonth(),
            'mensal'
        );

        for ($i = 11; $i >= 0; $i--) {
            $mes = Carbon::now()->subMonths($i);
            $totais = $totaisMensais[$mes->format('Y-m')] ?? [];
            $mesesLabels[] = ucfirst($mes->translatedFormat('M/Y'));
            $dadosEntradasMensal[] = (int) ($totais['entrada'] ?? 0);
            $dadosSaidasMensal[] = (int) ($totais['saida'] ?? 0);
        }

        // 5. Gráfico de Movimentações (Últimos 5 Anos - Anual)
        $anosLabels = [];
        $dadosEntradasAnual = [];
        $dadosSaidasAnual = [];
        $totaisAnuais = $this->totaisMovimentacoesPorPeriodo(
            Carbon::now()->subYears(4)->startOfYear(),
            Carbon::now()->endOfYear(),
            'anual'
        );

        for ($i = 4; $i >= 0; $i--) {
            $ano = Carbon::now()->subYears($i)->year;
            $totais = $totaisAnuais[(string) $ano] ?? [];
            $anosLabels[] = (string) $ano;
            $dadosEntradasAnual[] = (int) ($totais['entrada'] ?? 0);
            $dadosSaidasAnual[] = (int) ($totais['saida'] ?? 0);
        }

        // 6. Gráfico de Categorias Populares
        $categorias = Categoria::withCount(['produtos' => function ($query) {
            $query->where('ativo', true);
        }])->orderByDesc('produtos_count')->take(5)->get();
        $categoriasLabels = $categorias->pluck('nome')->values();
        $categoriasTotais = $categorias->pluck('produtos_count')->values();

        return view('dashboard', compact(
            'totalProdutos',
            'valorTotalEstoque',
            'totalEstoqueBaixo',
            'movimentacoesHoje',
            'ultimasMovimentacoes',
            'produtosEstoqueBaixo',
            'diasLabels',
            'dadosEntradas',
            'dadosSaidas',
            'mesesLabels',
            'dadosEntradasMensal',
            'dadosSaidasMensal',
            'anosLabels',
            'dadosEntradasAnual',
            'dadosSaidasAnual',
            'categoriasLabels',
            'categoriasTotais'
        ));
    }

    private function totaisMovimentacoesPorPeriodo(Carbon $inicio, Carbon $fim, string $periodo): array
    {
        $driver = DB::connection()->getDriverName();
        $expressao = match ($periodo) {
            'diario' => match ($driver) {
                'pgsql' => "TO_CHAR(created_at, 'YYYY-MM-DD')",
                'mysql' => "DATE_FORMAT(created_at, '%Y-%m-%d')",
                default => "strftime('%Y-%m-%d', created_at)",
            },
            'mensal' => match ($driver) {
                'pgsql' => "TO_CHAR(created_at, 'YYYY-MM')",
                'mysql' => "DATE_FORMAT(created_at, '%Y-%m')",
                default => "strftime('%Y-%m', created_at)",
            },
            default => match ($driver) {
                'pgsql' => "TO_CHAR(created_at, 'YYYY')",
                'mysql' => "DATE_FORMAT(created_at, '%Y')",
                default => "strftime('%Y', created_at)",
            },
        };

        return Movimentacao::query()
            ->selectRaw("{$expressao} as periodo, tipo, SUM(quantidade) as total")
            ->whereBetween('created_at', [$inicio, $fim])
            ->groupByRaw("{$expressao}, tipo")
            ->get()
            ->groupBy('periodo')
            ->map(fn ($totais) => $totais->pluck('total', 'tipo')->all())
            ->all();
    }
}
