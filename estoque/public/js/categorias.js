$(document).ready(function() {
    // Configuração padrão do SweetAlert2 em modo Dark
    const swalDark = Swal.mixin({
        background: '#212529',
        color: '#ffffff',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d'
    });

    var table = $('#tabela-categorias').DataTable(
        window.getDataTableDefaults({
            // Mudar para URL relativa
            ajax: {
                url: '/api/categorias',
                error: function(xhr) {
                    if (xhr.status === 401 || xhr.status === 419) {
                        window.location.href = '/login';
                    }
                }
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'nome', name: 'nome' },
                { data: 'descricao', name: 'descricao', defaultContent: '-' },
                {
                    data: 'acoes', name: 'acoes', orderable: false, searchable: false, className: 'text-center',
                    render: function(data) {
                        const $acoes = $('<div>').html(data || '');
                        if (!window.userPermissions.canEdit) $acoes.find('.btn-editar').remove();
                        if (!window.userPermissions.canDelete) $acoes.find('.btn-excluir').remove();
                        return $acoes.html();
                    }
                }
            ]
        })
    );

    // Abrir modal para nova categoria
    $('#btnNovaCategoria').click(function() {
        $('#formCategoria')[0].reset();
        $('#categoria_id').val('');
        $('#modalTitulo').html('Cadastrar Nova <span class="text-laravel">Categoria</span>');
        $('#alertErros').addClass('d-none');
        $('#listaErros').empty();
        $('#modalCategoria').modal('show');
    });

    // Salvar ou Atualizar
    $('#formCategoria').submit(function(e) {
        e.preventDefault();

        let id = $('#categoria_id').val();
        // Mudar para URL relativa no POST
        let url = id ? `/api/categorias/${id}` : '/api/categorias';
        let method = id ? 'PUT' : 'POST';

        let btnSalvar = $('#btnSalvar');
        let textoOriginal = btnSalvar.html();
        
        btnSalvar.prop('disabled', true).html('Salvando...');

        $('#alertErros').addClass('d-none');
        $('#listaErros').empty();

        $.ajax({
            url: url,
            type: method,
            data: $(this).serialize(),
            success: function(response) {
                $('#modalCategoria').modal('hide');
                table.ajax.reload(null, false);
                swalDark.fire({
                    icon: 'success',
                    title: 'Sucesso!',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    let lista = $('#listaErros');
                    
                    $.each(errors, function(key, value) {
                        lista.append(`<li>${value[0]}</li>`);
                    });
                    
                    $('#alertErros').removeClass('d-none');
                } else {
                    swalDark.fire({
                        icon: 'error',
                        title: 'Erro!',
                        // Previne o "Cannot read properties of undefined (reading 'message')" caso dê erro de rede/servidor
                        text: xhr.responseJSON?.message || 'Ocorreu um erro inesperado.'
                    });
                }
            },
            complete: function() {
                btnSalvar.prop('disabled', false).html(textoOriginal);
            }
        });
    });

    // Editar
    $(document).on('click', '.btn-editar', function() {
        let id = $(this).data('id');

        $.get(`/api/categorias/${id}`, function(data) {
            $('#categoria_id').val(data.id);
            $('#nome').val(data.nome);
            $('#descricao').val(data.descricao);
            $('#modalTitulo').html('Editar <span class="text-laravel">Categoria</span>');
            $('#alertErros').addClass('d-none');
            $('#listaErros').empty();
            $('#modalCategoria').modal('show');
        }).fail(function() {
            swalDark.fire('Erro', 'Não foi possível carregar os dados.', 'error');
        });
    });

    // Excluir
    $(document).on('click', '.btn-excluir', function() {
        let id = $(this).data('id');

        swalDark.fire({
            title: 'Tem certeza?',
            text: "Esta ação não poderá ser revertida!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/api/categorias/${id}`,
                    type: 'DELETE',
                    success: function(response) {
                        table.ajax.reload(null, false);
                        swalDark.fire('Excluído!', response.message, 'success');
                    },
                    error: function(xhr) {
                        swalDark.fire('Erro!', xhr.responseJSON?.message || 'Erro ao excluir.', 'error');
                    }
                });
            }
        });
    });
});
