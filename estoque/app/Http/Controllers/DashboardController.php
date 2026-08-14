<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Movimentacao;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. KPIs dos Cards Superiores
        $totalProdutos = Produto::count();
        
        // Valor total em estoque (Preço de custo * Quantidade em estoque)
        $valorTotalEstoque = Produto::all()->sum(function ($produto) {
            return $produto->preco_custo * $produto->quantidade_estoque;
        });

        $totalEstoqueBaixo = Produto::whereColumn('quantidade_estoque', '<=', 'estoque_minimo')->count();
        
        $movimentacoesHoje = Movimentacao::whereDate('created_at', Carbon::today())->count();

        // 2. Tabelas Inferiores
        $ultimasMovimentacoes = Movimentacao::with('produto')->latest()->take(5)->get();
        
        $produtosEstoqueBaixo = Produto::whereColumn('quantidade_estoque', '<=', 'estoque_minimo')
        ->orderByRaw('(estoque_minimo - quantidade_estoque) DESC')
        ->take(5)
        ->get();

        // 3. Gráfico de Movimentações (Últimos 7 Dias)
        $diasLabels = [];
        $dadosEntradas = [];
        $dadosSaidas = [];

        for ($i = 6; $i >= 0; $i--) {
            $data = Carbon::today()->subDays($i);
            $diasLabels[] = $data->translatedFormat('D');

            $dadosEntradas[] = Movimentacao::whereDate('created_at', $data)
                ->where('tipo', 'entrada')
                ->sum('quantidade');

            $dadosSaidas[] = Movimentacao::whereDate('created_at', $data)
                ->where('tipo', 'saida')
                ->sum('quantidade');
        }

        // 4. Gráfico de Categorias Populares
        $categorias = Categoria::withCount('produtos')->take(5)->get();
        $categoriasLabels = $categorias->pluck('nome');
        $categoriasTotais = $categorias->pluck('produtos_count');

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
            'categoriasLabels',
            'categoriasTotais'
        ));
    }
}
