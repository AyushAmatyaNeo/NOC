(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        var $startDate = $("#form-advanceDate");
//        app.addDatePicker($startDate);
        app.datePickerWithNepali("form-advanceDate", "nepaliDate");

        $startDate.datepicker('setStartDate', new Date());
        app.setLoadingOnSubmit("advanceApprove-form");
        app.setLoadingOnSubmit("advance-form");

        var employeeId = $('#form-employeeId').val();
        window.app.floatingProfile.setDataFromRemote(employeeId);

    });
})(window.jQuery, window.app);

angular.module("hris", [])
        .controller("advanceDetailController", function ($scope, $http) {
            $scope.allowAmt = null;
            $scope.allowTerms = null;
            $scope.requestedAmt = null;
            $scope.terms = null;
            $scope.salaryDeduction = null;

            $scope.advanceChange = function () {
                var advanceId = angular.element(document.getElementById('form-advanceId')).val();
                if (typeof advanceId !== "undefined" || advanceId != null) {
                    var employeeId = angular.element(document.getElementById('form-employeeId')).val();

                    window.app.pullDataById(document.pullAdvanceDetailByEmpIdLink, {
                        'employeeId': employeeId,
                        'advanceId': advanceId
                    }).then(function (success) {
                        $scope.$apply(function () {
                            $scope.allowAmt = success.data.allowAmt;
                            $scope.allowTerms = success.data.allowTerms;
                        });
                    });
                }
            };

            $scope.requestChange = function () {
                if ($scope.requestedAmt !== null && $scope.terms !== null) {
                    $scope.salaryDeduction = parseFloat($scope.requestedAmt) / parseFloat($scope.terms);
                }
            };
            $scope.advanceChange();
        });
