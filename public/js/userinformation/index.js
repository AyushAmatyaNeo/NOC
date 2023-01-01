(function ($, app) {
    'use strict';
    $(document).ready(function () {

        var $table = $('#userTable');

        var action = `
            <div class="clearfix">
                <a class="btn btn-icon-only green" href="${document.editLink}/#:USER_ID#" style="height:17px;" title="View Detail">
                    <i class="fa fa-search"></i>
                </a>
            </div>
                `;

        app.initializeKendoGrid($table, [

            {field: "APPLICATION_ID", title: "Application Id",width: 160, locked: false},
            {field: "FULL_NAME", title: "Full Name",width: 160, locked: false},
            {field: "MOBILE_NO", title:"Mobile Number", width:160, locked:false},
            {field: "EMAIL_ID", title:"Email", width:160, locked:false},
            {field: "USERNAME", title:"User name", width:160, locked:false},
            {field: "PASSWORD", title:"Password", width:160, locked:false},
            {field: "USER_ID", title: "Action", width:160, template: action},
        ], null, null, null, 'Assigned List');

        app.searchTable($table, ['FULL_NAME', 'MOBILE_NO', 'EMAIL_ID', 'USERNAME', 'APPLICATION_ID'], false);

        $('#search').on('click', function () {
            document.body.style.cursor='wait';

            app.pullDataById('', {

            }).then(function (response) {
                if (response.success) {
                    document.body.style.cursor='default';
                    console.log(response);
                    app.renderKendoGrid($table, response.data);
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });

        });

    });
    
})(window.jQuery, window.app);