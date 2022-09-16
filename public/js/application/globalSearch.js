(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var $globalSearch = $('#globalSearch');
        var $globalSearchForm = $('#globalSearchForm');
        var employeeList = document.globalSearchemployeeList;

        var nameList = [];
        $.each(employeeList, function (key, item) {
            nameList.push(item['FULL_NAME']);
        });


        $globalSearch.autocomplete({
            source: nameList, select: function (element, selectedObject) {
                try {
                    var fullName = selectedObject.item.value;
                    var filteredEmployee = employeeList.filter(function (item) {
                        return fullName === item['FULL_NAME'];
                    });
                    if (filteredEmployee.length === 0) {
                        throw {message: 'No Employee Found with the name given.'}
                    }

                    window.location = document.globalSearchEmployeeProfileUrl + '/' + filteredEmployee[0]['EMPLOYEE_ID'];
                } catch (e) {
                    app.showMessage(e.message, 'error');
                }
            }
        });

    });

})(window.jQuery, window.app);