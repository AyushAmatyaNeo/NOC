(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        /**/
        var months = null;
        var selectedMonth = null;
        /**/
        var $year = $('#fiscalYearId');
        var $month = $('#monthId');
        var $table = $('#table');
        var $search = $('#search');

        $month.on('change', function () {
            changeGrid();
        });
        $search.on('click', function () {
            changeGrid();
        });

        app.setFiscalMonth($year, $month, function (yearList, monthList, currentMonth) {
            months = monthList;
        }, data.getFiscalYearMonthLink);

        var changeGrid = function () {
            var monthId = $month.val();

            if (monthId === null && monthId === '') {
                return;
            }

            fetchGridColumns({monthId: monthId}, function (kendoConfig) {
                if (typeof $table.data('kendoGrid') === 'undefined') {
                    $table.kendoGrid(kendoConfig);
                    app.searchTable($table, ['FULL_NAME']);
                } else {
                    $table.kendoGrid('destroy').empty();
                    $table.kendoGrid(kendoConfig);
                }
            });

        };
        var columns = null;
        var fields = null;
        var exportMap = null;
        var fetchGridColumns = function (q, fn) {
            app.serverRequest('', q).then(function (response) {
                var columnList = response.data;
                columns = [
                    {field: 'EMPLOYEE_CODE', title: 'Code', width: 70, locked: true},
                    {field: 'FULL_NAME', title: 'Employee', width: 150, locked: true},
                    {field: 'DEPARTMENT_NAME', title: 'Department', width: 120, locked: true}
                ];
                exportMap = [{'EMPLOYEE_CODE': "Code"}, {'FULL_NAME': "Employee"},{'DEPARTMENT_NAME':"Department"}];
                fields = {
                    'EMPLOYEE_CODE': {editable: false},
                    'FULL_NAME': {editable: false},
                    'DEPARTMENT_NAME': {editable: false},
                };
                $.each(columnList, function (k, v) {
                    columns.push({field: v['MONTH_DAY_FIELD'], title: v['MONTH_DAY_TITLE'], width: 50});
                    fields[v['MONTH_DAY_FIELD']] = {type: "number"};
                    exportMap[v['MONTH_DAY_FIELD']] = v['MONTH_DAY_TITLE'];
                });

                columns.push({field: 'ADDITION', title: 'Addition', width: 90});
                columns.push({field: 'DEDUCTION', title: 'Deduction', width: 90});
                exportMap.push({'ADDITION':'Addition'},
                    {'DEDUCTION': 'Deduction'});

                fn(getkendoConfig(columns, fields));

            }, function (error) {

            });
        }

        var getkendoConfig = function (c, f) {
            return {
                dataSource: {
                    transport: {
                        type: "json",
                        read: {
                            url: data.gridReadLink,
                            type: "POST",
                        },
                        update: {
                            url: data.gridUpdateLink,
                            type: "POST",
                        },
                        parameterMap: function (options, operation) {

                            if (operation === "read") {
                                selectedMonth = $month.val();
                                var q = document.searchManager.getSearchValues();
                                q['monthId'] = selectedMonth;
                                return q;
                            }
                            if (operation !== "read" && options.models) {
                                return {
                                    monthId: selectedMonth,
                                    models: kendo.stringify(options.models)};
                            }


                        }
                    },
                    batch: true,
                    schema: {
                        model: {
                            id: "EMPLOYEE_ID",
                            fields: f
                        }
                    },
                    pageSize: 20
                },
                pageable: true,
                height: 550,
                toolbar: ["excel","save", "cancel"],
                excel: {
                fileName: 'Manual Overtime',
                filterable: false,
                allPages: true
            },
                columns: c,
                editable: true
            };
        };

        $('#excelExport').on('click', function () {
            var month = app.findOneBy(months, {'MONTH_ID': selectedMonth})
            app.excelExport($table, exportMap, `OT Monthly  ${month['MONTH_EDESC']}.xlsx`);
        });
        $('#pdfExport').on('click', function () {
            var month = app.findOneBy(months, {'MONTH_ID': selectedMonth})
            app.exportToPDF($table, exportMap, `OT Monthly ${month['MONTH_EDESC']}.pdf`);
        });
        
//        $('#reset').on('click', function () {
//            $('.form-control').val("");
//        });
    });
})(window.jQuery, window.app);