(function ($,app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        
        var inputFieldId = "appraisalTypeEdesc";
        var formId = "appraisalType-form";
        var tableName =  "HRIS_APPRAISAL_TYPE";
        var columnName = "APPRAISAL_TYPE_EDESC";
        var checkColumnName = "APPRAISAL_TYPE_ID";
        var selfId = $("#appraisalTypeId").val();
        if (typeof(selfId) == "undefined"){
            selfId=0;
        }
        window.app.checkUniqueConstraints(inputFieldId,formId,tableName,columnName,checkColumnName,selfId, function () {
            App.blockUI({target: "#hris-page-content"});
        });
        window.app.checkUniqueConstraints("appraisalTypeCode",formId,tableName,"APPRAISAL_TYPE_CODE",checkColumnName,selfId);
        window.app.checkUniqueConstraints("appraisalTypeNdesc",formId,tableName,"APPRAISAL_TYPE_NDESC",checkColumnName,selfId);

    });
})(window.jQuery,window.app);

