(function ($, app) {
    'use strict';
    $(document).ready(function (e) {
        app.datePickerWithNepali('dateOfadvance', 'nepalidateOfadvance');
        $("#approve").on("click", function () {
            try {
                var $recommendedRemarks = $("#recommendedRemarks");
                var $approvedRemarks = $("#approvedRemarks");

                if (typeof $recommendedRemarks !== "undefined") {
                    $recommendedRemarks.removeAttr("required");
                }
                if (typeof $approvedRemarks !== "undefined") {
                    $approvedRemarks.removeAttr("required");
                }
                app.setLoadingOnSubmit('advance-form');
            } catch (e) {
                console.log("onApproveBtnClick", e.message);
            }
        });
    });
})(window.jQuery, window.app);
