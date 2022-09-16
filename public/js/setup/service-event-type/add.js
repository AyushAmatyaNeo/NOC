(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        
        var inputFieldId = "form-serviceEventTypeName";
        var formId = "serviceEventType-form";
        var tableName =  "HRIS_SERVICE_EVENT_TYPES";
        var columnName = "SERVICE_EVENT_TYPE_NAME";
        var checkColumnName = "SERVICE_EVENT_TYPE_ID";
        var selfId = $("#serviceEventTypeId").val();
        if (typeof(selfId) == "undefined"){
            selfId=0;
        }
        window.app.checkUniqueConstraints(inputFieldId,formId,tableName,columnName,checkColumnName,selfId);
        window.app.checkUniqueConstraints("form-serviceEventTypeCode",formId,tableName,"SERVICE_EVENT_TYPE_CODE",checkColumnName,selfId);
    });
})(window.jQuery, window.app);
