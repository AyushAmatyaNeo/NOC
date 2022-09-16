angular.module("hris", [])
        .controller("stageQuestionController", function ($scope, $http) {
            var questionId = "";
            
            $scope.getAssignedStageList = function (questionId) {
                window.app.pullDataById(document.url, {
                    action: "getAssignedStagList",
                    data: {
                        questionId: questionId
                    }
                }).then(function (success) {
                    $scope.$apply(function () {
                        var tempData = success.data;
                        $scope.stageList = tempData.stageList;
                        $scope.assignedList = tempData.assignedStageList;
                        console.log(tempData);
                        for (var i in $scope.stageList) {
                            for (var j in $scope.assignedList) {
                                if ($scope.stageList[i].STAGE_ID == $scope.assignedList[j].STAGE_ID) {
                                    $scope.stageList[i].checked = true;
                                    break;
                                }
                            }
                        }
                    });
                }, function (failure) {
                    console.log(failure);
                });
            }

            $(document).on("click", '#tree_3 ul li a', function () {
                $('#stageAssign').css('display', 'block');

                var attrId = $(this).attr("id");
                questionId = attrId.split("_")[0];
                console.log(questionId);
                $scope.$apply(function () {
                    $scope.getAssignedStageList(questionId);
                });
            });
            $scope.assignStage = function (stageDetail) {
                console.log(questionId);
                var stageId = stageDetail.STAGE_ID;
                var checked = stageDetail.checked;
                window.app.pullDataById(document.url, {
                    action: 'stageAssign',
                    data: {
                        stageId: stageId,
                        questionId: questionId,
                        checked: checked
                    },
                }).then(function (success) {
                    window.toastr.success(success.data.msg, "Notifications");

                }, function (failure) {
                    console.log(failure);
                });
            }
        });