(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, true);
        var $table = $("#table");
        var $search = $('#search');
        var map;
        
        var $leave = $('#leaveId');
        var leaveList = document.leaves;
        var $leaveYear = $('#leaveYear');
        app.populateSelect($leave, document.leaves, 'LEAVE_ID', 'LEAVE_ENAME');


        function reinitializeKendo(optionalColumns) {
//            console.log(optionalColumns);
            var columns = [
                {field: "EMPLOYEE_CODE", title: "Code", width: 70, locked: true},
                {field: "FULL_NAME", title: "Employee", width: 110, locked: true},
                {field: "DEPARTMENT_NAME", title: "Department", width: 110, locked: true},
                {field: "FUNCTIONAL_TYPE_EDESC", title: "Functional Type", width: 110, locked: true},
                {field: "DESIGNATION_TITLE", title: "Designation", width: 110, locked: true},
                {field: "POSITION_NAME", title: "Position", width: 100},
            ];
             map = {
                'EMPLOYEE_CODE': 'Code',
                'FULL_NAME': 'Name',
                'DEPARTMENT_NAME': 'Department',
                'DESIGNATION_TITLE': 'Designation',
                'POSITION_NAME': 'Position',
                'FUNCTIONAL_TYPE_EDESC': 'Functional Type',
            };
            var columnsList;

            if (optionalColumns != null) {
                $.each(optionalColumns, function (i, val) {
                    
                    for (var ii in leaveList) {
//                        console.log(leaveList[ii]['LEAVE_ID']);
                        if (leaveList[ii]['LEAVE_ID'] == val) {
//                            console.log(leaveList[ii]);

                            columnsList = {
                                title: leaveList[ii]['LEAVE_ENAME'],
                                columns: [
                                    {
                                        title: 'TAKEN',
                                        field: 'L' + val + '_' + 'TAKEN',
                                        width: 100
                                    },
                            {
                                title: 'BALANCE',
                                field: 'L' + val + '_' + 'BALANCE',
                                width: 100
                            },
                                ]
                            };
                            map['L' + val + '_TAKEN'] = leaveList[ii]['LEAVE_ENAME'] + 'TAKEN';
                    map['L' + val + '_BALANCE'] = leaveList[i]['LEAVE_ENAME'] + 'BALANCE';
                            columns.push(columnsList);

                        }

                    }


                });
            } else {

                for (var i in leaveList) {

                    columnsList = {
                        title: leaveList[i]['LEAVE_ENAME'],
                        columns: [
                            {
                                title: 'TAKEN',
                                field: 'L' + leaveList[i]['LEAVE_ID'] + '_' + 'TAKEN',
                                width: 100
                            },
                            {
                                title: 'BALANCE',
                                field: 'L' + leaveList[i]['LEAVE_ID'] + '_' + 'BALANCE',
                                width: 100
                            },
                        ]
                    };
                    map['L' + leaveList[i]['LEAVE_ID'] + '_TAKEN'] = leaveList[i]['LEAVE_ENAME'] + ' TAKEN';
                    map['L' + leaveList[i]['LEAVE_ID'] + '_BALANCE'] = leaveList[i]['LEAVE_ENAME'] + ' BALANCE';
                    columns.push(columnsList);

                }

            }
            app.initializeKendoGrid($table, columns,null,null,null,'Leave Taken Between Dates Report.xlsx');

        }
        app.searchTable($table, ['EMPLOYEE_CODE', 'FULL_NAME']);

        $search.on('click', function () {
            var fromDate = $('#fromDate').val();
            var toDate = $('#toDate').val();

            var leaveColumns = $leave.val();
            $table.empty();

            if (fromDate == -1 || fromDate == '') {
                app.errorMessage("Please select From Date", "Notification");
                return;
            }
            if (toDate == -1 || toDate == '') {
                return;
                app.errorMessage("Please select To Date", "Notification");
            }
            reinitializeKendo(leaveColumns);

            var q = document.searchManager.getSearchValues();
            q['fromDate'] = fromDate;
            q['toDate'] = toDate;
            q['leaveYear'] = $leaveYear.val();
            App.blockUI({target: "#hris-page-content"});
            app.pullDataById(document.pullBalanceBetweenDates, q).then(function (success) {
                App.unblockUI("#hris-page-content");
                console.log(success.data);
                app.renderKendoGrid($table, success.data);
            }, function (failure) {
                App.unblockUI("#hris-page-content");
            });
        });

        $('#excelExport').on("click", function () {
            app.excelExport($table, map, "Employee Leave Balance Report.xlsx");
        });
        $('#pdfExport').on("click", function () {
            app.exportToPDF($table, map, "Employee Leave Balance Report.pdf", 'A2');
        });
        
//         $("#reset").on("click", function () {
//            $(".form-control").val("");
//            document.searchManager.reset();
//        });


        function leaveYearChange(leaveYear) {
            leaveList = document.allLeaveForReport[leaveYear];
            app.populateSelect($leave, leaveList, 'LEAVE_ID', 'LEAVE_ENAME', 'All Leaves', -1, -1);
        }
        leaveYearChange($leaveYear.val());
        $leaveYear.on('change', function () {
            let selectedLeaveYear = $(this).val();
            leaveYearChange(selectedLeaveYear);
        });

    });
})(window.jQuery, window.app);
