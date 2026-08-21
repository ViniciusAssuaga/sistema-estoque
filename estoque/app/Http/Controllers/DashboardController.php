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
        $totalProdutos = Produto::count();
        
        // Valor total em estoque calculado direto na Query do banco de dados
        $valorTotalEstoque = Produto::selectRaw('SUM(preco_custo * quantidade_estoque) as total')
            ->value('total') ?? 0;

        $totalEstoqueBaixo = Produto::whereColumn('quantidade_estoque', '<=', 'estoque_minimo')->count();
        
        $movimentacoesHoje = Movimentacao::whereDate('created_at', Carbon::today())->count();

        // 2. Tabelas Inferiores
        $ultimasMovimentacoes = Movimentacao::with('produto')->latest()->take(5)->get();
        
        $produtosEstoqueBaixo = Produto::whereColumn('quantidade_estoque', '<=', 'estoque_minimo')
            ->orderByRaw('(estoque_minimo - quantidade_estoque) DESC')
            ->take(5)
            ->get();

        // 3. Gráfico de Movimentações (Últimos 7 Dias - Semanal)
        $diasLabels = [];
        $dadosEntradas = [];
        $dadosSaidas = [];

        for ($i = 6; $i >= 0; $i--) {
            $data = Carbon::today()->subDays($i);
            $diasLabels[] = ucfirst($data->translatedFormat('D'));

            $dadosEntradas[] = (int) Movimentacao::whereDate('created_at', $data)
                ->where('tipo', 'entrada')
                ->sum('quantidade');

            $dadosSaidas[] = (int) Movimentacao::whereDate('created_at', $data)
                ->where('tipo', 'saida')
                ->sum('quantidade');
        }

        // 4. Gráfico de Movimentações (Últimos 12 Meses - Mensal)
        $mesesLabels = [];
        $dadosEntradasMensal = [];
        $dadosSaidasMensal = [];

        for ($i = 11; $i >= 0; $i--) {
            $mes = Carbon::now()->subMonths($i);
            $mesesLabels[] = ucfirst($mes->translatedFormat('M/Y'));

            $dadosEntradasMensal[] = (int) Movimentacao::whereYear('created_at', $mes->year)
                ->whereMonth('created_at', $mes->month)
                ->where('tipo', 'entrada')
                ->sum('quantidade');

            $dadosSaidasMensal[] = (int) Movimentacao::whereYear('created_at', $mes->year)
                ->whereMonth('created_at', $mes->month)
                ->where('tipo', 'saida')
                ->sum('quantidade');
        }

        // 5. Gráfico de Movimentações (Últimos 5 Anos - Anual)
        $anosLabels = [];
        $dadosEntradasAnual = [];
        $dadosSaidasAnual = [];

        for ($i = 4; $i >= 0; $i--) {
            $ano = Carbon::now()->subYears($i)->year;
            $anosLabels[] = (string) $ano;

            $dadosEntradasAnual[] = (int) Movimentacao::whereYear('created_at', $ano)
                ->where('tipo', 'entrada')
                ->sum('quantidade');

            $dadosSaidasAnual[] = (int) Movimentacao::whereYear('created_at', $ano)
                ->where('tipo', 'saida')
                ->sum('quantidade');
        }

        // 6. Gráfico de Categorias Populares
        $categorias = Categoria::withCount('produtos')->take(5)->get();
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
}
