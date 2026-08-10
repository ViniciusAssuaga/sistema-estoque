// public/js/datatables-defaults.js (ou no local correto que o Blade lê)
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
            url: 'https://cdn.jsdelivr.net/npm/datatables.net-plugins@1.13.6/i18n/pt-BR.json',
            processing: '<div class="spinner-custom"></div>'
        }
    }, customOptions);
};
