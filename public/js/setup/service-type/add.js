/**
 * Created by punam on 9/28/16.
 */
(function ($,app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2(); 
        
        var inputFieldId = "form-serviceTypeName";
        var formId = "serviceType-form";
        var tableName =  "HRIS_SERVICE_TYPES";
        var columnName = "SERVICE_TYPE_NAME";
        var checkColumnName = "SERVICE_TYPE_ID";
        var selfId = $("#serviceTypeId").val();
        if (typeof(selfId) == "undefined"){
            selfId=0;
        }
        window.app.checkUniqueConstraints(inputFieldId,formId,tableName,columnName,checkColumnName,selfId, function () {
            App.blockUI({target: "#hris-page-content"});
        });   
        window.app.checkUniqueConstraints("form-serviceTypeCode",formId,tableName,"SERVICE_TYPE_CODE",checkColumnName,selfId);
    });
})(window.jQuery,window.app);
