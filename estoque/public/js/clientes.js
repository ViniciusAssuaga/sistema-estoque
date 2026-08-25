$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // --- REGRAS DE MÁSCARA E TAMANHO POR DDI ---
    const ddiRules = {
        '+55': {
            placeholder: '(00) 00000-0000',
            digits: [10, 11],
            applyMask: function ($el) {
                const behavior = function (val) {
                    return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
                };
                $el.mask(behavior, {
                    onKeyPress: function (val, e, field, options) {
                        field.mask(behavior.apply({}, arguments), options);
                    }
                });
            }
        },
        '+1': {
            placeholder: '(000) 000-0000',
            digits: [10],
            applyMask: function ($el) {
                $el.mask('(000) 000-0000');
            }
        },
        '+351': {
            placeholder: '000 000 000',
            digits: [9],
            applyMask: function ($el) {
                $el.mask('000 000 000');
            }
        },
        '+34': {
            placeholder: '000 00 00 00',
            digits: [9],
            applyMask: function ($el) {
                $el.mask('000 00 00 00');
            }
        },
        '+54': {
            placeholder: '9 00 0000-0000',
            digits: [10, 11],
            applyMask: function ($el) {
                const behavior = function (val) {
                    return val.replace(/\D/g, '').length === 11 ? '0 00 0000-0000' : '00 0000-00009';
                };
                $el.mask(behavior, {
                    onKeyPress: function (val, e, field, options) {
                        field.mask(behavior.apply({}, arguments), options);
                    }
                });
            }
        }
    };

    function atualizarMascaraTelefone() {
        const ddi = $('#ddi').val();
        const $tel = $('#telefone_numero');
        const rule = ddiRules[ddi] || ddiRules['+55'];

        $tel.unmask();
        $tel.attr('placeholder', rule.placeholder);
        rule.applyMask($tel);
    }

    // Atualiza máscara ao trocar a opção no select DDI
    $('#ddi').on('change', function () {
        $('#telefone_numero').val('');
        atualizarMascaraTelefone();
    });

    // Inicializa a máscara padrão do telefone
    atualizarMascaraTelefone();

    // --- MÁSCARA CPF / CNPJ ---
    const cpfCnpjMaskBehavior = function (val) {
        return val.replace(/\D/g, '').length <= 11 ? '000.000.000-009' : '00.000.000/0000-00';
    };
    const cpfCnpjOptions = {
        onKeyPress: function (val, e, field, options) {
            field.mask(cpfCnpjMaskBehavior.apply({}, arguments), options);
        }
    };
    $('#cpf_cnpj').mask(cpfCnpjMaskBehavior, cpfCnpjOptions);

    // Validador JS de CPF
    function validarCPF(cpf) {
        cpf = cpf.replace(/[^\d]+/g, '');
        if (cpf.length !== 11 || /^(\d)\1+$/.test(cpf)) return false;
        let add = 0;
        for (let i = 0; i < 9; i++) add += parseInt(cpf.charAt(i)) * (10 - i);
        let rev = 11 - (add % 11);
        if (rev === 10 || rev === 11) rev = 0;
        if (rev !== parseInt(cpf.charAt(9))) return false;
        add = 0;
        for (let i = 0; i < 10; i++) add += parseInt(cpf.charAt(i)) * (11 - i);
        rev = 11 - (add % 11);
        if (rev === 10 || rev === 11) rev = 0;
        return rev === parseInt(cpf.charAt(10));
    }

    // Validador JS de CNPJ
    function validarCNPJ(cnpj) {
        cnpj = cnpj.replace(/[^\d]+/g, '');
        if (cnpj.length !== 14 || /^(\d)\1+$/.test(cnpj)) return false;
        let tamanho = cnpj.length - 2;
        let numeros = cnpj.substring(0, tamanho);
        let digitos = cnpj.substring(tamanho);
        let soma = 0;
        let pos = tamanho - 7;
        for (let i = tamanho; i >= 1; i--) {
            soma += numeros.charAt(tamanho - i) * pos--;
            if (pos < 2) pos = 9;
        }
        let resultado = soma % 11 < 2 ? 0 : 11 - (soma % 11);
        if (resultado !== parseInt(digitos.charAt(0))) return false;
        tamanho = tamanho + 1;
        numeros = cnpj.substring(0, tamanho);
        soma = 0;
        pos = tamanho - 7;
        for (let i = tamanho; i >= 1; i--) {
            soma += numeros.charAt(tamanho - i) * pos--;
            if (pos < 2) pos = 9;
        }
        resultado = soma % 11 < 2 ? 0 : 11 - (soma % 11);
        return resultado === parseInt(digitos.charAt(1));
    }

    // --- DATATABLE ---

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
                render: function (data) {
                    const $acoes = $('<div>').html(data || '');
                    if (!window.userPermissions.canEdit) $acoes.find('.btn-editar').remove();
                    if (!window.userPermissions.canDelete) $acoes.find('.btn-excluir').remove();
                    return $acoes.html();
                }
            }
        ],
        order: [[0, 'asc']]
    }));

    // --- MANIPULAÇÃO DO MODAL ---

    $('#btnNovoCliente').on('click', function () {
        $('#formCliente')[0].reset();
        $('#cliente_id').val('');
        $('#ddi').val('+55');
        atualizarMascaraTelefone();
        $('#telefone_numero').val('');
        if ($('#telefone').length) $('#telefone').val('');
        $('#alertErros').addClass('d-none');
        $('#listaErros').empty();
        $('#modalTitulo').html('Cadastrar Novo <span class="text-laravel">Cliente</span>');
        $('#btnSalvar').text('Salvar Cliente');
        $('#modalCliente').modal('show');
    });

    $('#formCliente').on('submit', function (e) {
        e.preventDefault();

        // Limpa erros anteriores
        $('#alertErros').addClass('d-none');
        $('#listaErros').empty();

        const ddi = $('#ddi').val();
        const numTel = $('#telefone_numero').val().trim();
        const apenasNumerosTel = numTel.replace(/\D/g, '');
        const rule = ddiRules[ddi] || ddiRules['+55'];

        // Validação dinâmica do tamanho do número por DDI no Front
        if (numTel !== '') {
            if (!rule.digits.includes(apenasNumerosTel.length)) {
                $('#listaErros').append(`<li>O telefone para o DDI ${ddi} deve conter ${rule.digits.join(' ou ')} dígitos.</li>`);
                $('#alertErros').removeClass('d-none');
                return;
            }
            // Sincroniza o valor completo no input oculto "telefone" se existir
            if ($('#telefone').length) {
                $('#telefone').val(`${ddi} ${numTel}`);
            }
        } else {
            if ($('#telefone').length) $('#telefone').val('');
        }

        // Validação JS de CPF/CNPJ antes de enviar
        const cpfCnpj = $('#cpf_cnpj').val().replace(/[^\d]+/g, '');
        if (cpfCnpj !== '') {
            let valido = false;
            if (cpfCnpj.length === 11) valido = validarCPF(cpfCnpj);
            else if (cpfCnpj.length === 14) valido = validarCNPJ(cpfCnpj);

            if (!valido) {
                $('#listaErros').append('<li>O CPF/CNPJ digitado é inválido.</li>');
                $('#alertErros').removeClass('d-none');
                return;
            }
        }

        const id = $('#cliente_id').val();
        const isEdit = id !== '';
        const url = isEdit ? `/clientes/${id}` : window.routes.clientesStore;
        const method = isEdit ? 'PUT' : 'POST';

        const $btn = $('#btnSalvar');
        const textoOriginal = $btn.text();
        $btn.prop('disabled', true).text('Salvando...');

        $.ajax({
            url: url,
            method: method,
            data: $(this).serialize(),
            success: function (response) {
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
                        text: 'Ocorreu um erro ao processar a requisição.',
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
            }
        });
    });

    $(document).on('click', '.btn-editar', function () {
        const id = $(this).data('id');

        $('#alertErros').addClass('d-none');
        $('#listaErros').empty();

        $.get(`/clientes/${id}/edit`, function (data) {
            $('#cliente_id').val(data.id);
            $('#nome').val(data.nome);
            $('#email').val(data.email);

            if (data.telefone) {
                const partes = data.telefone.trim().split(' ');
                if (partes.length > 1 && partes[0].startsWith('+')) {
                    const ddiSalvo = partes[0];
                    const numSalvo = partes.slice(1).join(' ');

                    if ($(`#ddi option[value="${ddiSalvo}"]`).length > 0) {
                        $('#ddi').val(ddiSalvo);
                    } else {
                        $('#ddi').val('+55');
                    }

                    atualizarMascaraTelefone();
                    $('#telefone_numero').val(numSalvo).trigger('input');
                    if ($('#telefone').length) $('#telefone').val(data.telefone);
                } else {
                    $('#ddi').val('+55');
                    atualizarMascaraTelefone();
                    $('#telefone_numero').val(data.telefone).trigger('input');
                    if ($('#telefone').length) $('#telefone').val(`+55 ${data.telefone}`);
                }
            } else {
                $('#ddi').val('+55');
                atualizarMascaraTelefone();
                $('#telefone_numero').val('');
                if ($('#telefone').length) $('#telefone').val('');
            }

            $('#cpf_cnpj').val(data.cpf_cnpj).trigger('input');

            $('#modalTitulo').html('Editar <span class="text-laravel">Cliente</span>');
            $('#btnSalvar').text('Atualizar Cliente');
            $('#modalCliente').modal('show');
        });
    });

    $(document).on('click', '.btn-excluir', function () {
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
                    success: function (response) {
                        tabelaClientes.ajax.reload(null, false);

                        window.swalPadrao.fire({
                            icon: 'success',
                            title: 'Excluído!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function () {
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
