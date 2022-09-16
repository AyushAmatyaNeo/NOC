(function ($,app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        
        var inputFieldId = "questionEdesc";
        var formId = "appraisalQuestion-form";
        var tableName =  "HRIS_APPRAISAL_QUESTION";
        var columnName = "QUESTION_EDESC";
        var checkColumnName = "QUESTION_ID";
        var selfId = $("#questionId").val();
        if (typeof(selfId) == "undefined"){
            selfId=0;
        }
//        window.app.checkUniqueConstraints(inputFieldId,formId,tableName,columnName,checkColumnName,selfId);      
        window.app.checkUniqueConstraints("questionCode",formId,tableName,"QUESTION_CODE",checkColumnName,selfId);
//        window.app.checkUniqueConstraints("questionNdesc",formId,tableName,"QUESTION_NDESC",checkColumnName,selfId);
       // window.app.checkUniqueConstraints("orderNo",formId,tableName,"ORDER_NO",checkColumnName,selfId);
    });
})(window.jQuery,window.app);

