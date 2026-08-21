$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    const tabelaClientes = $('#tabela-clientes').DataTable(window.getDataTableDefaults({
        processing: true,
        serverSide: true,
        ajax: window.routes.clientesIndex,
        columns: [
            { data: 'nome', name: 'nome' },
            { data: 'email', name: 'email' },
            { data: 'telefone', name: 'telefone' },
            { data: 'cpf_cnpj', name: 'cpf_cnpj' },
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

    $('#btnNovoCliente').on('click', function() {
        $('#formCliente')[0].reset();
        $('#cliente_id').val('');
        $('#alertErros').addClass('d-none');
        $('#listaErros').empty();
        $('#modalTitulo').html('Cadastrar Novo <span class="text-laravel">Cliente</span>');
        $('#btnSalvar').text('Salvar Cliente');
        $('#modalCliente').modal('show');
    });

    $('#formCliente').on('submit', function(e) {
        e.preventDefault();

        const id = $('#cliente_id').val();
        const isEdit = id !== '';
        const url = isEdit ? `/clientes/${id}` : window.routes.clientesStore;
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
                $('#modalCliente').modal('hide');
                $btn.prop('disabled', false).text(textoOriginal);
                tabelaClientes.ajax.reload(null, false);

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

        $.get(`/clientes/${id}/edit`, function(data) {
            $('#cliente_id').val(data.id);
            $('#nome').val(data.nome);
            $('#email').val(data.email);
            $('#telefone').val(data.telefone);
            $('#cpf_cnpj').val(data.cpf_cnpj);

            $('#modalTitulo').html('Editar <span class="text-laravel">Cliente</span>');
            $('#btnSalvar').text('Atualizar Cliente');
            $('#modalCliente').modal('show');
        });
    });

    $(document).on('click', '.btn-excluir', function() {
        const id = $(this).data('id');

        window.swalPadrao.fire({
            title: 'Excluir Cliente?',
            text: "Esta ação não poderá ser desfeita!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#D63327',
            cancelButtonColor: '#333333',
            confirmButtonText: 'Sim, excluir',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const botaoConfirmar = Swal.getConfirmButton();
                botaoConfirmar.disabled = true;
                botaoConfirmar.textContent = 'Excluindo...';

                $.ajax({
                    url: `/clientes/${id}`,
                    method: 'DELETE',
                    success: function(response) {
                        tabelaClientes.ajax.reload(null, false);

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
                            text: 'Não foi possível excluir o cliente.',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                });
            }
        });
    });
});
