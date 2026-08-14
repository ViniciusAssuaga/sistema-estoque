$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    const tabelaFornecedores = $('#tabela-fornecedores').DataTable(window.getDataTableDefaults({
        processing: true,
        serverSide: true,
        ajax: window.routes.fornecedoresIndex,
        columns: [
            { data: 'razao_social', name: 'razao_social' },
            { data: 'nome_fantasia', name: 'nome_fantasia', defaultContent: '-' },
            { data: 'cnpj', name: 'cnpj' },
            { data: 'email', name: 'email', defaultContent: '-' },
            { data: 'telefone', name: 'telefone', defaultContent: '-' },
            { data: 'acoes', name: 'acoes', orderable: false, searchable: false }
        ],
        order: [[0, 'asc']]
    }));

    $('#btnNovoFornecedor').on('click', function() {
        $('#formFornecedor')[0].reset();
        $('#fornecedor_id').val('');
        $('#alertErros').addClass('d-none');
        $('#listaErros').empty();
        $('#modalTitulo').html('Cadastrar Novo <span class="text-laravel">Fornecedor</span>');
        $('#btnSalvar').text('Salvar Fornecedor');
        $('#modalFornecedor').modal('show');
    });

    $('#formFornecedor').on('submit', function(e) {
        e.preventDefault();

        const id = $('#fornecedor_id').val();
        const isEdit = id !== '';
        const url = isEdit ? `/api/fornecedores/${id}` : window.routes.fornecedoresStore;
        const method = isEdit ? 'PUT' : 'POST';

        $('#alertErros').addClass('d-none');
        $('#listaErros').empty();

        const $btn = $('#btnSalvar');
        const textoOriginal = $btn.text();
        $btn.prop('disabled', true).text('Salvando...');

        $.ajax({
            url: url,
            method: method,
            data: $(this).serialize(),
            success: function(response) {
                $('#modalFornecedor').modal('hide');
                $btn.prop('disabled', false).text(textoOriginal);
                tabelaFornecedores.ajax.reload(null, false);

                Swal.fire({
                    icon: 'success',
                    title: 'Sucesso!',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false,
                    background: '#1E1E1E',
                    color: '#E0E0E0'
                });
            },
            error: function(xhr) {
                $btn.prop('disabled', false).text(textoOriginal);

                if (xhr.status === 422) {
                    const erros = xhr.responseJSON.errors;
                    $.each(erros, function(key, messages) {
                        $.each(messages, function(i, msg) {
                            $('#listaErros').append('<li>' + msg + '</li>');
                        });
                    });
                    $('#alertErros').removeClass('d-none');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: 'Ocorreu um erro ao processar a requisição.',
                        background: '#1E1E1E',
                        color: '#E0E0E0'
                    });
                }
            }
        });
    });

    $(document).on('click', '.btn-editar', function() {
        const id = $(this).data('id');

        $('#alertErros').addClass('d-none');
        $('#listaErros').empty();

        $.get(`/api/fornecedores/${id}`, function(res) {
            let fornecedor = res.data ? res.data : res;

            // Preenche os campos do modal
            $('#fornecedor_id').val(fornecedor.id);
            $('#razao_social').val(fornecedor.razao_social);
            $('#nome_fantasia').val(fornecedor.nome_fantasia);
            $('#cnpj').val(fornecedor.cnpj);
            $('#email').val(fornecedor.email);
            $('#telefone').val(fornecedor.telefone);

            $('#modalTitulo').html('Editar <span class="text-laravel">Fornecedor</span>');
            $('#btnSalvar').text('Atualizar Fornecedor');
            $('#modalFornecedor').modal('show');
        });
    });

    $(document).on('click', '.btn-excluir', function() {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Excluir Fornecedor?',
            text: "Esta ação não poderá ser desfeita!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#D63327',
            cancelButtonColor: '#333333',
            confirmButtonText: 'Sim, excluir',
            cancelButtonText: 'Cancelar',
            background: '#1E1E1E',
            color: '#E0E0E0'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/api/fornecedores/${id}`,
                    method: 'DELETE',
                    success: function(response) {
                        tabelaFornecedores.ajax.reload(null, false);

                        Swal.fire({
                            icon: 'success',
                            title: 'Deletado!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false,
                            background: '#1E1E1E',
                            color: '#E0E0E0'
                        });
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro!',
                            text: 'Não foi possível excluir o fornecedor.',
                            background: '#1E1E1E',
                            color: '#E0E0E0'
                        });
                    }
                });
            }
        });
    });
});
