(function ($) {
    'use strict';
    $(document).ready(function () {
        console.log(document.appraisals);
        $("#appraisalListTable").kendoGrid({
            excel: {
                fileName: "AppraisalList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.appraisals,
                page: 1,
            },
            height: 450,
            scrollable: true,
            sortable: true,
            filterable: true,
            pageable: true,
            rowTemplate: kendo.template($("#rowTemplate").html()),
            columns: [
                {field: "APPRAISAL_EDESC", title: "Appraisal Name", width: 120},
                {field: "APPRAISAL_TYPE_EDESC", title: "Appraisal Type", width: 120},
                {field: "STAGE_EDESC", title: "Current Status", width: 100},
                {field: "START_DATE", title: "Start Date", width: 80},
                {field: "END_DATE", title: "End Date", width: 80},
                {title: "Action", width: 90}
            ],
        });

        app.searchTable('appraisalListTable', ['APPRAISAL_EDESC', 'APPRAISAL_TYPE_EDESC', 'STAGE_EDESC', 'START_DATE', 'END_DATE']);

        app.pdfExport(
                'appraisalListTable',
                {
                    'APPRAISAL_EDESC': ' Appraisal',
                    'APPRAISAL_TYPE_EDESC': 'Type',
                    'STAGE_EDESC': 'Stage',
                    'START_DATE': 'Start Date',
                    'END_DATE': 'End Date',
                }
        );

        $("#export").click(function (e) {
            var grid = $("#appraisalTable").data("kendoGrid");
            grid.saveAsExcel();
        });
    });
})(window.jQuery);


angular.module("hris", [])
        .controller("appraisalList", function ($scope, $http, $window) {
            $scope.msg = $window.localStorage.getItem("msg");
            if ($window.localStorage.getItem("msg")) {
                window.toastr.success($scope.msg, "Notifications");
            }
            $window.localStorage.removeItem("msg");
        });