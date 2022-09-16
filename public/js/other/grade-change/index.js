(function ($) {
    'use strict';
    $(document).ready(function () {
        $("#gradeChangeTable").kendoGrid({
            excel: {
                fileName: "GradeChangeList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.gradeChangeList,
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
                {field: "APPOINTMENT_DATE", title: "Appointment Date", width: 200},
                {field: "CURRENT_SERVICE", title: "Current Service", width: 180},
                {field: "EFFECTIVE_DATE", title: "Effective Date", width: 100},
                {field: "LAST_GRADE", title: "Last Grade", width: 140},
                {field: "NEW_GRADE", title: "New Grade", width: 140},
                {field: "GRADE_EFFECTIVE_DATE", title: "Grade Effective Date", width: 140},
                {title: "Action", width: 100}
            ],
        });
        $("#export").click(function (e) {
            var grid = $("#gradeChangeTable").data("kendoGrid");
            grid.saveAsExcel();
        });
    });
})(window.jQuery);