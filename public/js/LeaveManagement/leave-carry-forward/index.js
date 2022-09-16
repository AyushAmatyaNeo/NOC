(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, true);

        $("#reset").on("click", function () {
            if (typeof document.ids !== "undefined") {
                $.each(document.ids, function (key, value) {
                    $("#" + key).val(value).change();
                });
            }

        });



        var $table = $('#table');

        var actiontemplateConfig = {
            update: {
                'ALLOW_UPDATE': document.acl.ALLOW_UPDATE,
                'params': ["ID"],
                'url': document.editLink
            },
            delete: {
                'ALLOW_DELETE': document.acl.ALLOW_DELETE,
                'params': ["ID"],
                'url': document.deleteLink
            }
        };

        app.initializeKendoGrid($table, [
            {field: "FULL_NAME", title: "Employee"},
            {field: "LEAVE_ENAME", title: "Leave Type"},
            {field: "CARRY_FORWARD_DAYS", title: "Carry Forwarded Days"},
            {field: "ENCASH_DAYS", title: "Leave days For Encashment"},
            {field: "ID", title: "Action", width: 120, template: app.genKendoActionTemplate(actiontemplateConfig)}
        ]);


        $('#viewLeaveRequestStatus').on('click', function () {

            var data = $("#employeeId").val();

            app.serverRequest(document.viewLink, {
                'employees': data
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


        app.searchTable('leaveRequestTable', ['LEAVE_ENAME', 'REQUESTED_DT_AD', 'REQUESTED_DT_BS', 'FROM_DATE_AD', 'FROM_DATE_BS', 'TO_DATE_AD', 'TO_DATE_BS', 'NO_OF_DAYS', 'STATUS_DETAIL']);
        var exportMap = {
            'LEAVE_ENAME': 'Leave',
            'REQUESTED_DT_AD': 'Requested Date(AD)',
            'REQUESTED_DT_BS': 'Requested Date(BS)',
            'FROM_DATE_AD': 'Start Date(AD)',
            'FROM_DATE_BS': 'Start Date(BS)',
            'TO_DATE_AD': 'End Date(AD)',
            'TO_DATE_BS': 'End Date(BS)',
            'HALF_DAY_DETAIL': 'Day Interval',
            'GRACE_PERIOD_DETAIL': 'Grace',
            'STATUS_DETAIL': 'Status',
            'REMARKS': 'Remarks',
            'RECOMMENDER_NAME': 'Recommender',
            'RECOMMENDED_REMARKS': 'Recommender Remarks',
            'RECOMMENDED_DT': 'Recommended Date',
            'APPROVER_NAME': 'Approver',
            'APPROVED_REMARKS': 'Approver Remarks',
            'APPROVED_DT': 'Aprroved Date'
        };
        $('#excelExport').on('click', function () {
            app.excelExport($table, exportMap, 'leave Request List');
        });

        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, exportMap, 'leave Request List');
        });

    });
})(window.jQuery, window.app);
