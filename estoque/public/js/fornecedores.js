$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    const tabelaFornecedores = $('#tabela-fornecedores').DataTable(window.getDataTableDefaults({
        processing: true,
        serverSide: true,
        ajax: {
            url: window.routes.fornecedoresIndex,
            error: function(xhr) {
                if (xhr.status === 401 || xhr.status === 419) {
                    window.location.href = '/login';
                }
            }
        },
        columns: [
            { data: 'razao_social', name: 'razao_social' },
            { data: 'nome_fantasia', name: 'nome_fantasia', defaultContent: '-' },
            { data: 'cnpj', name: 'cnpj' },
            { data: 'email', name: 'email', defaultContent: '-' },
            { data: 'telefone', name: 'telefone', defaultContent: '-' },
            {
                data: 'acoes', name: 'acoes', orderable: false, searchable: false,
                render: function(data) {
                    const $acoes = $('<div>').html(data || '');
                    if (!window.userPermissions.canEdit) $acoes.find('.btn-editar').remove();
                    if (!window.userPermissions.canDelete) $acoes.find('.btn-excluir').remove();
                    return $acoes.html();
                }
            }
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

                window.swalPadrao.fire({
                    icon: 'success',
                    title: 'Sucesso!',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false,
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
                    window.swalPadrao.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: 'Ocorreu um erro ao processar a requisição.',
                        showConfirmButton: false,
                        timer: 2000
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

        window.swalPadrao.fire({
            title: 'Excluir Fornecedor?',
            text: "Esta ação não poderá ser desfeita!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#D63327',
            cancelButtonColor: '#333333',
            confirmButtonText: 'Sim, excluir',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/api/fornecedores/${id}`,
                    method: 'DELETE',
                    success: function(response) {
                        tabelaFornecedores.ajax.reload(null, false);

                        window.swalPadrao.fire({
                            icon: 'success',
                            title: 'Excluído!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function() {
                        window.swalPadrao.fire({
                            icon: 'error',
                            title: 'Erro!',
                            text: 'Não foi possível excluir o fornecedor.',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                });
            }
        });
    });
});
