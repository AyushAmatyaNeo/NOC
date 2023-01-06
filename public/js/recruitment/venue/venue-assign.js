(function ($, app) {
    'use strict';
    $(document).ready(function () {

        /**
         * Venue assign add/edit
         */
        $("select").select2();
        app.populateSelect($('#vacancies'), document.adno , 'VACANCY_ID', 'AD_NO', null, null);

        /**
         * Venue assign index
         */
        var $table = $('#venueAssignTable');

        var action = `
            <div class="clearfix">
                <a class="btn btn-icon-only yellow" href="${document.editLink}/#:VENUE_ASSIGN_ID#" style="height:17px;" title="View Detail">
                    <i class="fa fa-edit"></i>
                </a>
            </div>
                `;

        app.initializeKendoGrid($table, [

            {field: "VENUE_NAME", title: "Venue Name",width: 160, locked: false},
            {field: "ASSIGNED_VACANCIES", title: "Assigned Vacancies", width:160, locked:false},
            {field: "EXAM_TYPE", title: "Exam Type", width:160, locked:false},
            {field: "EXAM_DATE", title: "Exam Date",  width: 160, locked: false},
            {field: "START_TIME", title: "Start Time",  width: 160, locked: false},
            {field: "END_TIME", title: "End Time",  width: 160, locked: false},
            {field: "STATUS", title:"Status", width:160, locked:false},
            {field: "VENUE_SETUP_ID", title: "Action", width:160, template: action},
        ], null, null, null, 'Assigned List');

        app.searchTable($table, ['VENUE_NAME', 'EXAM_TYPE'], false);

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
