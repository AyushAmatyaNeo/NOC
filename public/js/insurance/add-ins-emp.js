(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.datePickerWithNepali("insuranceDt", "nepaliInsuranceDt");
        app.datePickerWithNepali("maturedDt", "nepaliMaturedDt");
    });
})(window.jQuery, window.app);