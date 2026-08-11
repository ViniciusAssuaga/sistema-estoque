@extends('layouts.app')

@section('title', 'Clientes')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="container-fluid p-4">
    <!-- CABEÇALHO -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-0">Gerenciamento de <span class="text-laravel">Clientes</span></h1>
            <small class="text-secondary">Painel Server-Side com Laravel e DataTables</small>
        </div>
        <button class="btn btn-laravel px-4 py-2" id="btnNovoCliente">
            + Novo Cliente
        </button>
    </div>

    <!-- TABELA -->
    <div class="card shadow-lg rounded-3">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="tabela-clientes" class="table table-hover align-middle w-100">
                    <thead class="table-dark-custom">
                        <tr>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Telefone</th>
                            <th>CPF/CNPJ</th>
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
<div class="modal fade" id="modalCliente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white border-secondary">
            <form id="formCliente">
                <input type="hidden" id="cliente_id">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title" id="modalTitulo">Cadastrar Novo <span class="text-laravel">Cliente</span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-start">
                    <div id="alertErros" class="alert alert-danger d-none bg-dark text-danger border-danger">
                        <ul id="listaErros" class="mb-0"></ul>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary">Nome</label>
                        <input type="text" name="nome" id="nome" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-secondary">E-mail</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-secondary">Telefone</label>
                        <input type="text" name="telefone" id="telefone" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-secondary">CPF/CNPJ</label>
                        <input type="text" name="cpf_cnpj" id="cpf_cnpj" class="form-control">
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-laravel btn-sm" id="btnSalvar">Salvar Cliente</button>
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
            clientesIndex: "{{ route('clientes.index') }}",
            clientesStore: "{{ route('clientes.store') }}"
        };
    </script>
    <script src="{{ asset('js/datatables-defaults.js') }}"></script>
    <script src="{{ asset('js/clientes.js') }}"></script>
@endpush
