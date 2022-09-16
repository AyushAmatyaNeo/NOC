(function ($) {
    'use strict';
    $(document).ready(function () {
        $("#workforceTable").kendoGrid({
            excel: {
                fileName: "WorkforceList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.workforceList,
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
                {field: "BRANCH_NAME", title: "Branch Name", width: 200},
                {field: "DESIGNATION", title: "Designation", width: 180},
                {field: "DARBANDI", title: "Darbandi", width: 100},
                {field: "VACANT", title: "Vacant", width: 140}
            ],
        });
        $("#export").click(function (e) {
            var grid = $("#workforceTable").data("kendoGrid");
            grid.saveAsExcel();
        });
    });
})(window.jQuery);