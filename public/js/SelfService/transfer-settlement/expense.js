(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var action = `
        <div>
           
            <a class="btn btn-icon-only green" href="${document.viewLink}/#:JOB_HISTORY_ID#/#:SERIAL_NUMBER#" style="height:17px;" title="View Detail">
                <i class="fa fa-search"></i>
            </a>
            #if(STATUS=='RQ'){#
            <a class="btn btn-icon-only yellow" href="${document.editLink}/#:JOB_HISTORY_ID#/#:SERIAL_NUMBER#" style="height:17px;" title="Edit">
                <i class="fa fa-edit"></i>
            </a>
            <a  class="btn btn-icon-only red confirmation" href="${document.deleteLink}/#:JOB_HISTORY_ID#/#:SERIAL_NUMBER#" style="height:17px;" title="Cancel">
            <i class="fa fa-times"></i>
            </a>

            #}#
                 
        </div>
    `;
    

        $("select").select2();

        app.datePickerWithNepali('startDate', 'nepaliStartDate');
        app.datePickerWithNepali('eventDate', 'nepaliEventDate');

        $("#reset").on("click", function () {
            if (typeof document.ids !== "undefined") {
                $.each(document.ids, function (key, value) {
                    $("#" + key).val(value).change();
                });
            }
             $(".form-control").val("");
        });

        var $table = $('#table');
        app.initializeKendoGrid($table, [
            {title: "Start Date",
                columns: [{
                        field: "START_DATE",
                        title: "English",
                    },
                    {
                        field: "START_DATE_BS",
                        title: "Nepali",
                    }]},
            {title: "Event Date",
                columns: [{
                        field: "EVENT_DATE",
                        title: "English",
                    },
                    {field: "EVENT_DATE_BS",
                        title: "Nepali",
                    }]},
            {field: "FROM_BRANCH", title: "From Branch"},
            {field: "TO_BRANCH", title: "To Branch"},
            //{field: "REQUESTED_TYPE_DETAIL", title: "Request For"},
            {field: "REQ_SUM", title: "Requested Amount"},
            {field: "AP_SUM", title: "Approved Amount"},
            {field: "STATUS_DETAIL", title: "Status"},
            {field: "JOB_HISTORY_ID", title: "Action", template: action}
        ]);


        $('#search').on('click', function () {
            console.log('adsf');
            var statusId = $('#statusId').val();
            var startDate = $('#startDate').val();
            var eventDate = $('#eventDate').val();

            app.pullDataById('', {
                'statusId': statusId,
                'startDate': startDate,
                'eventDate': eventDate
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


        app.searchTable($table, ['START_DATE_AD']);
        var exportMap = {
            'START_DATE': 'Start Date(AD)',
            'START_DATE_BS': 'Start Date(BS)',
            'END_DATE': 'End Date(AD)',
            'END_DATE_BS': 'End Date(BS)',
            'EVENT_DATE': 'Event Date(AD)',
            'EVENT_DATE_BS': 'Event Date(BS)',
            'FROM_BRANCH': 'From Branch',
            'TO_BRANCH': 'To Branch',
            'REQ_SUM': 'Requested Amount',
            'AP_SUM': 'Approved Amount',
            'STATUS_DETAIL': 'Status',
        };
        $('#excelExport').on('click', function () {
            app.excelExport($table, exportMap, 'Transfer Settlement List.xlsx');
        });

        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, exportMap, 'Transfer Settlement List.pdf');
        });

    });
})(window.jQuery, window.app);
