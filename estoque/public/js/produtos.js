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

    // Se já existir uma instância prévia na mesma tabela, destrói antes de criar
    if ($.fn.DataTable.isDataTable('#tabela-produtos')) {
        $('#tabela-produtos').DataTable().destroy();
    }

    const tabela = $('#tabela-produtos').DataTable(window.getDataTableDefaults({
        ajax: {
            url: window.routes.produtosIndex,
            data: function (d) {
                d.categoria_id = $('#filtro_categoria').val(); // Envia o ID da categoria selecionada para o servidor
            }
        },
        columns: [
            { data: 'sku', name: 'sku' },
            { data: 'nome', name: 'nome' },
            { data: 'categoria_nome', name: 'categoria.nome' },
            { data: 'preco_custo_formatted', name: 'preco_custo' },
            { data: 'preco_venda_formatted', name: 'preco_venda' },
            { data: 'estoque_badge', name: 'quantidade_estoque' },
            { data: 'status_badge', name: 'ativo' },
            { data: 'acoes', name: 'acoes', orderable: false, searchable: false }
        ],
        order: [[1, 'asc']],
        initComplete: function(settings, json) {
            const selectHtml = `
                <label class="d-inline-flex align-items-center" style="margin-left: 100px !important; z-index: 10; margin-bottom: 0;">
                    Categoria:
                    <select id="filtro_categoria" class="form-select form-select-sm bg-dark text-white border-secondary ms-2" style="width: 180px;">
                        <option value="">Todas</option>
                        ${window.categoriasOptions || ''}
                    </select>
                </label>
            `;
            
            $('#tabela-produtos_wrapper .dataTables_length').addClass('d-flex align-items-center').append(selectHtml);
        }
    }));

    // Recarrega a tabela via Ajax sempre que o filtro de categoria for alterado
    $(document).on('change', '#filtro_categoria', function() {
        tabela.ajax.reload();
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
            $('#categoria_id').val(data.categoria_id);
            
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
        
        const $btn = $(this).find('button[type="submit"]');
        const textoOriginal = $btn.text();
        
        $btn.prop('disabled', true).text('Salvando...');

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
