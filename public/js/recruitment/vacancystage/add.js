(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var inputFieldId = "VacancyId";
        var formId = "VacancyStageForm";
        var tableName =  "HRIS_REC_VACANCY";
        var columnName = "VACANCY_ID";
        var checkColumnName = "VACANCY_ID";
        var selfId = $("#VacancyId").val();
        if (typeof(selfId) == "undefined"){
            selfId=0;
        }
        window.app.checkUniqueConstraints(inputFieldId,formId,tableName,columnName,checkColumnName,selfId);
  });
})(window.jQuery, window.app);