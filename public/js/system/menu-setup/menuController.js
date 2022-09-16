var angularApp = angular.module('hris', ['ui.bootstrap']);

angularApp.controller('menuUpdateController', function ($scope, $uibModal, $log, $document) {
    var menuId = "";
    $scope.menuDtl = {
        menuCode: '',
        menuName: '',
        route: '',
        action: '',
        menuIndex: '',
        iconClass: '',
        menuDescription: '',
        menuId: '',
        isVisible: 'Y'
    };

    $scope.permissionList = function (menuId) {
        $('#rolePanel').css('display', 'block');
        window.app.pullDataById(document.pullRolePermissionListLink, {
            menuId: menuId
        }).then(function (success) {
            $scope.$apply(function () {
                $scope.roleList = success.data;
                $scope.assignedList = success.data1;

                for (var i in $scope.roleList) {
                    for (var j in $scope.assignedList) {
                        if ($scope.roleList[i].ROLE_ID == $scope.assignedList[j].ROLE_ID) {
                            $scope.roleList[i].checked = true;
                            break;
                        }
                    }
                }
            });
        }, function (failure) {
            console.log(failure);
        });

    };
    $scope.assignRole = function (roleDtl) {
        var roleId = roleDtl.ROLE_ID;
        var checked = roleDtl.checked;
        var selectedMenu = menuId;

        window.app.pullDataById(document.permissionAssignLink, {
            roleId: roleId,
            menuId: selectedMenu,
            checked: checked
        }).then(function (success) {
            window.toastr.success(success.data, "Notifications");

        }, function (failure) {
            console.log(failure);
        });
    }
    $scope.isDisabled = true;
    $(document).on('click', '#tree_3 ul li a', function () {
        $('#editForm').css('display', 'block');

        var attrId = $(this).attr("id");
        menuId = attrId.split("_")[0];

        $scope.$apply(function () {
            $scope.permissionList(menuId);
        });
        App.blockUI({target: "#hris-page-content"});
        window.app.pullDataById(document.pullMenuDetailLink, {
            id: menuId
        }).then(function (success) {
            App.unblockUI("#hris-page-content");
            $scope.$apply(function () {
                var temp = success.data;
                console.log(temp);
                $scope.menuDtl.menuId = temp.MENU_ID;
                $scope.menuDtl.menuCode = temp.MENU_CODE;
                $scope.menuDtl.menuName = temp.MENU_NAME;
                $scope.menuDtl.route = temp.ROUTE;
                $scope.menuDtl.action = temp.ACTION;
                $scope.menuDtl.menuIndex = parseInt(temp.MENU_INDEX);
                $scope.menuDtl.iconClass = temp.ICON_CLASS;
                $scope.menuDtl.menuDescription = temp.MENU_DESCRIPTION;
                $scope.menuDtl.isVisible = temp.IS_VISIBLE;
                $scope.isDisabled = false;
            });
        }, function (failure) {
            App.unblockUI("#hris-page-content");
            console.log(failure);
        });
    });

    $scope.submitForm = function () {
        if ($scope.userForm.$valid) {
            App.blockUI({target: "#hris-page-content"});
            window.app.pullDataById(document.menuUpdateLink, {
                dataArray: $scope.menuDtl
            }).then(function (success) {
                App.unblockUI("#hris-page-content");
                console.log(success);
                $scope.$apply(function () {
                    var newData = success.menuData;
                    $("#tree_3").jstree(true).settings.core.data = newData;
                    $("#tree_3").jstree(true).refresh();

                    if (success.data != "") {
                        window.toastr.success(success.data, "Notifications");
                    }
                    if (success.menuIndexErr != "") {
                        $scope.menuIndexErr = success.menuIndexErr;
                    } else {
                        $scope.menuIndexErr = null;
                    }
                });
            }, function (failure) {
                App.unblockUI("#hris-page-content");
                console.log(failure);
            });
        }
    }
    $scope.deleteMenu = function () {
        window.app.pullDataById(document.menuDeleteLink, {
            menuId: menuId
        }).then(function (success) {
            $scope.$apply(function () {
                var newData = success.data;
                $('#editForm').css('display', 'none');
                $('#rolePanel').css('display', 'none');
                $scope.menuDtl.menuName = "";
                $scope.isDisabled = true;

                $("#tree_3").jstree(true).settings.core.data = newData;
                $("#tree_3").jstree(true).refresh();

                window.toastr.success(success.message, "Notifications");
            });
        }, function (failure) {
            console.log(failure);
        });
    }

// MODEL CODE
    $ctrl = this;
    $ctrl.animationsEnabled = false;

    $scope.open = function (type) {
        var modalInstance = $uibModal.open({
            animation: $ctrl.animationsEnabled,
            ariaLabelledBy: 'modal-title',
            ariaDescribedBy: 'modal-body',
            templateUrl: 'myModalContent.html',
            controller: function ($scope, $uibModalInstance, menuId) {
                $scope.cancel = function () {
                    $uibModalInstance.dismiss('cancel');
                };
                $scope.menuDtl = {
                    menuCode: '',
                    menuName: '',
                    route: '',
                    action: '',
                    menuIndex: '',
                    iconClass: '',
                    menuDescription: '',
                    isVisible: 'Y'
                };
                $scope.submitForm = function () {
                    if ($scope.userForm.$valid) {
                        App.blockUI({target: "#hris-page-content"});
                        window.app.pullDataById(document.menuInsertionLink, {
                            dataArray: $scope.menuDtl,
                            parentMenu: menuId
                        }).then(function (success) {
                            App.unblockUI("#hris-page-content");
                            console.log(success);
                            $scope.$apply(function () {
                                var newData = success.menuData;
                                $("#tree_3").jstree(true).settings.core.data = newData;
                                $("#tree_3").jstree(true).refresh();

                                if (success.data != "") {
                                    window.toastr.success(success.data, "Notifications");
                                    $uibModalInstance.close('cancel');
                                }
                            });
                        }, function (failure) {
                            App.unblockUI("#hris-page-content");
                            console.log(failure);
                        });
                    }
                }
            },
            controllerAs: '$ctrl',
            resolve: {
                menuId: function () {
                    return type ? menuId : null;
                }
            }
        });

        modalInstance.result.then(function (selectedItem) {
            console.log("Model closed with following result", selectedItem);
        }, function () {
            console.log("Model Disposed");
        });
    };

//   END OF MODEL CODE
});






