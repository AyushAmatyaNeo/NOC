(function ($, app) {
    'use strict';

    $(document).ready(function () {
        var $passwordChangeMenu = $('#passwordChangeMenu');
        var $passwordChangeModal = $('#changePasswordModal');

        $passwordChangeMenu.on('click', function () {
            $passwordChangeModal.modal('show');
        });


        var $oldPassword = $('#oldPassword');
        var $newPassword = $('#newPassword');
        var $reNewPassword = $('#reNewPassword');
        var $changePwdBtn = $('#changePwdBtn');

        var removeErrorSpan = function ($input) {
            var inputErrorSpan = $input.parent().find('span.errorMsg');
            if (inputErrorSpan.length > 0) {
                inputErrorSpan.remove();
            }
        };

        $changePwdBtn.on('click', function () {
            app.pullDataById(document.restfulUrl, {
                action: 'pullCurUserPwd',
                data: {}
            }).then(function (success) {

                removeErrorSpan($oldPassword);
                removeErrorSpan($newPassword);
                removeErrorSpan($reNewPassword);

                var reg = /^(?=.*?[A-Z])(?=(.*[a-z]){1,})(?=(.*[\d]){1,})(?=(.*[\W]){1,})(?!.*\s).{8,}$/;

                // console.log('sdfdsf');
                // console.log($newPassword.val());
                // console.log(reg.test($newPassword.val()));


                try {


                    if (success.data != $oldPassword.val()) {
                        throw {'message': 'Old Password is not Correct', 'object': $oldPassword};
                    }

                    if ($newPassword.val() === "") {
                        throw {'message': 'This field cannot be empty.', 'object': $newPassword};

                    }

                    if (!reg.test($newPassword.val())) {
                        throw {'message': 'the password should be at least 8 character long and should contain Numeric, Alphabet, Capital Letter, Symbol Combinations', 'object': $newPassword}
                    }


                    if ($newPassword.val() !== $reNewPassword.val()) {
                        throw {'message': 'Do Not Match With New Password', 'object': $reNewPassword};
                    }
                    app.pullDataById(document.restfulUrl, {
                        action: 'updateCurUserPwd',
                        data: {
                            'newPassword': $newPassword.val()
                        }}).then(function (e) {
                        if (e.success) {
                            $passwordChangeModal.modal('hide');
                            app.showMessage("Password changed successfully.");
                        }
                    });

                } catch (e) {
                    var $parent = e['object'].parent();
                    var $error = $('<span id="pwdMisMatchErr" class="errorMsg"></span>');
                    $error.append(e['message']);
                    if ($parent.find('span.errorMsg').length === 0) {
                        $parent.append($error);
                    }
                }

            });

        });





    });
}
)(window.jQuery, window.app);
