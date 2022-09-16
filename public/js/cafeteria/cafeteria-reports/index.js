(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();

        var $employeeTable = $('#employeeTable');
        var $search = $('#search');
        var $time = $("#time");
        
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, false);
        var map = {};
        
        function generateEmployeeWiseReport(reportData) {
            $employeeTable.kendoGrid({
                toolbar: ["excel"],
                excel: {
                    fileName: "Department Wise Attendance Report.xlsx",
                    filterable: true,
                    allPages: true
                },
                dataSource: {
                    data: reportData,
                    //pageSize: 20,
                    group: { field: "FULL_NAME" , aggregates:[
                        { field: "QUANTITY", aggregate: "sum" },
                        { field: "TOTAL_AMOUNT", aggregate: "sum" }
                    ]},
                    aggregate: [ 
                        { field: "QUANTITY", aggregate: "sum" },
                        { field: "TOTAL_AMOUNT", aggregate: "sum" }
                    ],
                    schema:{
                        model: {
                            fields: {
                                QUANTITY: { type: "number" },
                                TOTAL_AMOUNT: { type: "number" }
                            }
                        }
                    },
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
                columns: [
                    //{field: "LOG_DATE", title: "Log Date", width: "50px"},
                    {field: "MENU_NAME", title: "Menu Name", width: "50px", groupFooterTemplate: "Total"},
                    {field: "QUANTITY", title: "Quantity", width: "50px", aggregates: ["sum"], groupFooterTemplate: "#=sum#"},
                    {field: "TOTAL_AMOUNT", title: "Total Amount", width: "50px", aggregates: ["sum"], groupFooterTemplate: "#=sum#"}
                ]
            });
        } 
        
        function parseDate(rawDate) {
            return new Date(rawDate);   
        }
        
        function generateEmployeeDateWiseReport(reportData) {
            $employeeTable.kendoGrid({
                toolbar: ["excel"],
                excel: {
                    fileName: "Department Wise Attendance Report.xlsx",
                    filterable: true,
                    allPages: true
                },
                dataSource: {
                    data: reportData,
                    //pageSize: 20,
                    sort: { field: "LOG_DATE" },
                    
                    group: [{ field: "FULL_NAME" , aggregates:[
                        { field: "QUANTITY", aggregate: "sum" },
                        { field: "TOTAL_AMOUNT", aggregate: "sum" }
                    ]},
                    { field: "LOG_DATE" , aggregates:[
                        { field: "QUANTITY", aggregate: "sum" },
                        { field: "TOTAL_AMOUNT", aggregate: "sum" }
                    ]}],
                    aggregate: [ 
                        { field: "QUANTITY", aggregate: "sum" },
                        { field: "TOTAL_AMOUNT", aggregate: "sum" }
                    ],
                    schema:{
                        model: {
                            fields: {
                                QUANTITY: { type: "number" },
                                TOTAL_AMOUNT: { type: "number" },
                                LOG_DATE: { type: "date", parse: parseDate }
                            }
                        }
                    }
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
                columns: [
                    //{field: "LOG_DATE", title: "Log Date", width: "50px"},
                    {field: "MENU_NAME", title: "Menu Name", width: "50px", groupFooterTemplate: "Total"},
                    {field: "QUANTITY", title: "Quantity", width: "50px", aggregates: ["sum"], groupFooterTemplate: "#=sum#"},
                    {field: "TOTAL_AMOUNT", title: "Total Amount", width: "50px", aggregates: ["sum"], groupFooterTemplate: "#=sum#"}
                ]
            });
        } 
        
        function generateMenuConsumptionReport(reportData) {
            $employeeTable.kendoGrid({
                toolbar: ["excel"],
                excel: {
                    fileName: "Department Wise Attendance Report.xlsx",
                    filterable: true,
                    allPages: true
                },
                dataSource: {
                    data: reportData,
                    //pageSize: 20,
                    aggregate: [ 
                        { field: "QUANTITY", aggregate: "sum" },
                        { field: "TOTAL_AMOUNT", aggregate: "sum" }
                    ],
                    schema:{
                        model: {
                            fields: {
                                QUANTITY: { type: "number" },
                                TOTAL_AMOUNT: { type: "number" }
                            }
                        }
                    },
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
                columns: [
                    //{field: "LOG_DATE", title: "Log Date", width: "50px"},
                    {field: "MENU_NAME", title: "Menu Name", width: "50px", footerTemplate: "Total"},
                    {field: "QUANTITY", title: "Quantity", width: "50px", aggregates: ["sum"], footerTemplate: "#=sum#"},
                    {field: "TOTAL_AMOUNT", title: "Amount", width: "50px", aggregates: ["sum"], footerTemplate: "#=sum#"}
                ]
            });
        } 
        
        function generateEmployeeWiseSummary(reportData){
            var data="";
            data+='<table class="table">';
            data+='<tr><th>Employee Code</th><th>Employee Name</th><th>Amount</th><th>Remarks</th></tr>';
            for(let i = 0; i < reportData.length; i++){
                data+='<tr><td>'+reportData[i].EMPLOYEE_CODE+'</td><td>'+reportData[i].FULL_NAME+'</td><td>'+reportData[i].TOTAL+'</td></tr>';
            }
            data+='</table>';
            $employeeTable.append(data);
        }
        
        function generateEmployeeCalendar(reportData, columns){
              $employeeTable.kendoGrid({
                toolbar: ["excel"],
                excel: {
                    fileName: "Department Wise Attendance Report.xlsx",
                    filterable: true,
                    allPages: true
                },
                dataSource: {
                    data: reportData,
                    //pageSize: 20,
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

        app.populateSelect($time, document.timeList, 'TIME_ID', 'TIME_NAME');
        
        $('#excelExport').on('click', function () {
                $employeeTable.table2excel({
                    exclude: ".noExl",
                    name: "Employee-Wise-Summary",
                    filename: "Employee Wise Summary" 
                });
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($employeeTable, map, 'Employee List.pdf');
        });

//        $("#reportType").change(function(){
//            $("#reportType").val() == 1 ? $(".actions").hide(): $(".actions").show() ;
//        });

        $search.on('click', function () {
            $employeeTable.empty();
            var data = document.searchManager.getSearchValues();
            data['time'] = $time.val();
            data['toDate'] = $("#toDate").val();
            data['reportType'] = $("#reportType").val();
            data['fromDate'] = $("#fromDate").val();
            if(data['fromDate'] == ''){ alert('From Date is required.'); return false; }
            data['payType'] = $("#payType").val();
            
            app.serverRequest('', data).then(function (response) {
                if (response.success) {
                    if(data['reportType'] == 1){
                        generateEmployeeWiseReport(response.data);
                        map = {
                            "EMPLOYEE_CODE" : "EMPLOYEE_CODE",
                            "FULL_NAME" : "FULL_NAME",
                            "MENU_NAME" : "MENU_NAME",
                            "QUANTITY" : "QUANTITY",
                            "TOTAL_AMOUNT" : "TOTAL_AMOUNT",
                        };
                    }
                 else if(data['reportType'] == 2){
                    generateEmployeeWiseSummary(response.data);
                    map = {
                        "EMPLOYEE_CODE" : "EMPLOYEE_CODE",
                        "FULL_NAME" : "FULL_NAME",
                        "AMOUNT" : "AMOUNT",
                        "REMARKS" : "REMARKS"
                    };
                }
                 else if(data['reportType'] == 3){
                    generateEmployeeDateWiseReport(response.data);
                        map = {
                            "EMPLOYEE_CODE" : "EMPLOYEE_CODE",
                            "LOG_DATE" : "LOG_DATE",
                            "FULL_NAME" : "FULL_NAME",
                            "MENU_NAME" : "MENU_NAME",
                            "QUANTITY" : "QUANTITY",
                            "TOTAL_AMOUNT" : "TOTAL_AMOUNT",
                        };
                }
                 else if(data['reportType'] == 4){
                    generateMenuConsumptionReport(response.data);
                        map = {
                            "MENU_NAME" : "MENU_NAME",
                            "QUANTITY" : "QUANTITY",
                            "TOTAL" : "TOTAL"
                        };
                }
                 else if(data['reportType'] == 5){
                    var columns = [];
                    
                    for(let i in response.data[0]){
                        if(i == 'FULL_NAME'){
                            columns.push({field: i, title: "Employee", width: "150px"});
                            continue;
                        }
                        columns.push({field: i, title: i.replace(/_/g, "-").replace(/DATE-/g, ""), width: "150px"});
                    }
                    columns.push({field: "TOTAL", title: "Total", width: "150px"});
                    
                    for (let i = 0; i < response.data.length; i++) {
                        response.data[i].TOTAL = 0;
                        parseFloat(response.data[i].TOTAL);
                        for(let j in response.data[i]){
                            if(j == 'FULL_NAME' || j == 'TOTAL'){
                                continue;
                            }
                            response.data[i].TOTAL += (parseFloat(response.data[i][j]) || 0);
                        }
                    }
                    
                    generateEmployeeCalendar(response.data, columns);
                        map = {
                            
                        };
                }
            }
            }, function (error) {
                app.showMessage(error, 'error');
            }); 
        });
    }); 
})(window.jQuery, window.app);