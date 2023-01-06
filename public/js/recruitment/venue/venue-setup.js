(function ($, app) {
    'use strict';
    $(document).ready(function () {

        var $table = $('#venueTable');

        var action = `
            <div class="clearfix">
                <a class="btn btn-icon-only green" href="${document.editLink}/#:VENUE_SETUP_ID#" style="height:17px;" title="View Detail">
                    <i class="fa fa-search"></i>
                </a>
            </div>
                `;

        app.initializeKendoGrid($table, [

            {field: "VENUE_NAME", title: "Venue Name",width: 160, locked: false},
            {field: "STATUS", title:"Status", width:160, locked:false},
            {field: "VENUE_SETUP_ID", title: "Action", width:160, template: action},
        ], null, null, null, 'Assigned List');

        app.searchTable($table, ['VENUE_NAME'], false);

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
