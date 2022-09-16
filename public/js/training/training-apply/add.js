(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.datePickerWithNepali('dateOfadvance', 'nepalidateOfadvance');

        var $employeeId = $('#employeeId');

        app.populateSelect($employeeId, document.employeeList, 'EMPLOYEE_ID', 'FULL_NAME', '---', '');


        $employeeId.on('change', function () {
            var selectedEmpVal = $(this).val();
            console.log(selectedEmpVal);

            app.serverRequest(document.pullEmployeeTraining, {
                employeeId: selectedEmpVal
            }).then(function (response) {
                console.log(response);

            }, function (error) {
                console.log(error);
            });
        });


    });

})
        (window.jQuery, window.app);
    