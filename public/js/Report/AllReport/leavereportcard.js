
(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        var $table = $('#table');
        var $search = $('#search');
        
        $('#excelExport').on('click', function () {
            app.excelExport($employeeTable, map, 'Leave Report Card.xlsx');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($employeeTable, map, 'Leave Report Card.pdf');
        });
        
        $search.on('click', function () {
            var tableData = '';
            var index;
            var responseData;
            var SN;
            var data = document.searchManager.getSearchValues();
            app.serverRequest(document.pullEmployeeListForEmployeeTableLink, data).then(function (response) {
                if (response.success) {
                    $table.empty();
                    responseData = response.data;
                    var uniqueEmployeeId = [];
                    for(let i = 0; i < responseData.length; i++){
                        if(uniqueEmployeeId.indexOf(responseData[i].EMPLOYEE_ID) == -1){
                            uniqueEmployeeId.push(responseData[i].EMPLOYEE_ID);
                        }
                    } 
                    for(let i = 0; i < uniqueEmployeeId.length; i++){
                        for(var j = 0; j < responseData.length; j++){
                            if(uniqueEmployeeId[i] == responseData[j].EMPLOYEE_ID){
                                index = j;
                                break;
                            }
                        }
                        tableData+='<table><tr><td>Employee Code</td><td>'+responseData[index].EMPLOYEE_CODE+'</td></tr>';
                        tableData+='<tr><td>Name</td><td>Present Address</td><td>Present Address</td></tr>';
                        tableData+='<tr><td>'+responseData[index].FULL_NAME+'</td><td>-</td><td>-</td></tr>';
                        tableData+='<tr><td>Date Of Join</td><td>Designation</td><td>Department</td></tr>';
                        tableData+='<tr><td>'+responseData[index].JOIN_DATE_AD+'</td><td>-</td><td>-</td></tr>';
                        tableData+='<tr><td rowspan="2">SrNo</td><td rowspan="2">Date</td><td rowspan="2">Type Of Leave</td><td colspan="3">Leave Required</td></tr></table>';

                        $('#table').append(tableData);
                    }
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });  
        });
        
    });
})(window.jQuery, window.app);