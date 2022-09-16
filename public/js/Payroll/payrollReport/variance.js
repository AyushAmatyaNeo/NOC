(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('Select').select2();
        console.log(document.columnList);

        var monthList = null;
        var $fiscalYear = $('#fiscalYearId');
        var $month = $('#monthId');
        var previousColumns = [];
        var currentColumns = [];
        var $table = $('#table');
        var map = {
            'EMPLOYEE_CODE': 'Employee Code',
            'FULL_NAME': 'Employee',
            'DEPARTMENT_NAME': 'Department',
        }
        var exportType = {
            "PRE_ACCOUNT": "STRING",
            "CUR_ACCOUNT": "STRING"
        };


        function initKendoGrid(data){
            let varianceData=[];
            
            $.each(data, function (index, value)
            {
                let tempPreTot=0;
                let tempCurTot=0;
                $.each(document.columnList.addition, function (i, v)
                {
                    let tempPreVal=(value[v['PREV']])?value[v['PREV']]:0;
                    let tempCurVal=(value[v['CURR']])?value[v['CURR']]:0;
                    tempPreTot+=parseFloat(tempPreVal);
                    tempCurTot+=parseFloat(tempCurVal);
                });
                data[index]['P_TOTAL']=tempPreTot;
                data[index]['C_TOTAL']=tempCurTot;
                data[index]['D_TOTAL']=tempPreTot-tempCurTot;
                
                if ((tempPreTot - tempCurTot) != 0 ||
                value['ADDRESS_REMARKS'] != "Not Changed" || value['ACCOUNT_REMARKS'] != "Not Changed")
                {
                    varianceData.push(value);
                }

            });
            
            let dataSchemaCols = {};
            let aggredCols = [];
            previousColumns=[];
            currentColumns=[];
            var columns = [
                {field: "EMPLOYEE_CODE", title: "Code", width: 80, locked: true},
                {field: "FULL_NAME", title: "Employee", width: 120, locked: true},
                {field: "DEPARTMENT_NAME", title: "Department", width: 120, locked: true},
                {title: "Previous", columns: previousColumns},
                {title: "Current", columns: currentColumns}
            ];
            $table.empty();

            $.each(document.columnList.previous, function (index, value) {
                previousColumns.push({
                    field: value['VARIANCE'],
                    title: value['VARIANCE_NAME'],
                    width: 100,
//                    aggregates: ["sum"],
//                    footerTemplate: "#=sum||''#"
                });
                map[value['VARIANCE']] = 'Previous_' + value['VARIANCE_NAME'];
//                dataSchemaCols[value['VARIANCE']] = {type: "number"};
//                aggredCols.push({field: value['VARIANCE'], aggregate: "sum"});
            });
            
             previousColumns.push({
                    field: "P_TOTAL",
                    title: "Total",
                    width: 100,
                    aggregates: ["sum"],
                    footerTemplate: "#=sum||''#"
                });
                map["P_TOTAL"] = 'Previous_' + 'Total';
                dataSchemaCols["P_TOTAL"] = {type: "number"};
                aggredCols.push({field: "P_TOTAL", aggregate: "sum"});
    
            $.each(document.columnList.current, function (index, value) {
                currentColumns.push({
                    field: value['VARIANCE'],
                    title: value['VARIANCE_NAME'],
                    width: 100,
//                    aggregates: ["sum"],
//                    footerTemplate: "#=sum||''#"
                });
//                map[value['VARIANCE']] = 'Current_' + value['VARIANCE_NAME'];
//                dataSchemaCols[value['VARIANCE']] = {type: "number"};
//                aggredCols.push({field: value['VARIANCE'], aggregate: "sum"});
            });
            
            currentColumns.push({
                    field: "C_TOTAL",
                    title: "Total",
                    width: 100,
                    aggregates: ["sum"],
                    footerTemplate: "#=sum||''#"
                });
                map["C_TOTAL"] = 'Previous_' + 'Total';
                dataSchemaCols["C_TOTAL"] = {type: "number"};
                aggredCols.push({field: "C_TOTAL", aggregate: "sum"});
    
            $.each(document.columnList.difference, function (index, value) {
                columns.push({
                    field: value['VARIANCE'],
                    title: value['VARIANCE_NAME'],
                    width: 100,
//                    aggregates: ["sum"],
//                    footerTemplate: "#=sum||''#"
                });
//                map[value['VARIANCE']] = 'Difference_' + value['VARIANCE_NAME'];
//                dataSchemaCols[value['VARIANCE']] = {type: "number"};
//                aggredCols.push({field: value['VARIANCE'], aggregate: "sum"});
            });
            
            columns.push({
                    field: "D_TOTAL",
                    title: "Variance",
                    width: 100,
                    aggregates: ["sum"],
                    footerTemplate: "#=sum||''#"
                });
                map["D_TOTAL"] = 'Variance';
                dataSchemaCols["D_TOTAL"] = {type: "number"};
                aggredCols.push({field: "D_TOTAL", aggregate: "sum"});
            
    
            map['PRE_ADDRESS'] = 'Previous Address';
            map['CUR_ADDRESS'] = 'Address';
            map['ADDRESS_REMARKS'] = 'Address Remarks';
            map['PRE_ACCOUNT'] = 'Previous A/C';
            map['CUR_ACCOUNT'] = 'A/C';
            map['ACCOUNT_REMARKS'] = 'A/C Remarks';
            columns.push(
                    {field: "PRE_ADDRESS", title: "Previous Address", width: 120},
                    {field: "CUR_ADDRESS", title: "Address", width: 120},
                    {field: "ADDRESS_REMARKS", title: "Address Remarks", width: 120},
                    {field: "PRE_ACCOUNT", title: "Previous A/C", width: 120},
                    {field: "CUR_ACCOUNT", title: "A/C", width: 120},
                    {field: "ACCOUNT_REMARKS", title: "A/C Remarks", width: 120}
            );

            $table.kendoGrid({
                toolbar: ["excel"],
                excel: {
                    fileName: "Variance Report.xlsx",
                    filterable: false,
                    allPages: true
                },
                // pdf: {
                //     fileName: "Variance Report.pdf",
                //     allPages: true,
                //     paperSize: "A4",
                //     margin: { top: "2cm", left: "1cm", right: "1cm", bottom: "1cm" },
                //     landscape: true,
                //     repeatHeaders: true,
                //     template: $("#page-template").html(),
                //     scale: 0.8
                // },
                excelExport: function(e) {
                var rows = e.workbook.sheets[0].rows;
                var columns = e.workbook.sheets[0].columns;
                
                rows.unshift({
                    cells: [
                    {value: "Variance Report", colSpan: columns.length, textAlign: "left"}
                    ]
                });
                if(document.preference != undefined){
                    if(document.preference.companyAddress != null){
                        rows.unshift({
                            cells: [
                            {value: document.preference.companyAddress, colSpan: columns.length, textAlign: "left"}
                            ]
                        });
                    }
                }
                if(document.preference != undefined){
                    if(document.preference.companyName != null){
                        rows.unshift({
                            cells: [
                            {value: document.preference.companyName, colSpan: columns.length, textAlign: "left"}
                            ]
                        });
                    }
                }
            },
                dataSource: {
                    data: varianceData,
                    schema: {
                        model: {
                            fields: dataSchemaCols
                        }
                    },
                    pageSize: 20,
                    aggregate: aggredCols
                },
                height: 550,
                scrollable: true,
                sortable: true,
                groupable: true,
                filterable: true,
                pageable: {
                    input: true,
                    numeric: false
                },
                columns: columns
            });

        }

        app.setFiscalMonth($fiscalYear, $month, function (years, months, currentMonth) {
            monthList = months;
        });

        $('#searchEmployeesBtn').on('click', function () {
            var q = document.searchManager.getSearchValues();
            q['monthId'] = $month.val();
            document.month = $("#monthId option:selected").text();
            document.fiscalYear = $("#fiscalYearId option:selected").text();
            app.serverRequest(document.pullVarianceListLink, q).then(function (response) {
                if (response.success) {
                    // app.renderKendoGrid($table, response.data);
                    initKendoGrid(response.data);
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });
        });



        $('#excelExport').on('click', function () {
            app.excelExport($table, map, 'VairanceReport.xlsx', exportType);
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, map, 'VairanceReport.pdf');
        });
    });
})(window.jQuery, window.app);


