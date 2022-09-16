angular.module('hris', [])
        .controller('questionOptionController', function ($scope, $http,$window) {
            $scope.headings = document.headings;
            $scope.answerTypeList = document.answerTypes;
            $scope.questionOptionList = [];
            $scope.questionOptionTemplate = {
                optionId : 0,
                optionEdesc: "",
                optionNdesc: "",
                checkbox: "checkboxq0",
                checked: false
            };
            $scope.question = {
                questionCode: '',
                questionEdesc: '',
                questionNdesc: '',
                answerType: '',
                headingId: '',
                orderNo: '',
                appraiseeFlag: 'Y',
                appraiserFlag: 'Y',
                reviewerFlag: 'Y',
                appraiseeRating: 'Y',
                appraiserRating: 'Y',
                reviewerRating: 'Y',
                minValue: '',
                maxValue: '',
                remarks: ''
            };
            var questionId = parseInt(angular.element(document.getElementById('questionId')).val());
            if (questionId!==0) {
                window.app.pullDataById(document.urlPullQuestionDtl, {
                        data: {
                            'questionId': questionId
                        }
                    }).then(function (success) {
                        $scope.$apply(function () {
                           var tempData = success.data;
                           var questionDetail = tempData.questionDetail
                           var questionOptionList = tempData.questionOptionList;
                           var num = tempData.num;
                           console.log(questionDetail);
                           $scope.question.questionCode = questionDetail.QUESTION_CODE;
                           $scope.question.questionEdesc = questionDetail.QUESTION_EDESC;
                           $scope.question.questionNdesc = questionDetail.QUESTION_NDESC;
                           $scope.question.answerType = questionDetail.ANSWER_TYPE;
                           $scope.question.headingId = questionDetail.HEADING_ID;
                           $scope.question.orderNo = parseInt(questionDetail.ORDER_NO);
                           $scope.question.appraiseeFlag = questionDetail.APPRAISEE_FLAG;
                           $scope.question.appraiserFlag = questionDetail.APPRAISER_FLAG;
                           $scope.question.reviewerFlag = questionDetail.REVIEWER_FLAG;
                           $scope.question.appraiseeRating = questionDetail.APPRAISEE_RATING;
                           $scope.question.reviewerRating = questionDetail.REVIEWER_RATING;
                           $scope.question.appraiserRating = questionDetail.APPRAISER_RATING;
                           $scope.question.minValue = parseInt(questionDetail.MIN_VALUE);
                           $scope.question.maxValue = parseInt(questionDetail.MAX_VALUE);
                           $scope.question.remarks = questionDetail.REMARKS;
                           
                           if(num>0){
                                $scope.counter = num;
                                for (var j = 0; j < num; j++) {
                                    $scope.questionOptionList.push(angular.copy({
                                        optionId: tempData.questionOptionList[j].OPTION_ID,
                                        optionEdesc: tempData.questionOptionList[j].OPTION_EDESC,
                                        optionNdesc: tempData.questionOptionList[j].OPTION_NDESC,
                                        checkbox: "checkboxq" + j,
                                        checked: false
                                    }));
                                }
                           }else{
                               $scope.questionOptionList.push(angular.copy($scope.questionOptionTemplate));
                           }
                        });
                    },function(failure){
                        console.log(failure);
                    });
            }else{
                $scope.question.answerType = Object.keys($scope.answerTypeList)[0];
                $scope.question.headingId = Object.keys($scope.headings)[0];
                $scope.questionOptionList.push(angular.copy($scope.questionOptionTemplate));

            }
            
            $scope.counter = 1;
            $scope.addQuestionOption = function () {
                $scope.questionOptionList.push(angular.copy({
                    optionId:0,
                    optionEdesc: "",
                    optionNdesc: "",
                    checkbox: "checkboxq"+$scope.counter,
                    checked: false
                }));
                $scope.counter++;
            }
            
            $scope.submitForm = function () {
                
                if ($scope.appraisalQuestionForm.$valid) {
                    console.log("hellow");
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
                    
                    $scope.optionListEmpty = 1;
                    if (($scope.questionOptionList.length == 1 && angular.equals($scope.questionOptionTemplate, $scope.questionOptionList[0]))||($scope.questionOptionList.length==0)) {
                        console.log("app log", "The form is not filled");
                        $scope.optionListEmpty = 0;
                    }
                    console.log(($scope.questionOptionList.length));
                    App.blockUI({target: "#hris-page-content"});
                    window.app.pullDataById(document.urlSubmit, {
                        data: {
                            questionOptionList: ($scope.questionOptionList.length!==0)?$scope.questionOptionList:null,
                            questionDetail: $scope.question,
                            questionId:parseInt(questionId),
                            optionListEmpty:parseInt($scope.optionListEmpty)
                        },
                    }).then(function (success) {
                        $scope.$apply(function () {
                            console.log(success.data);
                            $window.location.href =  document.urlIndex;
                            $window.localStorage.setItem("msg",success.data);
                            App.unblockUI("#hris-page-content");
//                            setTimeout(function() {
//                               window.toastr.success(params, "Notifications");
//                            }, 3000);
                        });
                    }, function (failure) {
                        App.unblockUI("#hris-page-content");
                        console.log(failure);
                    });
                }
            }
            $scope.delete = function () {
                var tempId = 0;
                var length = $scope.questionOptionList.length;
                for (var i = 0; i < length; i++) {
                    if ($scope.questionOptionList[i - tempId].checked) {
                        var optionId = $scope.questionOptionList[i - tempId].optionId;
                        if (optionId != 0) {
                            window.app.pullDataById(document.urlDeleteQuestionOption, {
                                data: {
                                    "optionId": optionId
                                }
                            }).then(function (success) {
                                $scope.$apply(function () {
                                    console.log(success.data);
                                });
                            }, function (failure) {
                                console.log(failure);
                            });
                        }
                        $scope.questionOptionList.splice(i - tempId, 1);
                        tempId++;
                    }
                }
            }
            console.log($scope.questionOptionList);
        });