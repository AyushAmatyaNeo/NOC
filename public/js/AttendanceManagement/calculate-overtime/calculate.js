(function ($, app) {
	'use strict';
	$(document).ready(function () {
        $('#calcOTDetailsTable').hide();

		var $calcOTDetailsTable = $('#calcOTDetailsTable');
        var $calcOTDet = $('#calcOvertime');
        let totalCount;
        var $readOTDet = $('#readOvertime');

        $("#readOvertime").hide();
        $("#calcOvertime").hide();
        if(document.isotCalc == "Y"){
            $("#readOvertime").show();
        } else{
            $("#calcOvertime").show();
        }

        let viewLink = `<span><a class="btn btn-icon-only btn-success" href="${document.viewLink}/#: OVERTIME_ID #" style="height:17px;" title="view">
                            <i class="fa fa-search-plus"></i>
                        </a></span>`;

        const actiontemplateConfig = viewLink;

		app.initializeKendoGrid($calcOTDetailsTable, [
            {field: "SERIAL_NUMBER", title: "S.N.", width: 50, locked: true},
            {field: "EMPLOYEE_NAME", title: "Employee Name",width: 110, locked: true},
            {field: "APPROVED_REMARKS",title: "Work Reason", width: 110, locked: true},
            {field: "DESIGNATION_TITLE", title: "Designation", width: 110, locked: true},
            {field: "OVERTIME_DATE_FORMATTED", title: "Date", width: 80, locked: true},
            {field: "START_TIME_FORMATTED", title: "In Time", width: 70, locked: true},
            {field: "END_TIME_FORMATTED", title: "Out Time", width: 70, locked: true},
            {field: "RAW_OT", title: "Raw Ot", width: 60},
            {field: "CALC_OT", title: "Calc", width: 60},
            {field: "REP_LEAVE", title: "Satta Bida", width: 60},
            {field: "REP_LEAVE_AMOUNT", title: "Amount (Satta Bida)", width: 110},
            {field: "LUNCH_ALLOWANCE", title: "Khaja Kharcha", width: 80},
            {field: "EXTRA_LUNCH_ALLOWANCE", title: "Khaja Kharcha (AFS beyond 10 P.M.)", width: 150},
            //{field: "TOTAL_LUNCH_ALLOWANCE", title: "Khaja Kharcha (Total)", width: 60},
            {field: "NIGHT_TIME_ALLOWANCE", title: "Ratri vatta", width: 60},
            {field: "OVERTIME_ID", title: "Action", width: 90, locked: true, template: actiontemplateConfig}
        ], null, null, null, 'Overtime Report List');
        app.searchTable('calcOTDetailsTable', ['EMPLOYEE_NAME', 'DESIGNATION_TITLE'], true);

        var map = {
            'SERIAL_NUMBER': 'S.N.',
            'EMPLOYEE_NAME': 'Employee Name',
            'APPROVED_REMARKS': 'Work Reason',
            'DESIGNATION_TITLE': 'Designation',
            'OVERTIME_DATE_FORMATTED': 'Date',
            'START_TIME_FORMATTED': 'In Time',
            'END_TIME_FORMATTED': 'Out Time',
            'RAW_OT': 'Raw Ot',
            'CALC_OT': 'Calc',
            'REP_LEAVE': 'Satta Bida',
            'REP_LEAVE_AMOUNT': 'Amount (Satta Bida)',
            'LUNCH_ALLOWANCE': 'Khaja Kharcha',
            'EXTRA_LUNCH_ALLOWANCE': 'Khaja Kharcha (10 P.M. Onwards)',
            //'TOTAL_LUNCH_ALLOWANCE': 'Khaja Kharcha (Total)',
            'NIGHT_TIME_ALLOWANCE': 'Ratri vatta'
        };

        var exportColumnParameters = [];
        for(var key in map){
            exportColumnParameters.push({'VALUES' : key, 'COLUMNS' : map[key]});
        }

        $('.k-grid-pdf').on('click', function(){
            var fc = app.filterExportColumns(null, map);
            app.exportToPDF($calcOTDetailsTable, fc, 'Overtime Report.pdf');
            return false;
        });

        $('.k-grid-excel').on('click', function(){
            var fc = app.filterExportColumns(null, map);
            app.excelExport($calcOTDetailsTable, fc, 'Overtime Report.xlsx');
            return false;
        });

        $calcOTDet.on('click', function () {
            $('#calcOTDetailsTable').show();
            let ymdata = {};
            ymdata['monthId'] = $("#allmonthsId").val();
            ymdata['yearId'] = $("#allyearsId").val();

            app.serverRequest(document.calculateLink, ymdata).then(function (response) {
                if (response.success) {
                    $("#readOvertime").hide();
                    $("#calcOvertime").hide();
                    $("#searchFieldDiv").show();

                    if(response.isotCalc == 'Y'){
                        $("#readOvertime").show();
                    }
                    else{
                        $("#calcOvertime").show();
                    }
                    totalCount = 1;
                    for ( var x in response.data) {
                        response.data[x]['SERIAL_NUMBER'] = totalCount;
                        totalCount++;
                    } 
                    app.renderKendoGrid($calcOTDetailsTable, response.data);
                    app.showMessage(response.message, 'success');
                } else {
                    app.showMessage(response.message, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });

            $('#myModal').modal('toggle');
        });

        //Delete Overtime Records for Test
        $('#deleteOTData').on('click', function(){
            $('#calcOTDetailsTable').show();

            app.serverRequest(document.deleteOTDataLink, {}).then(function (response) {
                if (response.success) {
                    $("#searchFieldDiv").show();
                    totalCount = 1;
                    for ( var x in response.data) {
                        response.data[x]['SERIAL_NUMBER'] = totalCount;
                        totalCount++;
                    } 
                    app.renderKendoGrid($calcOTDetailsTable, response.data);
                    if(response.message) {
                        app.showMessage(response.message, 'success');    
                    }
                    
                    if(response.message1) {
                        app.showMessage(response.message1, 'warning');
                    }
                } else {
                    app.showMessage(response.message, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });

        });

        //Apply Read Functionality
        $('#viewAttendanceList').on('click', function(){
            $('#calcOTDetailsTable').show();
            let rotdt = {};
            rotdt['companyId'] = $("#companyId").val();
            rotdt['branchId'] = $("#branchId").val();
            rotdt['departmentId'] = $("#departmentId").val();
            rotdt['designationId'] = $("#designationId").val();
            rotdt['positionId'] = $("#positionId").val();
            rotdt['serviceTypeId'] = $("#serviceTypeId").val();
            rotdt['serviceEventTypeId'] = $("#serviceEventTypeId").val();
            rotdt['employeeId'] = $("#employeeId").val();
            rotdt['yearId'] = $("#yearsotreadId").val();
            rotdt['monthId'] = $("#monthsotreadId").val();

            app.serverRequest(document.otreadwfilterLink, rotdt).then(function (response) {
                if (response.success) {
                    $("#searchFieldDiv").show();
                    totalCount = 1;
                    for ( var x in response.data) {
                        response.data[x]['SERIAL_NUMBER'] = totalCount;
                        totalCount++;
                    } 
                    app.renderKendoGrid($calcOTDetailsTable, response.data);
                    if(response.message) {
                        app.showMessage(response.message, 'success');    
                    }
                    
                    if(response.message1) {
                        app.showMessage(response.message1, 'warning');
                    }
                } else {
                    app.showMessage(response.message, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });

        });

        $readOTDet.on('click', function(){
            $('#calcOTDetailsTable').show();
            let rotdata = {};
            rotdata['monthId'] = $("#allmonthsId").val();
            rotdata['yearId'] = $("#allyearsId").val();

             app.serverRequest(document.otreadLink, rotdata).then(function (response) {
                if (response.success) {
                    $("#searchFieldDiv").show();
                    totalCount = 1;
                    for ( var x in response.data) {
                        response.data[x]['SERIAL_NUMBER'] = totalCount;
                        totalCount++;
                    } 
                    app.renderKendoGrid($calcOTDetailsTable, response.data);
                    app.showMessage(response.message, 'success');
                } else {
                    app.showMessage(response.message, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });

            $('#myModal').modal('toggle');
        });
        //Read Functionality Ends
	});
})(window.jQuery, window.app);	