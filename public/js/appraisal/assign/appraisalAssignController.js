(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
    });
})(window.jQuery, window.app);

angular.module('hris', ['ui.bootstrap'])
        .controller('appraisalAssignController', function ($scope, $uibModal, $log, $document) {
            $scope.employeeList = [];
            $scope.all = false;
            $scope.assignShowHide = false;
            $scope.showHideAssignBtn = false;
            var $tableContainer = $("#loadingTable");
            $scope.view = function () {
                $scope.all = false;
                $scope.assignShowHide = false;
                var companyId = angular.element(document.getElementById('companyId')).val();
                var serviceTypeId = angular.element(document.getElementById('serviceTypeId')).val();
                var positionId = angular.element(document.getElementById('positionId')).val();
                var branchId = angular.element(document.getElementById('branchId')).val();
                var departmentId = angular.element(document.getElementById('departmentId')).val();
                var designationId = angular.element(document.getElementById('designationId')).val();
                var employeeId = angular.element(document.getElementById('employeeId')).val();
                var appraisalId = angular.element(document.getElementById('appraisalId')).val();
                console.log(appraisalId);
                App.blockUI({target: "#hris-page-content"});
                window.app.pullDataById(document.url, {
                    action: 'pullEmployeeWidAssignDetail',
                    data: {
                        branchId: branchId,
                        departmentId: departmentId,
                        designationId: designationId,
                        employeeId: employeeId,
                        appraisalId: appraisalId,
                        companyId: companyId,
                        serviceTypeId: serviceTypeId,
                        positionId: positionId
                    }
                }).then(function (success) {
                    App.unblockUI("#hris-page-content");
                    console.log("Employee list for assign", success);
                    $scope.$apply(function () {
                        $scope.employeeList = success.data;
                        console.log(success.data);
                        for (var i = 0; i < $scope.employeeList.length; i++) {
                            $scope.employeeList[i].checked = false;
                        }
                    });
                }, function (failure) {
                    App.unblockUI("#hris-page-content");
                    console.log("Employee Get All", failure);
                });
            };
            var employeeIdFromParam = window.location.href.substr(window.location.href.lastIndexOf('/') + 1);
            if (parseInt(employeeIdFromParam) > 0) {
                angular.element(document.getElementById('employeeId')).val(employeeIdFromParam).change();
                $scope.view();
            }
            var l = null;
            $scope.checkAll = function (item) {
                for (var i = 0; i < $scope.employeeList.length; i++) {
                    $scope.employeeList[i].checked = item;
                }
                $scope.assignShowHide = item && ($scope.employeeList.length > 0);
                if ($scope.assignShowHide) {
                    l = Ladda.create(document.querySelector('#assignBtn'));
                }
            };
            $scope.checkUnit = function (item) {
                for (var i = 0; i < $scope.employeeList.length; i++) {
                    if ($scope.employeeList[i].checked) {
                        $scope.assignShowHide = true;
                        l = Ladda.create(document.querySelector('#assignBtn'));
                        break;
                    }
                    $scope.assignShowHide = false;
                }
            };
            $scope.appraiserOptions = document.employeeList;
            $scope.appraiserSelected = $scope.appraiserOptions[0]
            $scope.reviewerOptions = document.employeeList;
            $scope.reviewerSelected = $scope.reviewerOptions[0];

            var test = [];
            jQuery.merge(test, document.employeeList);
            test.unshift({'id': '-1', 'name': 'none'});
            $scope.altAppraiserOptions = test;
            $scope.altAppraiserSelected = $scope.altAppraiserOptions[0]
            $scope.altReviewerOptions = test;
            $scope.altReviewerSelected = $scope.altReviewerOptions[0];
            $scope.superReviewerOptions = test;
            $scope.superReviewerSelected = $scope.superReviewerOptions[0];
            // MODEL CODE
            $ctrl = this;
            $ctrl.animationsEnabled = false;
            $scope.open = function (type) {
                console.log(type);
                var modalInstance = $uibModal.open({
                    animation: $ctrl.animationsEnabled,
                    ariaLabelledBy: 'modal-title',
                    ariaDescribedBy: 'modal-body',
                    templateUrl: 'myModalContent.html',
                    controller: function ($scope, $uibModalInstance) {
                        if (parseInt(type) == 2) {
                            $scope.role = 'Reviewer';
                        } else if (parseInt(type) == 3) {
                            $scope.role = 'Appraiser';
                        } else if (type == 'A2') {
                            $scope.role = 'Alternativce Reviewer';
                        } else if (type == 'A3') {
                            $scope.role = 'Alternative Appraiser';
                        } else if (type == 'S2') {
                            $scope.role == 'Super Reviewer';
                        }
                        $scope.cancel = function () {
                            $uibModalInstance.dismiss('cancel');
                        };
                        $scope.filterForRole = function () {
                            var companyId = angular.element(document.getElementById('recomCompanyId')).val();
                            var branchId = angular.element(document.getElementById('recomBranchId')).val();
                            var departmentId = angular.element(document.getElementById('recomDepartmentId')).val();
                            var designationId = angular.element(document.getElementById('recomDesignationId')).val();
                            var employeeId = angular.element(document.getElementById('recomEmployeeId')).val();
                            window.app.pullDataById(document.url, {
                                action: 'pullEmployeeListForReportingRole',
                                data: {
                                    branchId: branchId,
                                    departmentId: departmentId,
                                    designationId: designationId,
                                    companyId: companyId,
                                    employeeId: employeeId
                                }
                            }).then(function (success) {
                                console.log("Employee list for success", success.data);
                                $scope.$apply(function () {
                                    $uibModalInstance.close(success.data);
                                });
                            }, function (failure) {
                                console.log("Employee list for failure", failure);
                            });
                        }
                    },
                    controllerAs: '$ctrl'
                });
                modalInstance.result.then(function (selectedItem) {
                    if (parseInt(type) === 2) {
                        $scope.reviewerOptions = selectedItem;
                        $scope.reviewerSelected = $scope.reviewerOptions[0];
                        $scope.reviewerAssign = true;
                        $scope.showHideAssignBtn = true;
                    } else if (parseInt(type) === 3) {
                        $scope.appraiserOptions = selectedItem;
                        $scope.appraiserSelected = $scope.appraiserOptions[0];
                        $scope.appraiserAssign = true;
                        $scope.showHideAssignBtn = true;
                    } else if (type === 'A2') { //for alternate reviewer
                        selectedItem.unshift({'id': '-1', 'name': 'none'});
                        $scope.altReviewerOptions = selectedItem;
                        $scope.altReviewerSelected = $scope.altReviewerOptions[0];
                    } else if (type === 'A3') { //for alternate appraiser
                        selectedItem.unshift({'id': '-1', 'name': 'none'});
                        $scope.altAppraiserOptions = selectedItem;
                        $scope.altAppraiserSelected = $scope.altAppraiserOptions[0];
                    } else if (type === 'S2') { //for super reviewer
                        selectedItem.unshift({'id': '-1', 'name': 'none'});
                        $scope.superReviewerOptions = selectedItem;
                        $scope.superReviewerSelected = $scope.superReviewerOptions[0];
                    }
                    console.log("Model closed with following result", selectedItem);
                }, function () {
                    console.log("Model Disposed");
                });
                modalInstance.rendered.then(function () {
                    $("select").select2();
                    comBranchDeptDesignSearch("recomCompanyId", "recomBranchId", "recomDepartmentId", "recomDesignationId", "recomEmployeeId");
                });
            };
            $scope.checkReportingHierarchy = function () {
                if ($scope.reviewerAssign || $scope.appraiserAssign || $scope.altAppraiserAssign || $scope.altReviewerAssign || $scope.superReviewerAssign || $scope.stageAssign) {
                    $scope.showHideAssignBtn = true;
                } else {
                    $scope.showHideAssignBtn = false;
                }
            }
            $scope.assign = function () {
                var reviewerElement = angular.element(document.getElementById('reviewerId'));
                var reviewerId = reviewerElement.val();
                var reviewerName = document.getElementById('reviewerId').options[document.getElementById('reviewerId').selectedIndex].text;

                var appraiserElement = angular.element(document.getElementById('appraiserId'));
                var appraiserId = appraiserElement.val();
                var appraiserName = document.getElementById('appraiserId').options[document.getElementById('appraiserId').selectedIndex].text;

                var appraisalElement = angular.element(document.getElementById('appraisalId'));
                var appraisalId = appraisalElement.val();
                var appraisalName = document.getElementById('appraisalId').options[document.getElementById('appraisalId').selectedIndex].text;

                var altAppraiserElement = angular.element(document.getElementById('altAppraiserId'));
                var altAppraiserId = altAppraiserElement.val();
                var altAppraiserName = document.getElementById('altAppraiserId').options[document.getElementById('altAppraiserId').selectedIndex].text;

                var altReviewerElement = angular.element(document.getElementById('altReviewerId'));
                var altReviewerId = altReviewerElement.val();
                var altReviewerName = document.getElementById('altReviewerId').options[document.getElementById('altReviewerId').selectedIndex].text;
                console.log(appraiserId);

                var superReviewerElement = angular.element(document.getElementById('superReviewerId'));
                var superReviewerId = superReviewerElement.val();
                var superReviewerName = document.getElementById('superReviewerId').options[document.getElementById('superReviewerId').selectedIndex].text;

                var stageElement = angular.element(document.getElementById('stageId'));
                var stageId = stageElement.val();
                var stageName = document.getElementById('stageId').options[document.getElementById('stageId').selectedIndex].text;

                var errorFlagR = false;
                if ($scope.reviewerAssign) {
                    if (reviewerId == "?") {
                        window.app.errorMessage(
                                "Reviewer is required!!!",
                                "Application Error"
                                );
                        errorFlagR = true;
                    } else {
                        errorFlagR = false;
                    }
                } else {
                }
                var errorFlagA = false;
                if ($scope.appraiserAssign) {
                    if (appraiserId == "?") {
                        window.app.errorMessage(
                                "Appraiser is required!!!"
                                ,
                                "Application Error"
                                );
                        errorFlagA = true;
                    } else {
                        errorFlagA = false;
                    }
                } else {
                }

                if (!errorFlagR && !errorFlagA) {
                    App.blockUI({target: "#hris-page-content"});
                    submitRecord(reviewerId, reviewerName, appraiserId, appraiserName, appraisalId, appraisalName, altAppraiserName, altAppraiserId, altReviewerName, altReviewerId, superReviewerId, superReviewerName, stageId, stageName);
                }
            };
            var submitRecord = function (reviewerId, reviewerName, appraiserId, appraiserName, appraisalId, appraisalName, altAppraiserName, altAppraiserId, altReviewerName, altReviewerId, superReviewerId, superReviewerName, stageId, stageName) {
                var promises = [];

                if (!$scope.reviewerAssign) {
                    var reviewerId1 = null;
                } else {
                    var reviewerId1 = reviewerId;
                }

                if (!$scope.altReviewerAssign) {
                    var altReviewerId1 = null;
                } else {
                    var altReviewerId1 = altReviewerId;
                }

                if (!$scope.superReviewerAssign) {
                    var superReviewerId1 = null;
                } else {
                    var superReviewerId1 = superReviewerId;
                }

                if (!$scope.appraiserAssign) {
                    var appraiserId1 = null;
                } else {
                    var appraiserId1 = appraiserId;
                }

                if (!$scope.altAppraiserAssign) {
                    var altAppraiserId1 = null;
                } else {
                    var altAppraiserId1 = altAppraiserId;
                }

                if (!$scope.stageAssign) {
                    var stageId1 = null;
                } else {
                    var stageId1 = stageId;
                }

                for (var index in $scope.employeeList) {
                    if ($scope.employeeList[index].checked) {
                        promises.push(window.app.pullDataById(document.url, {
                            action: 'assignAppraisal',
                            data: {
                                employeeId: $scope.employeeList[index].EMPLOYEE_ID,
                                reviewerId: reviewerId1,
                                appraiserId: appraiserId1,
                                appraisalId: appraisalId,
                                altAppraiserId: altAppraiserId1,
                                altReviewerId: altReviewerId1,
                                superReviewerId: superReviewerId1,
                                stageId: stageId1
                            }
                        }));
                    }
                }
                Promise.all(promises).then(function (success) {
                    App.unblockUI("#hris-page-content");
                    $scope.$apply(function () {
                        for (var index in $scope.employeeList) {
                            if ($scope.employeeList[index].checked) {
                                if ($scope.reviewerAssign) {
                                    if ($scope.employeeList[index].EMPLOYEE_ID == reviewerId) {
                                        var reviewerNameNew = null;
                                    } else {
                                        var reviewerNameNew = reviewerName;
                                    }

                                    $scope.employeeList[index].REVIEWER_NAME = reviewerNameNew;
                                    $scope.employeeList[index].APPRAISAL_EDESC = appraisalName;
                                }
                                if ($scope.altReviewerAssign) {
                                    if ($scope.employeeList[index].EMPLOYEE_ID == altReviewerId) {
                                        var altReviewerNameNew = null;
                                    } else {
                                        var altReviewerNameNew = (altReviewerName == "none") ? "" : altReviewerName;
                                    }
                                    $scope.employeeList[index].ALT_REVIEWER_NAME = altReviewerNameNew;
                                }

                                if ($scope.superReviewerAssign) {
                                    if ($scope.employeeList[index].EMPLOYEE_ID == superReviewerId) {
                                        var superReviewerNameNew = null;
                                    } else {
                                        var superReviewerNameNew = (superReviewerName == "none") ? "" : superReviewerName;
                                    }
                                    $scope.employeeList[index].SUPER_REVIEWER_NAME = superReviewerNameNew;
                                }

                                if ($scope.appraiserAssign) {
                                    if ($scope.employeeList[index].EMPLOYEE_ID == appraiserId) {
                                        var appraiserNameNew = null;
                                    } else {
                                        var appraiserNameNew = appraiserName;
                                    }
                                    $scope.employeeList[index].APPRAISER_NAME = appraiserNameNew;
                                    $scope.employeeList[index].APPRAISAL_EDESC = appraisalName;
                                }
                                if ($scope.altAppraiserAssign) {
                                    if ($scope.employeeList[index].EMPLOYEE_ID == altAppraiserId) {
                                        var altAppraiserNameNew = null;
                                    } else {
                                        var altAppraiserNameNew = (altAppraiserName == 'none') ? "" : altAppraiserName;
                                    }
                                    $scope.employeeList[index].ALT_APPRAISER_NAME = altAppraiserNameNew;
                                }
                                if ($scope.stageAssign) {
                                    if ($scope.employeeList[index].STAGE_ID == stageId) {
                                        var stageNameNew = null;
                                    } else {
                                        var stageNameNew = stageName;
                                    }
                                    $scope.employeeList[index].CURRENT_STAGE_NAME = stageNameNew;
                                } else if ($scope.employeeList[index].CURRENT_STAGE_NAME == null) {
                                    $scope.employeeList[index].CURRENT_STAGE_NAME = success[0].data.CURRENT_STAGE_NAME;
                                }
                                console.log(success[0].data.CURRENT_STAGE_NAME);
                            }
                        }
                    });
                    window.toastr.success("Reporting Hierarchy for Appraisal Assigned Successfully!", "Notification");
                });
            };
        });
 