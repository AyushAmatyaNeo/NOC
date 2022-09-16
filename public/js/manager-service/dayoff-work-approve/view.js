(function ($, app) {
    'use strict';
    $(document).ready(function (e) {
        $("#approve").on("click", function () {
            try {
                var recommendRemarksId = $("#form-recommendedRemarks");
                var approveRemarksId = $("#form-approvedRemarks");

                if (typeof recommendRemarksId !== "undefined") {
                    recommendRemarksId.removeAttr("required");
                }
                if (typeof approveRemarksId !== "undefined") {
                    approveRemarksId.removeAttr("required");
                }
                app.setLoadingOnSubmit('workOnDayoff-form');
            } catch (e) {
                console.log("onApproveBtnClick", e.message);
            }
        });
    });
})(window.jQuery, window.app);
