(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.datePickerWithNepali("eligibleAfter", "nepaliEligibleAfter");
        $("#flat").hide().prop('required',false);
        $("#type").change(function(){
            var type = $("#type").val();
            if(type == 'SW'){ $("#month").show().prop('required',true); }
            if(type == 'FW'){ $("#month").hide().prop('required',false); }
            if(type == 'FW'){ $("#flat").show().prop('required',true); }
            if(type == 'SW'){ $("#flat").hide().prop('required',false); }

        });
        // $("#flatAmt").change(function(){
        //     var type = $("#flatAmt").val();
        //     if(type == 'FW'){ $("#flat").show(); }
        //     if(type == 'SW'){ $("#flat").hide(); }
        // });
    });
})(window.jQuery, window.app);