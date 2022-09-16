(function ($) {
    'use strict';
    $(document).ready(function () {

        var $table = $('#holidayTable');
        app.initializeKendoGrid($table, [
            {field: "HOLIDAY_ENAME", title: "Holiday Name", template: "<span>#: (HOLIDAY_ENAME == null) ? '-' : HOLIDAY_ENAME #</span>"},
            {title: "Start Date",
                columns: [{
                        field: "START_DATE_AD",
                        title: "AD",
                        template: "<span>#: (START_DATE_AD == null) ? '-' : START_DATE_AD #</span>"
                    }, {field: "START_DATE_BS",
                        title: "BS",
                        template: "<span>#: (START_DATE_BS == null) ? '-' : START_DATE_BS #</span>"
                    }]},
            {title: "End Date",
                columns: [{
                        field: "END_DATE_AD",
                        title: "AD",
                        template: "<span>#: (END_DATE_AD == null) ? '-' : END_DATE_AD #</span>"},
                    {field: "END_DATE_BS",
                        title: "BS",
                        template: "<span>#: (END_DATE_BS == null) ? '-' : END_DATE_BS #</span>"
                    }]},
            {field: "HALFDAY_DETAIL", title: "Interval", template: "<span>#: (HALFDAY_DETAIL == null) ? '-' : HALFDAY_DETAIL #</span>"}
            ,
        ], null, null, null, 'Holiday List');

        app.searchTable('holidayTable', ["HOLIDAY_ENAME", "START_DATE_AD", "START_DATE_BS", "END_DATE_AD", "END_DATE_BS", "HALFDAY_DETAIL"]);

        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });

        var exportMap = {
            'HOLIDAY_ENAME': 'Holiday Name',
            'START_DATE_AD': 'START_DATE AD',
            'START_DATE_BS': 'START_DATE BS',
            'END_DATE_AD': 'END_DATE AD',
            'END_DATE_BS': 'END_DATE BS',
            'HALFDAY_DETAIL': 'Interval'
        };
        $('#excelExport').on('click', function () {
            app.excelExport($table, exportMap, 'Holiday List');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, exportMap, 'Holiday List');
        });


    });
}
)(window.jQuery, window.app);
