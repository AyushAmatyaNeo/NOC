(function ($, app) {
    'use strict';
    $("#rePassword").on("blur keyup", function () {
        var rePassword = $(this).val();
        var password = $("#password").val();
        checkPwd(password, rePassword);
    });

    var checkPwd = function (password, rePassword) {
        if (rePassword !== password) {
            $("#errorMsgRePwd").html("* Password doesn't match!!!.");
        } else {
            $("#errorMsgRePwd").html("");
        }
    }


    $("#password").on("blur keyup", function () {
        var passwordValue = $(this).val();
        var reg = /^(?=.*?[A-Z])(?=(.*[a-z]){1,})(?=(.*[\d]){1,})(?=(.*[\W]){1,})(?!.*\s).{8,}$/;

        if (!reg.test(passwordValue)) {
            $("#errorMsgPwd").html("The password should be at least 8 character long and should contain Numeric, Alphabet, Capital Letter, Symbol Combinations");
        }
        else {
            $("#errorMsgPwd").html("");
            checkPwd(passwordValue, $("#rePassword").val());
        }
    });

    $('#usernameForm').submit(function (e) {

        var err = [];
        $(".errorMsg").each(function () {
            var erroMsg = $.trim($(this).html());
            if (erroMsg !== "") {
                err.push("error");
            }
        });
        if (err.length > 0)
        {
            return false;
        }
    });
})(window.jQuery, window.app);
