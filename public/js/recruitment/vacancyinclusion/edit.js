(function ($, app) {
    //    'use strict';
        $(document).ready(function () {
            $('select').select2();
            
            $('#InclusionId').removeAttr("multiple");
            
            
        });
        window.app.checkUniqueConstraints(inputFieldId,formId,tableName,columnName,checkColumnName,selfId);
    })(window.jQuery, window.app);
    
    
    