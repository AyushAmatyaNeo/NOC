(function ($, app) {
    'use strict';
    $(document).ready(function () {
        //$('select').select2();  // This will add Ajax type search from values
        var gender = [{key: "1", value: "Male"}, {key: "2", value: "Female"}];
        var $gen = $("#Gender");
        app.populateSelect($gen, gender, 'key', 'value');

        // app.datePickerWithNepali('Start_dt');
        // app.datePickerWithNepali('End_dt');
        // app.datePickerWithNepali('Extended_dt');

        var status = [{key: "E", value: "Enable"}, {key: "D", value: "Disable"}];
        var $stat = $("#Status");
        app.populateSelect($stat, status, 'key', 'value');

        var OpenInternal = [{key: "OPEN", value: "OPEN"}, {key: "INTERNAL", value: "INTERNAL"}];
        var $OI = $("#OpenInternal");
        app.populateSelect($OI, OpenInternal, 'key', 'value');
    });
})(window.jQuery, window.app);
