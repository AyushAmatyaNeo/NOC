(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        
        console.log(document.selectedAlternateRecommneder);
        console.log(document.selectedAlternateApprover);
        
        var alternateRecommer=[];
        var alternateApprover=[];
        
        $.each(document.selectedAlternateRecommneder, function (k, v) {
                    alternateRecommer.push(v.R_A_ID);
            });
            
        $.each(document.selectedAlternateApprover, function (k, v) {
                    alternateApprover.push(v.R_A_ID);
            });
            
        $('#alternateRecomender').val(alternateRecommer).trigger('change.select2');
        $('#alternateApprover').val(alternateApprover).trigger('change.select2');
        
        
    });
})(window.jQuery, window.app);
