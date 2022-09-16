(function ($, app) {
    'use strict';
    $(document).ready(function () {
        app.datePickerWithNepali('fromDate', 'nepaliFromDate');
        var reportData;

        var $table = $("#grid");
        var $fromDate = $("#fromDate");

        app.searchTable($table, ['FULL_NAME', 'EMPLOYEE_CODE']);
        $("#searchFieldDiv").hide();

        app.getServerDate().then(function (response) {
            $fromDate.val(response.data.serverDate);
            $('#nepaliFromDate').val(nepaliDatePickerExt.fromEnglishToNepali(response.data.serverDate));
        });

        $("#generate").on("click", function () {
            var date1 = $("#fromDate").val();
            if(date1 == -1 || date1 == null || date1 == ''){
                app.showMessage('Date not selected', 'warning');
                return;
            }
            var date2 = date1;
            app.serverRequest('', {
                date1: date1,
                date2: date2
            }).then(function (response) {
                if (response.success) {
                    reportData = response.data;
                    generateReport();
                    $("#searchFieldDiv").show();
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });
        });

        function generateReport() {
            $("#grid").kendoGrid({
                toolbar: ["excel", "pdf"],
                excel: {
                    fileName: "Daily Expense Report.xlsx",
                    filterable: true
                },
                dataSource: {
                    data: reportData,
                    schema: {
                        model: {
                            fields: {
                                EMPLOYEE_CODE: {type: "string"},
                                FULL_NAME: {type: "string"},
                                EXPENSE: {type: "number"},
                                STATUS: {type: "string"}
                            }
                        }
                    },
                    pageSize: 20,
                    aggregate: [
                        {field: "EXPENSE", aggregate: "sum"}
                    ]
                },
                height: 550,
                scrollable: true,
                sortable: true,
                groupable: true,
                filterable: true,
                pageable: {
                    pageSize: 15,
                    pageSizes: true
                },
                columns: [
                    {field: "EMPLOYEE_CODE", title: "Code", width: "100px"},
                    {field: "FULL_NAME", title: "Employee", width: "100px", footerTemplate: "Total"},
                    {field: "EXPENSE", title: "Expense", width: "100px", aggregates: ["sum"], footerTemplate: "#=kendo.toString(sum, '0.00')||''#"},
                    {field: "STATUS", title: "Status", width: "100px"}
                ]
            });
        } 
    });
})(window.jQuery, window.app);
