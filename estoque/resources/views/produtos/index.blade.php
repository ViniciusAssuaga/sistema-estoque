@extends('layouts.app')

@section('title', 'Produtos')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="container-fluid p-4">
    <!-- CABEÇALHO -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-0">Gerenciamento de <span class="text-laravel">Produtos</span></h1>
            <small class="text-secondary">Painel Server-Side com Laravel e DataTables</small>
        </div>
        <button class="btn btn-laravel px-4 py-2" id="btnNovoProduto">
            + Novo Produto
        </button>
    </div>

    <div class="card shadow-lg rounded-3">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="tabela-produtos" class="table table-hover align-middle w-100">
                    <thead class="table-dark-custom">
                        <tr>
                            <th>SKU</th>
                            <th>Nome</th>
                            <th>Preço Custo</th>
                            <th>Preço Venda</th>
                            <th>Qtd. Estoque</th>
                            <th>Status</th>
                            <th style="width: 140px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Dados AJAX Server-Side -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL ÚNICO (CADASTRAR E EDITAR) -->
<div class="modal fade" id="modalProduto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalTitulo">Cadastrar Novo <span class="text-laravel">Produto</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formProduto">
                <input type="hidden" id="produto_id" name="id">

                <div class="modal-body">
                    <div id="alertErros" class="alert alert-danger d-none">
                        <ul class="mb-0" id="listaErros"></ul>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="sku" class="form-label fw-semibold">SKU / Código <span class="text-laravel">*</span></label>
                            <input type="text" class="form-control text-uppercase" id="sku" name="sku" placeholder="Ex: PROD-999" required>
                        </div>
                        <div class="col-md-8">
                            <label for="nome" class="form-label fw-semibold">Nome do Produto <span class="text-laravel">*</span></label>
                            <input type="text" class="form-control" id="nome" name="nome" placeholder="Ex: Cadeira Ergonômica" required>
                        </div>

                        <div class="col-md-6">
                            <label for="preco_custo" class="form-label fw-semibold">Preço Custo (R$) <span class="text-laravel">*</span></label>
                            <input type="text" class="form-control money" id="preco_custo" name="preco_custo" placeholder="0,00" required>
                        </div>
                        <div class="col-md-6">
                            <label for="preco_venda" class="form-label fw-semibold">Preço Venda (R$) <span class="text-laravel">*</span></label>
                            <input type="text" class="form-control money" id="preco_venda" name="preco_venda" placeholder="0,00" required>
                        </div>

                        <div class="col-md-6">
                            <label for="quantidade_estoque" class="form-label fw-semibold">Qtd. Estoque <span class="text-laravel">*</span></label>
                            <input type="number" class="form-control" id="quantidade_estoque" name="quantidade_estoque" placeholder="10" required>
                        </div>
                        <div class="col-md-6">
                            <label for="estoque_minimo" class="form-label fw-semibold">Estoque Mínimo</label>
                            <input type="number" class="form-control" id="estoque_minimo" name="estoque_minimo" value="5">
                        </div>

                        <div class="col-12">
                            <label for="descricao" class="form-label fw-semibold">Descrição</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="2" placeholder="Informações técnicas..."></textarea>
                        </div>

                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="ativo" name="ativo" checked>
                                <label class="form-check-label fw-semibold" for="ativo">Produto Ativo</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-laravel" id="btnSalvar">Salvar Produto</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    
    <!-- Passando rotas do Laravel para o JS externo -->
    <script>
        window.routes = {
            produtosIndex: "{{ route('produtos.index') }}",
            produtosStore: "{{ route('produtos.store') }}"
        };
    </script>

    <!-- Chamando o arquivo JS externo -->
    <script src="{{ asset('js/produtos.js') }}"></script>
@endpush
