$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // 1. Inicializa a Tabela
    const tabelaMovimentacoes = $('#tabela-movimentacoes').DataTable(window.getDataTableDefaults({
        processing: true,
        serverSide: true,
        ajax: {
            url: window.routes.movimentacoesIndex,
            error: function (xhr) {
                if (xhr.status === 401 || xhr.status === 419) {
                    window.location.href = '/login';
                }
            },
        },
        columns: [
            {
                data: 'id',
                name: 'id',
                type: 'num',
                render: function (data, type, row) {
                    if (type === 'sort' || type === 'type') {
                        return data;
                    }
                    return `#${data}`;
                }
            },
            {
                data: 'created_at',
                name: 'created_at',
                render: function (data, type, row) {
                    if (!data) return '-';

                    if (type === 'sort' || type === 'type') {
                        return data;
                    }

                    const dataObj = new Date(data);
                    return dataObj.toLocaleString('pt-BR', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit',
                        timeZone: 'UTC'
                    });
                }
            },
            { data: 'produto_nome', name: 'produto_nome' },
            {
                data: 'tipo',
                name: 'tipo',
                render: function (data) {
                    return data === 'entrada' ?
                        '<span class="badge bg-success">Entrada</span>' :
                        '<span class="badge bg-danger">Saída</span>';
                }
            },
            { data: 'quantidade', name: 'quantidade' },
            { data: 'observacao', name: 'observacao', defaultContent: '-' }
        ],
        order: [[1, 'desc']]
    }));

    let buscaProdutosTimeout;

    // 2. Botão Nova Movimentação
    $('#btnNovaMovimentacao').on('click', function () {
        $('#formMovimentacao')[0].reset();
        $('#produto_id').val('');
        $('#alertErros').addClass('d-none');
        $('#listaErros').empty();
        $('#lista-sugestoes').hide();
        $('#modalMovimentacao').modal('show');
    });

    // 3. Lógica do Autocomplete do Produto
    $('#produto_busca').on('input', function () {
        const termo = $(this).val().toLowerCase();
        const $sugestoes = $('#lista-sugestoes');

        if (termo.trim() === '') {
            $('#produto_id').val('');
            $sugestoes.hide().empty();
            return;
        }

        clearTimeout(buscaProdutosTimeout);
        buscaProdutosTimeout = setTimeout(function () {
            $.get(window.routes.produtosListarJson, { q: termo }, function (produtos) {
                $sugestoes.empty();
                if (produtos.length === 0) {
                    const $itemVazio = $('<div>', {
                        class: 'list-group-item bg-dark text-muted border-secondary disabled',
                        text: 'Nenhum produto encontrado'
                    });
                    $sugestoes.append($itemVazio);
                    $sugestoes.show();
                    return;
                }

                produtos.forEach(function (produto) {
                    const $item = $('<button>', {
                        type: 'button',
                        class: 'list-group-item list-group-item-action bg-dark text-white border-secondary item-produto'
                    }).attr({ 'data-id': produto.id, 'data-nome': produto.nome });
                    $item.append($('<span>').text(produto.nome));
                    $item.append($('<small>', { class: 'text-muted' }).text(` (Estoque: ${produto.quantidade_estoque})`));
                    $sugestoes.append($item);
                });
                $sugestoes.show();
            });
        }, 250);
    });

    $(document).on('click', '.item-produto', function () {
        const id = $(this).data('id');
        const nome = $(this).data('nome');
        $('#produto_id').val(id);
        $('#produto_busca').val(nome);
        $('#lista-sugestoes').hide().empty();
    });

    $('#produto_busca').on('blur', function () {
        setTimeout(() => {
            if ($('#produto_id').val() === '') {
                $(this).val('');
            }
            $('#lista-sugestoes').hide();
        }, 200);
    });

    // 4. Submit do formulário
    $('#formMovimentacao').on('submit', function (e) {
        e.preventDefault();

        if (!$('#produto_id').val()) {
            window.swalPadrao.fire({
                icon: 'warning',
                title: 'Atenção',
                text: 'Selecione um produto válido da lista.',
                showConfirmButton: false,
                timer: 2000
            });
            return;
        }

        $('#alertErros').addClass('d-none');
        $('#listaErros').empty();

        const $btn = $('#btnSalvar');
        const textoOriginal = $btn.text();
        $btn.prop('disabled', true).text('Salvando...');

        $.ajax({
            url: window.routes.movimentacoesStore,
            method: 'POST',
            data: $(this).serialize(),
            success: function (response) {
                $('#modalMovimentacao').modal('hide');
                $btn.prop('disabled', false).text(textoOriginal);
                tabelaMovimentacoes.ajax.reload(null, false);

                window.swalPadrao.fire({
                    icon: 'success',
                    title: 'Sucesso!',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            error: function (xhr) {
                $btn.prop('disabled', false).text(textoOriginal);
                if (xhr.status === 422) {
                    const erros = xhr.responseJSON.errors;
                    $.each(erros, function (key, messages) {
                        $.each(messages, function (i, msg) {
                            $('#listaErros').append('<li>' + msg + '</li>');
                        });
                    });
                    $('#alertErros').removeClass('d-none');
                } else {
                    window.swalPadrao.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: xhr.responseJSON?.message || 'Ocorreu um erro ao processar a movimentação.',
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
            }
        });
    });
});
