angular.module('hris', [])
        .controller('assignController', function ($scope) {
            $('select').select2();

            app.datePickerWithNepali('startDate', 'nepaliStartDate');
            $scope.jobResponsibilityList = [];
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
                for (var i = 0; i < $scope.jobResponsibilityList.length; i++) {
                    $scope.jobResponsibilityList[i].checked = item;
                }

                $scope.daysForAllFlag = item && $scope.jobResponsibilityList.length > 0;
            };

            $scope.daysForAllChange = function (days) {
                for (var i = 0; i < $scope.jobResponsibilityList.length; i++) {
                    if ($scope.jobResponsibilityList[i].checked) {
                        $scope.jobResponsibilityList[i].TOTAL_DAYS = days;
                    }
                }
            };
            $scope.prevBalForAllChange = function (days) {
                for (var i = 0; i < $scope.jobResponsibilityList.length; i++) {
                    if ($scope.jobResponsibilityList[i].checked) {
                        $scope.jobResponsibilityList[i].PREVIOUS_YEAR_BAL = days;
                    }
                }
            };

            $scope.checkUnit = function (item) {
                for (var i = 0; i < $scope.jobResponsibilityList.length; i++) {
                    if ($scope.jobResponsibilityList[i].checked) {
                        $scope.daysForAllFlag = true;
                        break;
                    }
                    $scope.daysForAllFlag = false;
                }
            };
            $scope.assign = function () {
                assignedBy = $('#assignedBy').val();
                startDate = $('#startDate').val();
                if(startDate == '' || startDate==undefined || startDate == null){
                    window.toastr.error("Please select Start Date", "Alert");
                }else{
                    var promises = [];
                    for (var index in $scope.jobResponsibilityList) {
                        if ($scope.jobResponsibilityList[index].checked) {
                            promises.push(window.app.serverRequest(document.pushEmployeeLinkJobRes, {
                                jobResId: $scope.jobResponsibilityList[index].JOB_RES_ID,
                                employeeId: $scope.jobResponsibilityList[index].EMPLOYEE_ID,
                                assignedBy: assignedBy,
                                startDate: startDate
                            }));
                        }
                    }
                    Promise.all(promises).then(function (success) {
                        $scope.$apply(function () {
                            $scope.view();
                        });
                        window.toastr.success("Job Responsibility assigned successfully", "Notifications");
                    });
                }
                
            };

            $scope.view = function () {
                $scope.daysForAllFlag = false;
                $scope.all = false;
                $scope.leaveName = $('#leaveId>option:selected').text();
                $scope.jobResEngName = $('#jobResonsibilityId>option:selected').text();
                leaveId = $('#leaveId').val();
                leaveYearId = $('#leaveYear').val();
                jobResId = $('#jobResonsibilityId').val();
                var q = document.searchManager.getSearchValues();
                q['leaveId'] = leaveId;
                q['leaveYear'] = leaveYearId;
                q['jobResId'] = jobResId;
                window.app.serverRequest(document.pullEmployeeLink, q).then(function (success) {
                    $scope.$apply(function () {
                        $scope.jobResponsibilityList = success.data;
                        for (var i = 0; i < $scope.jobResponsibilityList.length; i++) {
                            $scope.jobResponsibilityList[i].checked = false;
                        }
                    });
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