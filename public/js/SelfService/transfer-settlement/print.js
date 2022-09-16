angular.module("hris", [])
        .controller("printController", function ($scope, $http, $window) {
            $scope.printDiv = function (divName) {
                var printContents = document.getElementById(divName).innerHTML;
                var popupWin = window.open('', '_blank', 'width=1000,height=500,toolbar=0,scrollbars=0,status=0');
                popupWin.document.open();
                popupWin.document.write('<style>@page{size:landscape;}</style><html><head><link rel="stylesheet" type="text/css" href="' + document.urlCss + '" /></head><body onload="window.print()">' + printContents + '</body></html>');
                popupWin.document.close();
            }
            $scope.hgtRecommender = 14;
            $scope.hgtApprover = 14;

            $scope.recommenderView = function (event) {
                if (event) {
                    $scope.signRecommender = true;
                } else {
                    $scope.signRecommender = false;
                }
            }
            $scope.approverView = function (event) {
                if (event) {
                    $scope.signApprover = true;
                } else {
                    $scope.signApprover = false;
                }
            }
        });

$(document).ready(function () {
    app.setLoadingOnSubmit("travelApprove-form");
});
