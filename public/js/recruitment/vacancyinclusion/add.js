(function ($, app) {
    //    'use strict';
        $(document).ready(function () {
            $('select').select2();    
            
        });

        var inputFieldId = "InclusionId";
        var formId = "vacancyinclusion";
        var tableName =  "HRIS_REC_VACANCY_INCLUSION";
        var columnName = "INCLUSION_ID";
        var checkColumnName = "INCLUSION_ID";
        var selfId = $("#InclusionId").val();
        if (typeof(selfId) == "undefined"){
            selfId=0;
        }

        window.app.checkUniqueConstraints(inputFieldId,formId,tableName,columnName,checkColumnName,selfId);
    })(window.jQuery, window.app);
    
    
    