(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("#registerAttendanceForm").on("submit", function (e) {
            if (!$.trim($("#remarks").val())) {
                var errorMsgSpan = $(this).find('span');
                if (errorMsgSpan.length > 0) {
                    $(this).find('span').html("This field is required!!!");
                } else {
                    var errorMsgSpan = $('<span />', {
                        "class": 'errorMsg',
                        "style": 'margin-bottom:4%',
                        text: "This field is required!!!"
                    });
                    $("#remarks").after(errorMsgSpan);
                }
                return false;
            }
        });
        $("#remarks").blur(function(e){
             $("#registerAttendanceForm").find('span.errorMsg').remove();
        });
    });
})(window.jQuery, window.app);



