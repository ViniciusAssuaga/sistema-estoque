$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    const tabelaClientes = $('#tabela-clientes').DataTable({
        processing: true,
        serverSide: true,
        dom: "<'row mb-3 align-items-center'<'col-sm-12 col-md-3'l><'col-sm-12 col-md-4'f><'col-sm-12 col-md-5 d-flex justify-content-end'p>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row mt-3 align-items-center'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 d-flex justify-content-end'p>>",
        ajax: window.routes.clientesIndex,
        lengthMenu: [
            [10, 20, 50, 100, 1000, 20000],
            [10, 20, 50, 100, 1000, 20000]
        ],
        columns: [
            { data: 'nome', name: 'nome' },
            { data: 'email', name: 'email' },
            { data: 'telefone', name: 'telefone' },
            { data: 'cpf_cnpj', name: 'cpf_cnpj' },
            { data: 'acoes', name: 'acoes', orderable: false, searchable: false, className: 'text-end' }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json',
            processing: '<div class="spinner-custom"></div>'
        },
        order: [[0, 'asc']]
    });

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

        Swal.fire({
            title: 'Excluir Cliente?',
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
                    url: `/clientes/${id}`,
                    method: 'DELETE',
                    success: function(response) {
                        tabelaClientes.ajax.reload(null, false);

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
                            text: 'Não foi possível excluir o cliente.',
                            background: '#1E1E1E',
                            color: '#E0E0E0'
                        });
                    }
                });
            }
        });
    });
});
