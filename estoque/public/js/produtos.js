$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.money').mask('#.##0,00', { reverse: true });

    $('#sku').on('input', function() {
        this.value = this.value.toUpperCase();
    });

    const tabela = $('#tabela-produtos').DataTable({
        processing: true,
        serverSide: true,
        dom: "<'row mb-3 align-items-center'<'col-sm-12 col-md-3'l><'col-sm-12 col-md-4'f><'col-sm-12 col-md-5 d-flex justify-content-end'p>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row mt-3 align-items-center'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 d-flex justify-content-end'p>>",
        ajax: window.routes.produtosIndex,
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
        },
        lengthMenu: [
            [10, 20, 50, 100, 1000, 20000],
            [10, 20, 50, 100, 1000, 20000]
        ],
        columns: [
            { data: 'sku', name: 'sku' },
            { data: 'nome', name: 'nome' },
            { data: 'preco_custo_formatted', name: 'preco_custo' },
            { data: 'preco_venda_formatted', name: 'preco_venda' },
            { data: 'estoque_badge', name: 'quantidade_estoque' },
            { data: 'status_badge', name: 'ativo' },
            { data: 'acoes', name: 'acoes', orderable: false, searchable: false }
        ],
        language: {
            url: 'https://cdn.jsdelivr.net/npm/datatables.net-plugins@1.13.6/i18n/pt-BR.json',
            processing: '<div class="spinner-custom"></div>'
        },
        order: [[1, 'asc']]
    });

    $('#btnNovoProduto').on('click', function() {
        $('#formProduto')[0].reset();
        $('#produto_id').val('');
        $('#alertErros').addClass('d-none');
        $('#listaErros').empty();
        $('#ativo').prop('checked', true);

        $('#modalTitulo').html('Cadastrar Novo <span class="text-laravel">Produto</span>');
        $('#btnSalvar').text('Salvar Produto');
        
        $('#modalProduto').modal('show');
    });

    $(document).on('click', '.btn-editar', function() {
        const id = $(this).data('id');
        
        $('#alertErros').addClass('d-none');
        $('#listaErros').empty();

        $.get(`/produtos/${id}/edit`, function(data) {
            $('#produto_id').val(data.id);
            $('#sku').val(data.sku);
            $('#nome').val(data.nome);
            
            $('#preco_custo').val(parseFloat(data.preco_custo).toLocaleString('pt-BR', { minimumFractionDigits: 2 }));
            $('#preco_venda').val(parseFloat(data.preco_venda).toLocaleString('pt-BR', { minimumFractionDigits: 2 }));
            
            $('#quantidade_estoque').val(data.quantidade_estoque);
            $('#estoque_minimo').val(data.estoque_minimo);
            $('#descricao').val(data.descricao);
            $('#ativo').prop('checked', Boolean(data.ativo));

            $('#modalTitulo').html('Editar <span class="text-laravel">Produto</span>');
            $('#btnSalvar').text('Atualizar Produto');

            $('#modalProduto').modal('show');
        });
    });

    $('#formProduto').on('submit', function(e) {
        e.preventDefault();

        const id = $('#produto_id').val();
        const isEdit = id !== '';
        
        const url = isEdit ? `/produtos/${id}` : window.routes.produtosStore;
        const method = isEdit ? 'PUT' : 'POST';

        $('#alertErros').addClass('d-none');
        $('#listaErros').empty();
        
        const $btn = $('#btnSalvar');
        const textoOriginal = $btn.text();

        $.ajax({
            url: url,
            method: method,
            data: $(this).serialize(),
            success: function(response) {
                $('#modalProduto').modal('hide');
                $btn.prop('disabled', false).text(textoOriginal);

                tabela.ajax.reload(null, false);

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

    $(document).on('click', '.btn-excluir', function() {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Excluir Produto?',
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
                    url: `/produtos/${id}`,
                    method: 'DELETE',
                    success: function(response) {
                        tabela.ajax.reload(null, false);

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
                            text: 'Não foi possível excluir o produto.',
                            background: '#1E1E1E',
                            color: '#E0E0E0'
                        });
                    }
                });
            }
        });
    });
});
