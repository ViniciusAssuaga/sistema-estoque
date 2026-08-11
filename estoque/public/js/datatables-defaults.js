window.getDataTableDefaults = function(customOptions = {}) {
    return $.extend(true, {
        processing: true,
        serverSide: true,
        dom: "<'row mb-3 align-items-center'<'col-sm-12 col-md-3'l><'col-sm-12 col-md-4'f><'col-sm-12 col-md-5 d-flex justify-content-end'p>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row mt-3 align-items-center'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 d-flex justify-content-end'p>>",
        lengthMenu: [
            [10, 20, 50, 100, 1000, 20000],
            [10, 20, 50, 100, 1000, 20000]
        ],
        language: {
            processing: '<div class="spinner-custom"></div>',
            search: "Pesquisar:",
            lengthMenu: "Mostrar _MENU_ registros",
            info: "Mostrando de _START_ até _END_ de _TOTAL_ registros",
            infoEmpty: "Mostrando 0 até 0 de 0 registros",
            infoFiltered: "(Filtrados de _MAX_ registros no total)",
            loadingRecords: "Carregando...",
            zeroRecords: "Nenhum registro encontrado",
            emptyTable: "Nenhum dado disponível na tabela",
            paginate: {
                first: "Primeiro",
                previous: "Anterior",
                next: "Próximo",
                last: "Último"
            },
            aria: {
                sortAscending: ": ativar para ordenar a coluna de forma ascendente",
                sortDescending: ": ativar para ordenar a coluna de forma descendente"
            }
        }
    }, customOptions);
};

// Faz com que qualquer inicialização de DataTable herde esses padrões automaticamente
if (typeof jQuery !== 'undefined' && $.fn.dataTable) {
    $.extend(true, $.fn.dataTable.defaults, window.getDataTableDefaults());
}
