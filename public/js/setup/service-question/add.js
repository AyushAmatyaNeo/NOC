(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.setLoadingOnSubmit("serviceQuestion-form");
    });
})(window.jQuery, window.app);