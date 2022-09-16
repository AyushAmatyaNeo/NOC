(function ($) {
    'use strict';
    $(document).ready(function () {
        $("#appraisalStageTable").kendoGrid({
            excel: {
                fileName: "AppraisalStageList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.stages,
                page: 1,
            },
            height: 450,
            scrollable: true,
            sortable: true,
            filterable: true,
            pageable: true,
            rowTemplate: kendo.template($("#rowTemplate").html()),
            columns: [
                {field: "STAGE_EDESC", title: "Stage(in Eng.)", width: 180},
                {field: "STAGE_NDESC", title: "Stage(in Nep.)", width: 180},
                {field: "ORDER_NO", title: "Order No.", width: 80},
//                {title: "Action",width:100}
            ],
        });

        app.searchTable('appraisalStageTable', ['STAGE_EDESC', 'STAGE_NDESC', 'ORDER_NO', 'START_DATE', 'END_DATE']);

        app.pdfExport(
                'appraisalStageTable',
                {
                    'STAGE_EDESC': 'Stage in Eng',
                    'STAGE_NDESC': 'Stage in Nep',
                    'ORDER_NO': 'Order No'
                });

        $("#export").click(function (e) {
            var grid = $("#appraisalStageTable").data("kendoGrid");
            grid.saveAsExcel();
        });
    });
})(window.jQuery);