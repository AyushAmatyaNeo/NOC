(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.datePickerWithNepali("premiumDt", "nepaliPremiumDt");
    });
})(window.jQuery, window.app);