(function ($, app) {
    'use strict';
    $("#code").on("blur", function () {
        var employeeId = document.employeeId;
        var code = $(this).val();
        window.app.pullDataById(document.url, {
            action: 'checkCodeDetail',
            data: {
                'employeeId': employeeId,
                'code': code
            }
        }).then(function (success) {
            var tempData = success.data;
            if (tempData.errorFlag) {
                $("span.errorMsg").html(tempData.msg);
            } else {
                $("span.errorMsg").html(tempData.msg);
            }
            console.log(success);
        }, function (failure) {
            console.log(failure);
        });
    });
    $('#codeForm').submit(function (e) {
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

