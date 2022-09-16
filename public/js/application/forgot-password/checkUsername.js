(function ($, app) {
    'use strict';
//    $("#username").on("blur", function () {
//        var username = $("#username").val();
//        for (var i = 0; i < document.userList.length; i++) {
//            if (document.userList[i].USER_NAME === username) {
//                $(".errorMsg").html("");
//                return true;
//            }
//        }
//        ;
//        $(".errorMsg").html("There is no account registered for this username!!!.")
//    });
    $('#usernameForm').on("submit",function (e) {
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

