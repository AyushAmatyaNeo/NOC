(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.datePickerWithNepali('dateOfadvance', 'nepalidateOfadvance');
        $('').datepicker('setStartDate', new Date());
    });
})(window.jQuery, window.app);
