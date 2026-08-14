@extends('layouts.app')

@section('title', 'Movimentações')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="container-fluid p-4">
    <!-- CABEÇALHO -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-0">Gerenciamento de <span class="text-laravel">Movimentações</span></h1>
            <small class="text-secondary">Controle de Entradas e Saídas via API REST</small>
        </div>
        <button class="btn btn-laravel px-4 py-2" id="btnNovaMovimentacao">
            + Nova Movimentação
        </button>
    </div>

    <!-- TABELA -->
    <div class="card shadow-lg rounded-3">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="tabela-movimentacoes" class="table table-hover align-middle w-100">
                    <thead class="table-dark-custom">
                        <tr>
                            <th>ID</th>
                            <th>Data / Hora</th>
                            <th>Produto</th>
                            <th>Tipo</th>
                            <th>Quantidade</th>
                            <th>Observação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- O DataTables vai preencher aqui via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DE CADASTRO -->
<div class="modal fade" id="modalMovimentacao" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white border-secondary">
            <form id="formMovimentacao">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title">Registrar Nova <span class="text-laravel">Movimentação</span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-start">
                    <div id="alertErros" class="alert alert-danger d-none">
                        <ul class="mb-0" id="listaErros"></ul>
                    </div>

                    <div class="mb-3 position-relative">
                        <label class="form-label fw-semibold">Produto <span class="text-laravel">*</span></label>
                        <input type="text" id="produto_busca" class="form-control bg-dark text-white border-secondary" placeholder="Digite para buscar o produto..." autocomplete="off">
                        <input type="hidden" name="produto_id" id="produto_id" required>

                        <div id="lista-sugestoes" class="list-group position-absolute w-100 shadow-lg" style="z-index: 1000; max-height: 200px; overflow-y: auto; display: none;"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tipo <span class="text-laravel">*</span></label>
                        <select name="tipo" id="tipo" class="form-select bg-dark text-white border-secondary" required>
                            <option value="entrada">Entrada</option>
                            <option value="saida">Saída</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Quantidade <span class="text-laravel">*</span></label>
                        <input type="number" name="quantidade" id="quantidade" class="form-control" min="1" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Observação</label>
                        <textarea name="observacao" id="observacao" class="form-control" rows="2" placeholder="Ex: Compra de lote / Venda..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-laravel btn-sm" id="btnSalvar">Salvar Movimentação</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <script>
        window.routes = {
            movimentacoesIndex: "/api/movimentacoes",
            movimentacoesStore: "/api/movimentacoes",
            produtosIndex: "/api/produtos"
        };
    </script>
    <script src="{{ asset('js/datatables-defaults.js') }}"></script>
    <script src="{{ asset('js/movimentacoes.js') }}?v={{ time() }}"></script>
@endpush
