(function ($,app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
//        app.startEndDatePicker('startDate', 'endDate');
        app.startEndDatePickerWithNepali('nepaliStartDate1', 'startDate', 'nepaliEndDate1', 'endDate');
        
        var inputFieldId = "stageEdesc";
        var formId = "appraisalStage-form";
        var tableName =  "HRIS_APPRAISAL_STAGE";
        var columnName = "STAGE_EDESC";
        var checkColumnName = "STAGE_ID";
        var selfId = $("#stageId").val();
        if (typeof(selfId) == "undefined"){
            selfId=0;
        }
        window.app.checkUniqueConstraints(inputFieldId,formId,tableName,columnName,checkColumnName,selfId, function () {
            App.blockUI({target: "#hris-page-content"});
        });       
        window.app.checkUniqueConstraints("stageCode",formId,tableName,"STAGE_CODE",checkColumnName,selfId);
        window.app.checkUniqueConstraints("stageNdesc",formId,tableName,"STAGE_NDESC",checkColumnName,selfId);
//        window.app.checkUniqueConstraints("orderNo",formId,tableName,"ORDER_NO",checkColumnName,selfId);

    });
})(window.jQuery,window.app);

