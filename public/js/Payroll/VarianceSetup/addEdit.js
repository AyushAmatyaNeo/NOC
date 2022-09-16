(function ($, app) {
    "use strict";
    $(document).ready(function () {
        var $payId = $('#payId');
        $('select').select2();
        app.populateSelect($payId, document.payHeads, 'PAY_ID', 'PAY_EDESC', '---', '');

        if (typeof document.details != 'undefined') {
            var payHeadValues = document.details.PAY_ID;
            var payHeadValuesArray = payHeadValues.split(",");
            $payId.val(payHeadValuesArray).change();
        }

    });
})(window.jQuery, window.app);