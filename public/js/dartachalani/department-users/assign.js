(function ($) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        var $locationId = $("#locationId");
        var $employeeId = $('#employeeId');
        $locationId.on("change", function(){
            var location_id = $locationId.val();
            var deartment_id = $("#departmentId").val();
            app.serverRequest(document.getEmpList, {
                location_id: location_id,
                deartment_id: deartment_id
            }).then(function (response) {
                app.populateSelect($employeeId, response.data, 'EMPLOYEE_ID', 'EMPLOYEE_NAME');
                $employeeId.val(response.assignedEmployeesList);
            }, function (error) {
                console.log(error);
            });
        });
    });
})(window.jQuery);






