angular.module('competenciesModule', ['use', 'ngMessages'])
        .controller("competenciesController", function ($scope, $http,$window) {
            $scope.competenciesList = [];
            var employeeId = parseInt(angular.element(document.getElementById('employeeId')).val());
            var appraisalId = parseInt(angular.element(document.getElementById('appraisalId')).val());
            var currentStageId = parseInt(angular.element(document.getElementById('currentStageId')).val());
            $scope.ratingNames = ["A", "B", "C"];
            $scope.competenciesTemplate = {
                counter: 1,
                sno: 0,
                title: "",
                rating:"",
                comments:"",
                checkbox: "checkboxc0",
                checked: false
            };
            $scope.counter = "";
            $scope.viewCompetenciesList = function () {
                window.app.pullDataById(document.restfulUrl, {
                    action: 'pullAppraisalCompetenciesList',
                    data: {
                        'employeeId': employeeId,
                        'appraisalId': appraisalId
                    }
                }).then(function (success) {
                    $scope.$apply(function () {
                        var appraisalCompetenciesList = success.data;
                        var appraisalCompetenciesNum =(typeof success.data!=="undefined")?success.data.length:0;
                        console.log(appraisalCompetenciesList);
                        if (appraisalCompetenciesNum > 0) {
                            $scope.counter = appraisalCompetenciesNum;
                            for (var j = 0; j < appraisalCompetenciesNum; j++) {
                                $scope.competenciesList.push(angular.copy({
                                    counter: (j + 1),
                                    sno: appraisalCompetenciesList[j].SNO,
                                    title: appraisalCompetenciesList[j].TITLE,
                                    rating: appraisalCompetenciesList[j].RATING,
                                    comments: appraisalCompetenciesList[j].COMMENTS,
                                    checkbox: "checkboxc" + j,
                                    checked: false
                                }));
                            }
                        } else {
                            $scope.counter = 1;
                            $scope.competenciesList.push(angular.copy($scope.competenciesTemplate));
                        }
                    });
                }, function (failure) {
                    console.log(failure);
                });
            }
            if ((typeof employeeId == 'undefined' || employeeId !== 0) && (typeof appraisalId == 'undefined' || appraisalId !== 0)) {
                $scope.viewCompetenciesList();
            } else {
                $scope.counter = 1;
                $scope.competenciesList.push(angular.copy($scope.competenciesTemplate));
            }
            $scope.addCompetencies = function () {
                console.log("hellow");
                $scope.competenciesList.push(angular.copy({
                    counter: parseInt($scope.counter + 1),
                    sno: 0,
                    title: "",
                    rating:"",
                    comments:"",
                    checkbox: "checkboxc" + $scope.counter,
                    checked: false
                }));
                $scope.counter++;
            }
            $scope.deleteCompetencies = function () {
                var tempId = 0;
                var length = $scope.competenciesList.length;
                for (var i = 0; i < length; i++) {
                    if ($scope.competenciesList[i - tempId].checked) {
                        var sno = $scope.competenciesList[i - tempId].sno;
                        if (sno != 0) {
                            window.app.pullDataById(document.restfulUrl, {
                                action: "deleteAppraisalCompetencies",
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
                        $scope.competenciesList.splice(i - tempId, 1);
                        tempId++;
                    }
                }
            }
            $scope.submitCompetenciesForm = function () {
                console.log("form is going to be submitted");
                if ($scope.competenciesForm.$valid) {
                    var annualRatingCompetency = angular.element(document.getElementById('annualRatingCompetency')).val();
                    var appraiserOverallRating = angular.element(document.getElementById('appraiserOverallRating')).val();
                    var currentUser = angular.element(document.getElementById('currentUser')).val();
                    console.log(annualRatingCompetency);
                    console.log($scope.competenciesList);
//                    App.blockUI({target: "#hris-page-content"});
                    window.app.pullDataById(document.restfulUrl, {
                        action: "submitAppraisalCompetencies",
                        data: {
                            competenciesList: $scope.competenciesList,
                            employeeId: employeeId,
                            appraisalId: appraisalId,
                            annualRatingCompetency:annualRatingCompetency,
                            appraiserOverallRating:appraiserOverallRating,
                            currentUser:currentUser
                        },
                    }).then(function (success) {
                        $scope.$apply(function () {
                            console.log(success);
                            if(currentStageId==5){
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
                                $('.nav-tabs a[href="#portlet_tab2_2"]').tab('show');
                                $scope.competenciesList = [];
                                $scope.viewCompetenciesList();
                                App.unblockUI("#hris-page-content");
                            }
                            if(currentStageId==1){
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
                                $window.location.href = document.listurl;
                                $window.localStorage.setItem("msg","Appraisal Successfully Submitted!!!");
                            }
//                            App.unblockUI("#hris-page-content");
                        });
                    }, function (failure) {
                        App.unblockUI("#hris-page-content");
                        console.log(failure);
                    });
                }
            }
        });
        
//        angular.module("hris", ["competenciesModule"]);

(function ($) {
    'use strict';
    $(document).ready(function () {
        $("#annualRatingCompetency").on("change",function(){
            var annualRatingCompetency = $(this).val();
            var annualRatingKPI = $("#annualRating").val();
            $('#appraiserOverallRating').val(annualRatingKPI + annualRatingCompetency);
        });
    });
})(window.jQuery);
