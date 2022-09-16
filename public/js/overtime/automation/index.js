(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#table');
        var actiontemplateConfig = {
            update: {
                'ALLOW_UPDATE': document.acl.ALLOW_UPDATE,
                'params': ["COMPULSORY_OVERTIME_ID"],
                'url': document.editLink
            },
            delete: {
                'ALLOW_DELETE': document.acl.ALLOW_DELETE,
                'params': ["COMPULSORY_OVERTIME_ID"],
                'url': document.deleteLink
            }
        };
        var columns = [
            {field: "COMPULSORY_OT_DESC", title: "Description", width: 150},
            {field: "START_DATE", title: "From Date", width: 150},
            {field: "END_DATE", title: "To Date", width: 150},
            {field: "EARLY_OVERTIME_HR", title: "Early OT Hour", width: 150},
            {field: "LATE_OVERTIME_HR", title: "Late OT Hour", width: 150},
            {field: ["COMPULSORY_OVERTIME_ID"], title: "Action", width: 120, template: app.genKendoActionTemplate(actiontemplateConfig)}
        ];
        var map = {
            'COMPULSORY_OT_DESC': 'Description',
            'START_DATE': 'From Date',
            'END_DATE': 'To Date',
            'EARLY_OVERTIME_HR': 'Early OT Hour',
            'LATE_OVERTIME_HR': 'Late OT Hour',
        }
        app.initializeKendoGrid($table, columns, detailInit);

        app.searchTable($table, ['COMPULSORY_OT_DESC']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, map, 'OT List.xlsx');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, map, 'OT List.pdf');
        });

        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });

        function detailInit(e) {
            app.serverRequest(document.assignListLink, {
                compulsoryOvertimeId: e.data.COMPULSORY_OVERTIME_ID,
            }).then(function (success) {
                $("<div/>", {
                    class: "",
                    css: {
                        float: "left",
                        padding: "0px",
                    }
                }).appendTo(e.detailCell).kendoGrid({
                    toolbar: ["excel"],
                    excel: {
                        fileName: (e.data.COMPULSORY_OT_DESC || "Desc undefined") + ".xlsx"
                    },
                    dataSource: {
                        data: success.data,
                        pageSize: 10,
                    },
                    scrollable: false,
                    sortable: false,
                    pageable: false,
                    columns:
                            [
                                {field: "EMPLOYEE_NAME", title: "Employee"},
                                {field: "COMPANY_NAME", title: "Company"},
                                {field: "BRANCH_NAME", title: "Branch"},
                                {field: "DEPARTMENT_NAME", title: "Department"},
                            ]
                }).data("kendoGrid");
            }, function (failure) {
                console.log(failure);
            });
        }
    });
})(window.jQuery);