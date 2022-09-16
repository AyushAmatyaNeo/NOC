angular.module('hris', [])
        .controller('dashboardController', function ($scope) {
            $scope.roles = document.roles;
            $scope.role = null;
            $scope.dashboardItems = [];
            for (var key in document.dashboardItems) {
                $scope.dashboardItems.push({
                    value: key,
                    roleType: 'A',
                    checked: false
                });
            }

            $scope.roleTypes = document.roleTypes;
            $scope.roleChange = function ($role) {
                if ($role == null) {
                    return;
                }
                window.app.pullDataById(document.fetchRoleDashboardsLink, {
                    roleId: $role['ROLE_ID']
                }).then(function (success) {
                    console.log('fetchRoleDashboards res', success);
                    $scope.$apply(function () {
                        for (var dashboardIndex in $scope.dashboardItems) {
                            $scope.dashboardItems[dashboardIndex].checked = false;
                            $scope.dashboardItems[dashboardIndex].roleType = 'A';
                        }
                        var data = success.data;
                        for (var dashboard in data) {
                            for (var dashboardIndex in $scope.dashboardItems) {
                                if ($scope.dashboardItems[dashboardIndex].value.toUpperCase() == data[dashboard]['DASHBOARD'].toUpperCase()) {
                                    $scope.dashboardItems[dashboardIndex].checked = true;
                                    $scope.dashboardItems[dashboardIndex].roleType = data[dashboard].ROLE_TYPE;
                                }
                            }
                        }
                    });
                }, function (failure) {
                    console.log("failure", failure);
                });
            };
            $scope.assign = function (item) {

                if ($scope.role == null) {
                    alert("cannot assign ");
                    return;
                }
                var dashboard = item.value;
                var checked = item.checked;
                var roleType = item.roleType;

                window.app.pullDataById(document.assignDashboardLink, {
                    roleId: $scope.role["ROLE_ID"],
                    dashboard: dashboard,
                    roleType: roleType,
                    status: (typeof checked !== 'undefined') ? checked : false
                }).then(function (success) {
                    console.log(success);
                    $scope.$apply(function () {

                    });
                }, function (failure) {
                    console.log("failure", failure);
                });
            };

            $scope.roleTypeChange = function (dashboard) {
                console.log(dashboard);
                if (dashboard.checked) {
                    window.app.pullDataById(document.updateDashboardAssignLink, {
                        roleId: $scope.role["ROLE_ID"],
                        dashboard: dashboard.value,
                        roleType: dashboard.roleType
                    }).then(function (success) {
                        console.log(success);
                        $scope.$apply(function () {

                        });
                    }, function (failure) {
                        console.log("failure", failure);
                    });
                }
            };
        });