(function ($,app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        
        var inputFieldId = "headingEdesc";
        var formId = "appraisalHeading-form";
        var tableName =  "HRIS_APPRAISAL_HEADING";
        var columnName = "HEADING_EDESC";
        var checkColumnName = "HEADING_ID";
        var selfId = $("#headingId").val();
        if (typeof(selfId) == "undefined"){
            selfId=0;
        }
        window.app.checkUniqueConstraints(inputFieldId,formId,tableName,columnName,checkColumnName,selfId, function () {
            App.blockUI({target: "#hris-page-content"});
        });      
        window.app.checkUniqueConstraints("headingCode",formId,tableName,"HEADING_CODE",checkColumnName,selfId);
        window.app.checkUniqueConstraints("headingNdesc",formId,tableName,"HEADING_NDESC",checkColumnName,selfId);
    });
})(window.jQuery,window.app);

