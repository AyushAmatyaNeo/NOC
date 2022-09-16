(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
    });
})(window.jQuery, window.app);

angular.module('hris', [])
        .controller('trainingAssignController', function ($scope, $http, $window) {
            var $training = $('#trainingId');

            $scope.employeeList = [];
            $scope.all = false;
            $scope.assignShowHide = false;

            $scope.checkAll = function (item) {
                for (var i = 0; i < $scope.employeeList.length; i++) {
                    $scope.employeeList[i].checked = item;
                }
                $scope.assignShowHide = item && ($scope.employeeList.length > 0);
                if ($scope.assignShowHide) {
                }
            };

            $scope.checkUnit = function (item) {
                for (var i = 0; i < $scope.employeeList.length; i++) {
                    if ($scope.employeeList[i].checked) {
                        $scope.assignShowHide = true;
                        break;
                    }
                    $scope.assignShowHide = false;
                }
            };

            $scope.view = function () {
                var trainingId = $training.val();
                if (typeof trainingId === 'undefined' || trainingId === null || trainingId === '' || trainingId === '-1') {
                    window.jQuery('#trainingId').focus();
                    window.app.showMessage('No Training Selected.', 'error');
                    return;
                }
                var searchData = document.searchManager.getSearchValues();
                searchData['trainingId'] = trainingId;
                $scope.all = false;
                $scope.assignShowHide = false;

                window.app.serverRequest(document.pullEmployeeForTrainingAssignLink, searchData).then(function (success) {
                    $scope.$apply(function () {
                        $scope.employeeList = success.data;
                        for (var i = 0; i < $scope.employeeList.length; i++) {
                            $scope.employeeList[i].checked = false;
                        }
                    });
                });
            };

            $scope.assign = function () {
                var trainingId = angular.element(document.getElementById('trainingId')).val();
                if (typeof trainingId === 'undefined' || trainingId === null || trainingId == '' || trainingId == -1) {
                    window.toastr.error("No Training Selected.", "Alert");
                    return;
                }
                var requestData = [];
                for (var index in $scope.employeeList) {
                    if ($scope.employeeList[index].checked && (trainingId != $scope.employeeList[index].TRAINING_ID)) {
                        requestData.push({
                            employeeId: $scope.employeeList[index].EMPLOYEE_ID,
                            trainingId: trainingId,
                            oldTrainingId: $scope.employeeList[index].TRAINING_ID
                        });
                    }
                }
                app.bulkServerRequest(document.assignEmployeeTrainingLink, requestData, function () {
                    app.showMessage("Training Assigned Successfully.");
                    $scope.view();
                }, function (data, error) {
                    app.showMessage(error, 'error');
                });
            };

            window.jQuery('#trainingId').on('change', function () {
                $scope.view();
            });

            var employeeIdFromParam = window.location.href.substr(window.location.href.lastIndexOf('/') + 1);
            if (parseInt(employeeIdFromParam) > 0) {
                angular.element(document.getElementById('employeeId')).val(employeeIdFromParam).change();
                $scope.view();
            }
        });