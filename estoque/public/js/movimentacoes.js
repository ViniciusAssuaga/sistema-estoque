$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // 1. Inicializa a Tabela
    const tabelaMovimentacoes = $('#tabela-movimentacoes').DataTable(window.getDataTableDefaults({
        processing: true,
        serverSide: false,
        ajax: {
            url: window.routes.movimentacoesIndex,
            dataSrc: '' // Informa que o JSON retornado é um array direto
        },
        columns: [
            { data: 'id', name: 'id', render: (data) => `#${data}` },
            { 
                data: 'created_at',
                name: 'created_at',
                render: function(data) {
                    if (!data) return '-';
                    const dataObj = new Date(data);
                    return dataObj.toLocaleString('pt-BR', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit',
                        timeZone: 'UTC' // Ajusta para exibir o horário correto vindo do banco
                    });
                }
            },
            { data: 'produto.nome', name: 'produto.nome' },
            { 
                data: 'tipo', 
                name: 'tipo',
                render: function(data) {
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

    // Cache de produtos para evitar requisições excessivas (opcional, se a lista for muito grande pode buscar direto na API com filtro)
    let produtosCache = [];

    function carregarProdutosCache() {
        $.get(window.routes.produtosIndex, function(data) {
            produtosCache = data;
        });
    }

    // Carrega o cache ao iniciar a página
    carregarProdutosCache();

    // 2. Botão Nova Movimentação
    $('#btnNovaMovimentacao').on('click', function() {
        $('#formMovimentacao')[0].reset();
        $('#produto_id').val('');
        $('#alertErros').addClass('d-none');
        $('#listaErros').empty();
        $('#lista-sugestoes').hide();
        carregarProdutosCache(); // Atualiza o cache se necessário
        $('#modalMovimentacao').modal('show');
    });

    // 3. Lógica do Autocomplete do Produto
    $('#produto_busca').on('input', function() {
        const termo = $(this).val().toLowerCase();
        const $sugestoes = $('#lista-sugestoes');
        
        // Se limpou o campo, limpa o ID oculto
        if (termo.trim() === '') {
            $('#produto_id').val('');
            $sugestoes.hide().empty();
            return;
        }

        // Filtra os produtos do cache baseado no termo digitado
        const filtrados = produtosCache.filter(p => p.nome.toLowerCase().includes(termo));

        $sugestoes.empty();
        if (filtrados.length > 0) {
            filtrados.forEach(produto => {
                $sugestoes.append(`
                    <button type="button" class="list-group-item list-group-item-action bg-dark text-white border-secondary item-produto" data-id="${produto.id}" data-nome="${produto.nome}">
                        ${produto.nome} <small class="text-muted">(Estoque: ${produto.quantidade_estoque})</small>
                    </button>
                `);
            });
            $sugestoes.show();
        } else {
            $sugestoes.append('<div class="list-group-item bg-dark text-muted border-secondary">Nenhum produto encontrado</div>');
            $sugestoes.show();
        }
    });

    // Selecionar um produto da lista
    $(document).on('click', '.item-produto', function() {
        const id = $(this).data('id');
        const nome = $(this).data('nome');

        $('#produto_id').val(id);
        $('#produto_busca').val(nome);
        $('#lista-sugestoes').hide().empty();
    });

    // Se sair do campo sem selecionar (blur), valida se o campo oculto está vazio; se estiver, limpa o texto
    $('#produto_busca').on('blur', function() {
        // Usamos um setTimeout pequeno para permitir o clique na lista de sugestões antes de sumir
        setTimeout(() => {
            if ($('#produto_id').val() === '') {
                $(this).val('');
            }
            $('#lista-sugestoes').hide();
        }, 200);
    });

    // 4. Submit do formulário
    $('#formMovimentacao').on('submit', function(e) {
        e.preventDefault();

        // Validação extra de segurança caso o usuário tente burlar
        if (!$('#produto_id').val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Atenção',
                text: 'Selecione um produto válido da lista.',
                background: '#1E1E1E',
                color: '#E0E0E0'
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
            success: function(response) {
                $('#modalMovimentacao').modal('hide');
                $btn.prop('disabled', false).text(textoOriginal);
                tabelaMovimentacoes.ajax.reload(null, false);

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
                        text: xhr.responseJSON?.message || 'Ocorreu um erro ao processar a movimentação.',
                        background: '#1E1E1E',
                        color: '#E0E0E0'
                    });
                }
            }
        });
    });
});
