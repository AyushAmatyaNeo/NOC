angular.module('hris', [])
        .controller('assignController', function ($scope) {
            $('select').select2();
            $scope.leaveList = [];
            $scope.all = false;
            $scope.daysForAll = 0;
            $scope.prevBalForAll = 0;
            $scope.daysForAllFlag = false;
            var leaveId;
            var leaveYearId;
            $scope.leaveName;
            $scope.monthSelect = false;
            $scope.leaveMonthList = [];

            $scope.checkAll = function (item) {
                for (var i = 0; i < $scope.leaveList.length; i++) {
                    $scope.leaveList[i].checked = item;
                }

                $scope.daysForAllFlag = item && $scope.leaveList.length > 0;
            };

            $scope.daysForAllChange = function (days) {
                for (var i = 0; i < $scope.leaveList.length; i++) {
                    if ($scope.leaveList[i].checked) {
                        $scope.leaveList[i].TOTAL_DAYS = days;
                    }
                }
            };
            $scope.prevBalForAllChange = function (days) {
                for (var i = 0; i < $scope.leaveList.length; i++) {
                    if ($scope.leaveList[i].checked) {
                        $scope.leaveList[i].PREVIOUS_YEAR_BAL = days;
                    }
                }
            };

            $scope.checkUnit = function (item) {
                for (var i = 0; i < $scope.leaveList.length; i++) {
                    if ($scope.leaveList[i].checked) {
                        $scope.daysForAllFlag = true;
                        break;
                    }
                    $scope.daysForAllFlag = false;
                }
            };
            $scope.assign = function () {
                var promises = [];
                for (var index in $scope.leaveList) {
                    if ($scope.leaveList[index].checked) {
                        promises.push(window.app.serverRequest(document.pushEmployeeGroupShiftLink, {
                            employeeId: $scope.leaveList[index].EMPLOYEE_ID,
                            caseId: $('#caseId').val(),
                            action: 'A'
                        }));
                    }
                    else{
                        promises.push(window.app.serverRequest(document.pushEmployeeGroupShiftLink, {
                            employeeId: $scope.leaveList[index].EMPLOYEE_ID,
                            caseId: $('#caseId').val(),
                            action: 'D'
                        }));
                    }
                }
                Promise.all(promises).then(function (success) {
                    $scope.$apply(function () {
                        $scope.view();
                    });
                    window.toastr.success("Shift assigned successfully", "Notifications");
                });
            };

            $scope.view = function () {
                $scope.all = false;
                caseId = $('#caseId').val();
                var q = document.searchManager.getSearchValues();
                q['caseId'] = caseId;
                window.app.serverRequest(document.pullEmployeeGroupShiftLink, q).then(function (success) {
                    $scope.$apply(function () {
                        $scope.leaveList = success.data;
                        for (var i = 0; i < $scope.leaveList.length; i++) {
                            $scope.leaveList[i].CHECKED == 'Y' ? $scope.leaveList[i].checked = true : $scope.leaveList[i].checked = false;
                        }
                    });
                    $('select').select2();
                }, function (failure) {
                    throw failure;
                });
            };
            var employeeIdFromParam = window.location.href.substr(window.location.href.lastIndexOf('/') + 1);
            if (parseInt(employeeIdFromParam) > 0) {
                angular.element(document.getElementById('employeeId')).val(employeeIdFromParam).change();
                $scope.view();
            }
        });