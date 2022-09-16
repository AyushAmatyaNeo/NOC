(function ($, app, appraisalCustom) {
    'use strict';
    $(document).ready(function () {
        $('.print').on('click', function () {
            app.exportDomToPdf2($('#printable'));
        });
        app.setLoadingOnSubmit("hrAppraisalReview");
    });
})(window.jQuery, window.app, window.appraisalCustom);