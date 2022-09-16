(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        var inputFieldId = "leaveEname";
        var formId = "leaveSetup-form";
        var tableName = "HRIS_LEAVE_MASTER_SETUP";
        var columnName = "LEAVE_ENAME";
        var checkColumnName = "LEAVE_ID";
        var selfId = $("#leaveId").val();
        if (typeof (selfId) === "undefined") {
            selfId = 0;
        }
        window.app.checkUniqueConstraints(inputFieldId, formId, tableName, columnName, checkColumnName, selfId, function () {
            App.blockUI({target: "#hris-page-content"});
        });
        window.app.checkUniqueConstraints("leaveCode", formId, tableName, "LEAVE_CODE", checkColumnName, selfId);
        window.app.checkUniqueConstraints("leaveLname", formId, tableName, "LEAVE_LNAME", checkColumnName, selfId);

        if (document.searchSelectedValues !== undefined) {
            document.searchManager.setSearchValues(document.searchSelectedValues);
        }
    });
})(window.jQuery, window.app);
