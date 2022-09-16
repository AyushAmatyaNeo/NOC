(function ($) {
    'use strict';
    $(document).ready(function () {
        $('#printContract').on('click',function(){
           app.exportDomToPdf2('printDiv'); 
        });
        
    });
})(window.jQuery);