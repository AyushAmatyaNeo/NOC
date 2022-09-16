(function ($, app) {
    'use strict';
    $(document).ready(function () {

        $('select').select2();
        

        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, false);
      
        

        
        
        
    });
})(window.jQuery, window.app);
