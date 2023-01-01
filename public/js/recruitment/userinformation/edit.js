(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();

        var $userName = $("#form-userName");
        var $userNameAvailability = $("#userNameAvailability");

        var userNameAvailable;

        function checkIsUserAvailable() {
            app.serverRequest(document.checkUserName, {
                userName: $userName.val(),
                userId: $('#userId').val()
            }).then(function (response) {
                console.log(response);
                if (response.success == true && response.data == 'YES') {
                    $userNameAvailability.html('Available');
                    $userNameAvailability.css('color', 'green');
                    userNameAvailable = true;
                    document.getElementById("btnSubmit").disabled = false;

                } else {
                    $userNameAvailability.html('Not Available');
                    $userNameAvailability.css('color', 'red');
                    userNameAvailable = false;
                    document.getElementById("btnSubmit").disabled = true;
                }
            });
        }


        $userName.on("blur input", function () {
            checkIsUserAvailable();
        });
        


$userName.blur();
        $("#btnSubmit").click(function (e) {
            
            $userName.blur();
            if (typeof userNameAvailable == 'undefined' || userNameAvailable == false) {
                return false;
            }
            var password = $("#form-password").val();
            var confirmPassword = $("#form-repassword").val();
            var formGroup = $("#rePasswordDiv");
            if (password !== confirmPassword) {
                $("#form-repassword").focus();
                app.showMessage("Confirm Password doesn't match with Password.", 'error');
                return false;
            }
        });

        
    });
})(window.jQuery, window.app);