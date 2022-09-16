(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        var $qaDate = $("#qaDate");
        if (!($qaDate.is('[readonly]'))) {
            app.datePickerWithNepali("qaDate", "nepaliDate");
        } else {
            app.datePickerWithNepali("qaDate", "nepaliDate");
        }
    });
})(window.jQuery, window.app);

angular.module('hris', [])
        .controller("serviceQuestionList", function ($scope, $http) {
            $scope.serviceQuestionList = [];
            var serviceEventTypeId = angular.element(document.getElementById('serviceEventTypeId')).val();
            var empQaId = angular.element(document.getElementById('empQaId')).val();
            $scope.serviceEventTypeChange = function (serviceEventType) {
                console.log(serviceEventType);
                var serviceEventTypeId = serviceEventType;
                App.blockUI({target: "#hris-page-content"});
                window.app.pullDataById(document.pullServiceQuestionListLink, {
                    id: serviceEventTypeId,
                    empQaId: empQaId
                }).then(function (response) {
                    App.unblockUI("#hris-page-content");
                    try {
                        if (!response.success) {
                            throw response.error;
                        }
                        $scope.$apply(function () {
                            console.log(response.data);
                            $scope.serviceQuestionList = response.data;
                        });
                    } catch (e) {
                        window.app.errorMessage(e, 'Error');
                    }
                }, function (failure) {
                    App.unblockUI("#hris-page-content");
                    window.app.errorMessage(JSON.stringify(failure), "SYSTEM ERROR MESSAGE");
                });
            }
            $scope.serviceEventTypeChange(serviceEventTypeId);

            $scope.printDiv = function (divName) {
                var printContents = document.getElementById(divName).innerHTML;
                var popupWin = window.open('', '_blank', 'width=1000,height=500,toolbar=0,scrollbars=0,status=0');
                popupWin.document.open();
                popupWin.document.write('<style>@page{size:portrait;}</style><html><head><link rel="stylesheet" type="text/css" href="' + document.urlCss + '" /></head><body onload="window.print()">' + printContents + '</body></html>');
                popupWin.document.close();
            }

        });