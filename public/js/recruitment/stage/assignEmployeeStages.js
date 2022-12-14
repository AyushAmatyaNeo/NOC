(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        var $employeeId = $("#employeeId");
        var $stageId = $('#stageId');
        $employeeId.on("change", function(){
            var employeeId = $employeeId.val();
            var stageId = $stageId .val();
            app.serverRequest(document.getEmpStageList, {
                employeeId: employeeId,
                stageId: stageId
            }).then(function (response) {
                console.log(response.accessAs);
                $('#stageId').val(response.stageIds);
                $('#stageId').trigger('change');
                $('#vacancyId').val(response.vacancyIds);
                $('#vacancyId').trigger('change');
                if(response.accessAs != ''){
                    $('#accessAs'+response.accessAs).attr('checked','checked');
                }
            }, function (error) {
                console.log(error);
            });
        });
    });
})(window.jQuery, window.app);
