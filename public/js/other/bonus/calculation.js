(function ($) {
    'use strict';
    $(document).ready(function () {
        $("#bonusTable").kendoGrid({
            excel: {
                fileName: "BonusList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.bonusList,
                page: 1,
            },
            height: 450,
            scrollable: true,
            sortable: true,
            filterable: true,
            pageable: true,
            rowTemplate: kendo.template($("#rowTemplate").html()),
            columns: [
                {field: "EMPLOYEE_NAME", title: "Employee Name", width: 100},
                {field: "BONUS_AMOUNT", title: "BONUS_AMOUNT", width: 200},
            ],
        });
        $("#export").click(function (e) {
            var grid = $("#gradeChangeTable").data("kendoGrid");
            grid.saveAsExcel();
        });
    });
})(window.jQuery);