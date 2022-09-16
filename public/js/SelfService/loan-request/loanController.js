angular.module('hris', [])
        .controller('loanController', function ($scope, $http) {
            $scope.employeeChange = function () {
                var employeeId = angular.element(document.getElementById('employeeId')).val();
                window.app.floatingProfile.setDataFromRemote(employeeId);
                window.app.pullDataById(document.pullLoanListLink, {
                    'employeeId': employeeId
                }).then(function (success) {
                    $scope.$apply(function () {
                        $scope.loanList = success.data;
                        $scope.loanId = $scope.loanList[0];
                    });
                });
            }
        });
