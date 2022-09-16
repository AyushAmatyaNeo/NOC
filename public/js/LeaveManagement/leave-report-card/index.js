(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();

        var listDiv = $('#listDiv');
        var $assignTable = $('#employeeTable');
        var $leaveId = $("#leaveId");
        var $leaveYear = $('#leaveYear');

        function searchAction() {
            
            $assignTable.find("tr:gt(0)").remove();
            $("#table").empty();
            $("#table").append('<h3 style="text-align: center;">'+document.preference.companyName+'</h3>');
            $("#table").append('<h3 style="text-align: center;">'+document.preference.companyAddress+'</h3>');
            $("#table").append('<h3 style="text-align: center;">Employee Leave Report Card</h3><hr />');
            // var data = document.searchManager.getSearchValues();
            let id = $("#employeeId").val();
            
            for(let i in id){
                var data = {
                    employeeId: id[i],
                    leaveId : $("#leaveId").val(),
                    leaveYear : $leaveYear.val()
                }; 

            app.serverRequest(document.pullLeaveReportCardLink , {'data': data}).then(function (response) {
                var leaveDetails = response.data;
                var leaves = response.leaves;
                var htmlData = '<table class="table table-bordered">';
                htmlData+='<tr><th colspan="3">EMP CODE: </th><td>'+leaveDetails[0].EMPLOYEE_ID+'</td></tr>';
                htmlData+='<tr><td colspan="2">Name </td><th colspan="3">Present Address</th><td>'+leaveDetails[0].ADDR_TEMP_STREET_ADDRESS+'</td><th colspan="3">LEAVE DETAILS</th>';
                for(let i = 0; i < leaves.length; i++){
                    htmlData+='<td rowspan="2">'+leaves[i].LEAVE_ENAME+'</td>';
                }
                htmlData+="</tr>";
                htmlData+='<tr><td colspan="2">'+leaveDetails[0].FULL_NAME+'</td><td colspan="3">Permanent Address</td><td>'+leaveDetails[0].ADDR_PERM_STREET_ADDRESS+'</td><td>-</td></tr>';
                htmlData+='<tr><td colspan="2">Date Of Join</td><td colspan="3">Designation</td><td>Department</td><td colspan="3">LEAVE B/F FROM LAST YEAR</td>';
                for(let i = 0; i < leaves.length; i++){
                    htmlData+='<td>'+leaves[i].PREVIOUS_YEAR_BAL+'</td>';
                }
                htmlData+='</tr><tr><td colspan="2">'+leaveDetails[0].JOIN_DATE+'</td><td colspan="3">'+leaveDetails[0].DESIGNATION_TITLE+'</td><td>'+leaveDetails[0].DEPARTMENT+'</td><td colspan="3">LEAVE DUE THIS YEAR</td>';
                for(let i = 0; i < leaves.length; i++){
                    htmlData+='<td>'+leaves[i].TOTAL_DAYS+'</td>';
                }
                htmlData+='</tr><tr><td rowspan="2">Sr No</td><td rowspan="2">DATE</td><td rowspan="2">TYPE OF LEAVE</td><td colspan="3">Leave Required</td><td rowspan="2">Reason For Leave</td><td rowspan="2">Recommender</td><td rowspan="2">Approver</td><td colspan="'+leaves.length+'">Leave-to-Date</td></tr>';
                htmlData+='<tr><td>No Of Days</td><td>From</td><td>To</td>';
                for(let i = 0; i < leaves.length; i++){
                    htmlData+='<td>'+leaves[i].BALANCE+'</td>';
                }
                htmlData+='</tr>';
                for(let i = 0; i < leaveDetails.length; i++){
                    htmlData+='<tr><td>'+(i+1)+'</td><td>'+leaveDetails[i].FROM_DATE_AD+'</td><td>'+leaveDetails[i].LEAVE_ENAME+'</td><td>'+leaveDetails[i].NO_OF_DAYS+'</td><td>'+leaveDetails[i].FROM_DATE_AD+'</td><td>'+leaveDetails[i].TO_DATE_AD+'</td><td>'+leaveDetails[i].REMARKS+'</td><td>'+leaveDetails[i].RECOMMENDED_BY_NAME+'</td><td>'+leaveDetails[i].APPROVED_BY_NAME+'</td>';
                    for(let j = 0; j < leaves.length; j++){
                        if(leaveDetails[i].LEAVE_ID == leaves[j].LEAVE_ID){
                            leaves[j].BALANCE = leaves[j].BALANCE - leaveDetails[i].NO_OF_DAYS;
                        }
                        htmlData+='<td>'+leaves[j].BALANCE+'</td>';
                    }
                }
                htmlData+='</tr>';
                htmlData+='<tr><td colspan="9" style="font-weight: bold; text-align: center;">Balance</td>';
                    for(let j = 0; j < leaves.length; j++){
                        htmlData+='<td>'+leaves[j].BALANCE+'</td>';
                    }
                htmlData+='</tr></table><hr />';
                $("#table").append(htmlData);
                $("#table").css('border', '1px solid black');
            }, function (error) {

            });
            }
        };

        $('#search').on('click', function () {
            searchAction();
        });

        $('#masterCheckElement').on('click', function () {
            var elementChecked = $(this).is(':checked');
            if (elementChecked) {
                $('.insideChkBox').prop("checked", true);
            } else {
                $('.insideChkBox').prop("checked", false);
            }

        });

        $('#assignBtn').on('click', function () {
            createcodes();
        });

        function createcodes() {
            var assignLeaveId = $('#leaveId').val();

            $assignTable.each(function (i, row) {
                var $row = $(row);
                var $allCheckBox = $row.find('input[class*="insideChkBox"]');
                var postValues = [];

                $allCheckBox.each(function (key, value) {
                    var employeeId = $(this).attr("dataEmp");
                    var isEmpChecked = $(this).is(':checked');
                    postValues[key] = {'employeeId': employeeId, 'isChecked': isEmpChecked, 'leaveId': assignLeaveId};
                });

                app.serverRequest(document.assignSubMandatory, {
                    data: postValues}).then(function (response) {
                    if (response.success = true) {
                        app.showMessage("Sucessfully Assigned");
                    }
                }, function (failure) {
                    window.app.showMessage("failed");
                    throw failure;
                });
            });
        }

        $("#excelExport").click(function(){    
            $("#table").table2excel({
                exclude: ".noExl",
                name: "leave-card",
                filename: "leave-report-card.xlsx" 
            });
        });

        $('#pdfExport').on('click', function () {
            kendo.drawing.drawDOM($("#table")).then(function (group) {
                kendo.drawing.pdf.saveAs(group, "Leave-report-card.pdf");
            });
        });
        
        function leaveYearChange(leaveYear){
            let leaveList = document.allLeaveForReport[leaveYear];
            app.populateSelect($leaveId, leaveList, 'LEAVE_ID', 'LEAVE_ENAME', 'All Leaves', -1, -1);
        }
        leaveYearChange($leaveYear.val());
        
        $leaveYear.on('change', function () {
            let selectedLeaveYear = $(this).val();
            leaveYearChange(selectedLeaveYear);
        });

    });
})(window.jQuery, window.app);
