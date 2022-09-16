(function ($) {
    'use strict';
    $(document).ready(function () {
        $("#lifeInsuranceTable").kendoGrid({
            excel: {
                fileName: "LifeInsuranceList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.lifeInsuranceList,
                page: 1,
            },
            height: 450,
            scrollable: true,
            sortable: true,
            filterable: true,
            pageable: true,
            rowTemplate: kendo.template($("#rowTemplate").html()),
            columns: [
                {field: "SN", title: "SN", width: 100},
                {field: "INSURANCE_COMPANY", title: "Insurance Company", width: 200},
                {field: "INSURANCE_AMOUNT", title: "Insurance Amount", width: 180},
                {field: "IMPACT_ON_TAX", title: "Impact on Tax", width: 100},
                {field: "TYPE", title: "Type", width: 140}
            ],
        });
        $("#export").click(function (e) {
            var grid = $("#workforceTable").data("kendoGrid");
            grid.saveAsExcel();
        });
    });
})(window.jQuery);