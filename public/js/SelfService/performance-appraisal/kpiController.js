angular.module('kpiModule', ['use', 'ngMessages'])
        .controller("kpiController", function ($scope, $http, $window) {
            $scope.KPIList = [];
            var employeeId = parseInt(angular.element(document.getElementById('employeeId')).val());
            var appraisalId = parseInt(angular.element(document.getElementById('appraisalId')).val());
            var currentStageId = parseInt(angular.element(document.getElementById('currentStageId')).val());
            $scope.KPITemplate = {
                counter: 1,
                sno: 0,
                title: "",
                successCriteria: "",
                weight: "",
                keyAchievement: "",
                selfRating: "",
                appraiserRating: "",
                checkbox: "checkboxq0",
                checked: false
            };
            $scope.counter = "";
            $scope.viewKPIList = function () {
                App.blockUI({target: "#hris-page-content"});
                window.app.pullDataById(document.restfulUrl, {
                    action: 'pullAppraisalKPIList',
                    data: {
                        'employeeId': employeeId,
                        'appraisalId': appraisalId
                    }
                }).then(function (success) {
                    $scope.$apply(function () {
                        var appraisalKPIList = success.data;
                        var appraisalKPINum = (typeof success.data !== "undefined") ? success.data.length : 0;
                        ;
                        console.log(appraisalKPIList);
                        if (appraisalKPINum > 0) {
                            $scope.counter = appraisalKPINum;
                            for (var j = 0; j < appraisalKPINum; j++) {
                                $scope.KPIList.push(angular.copy({
                                    counter: (j + 1),
                                    sno: appraisalKPIList[j].SNO,
                                    title: appraisalKPIList[j].TITLE,
                                    successCriteria: appraisalKPIList[j].SUCCESS_CRITERIA,
                                    weight: parseInt(appraisalKPIList[j].WEIGHT),
                                    keyAchievement: appraisalKPIList[j].KEY_ACHIEVEMENT,
                                    selfRating: parseFloat(appraisalKPIList[j].SELF_RATING),
                                    appraiserRating: parseFloat(appraisalKPIList[j].APPRAISER_RATING),
                                    checkbox: "checkboxq" + j,
                                    checked: false
                                }));
                            }
                        } else {
                            $scope.counter = 1;
                            $scope.KPIList.push(angular.copy($scope.KPITemplate));
                        }
                    });
                    App.unblockUI("#hris-page-content");
                }, function (failure) {
                    console.log(failure);
                });
            }
            if ((typeof employeeId == 'undefined' || employeeId !== 0) && (typeof appraisalId == 'undefined' || appraisalId !== 0)) {
                $scope.viewKPIList();
            } else {
                $scope.counter = 1;
                $scope.KPIList.push(angular.copy($scope.KPITemplate));
            }
            $scope.KPIErrorMsg = "";
            $scope.sumAllTotal = function (list) {
                var total = 0;
                if (typeof list !== 'undefined' && list.length>0) {
                    console.log(list.length);
                    console.log(list[0].weight);
                    angular.forEach(list, function (item) {
                        var total1 = parseInt(item.weight);
                        total += parseInt(total1);
                    });
                    if (list.length == 1 && list[0].weight == 100) {
                        $scope.sumTotal = true;
                        $scope.KPIErrorMsg = "A single KPI weight should not be 100%";
                    } else {
                        if (total > 100) {
                            $scope.sumTotal = true;
                            $scope.KPIErrorMsg = "Total Weight Must be 100%.The total weight of your KPI is " + total;
                        } else if (total < 100) {
                            $scope.sumTotal = true;
                            $scope.KPIErrorMsg = "Total weight must be 100%. The total weight of your KPI is " + total;
                        } else {
                            $scope.sumTotal = false;
                            $scope.KPIErrorMsg = "";
                        }
                    }
                }
            }
            $scope.calculateAnnualRating = function (list) {
                var total = 0;
                if (typeof list !== 'undefined' && list.length>0) {
                    console.log(list);
                    angular.forEach(list, function (item) {
                        var weight = parseInt(item.weight);
                        var appraiserRating = parseFloat(item.appraiserRating);
                        var total1 = appraiserRating * (weight / 100);
                        total += parseFloat(total1);
                    });
                    console.log(total);
                    var annualRatingCompetency = angular.element(document.getElementById('annualRatingCompetency')).val();
                    var appraiserOverallRating = angular.element(document.getElementById('appraiserOverallRating'));
                    if(!isNaN(total)||(annualRatingCompetency!="" && annualRatingCompetency!=null)){
                       appraiserOverallRating.val(((!isNaN(total))?(window.app.floatToRound(total, 2)):"") + annualRatingCompetency);
                    }
                    $scope.annualRating =  window.app.floatToRound(total, 2);
                }
            }
            $scope.addKPI = function () {
                console.log("hellow");
                $scope.KPIList.push(angular.copy({
                    counter: parseInt($scope.counter + 1),
                    sno: 0,
                    title: "",
                    successCriteria: "",
                    weight: "",
                    keyAchievement: "",
                    selfRating: "",
                    appraiserRating: "",
                    checkbox: "checkboxq" + $scope.counter,
                    checked: false
                }));
                $scope.counter++;
            }
            $scope.deleteKPI = function () {
                var tempId = 0;
                var length = $scope.KPIList.length;
                for (var i = 0; i < length; i++) {
                    if ($scope.KPIList[i - tempId].checked) {
                        var sno = $scope.KPIList[i - tempId].sno;
                        if (sno != 0) {
                            window.app.pullDataById(document.restfulUrl, {
                                action: "deleteAppraisalKPI",
                                data: {
                                    "sno": sno
                                }
                            }).then(function (success) {
                                $scope.$apply(function () {
                                    console.log(success.data);
                                });
                            }, function (failure) {
                                console.log(failure);
                            });
                        }
                        $scope.KPIList.splice(i - tempId, 1);
                        tempId++;
//                        $scope.counter-1;
                    }
                }
            }
            $scope.submitKPIForm = function () {
                if ($scope.KPIForm.$valid) {
                    console.log("form is going to be submitted");
                    var annualRating = parseFloat(angular.element(document.getElementById('annualRating')).val());
                    var currentUser = angular.element(document.getElementById('currentUser')).val();
                    console.log(annualRating);
                    App.blockUI({target: "#hris-page-content"});
                    window.app.pullDataById(document.restfulUrl, {
                        action: "submitAppraisalKPI",
                        data: {
                            KPIList: $scope.KPIList,
                            employeeId: employeeId,
                            appraisalId: appraisalId,
                            annualRatingKPI: annualRating,
                            currentUser: currentUser
                        },
                    }).then(function (success) {
                        $scope.$apply(function () {
                            console.log(success);

                            if (currentStageId != 7) {
                                $('.nav-tabs a[href="#portlet_tab2_COM"]').tab('show');
                                $scope.KPIList = [];
                                $scope.viewKPIList();
                                App.unblockUI("#hris-page-content");
                            }
                            if (currentStageId == 7) {
                                $window.location.href = document.listurl;
                                $window.localStorage.setItem("msg", "Appraisal Successfully Submitted!!!");
                            }
                        });
                    }, function (failure) {
                        App.unblockUI("#hris-page-content");
                        console.log(failure);
                    });
                }
            }
        });

angular.module("hris", ["kpiModule", "competenciesModule"]);
