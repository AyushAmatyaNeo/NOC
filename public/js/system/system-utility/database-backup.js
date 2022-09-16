(function ($, app) {
    'use strict';
    $(document).ready(function () {


        $('#btn-dataBackup').on('click', function () {

            app.pullDataById('', {}).then(function (response) {
                console.log(response);
                if(response.success==false){
                    app.errorMessage(response.message, 'error');
                }else{
                    app.showMessage(response.message, 'success','sucess');
                }
            }, function (error) {
                app.errorMessage(error, 'error');
            });


        });

    });
})(window.jQuery, window.app);