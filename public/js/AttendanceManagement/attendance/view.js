(function ($,app) {
    'use strict';
    $(document).ready(function () {
        
        app.datePickerWithNepali("attendanceDt","nepaliDate");
        
        var employeeId = $('#employeeId').val();
        window.app.floatingProfile.setDataFromRemote(employeeId);

        $('select').select2();
        $('#inTime').combodate({
            minuteStep: 1
        });
        $('#outTime').combodate({
            minuteStep: 1
        });

        $('.hour').attr("disabled", true);
        $('.minute').attr("disabled", true);
        $('.ampm').attr("disabled", true);
        
        app.setLoadingOnSubmit("form");

    });
})(window.jQuery,window.app);

$(function() {
    $('body').on('keydown', '#form-approvedRemarks', function(e) {
        if (e.which === 32 &&  e.target.selectionStart === 0) {
            return false;
        }
    });
});