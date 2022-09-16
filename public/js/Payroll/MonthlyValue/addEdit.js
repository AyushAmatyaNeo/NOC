(function ($, app) {
    "use strict";
    $(document).ready(function () {
        app.checkUniqueConstraints("mthCode",
                "monthlyValueFormId",
                document.tableName,
                document.mthCodeAttr,
                document.mthIdAttr,
                (typeof document.mthId !== 'undefined') ? document.mthId : 0, function () {
            App.blockUI({target: "#hris-page-content"});
        });
    });
})(window.jQuery, window.app);