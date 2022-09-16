(function ($, app) {
    'use strict';
    $(document).ready(function () {
        
        var inputFieldId = "form-academicDegreeName";
        var formId = "academicDegree-form";
        var tableName =  "HRIS_ACADEMIC_DEGREES";
        var columnName = "ACADEMIC_DEGREE_NAME";
        var checkColumnName = "ACADEMIC_DEGREE_ID";
        var selfId = $("#academicDegreeId").val();
        if (typeof(selfId) == "undefined"){
            selfId=0;
        }
        window.app.checkUniqueConstraints("form-academicDegreeCode",formId,tableName,"ACADEMIC_DEGREE_CODE",checkColumnName,selfId, function () {
            App.blockUI({target: "#hris-page-content"});
        });
        window.app.checkUniqueConstraints(inputFieldId,formId,tableName,columnName,checkColumnName,selfId);
        window.app.checkUniqueConstraints("form-weight",formId,tableName,"WEIGHT",checkColumnName,selfId);
    });
})(window.jQuery, window.app);
