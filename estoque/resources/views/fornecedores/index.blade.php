@extends('layouts.app')

@section('title', 'Fornecedores')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="container-fluid p-4">
    <!-- CABEÇALHO -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-0">Gerenciamento de <span class="text-laravel">Fornecedores</span></h1>
            <small class="text-secondary">Painel de Fornecedores via API REST</small>
        </div>
        @if(auth()->user()->canCreateRecords())
            <button class="btn btn-laravel px-4 py-2" id="btnNovoFornecedor">
                + Novo Fornecedor
            </button>
        @endif
    </div>

    <!-- TABELA -->
    <div class="card shadow-lg rounded-3">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="tabela-fornecedores" class="table table-hover align-middle w-100">
                    <thead class="table-dark-custom">
                        <tr>
                            <th>Razão Social</th>
                            <th>Nome Fantasia</th>
                            <th>CNPJ</th>
                            <th>E-mail</th>
                            <th>Telefone</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DE CADASTRO / EDIÇÃO -->
<div class="modal fade" id="modalFornecedor" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white border-secondary">
            <form id="formFornecedor">
                <input type="hidden" id="fornecedor_id">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title" id="modalTitulo">Cadastrar Novo <span class="text-laravel">Fornecedor</span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-start">
                    <div id="alertErros" class="alert alert-danger d-none">
                        <ul class="mb-0" id="listaErros"></ul>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Razão Social <span class="text-laravel">*</span></label>
                        <input type="text" name="razao_social" id="razao_social" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nome Fantasia</label>
                        <input type="text" name="nome_fantasia" id="nome_fantasia" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">CNPJ <span class="text-laravel">*</span></label>
                        <input type="text" name="cnpj" id="cnpj" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">E-mail</label>
                        <input type="email" name="email" id="email" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Telefone</label>
                        <input type="text" name="telefone" id="telefone" class="form-control">
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-laravel btn-sm" id="btnSalvar">Salvar Fornecedor</button>
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
            fornecedoresIndex: "/api/fornecedores",
            fornecedoresStore: "/api/fornecedores"
        };
    </script>
    <!-- Substituídos os assets com URL absoluta por caminhos relativos -->
    <script src="/js/datatables-defaults.js"></script>
    <script src="/js/fornecedores.js?v={{ time() }}"></script>
@endpush
