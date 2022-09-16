/**
 * Created by root on 11/11/16.
 */
(function ($,app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        
        var inputFieldId = "form-academicCourseName";
        var formId = "academicCourse-form";
        var tableName =  "HRIS_ACADEMIC_COURSES";
        var columnName = "ACADEMIC_COURSE_NAME";
        var checkColumnName = "ACADEMIC_COURSE_ID";
        var selfId = $("#academicCourseId").val();
        if (typeof(selfId) == "undefined"){
            selfId=0;
        }
        window.app.checkUniqueConstraints(inputFieldId,formId,tableName,columnName,checkColumnName,selfId, function () {
            App.blockUI({target: "#hris-page-content"});
        });
        window.app.checkUniqueConstraints("form-academicCourseCode",formId,tableName,"ACADEMIC_COURSE_CODE",checkColumnName,selfId);
    });
})(window.jQuery,window.app);
