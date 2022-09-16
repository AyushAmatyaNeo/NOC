(function ($, app) {
    $(document).ready(function () {
        $('select').select2();
        app.setLoadingOnSubmit("appraisalDefaultRating-form");
    });
})(window.jQuery, window.app);