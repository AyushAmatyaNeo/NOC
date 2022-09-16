(function ($) {
    'use strict';
    $('#dataTable').kendoGrid({height: 400,
        groupable: false,
        sortable: true,
        pageable: {
            refresh: true,
            pageSizes: true,
            buttonCount: 5
        }});
    $('#dataTable1').kendoGrid({height: 300,
        groupable: false,
        sortable: true,
        pageable: {
            refresh: true,
            pageSizes: true,
            buttonCount: 5
        }});
    $('#dataTable2').kendoGrid({height: 300,
        groupable: false,
        sortable: true,
        pageable: {
            refresh: true,
            pageSizes: true,
            buttonCount: 5
        }});
    $('#dataTable3').kendoGrid({height: 300,
        groupable: false,
        sortable: true,
        pageable: {
            refresh: true,
            pageSizes: true,
            buttonCount: 5
        }});
})(window.jQuery);