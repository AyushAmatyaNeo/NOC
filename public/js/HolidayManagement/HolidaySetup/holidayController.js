angular.module('hris', [])
        .controller('holidayController', function ($scope, $http, $window) {
            $scope.holidayDtl = {
                holidayCode: '',
                holidayEname: '',
                holidayLname: '',
                startDate: '',
                endDate: '',
                halfday: '',
                assignOnEmployeeSetup: '',
                remarks: ''
            };

            var holidayId = angular.element(document.getElementById('holidayId'));

            var getHolidayDetail = function () {
                var holidayIdValue = holidayId.val();
                App.blockUI({target: "#hris-page-content"});
                window.app.pullDataById(document.pullHolidayDetailWS, {
                    id: holidayIdValue
                }).then(function (response) {
                    App.unblockUI("#hris-page-content");
                    try {
                        if (!response.success) {
                            throw response.error;
                        }
                        console.log(response);
                        $scope.$apply(function () {
                            var temp = response.data;
                            $scope.holidayDtl.holidayCode = temp.HOLIDAY_CODE;
                            $scope.holidayDtl.holidayEname = temp.HOLIDAY_ENAME;
                            $scope.holidayDtl.holidayLname = temp.HOLIDAY_LNAME;
                            $scope.holidayDtl.startDate = temp.START_DATE;
                            $scope.holidayDtl.endDate = temp.END_DATE;
                            $scope.holidayDtl.halfday = temp.HALFDAY;
                            $scope.holidayDtl.remarks = temp.REMARKS;
                            $scope.holidayDtl.assignOnEmployeeSetup = temp.ASSIGN_ON_EMPLOYEE_SETUP;

                            setTimeout(function () {
                                window.app.startEndDatePickerWithNepali('nepaliStartDate1', 'startDate', 'nepaliEndDate1', 'endDate');

                                /* prevent past event post */
//                                $('#startDate').datepicker("setStartDate", new Date());
//                                $('#endDate').datepicker("setStartDate", new Date());
                                /* end of  prevent past event post */
                            }, 500);

                        });

                    } catch (e) {
                        window.app.errorMessage(e, 'Error');
                    }
                }, function (failure) {
                    App.unblockUI("#hris-page-content");
                    window.app.errorMessage(JSON.stringify(failure), "SYSTEM ERROR MESSAGE");
                });

            }
            holidayId.on("change", getHolidayDetail);
            getHolidayDetail();

            $scope.update = function () {
                if ($scope.holidayForm.$valid) {
                    var err = [];
                    $(".errorMsg").each(function () {
                        var erroMsg = $.trim($(this).html());
                        if (erroMsg !== "") {
                            err.push("error");
                        }
                    });
                    if (err.length > 0)
                    {
                        return;
                    }

                    var holidayId = angular.element(document.getElementById('holidayId')).val();
                    App.blockUI({target: "#hris-page-content"});
                    window.app.pullDataById(document.updateHolidayDetailWS, {
                        data: {
                            holidayId: holidayId,
                            dataArray: $scope.holidayDtl,
                        },
                    }).then(function (response) {
                        App.unblockUI("#hris-page-content");
                        try {
                            if (!response.success) {
                                throw response.error;
                            }
                            $scope.$apply(function () {
                                var val = document.getElementById('holidayId');
                                document.holidays = document.holidays.map(function (item) {
                                    if (item.id === val.value) {
                                        item.text = $scope.holidayDtl.holidayEname;
                                        item.selected = true;
                                    }
                                    return item;
                                });

                                $window.location.href = document.urlIndex;
                                $window.localStorage.setItem("msg", response.data);
                            });

                        } catch (e) {
                            window.app.errorMessage(e, 'Error');
                            App.unblockUI("#hris-page-content");
                        }

                    }, function (failure) {
                        App.unblockUI("#hris-page-content");
                        window.app.errorMessage(JSON.stringify(failure), "SYSTEM ERROR MESSAGE");
                    });
                }
            };
        });
