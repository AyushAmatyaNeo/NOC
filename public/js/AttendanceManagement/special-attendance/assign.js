(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();

        let $branch = $('#branchId');
        let $province = $('#province');

        let populateBranch;

        app.datePickerWithNepali('fromDate', 'nepaliFromDate');
        app.datePickerWithNepali('toDate', 'nepaliToDate');

        $province.on("change", function () {
            populateBranch = [];
            $.each(document.braProv, function (k, v) {
                if (v == $province.val()) {
                    populateBranch.push(k);
                }
            });
            $branch.val(populateBranch).change();
        });
    });
})(window.jQuery, window.app);

angular.module('hris', [])
    .controller('HolidayAssignController', function ($scope) {
        var $companyId = angular.element(document.getElementById('companyId'));
        var $branchId = angular.element(document.getElementById('branchId'));
        var $locationId = angular.element(document.getElementById('locationId'));
        var $departmentId = angular.element(document.getElementById('departmentId'));
        var $designationId = angular.element(document.getElementById('designationId'));
        var $positionId = angular.element(document.getElementById('positionId'));
        var $serviceTypeId = angular.element(document.getElementById('serviceTypeId'));
        var $serviceEventTypeId = angular.element(document.getElementById('serviceEventTypeId'));
        var $employeeId = angular.element(document.getElementById('employeeId'));
        var $genderId = angular.element(document.getElementById('genderId'));
        var $employeeTypeId = angular.element(document.getElementById('employeeTypeId'));


        $scope.spList = document.spList;
        $scope.updateList = [];
        $scope.employeeList = [];
        $scope.alreadyAssignedEmpList = [];
        $scope.all = false;

        $scope.checkAll = function (checked) {
            for (var i = 0; i < $scope.employeeList.length; i++) {
                $scope.employeeList[i].checked = checked;
                let updateListIndex = $scope.updateList.findIndex((x) => x.employeeId == $scope.employeeList[i].EMPLOYEE_ID);
                if (updateListIndex == -1) {
                    $scope.updateList.push({ employeeId: $scope.employeeList[i].EMPLOYEE_ID, assignFlag: checked });
                } else {
                    $scope.updateList[updateListIndex].assignFlag = checked;
                }
            }
        };

        $scope.spChangeFn = function () {
            let fromDate = $("#fromDate").val();
            let toDate = $("#toDate").val();
            if (toDate !== null && toDate !== '') { return; }
            $scope.updateList = [];
            if ($scope.sp == null || (fromDate == null || fromDate == '')) {
                $scope.alreadyAssignedEmpList = [];
                var empList = angular.copy($scope.employeeList);
                $scope.employeeList = [];
                for (var i in empList) {
                    var emp = empList[i];
                    emp.checked = ($scope.alreadyAssignedEmpList.indexOf(emp.EMPLOYEE_ID) >= 0);
                    $scope.employeeList.push(emp);
                }
                return;
            }

            $scope.all = false;

            window.app.serverRequest(document.wsGetAssignedEmployees, {
                spId: $scope.sp,
                fromDate: fromDate
            }).then(function (response) {
                if (response.success) {
                    $scope.$apply(function () {
                        $scope.alreadyAssignedEmpList = response.data;
                        var empList = angular.copy($scope.employeeList);
                        $scope.employeeList = [];
                        for (var i in empList) {
                            var emp = empList[i];
                            emp.checked = ($scope.alreadyAssignedEmpList.indexOf(emp.EMPLOYEE_ID) >= 0);
                            $scope.employeeList.push(emp);
                        }
                    });
                } else {
                    console.log("getSpAssignedEmployees=>", response.error);
                }

            }, function (failure) {
            });
        };

        $scope.view = function () {
            window.app.pullDataById(document.wsGetEmployeeList, {
                companyId: $companyId.val(),
                branchId: $branchId.val(),
                locationId: $locationId.val(),
                departmentId: $departmentId.val(),
                designationId: $designationId.val(),
                positionId: $positionId.val(),
                serviceTypeId: $serviceTypeId.val(),
                serviceEventTypeId: $serviceEventTypeId.val(),
                employeeId: $employeeId.val(),
                genderId: $genderId.val(),
                employeeTypeId: $employeeTypeId.val()
            }).then(function (response) {
                $scope.$apply(function () {
                    $scope.employeeList = [];
                    $scope.updateList = [];
                    var empList = response.data;
                    for (var i in empList) {
                        var emp = empList[i];
                        emp.checked = ($scope.alreadyAssignedEmpList.indexOf(emp.EMPLOYEE_ID) >= 0);
                        $scope.employeeList.push(emp);
                    }
                });
                window.app.scrollTo('employeeTable');

            }, function (failure) {

            });
            $scope.spChangeFn();
        };

        $scope.checkUnit = function (index, employeeId, checked) {
            $scope.employeeList[index].checked = checked;
            let updateListIndex = $scope.updateList.findIndex((x) => x.employeeId == employeeId);
            if (updateListIndex == -1) {
                $scope.updateList.push({ employeeId: employeeId, assignFlag: checked });
            } else {
                $scope.updateList[updateListIndex].assignFlag = checked;
            }
        }

        $scope.assign = function () {
            let fromDate = $("#fromDate").val();
            let toDate = $("#toDate").val();
            if (fromDate == null) {
                window.app.showMessage("No date selected", "error");
                return;
            }
            if ($scope.employeeList.length == 0) {
                window.app.showMessage("No Employees to Assign.", "error");
                return;
            }
            if ($scope.sp == null) {
                window.app.showMessage("Select the Attendance type first to assign to", "error");
                return;
            }

            window.app.serverRequest(document.wsAssignSpToEmployees, {
                spId: $scope.sp,
                employeeIdList: $scope.updateList,
                fromDate: fromDate,
                toDate: toDate,
                displayInOutFlag: $("#displayInOutFlag").is(":checked")
            }).then(function (response) {
                if (response.success) {
                    $scope.updateList = [];
                    var sp = $scope.spList.filter(function (item) {
                        return item['ID'] == $scope.sp;
                    })[0];
                    window.app.showMessage('Special Attendance Assign Success.', 'success');
                } else {
                    window.app.showMessage(response.error);
                }
            }, function (failure) {
                console.log("Special Attendance Assign Failed.", failure);
            });

        };

    });