(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.datePickerWithNepali("eligibleAfter", "nepaliEligibleAfter");
        $("#flat").hide().prop('required',false);
        var type = $("#type").val();
        if(type == 'SW'){ $("#month").show(); }
        if(type == 'FW'){ $("#month").hide(); }
        if(type == 'FW'){ $("#flat").show(); }
        if(type == 'SW'){ $("#flat").hide(); }
        // $("#flatAmt").change(function(){
        //     var type = $("#flatAmt").val();
        //     if(type == 'FW'){ $("#flat").show(); }
        //     if(type == 'SW'){ $("#flat").hide(); }
        // });
    });
})(window.jQuery, window.app);