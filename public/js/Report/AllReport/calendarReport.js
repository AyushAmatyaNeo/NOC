(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.populateSelect($('#cal_emp'), document.empList, 'EMPLOYEE_ID', 'FULL_NAME','Please Select',-1,document.selfEmployeeId);
        
    });
})(window.jQuery, window.app);