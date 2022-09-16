(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('Select').select2();

        var monthList = null;
        var $fiscalYear = $('#fiscalYearId');
        var $reportType = $('#reportType');
        var $groupVariable = $('#groupVariable');
        var $table = $('#table');
        var map = {};
        var salaryData;
        var $companyId = $('#companyId');
        var $groupId = $('#groupId');
        var groupList = [];
        var data = document.data;
        var getGroupListLink = data['links']['getGroupListLink'];
        var salarySheetList = data['salarySheetList'];
        var selectedSalarySheetList = [];


        var initKendoGrid = function (defaultColumns, data) {
            let dataSchemaCols = {};
            let aggredCols = [];
            $table.empty();
            map = {
                'EMPLOYEE_CODE': 'Employee Code',
                'FULL_NAME': 'Employee',
                'POSITION_NAME': 'Position',
                'LOCATION_EDESC': 'Loacation',
                'EMPLOYEE_TYPE': 'Employee Type',
                'ANNUAL_INCOME': 'Income',
                'ANNUAL_CIT': 'CIT',
                'ANNUAL_EPF_DEDUCTION': 'EPF Deduction',
                'ANNUAL_CIT_EPF': 'Sum CIT + EPF',
                'EXTRA_AMOUNT': 'Extra Amount',
                'ANNUAL_TAXABLE_AMOUNT': 'Taxable amount',
                'TEN_PER': '10% Tax',
                'TWENTY_PER': '20% Tax',
                'THIRTY_PER': '30% Tax',
                'THIRTY_SIX_PER': '36% Tax',
                'TAX_NEED_TO_BE': 'Tax to be Deducted',
                'TAX_IN_SYSTEM': 'Tax Deducted',
                'TAX_DIFF': 'Tax Difference',
            }
            var columns = [
                {field: "EMPLOYEE_CODE", title: "Code", width: 80, locked: true},
                {field: "FULL_NAME", title: "Employee", width: 120, locked: true},
                {field: "POSITION_NAME", title: "Position", width: 100, locked: true},
                {field: "LOCATION_EDESC", title: "Location", width: 100, locked: true},
                {field: "EMPLOYEE_TYPE", title: "Employee Type", width: 80, locked: true},
                {field: "ANNUAL_INCOME", title: "Income", width: 90, locked: false},
                {field: "EMPLOYEE_ID", title: "View Details",width: 90, locked: false, template : `
                <span>
                    <a class="btn btn-icon-only btn-success viewBtn" id="viewBtn" style = "height:17px; width13px;" title="view">
                    <i class="fa fa-search-plus"></i>
                </span>`},
                {field: "ANNUAL_CIT", title: "CIT", width: 90, locked: false},
                {field: "ANNUAL_EPF_DEDUCTION", title: "EPF Deduction", width: 90, locked: false},
                {field: "ANNUAL_CIT_EPF", title: "Sum CIT + EPF", width: 90, locked: false},
                {field: "EXTRA_AMOUNT", title: "Extra Amount", width: 90, locked: false},
                {field: "ANNUAL_TAXABLE_AMOUNT", title: "Taxable amount", width: 90, locked: false},
                {field: "TEN_PER", title: "10% Tax", width: 90, locked: false},
                {field: "TWENTY_PER", title: "20% Tax", width: 90, locked: false},
                {field: "THIRTY_PER", title: "30% Tax", width: 90, locked: false},
                {field: "THIRTY_SIX_PER", title: "36% Tax", width: 90, locked: false},
                {field: "TAX_NEED_TO_BE", title: "Tax to be Deducted", width: 90, locked: false},
                {field: "TAX_IN_SYSTEM", title: "Tax Deducted", width: 90, locked: false},
                {field: "TAX_DIFF", title: "Tax Difference", width: 90, locked: false}
            ];

            // $.each(defaultColumns, function (index, value) {
            //     columns.push({
            //         field: value['VARIANCE'],
            //         title: value['VARIANCE_NAME'],
            //         width: 80,
            //         aggregates: ["sum"],
            //         //footerTemplate: "#=sum||''#"
            //         footerTemplate: "#=kendo.toString(sum,'0.00')#"

            //     });
            //     map[value['VARIANCE']] = value['VARIANCE_NAME'];
            //     dataSchemaCols[value['VARIANCE']] = {type: "number"};
            //     aggredCols.push({field: value['VARIANCE'], aggregate: "sum"});
            // });

            $table.kendoGrid({
                dataSource: {
                    data: data,
                    schema: {
                        model: {
                            fields: dataSchemaCols
                        }
                    },
                    pageSize: 20,
                    aggregate: aggredCols
                },
                toolbar: ["excel"],
                excel: {
                    fileName: "Employee Wise Group Sheet Report.xlsx",
                    filterable: false,
                    allPages: true
                },
                excelExport: function (e) {
                    var rows = e.workbook.sheets[0].rows;
                    var columns = e.workbook.sheets[0].columns;
                    const salaryTypes = document.salaryType;
                    const salaryType = salaryTypes.filter(salaryType => salaryType.SALARY_TYPE_ID == selectedSalarySheetList[0].SALARY_TYPE_ID);

                    if (document.preference != undefined) {
                        if (document.preference.companyAddress != null) {
                            rows.unshift({
                                cells: [
                                    {
                                        value: document.preference.companyAddress,
                                        colSpan: columns.length,
                                        textAlign: "left"
                                    }
                                ]
                            });
                        }
                    }
                    if (document.preference != undefined) {
                        if (document.preference.companyName != null) {
                            rows.unshift({
                                cells: [
                                    {value: document.preference.companyName, colSpan: columns.length, textAlign: "left"}
                                ]
                            });
                        }
                    }
                },
                height: 550,
                scrollable: true,
                sortable: true,
                groupable: true,
                filterable: true,
                pageable: {
                    refresh: true,
                    pageSizes: true,
                    input: true,
                    numeric: false
                },
                columns: columns
            });

        }

        app.searchTable($table, ['EMPLOYEE_CODE', 'FULL_NAME']);

        $('#searchEmployeesBtn').on('click', function () {
            var q = document.searchManager.getSearchValues();
            q['fiscalId'] = $fiscalYear.val();
            q['groupVariable'] = $groupVariable.val();
            q['groupId'] = $groupId.val();

            app.serverRequest(document.pullFinalReconcilaionSheetLink, q).then(function (response) {
                if (response.success) {
                    salaryData = response.data;
                    initKendoGrid(response.columns, response.data);
                }
                //app.renderKendoGrid($table, response.data);
                else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });
        });

        $('#excelExport').on('click', function () {
            app.excelExport($table, map, 'Final Reconcilation Sheet.xlsx');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, map, 'Final Reconcilation Sheet.pdf', 'A1');
        });

        var groupChangeFn = function () {
            let selectedGroup = $groupId.val();

            if (selectedGroup == null) {
                let allGroup = [];
                $.each(groupList, function (key, value) {
//                    console.log(value);
                    allGroup.push(value['GROUP_ID']);
                });
                selectedGroup = allGroup;
            }
            // let selectedSalaryTypeId = $salaryTypeId.val();
//            console.log('gval', $(this).val());
        }

        $companyId.on('change', function () {
            groupChangeFn();
        });

        // $salaryTypeId.on('change', function () {
        //     groupChangeFn();
        // });

        (function ($groupId, link) {
            var onDataLoad = function (data) {
                groupList = data;
                app.populateSelect($groupId, groupList, 'GROUP_ID', 'GROUP_NAME', 'Select Group');
            };
            app.serverRequest(link, {}).then(function (response) {
                if (response.success) {
                    onDataLoad(response.data);
                }
            }, function (error) {

            });
        })($groupId, getGroupListLink);

        $groupId.select2();
        // $salaryTypeId.select2();

        $groupId.on("select2:select select2:unselect", function (event) {
            groupChangeFn();
        });

        $(document).on('click','.viewBtn',function(){
            $( "#modal1" ).empty();
            var row = $(this).closest("tr");
            var grid = $table.data("kendoGrid");
            var dataItem = grid.dataItem(row);
            console.log(dataItem);
            app.serverRequest(document.getEmployeeAdditionBreakDown, { 
                fiscalYear: dataItem['FISCAL_YEAR'],
                employeeId: dataItem['EMPLOYEE_ID']
            }).then(function (response) {
                var html = '<h3 align="center">All addition breakdown</h3><table class="table table-striped header-fixed"><tr><th>Employee Code</th><th>Employee Name</th><th>Pay Head</th><th>Amount</th><th style="width:80px;">Details</th></tr>';
                for(var i in response.data){
                    html+="<tr><td>"+response.data[i].EMPLOYEE_CODE+"<input hidden id='idEmployee' class = 'idEmployee' value="+response.data[i].EMPLOYEE_ID+"><input  hidden id='idFiscal' class = 'idFiscal' value="+response.data[i].FISCAL_YEAR+"></td><td>"+response.data[i].FULL_NAME+"</td><td>"+response.data[i].PAY_EDESC+"<input  hidden id='idPay' class = 'idPay' value="+response.data[i].PAY_ID+"></td><td>"+response.data[i].VAL+"</td><td><a class='btn btn-icon-only btn-success viewBtn2' id='viewBtn2' style = 'height:23px; width:50px;' title='view'> <i class='fa fa-search-plus'></i></td></tr>";
                }
                html+="</table>";
                $( "#modal1" ).append(html);
                $( "#modal-toggle1" ).click();
            },function (error) {
                console.log(error);
            });
                
        });

        $(document).on('click','.viewBtn2',function(){
            $( "#modal2" ).empty();
            var pay_id = $(this).closest("tr").find("td input.idPay").val();
            var employee_id = $(this).closest("tr").find("td input.idEmployee").val();
            var fiscal_id = $(this).closest("tr").find("td input.idFiscal").val();
            // var grid = $table.data("kendoGrid");
            // var dataItem = grid.dataItem(row);
            console.log(pay_id);
            console.log(employee_id);

            app.serverRequest(document.getEmployeeSubDetail, { 
                payId: pay_id,
                employeeId: employee_id,
                fiscalYear: fiscal_id
            }).then(function (response) {
                console.log(response.data);
                var html = '<h3 align="center">Details</h3><table class="table table-striped header-fixed"><tr><th>Employee Name</th><th>Month</th><th>Salary Type</th><th>Pay Detail</th><th>Value</th></tr>';
                for(var i in response.data){
                    html+="<tr><td>"+response.data[i].FULL_NAME+"</td><td>"+response.data[i].MONTH_EDESC+"</td><td>"+response.data[i].SALARY_TYPE_NAME+"</td><td>"+response.data[i].PAY_EDESC+"</td><td>"+response.data[i].VAL+"</td></tr>";
                }
                html+="</table>";
                $( "#modal2" ).append(html);
                $( "#modal-toggle2" ).click();
            },function (error) {
                console.log(error);
            });
                
        });


    });
})(window.jQuery, window.app);


