(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        
        app.datePickerWithNepali('startDate', 'nepaliStartDate');
        app.datePickerWithNepali('eventDate', 'nepaliEventDate');
        app.datePickerWithNepali('endDate', 'nepaliEndDate');


        var $table = $('#table');
        var action = `
            <div class="clearfix">
                #if(ALLOW_ADD=='Y'){#
                    <a  class="btn btn-icon-only blue" href="${document.expenseAddLink}/#:JOB_HISTORY_ID#" style="height:17px;" title="Apply For Expense">
                        <i class="fa fa-arrow-right"></i>
                    </a>
                #}else{#
                    <a class="btn btn-icon-only green" href="${document.viewLink}/#:JOB_HISTORY_ID#/#:SERIAL_NUMBER#" style="height:17px;" title="View Detail">
                        <i class="fa fa-search"></i>
                    </a>
                #}#
            </div>
        `;
        app.initializeKendoGrid($table, [
            {field: "FULL_NAME", title: "Employee Name", width:'100', locked: false},
            {title: "Start Date",
                columns: [{
                        field: "START_DATE_AD",
                        title: "English",
                        locked: true
                    },
                    {
                        field: "START_DATE_BS",
                        title: "Nepali",
                        locked: true
                    }]},

            {title: "Event Date",
                columns: [{
                        field: "EVENT_DATE_AD",
                        title: "English",
                        locked: true
                    },
                    {field: "EVENT_DATE_BS",
                        title: "Nepali",
                        locked: true
                    }]},
            {field: "TO_BRANCH", title: "To Branch", width:'100', locked: false},
            {field: "TO_DEPARTMENT", title: "To Department", width:'100', locked: false},
            {field: "TO_DESIGNATION", title: "To Designation", width:'100', locked: false},
            {field: "TO_POSITION", title: "To Position", width:'100', locked: false},
            {field: "JOB_HISTORY_ID", title: "Action", template: action}
        ], null, null, null, 'Travel Request List');


        $('#search').on('click', function () {
            var startDate = $('#startDate').val();
            var endDate = $('#endDate').val();
            var eventDate = $('#eventDate').val();

            app.pullDataById('', {
                'startDate': startDate,
                'endDate': endDate,
                'eventDate': eventDate,
                'employees': $('#employeeId').val()
            }).then(function (response) {
                if (response.success) {
                    app.renderKendoGrid($table, response.data);
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });

        });


        app.searchTable($table, ['EMPLOYEE_NAME', 'EMPLOYEE_CODE']);
        var exportMap = {
            'FULL_NAME': 'Employee Name',
            'START_DATE_AD': 'Start Date',
            'START_DATE_BS': 'From Date(BS)',
            'END_DATE_AD': 'End Date(AD)',
            'END_DATE_BS': 'End Date(BS)',
            'EVENT_DATE_AD': 'Event Date(AD)',
            'EVENT_DATE_BS': 'Event Date(BS)',
            'TO_BRANCH': 'To Branch',
            'TO_DEPARTMENT': 'To Department',
            'TO_DESIGNATION': 'To Designation',
            'TO_POSITION': 'To Position'
        };
        $('#excelExport').on('click', function () {
            app.excelExport($table, exportMap, 'Transfer List.xlsx');
        });

        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, exportMap, 'Transfer List.pdf');
        });

        $("#reset").on("click", function () {
            $(".form-control").val("");
        });

    });
})(window.jQuery, window.app);
