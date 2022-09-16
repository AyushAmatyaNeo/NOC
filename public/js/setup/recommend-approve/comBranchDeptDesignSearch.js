var comBranchDeptDesignSearch = function (recomCompanyId, recomBranchId, recomLocationId, recomDepartmentId, recomDesignationId, recomEmployeeId) {
    /*
     * Search javascript code starts here
     */
    var $recomCompany = $('#' + recomCompanyId);
    var $recomBranch = $('#' + recomBranchId);
    var $recomLocationId = $('#' + recomLocationId);
    var $recomDepartment = $('#' + recomDepartmentId);
    var $recomDesignation = $('#' + recomDesignationId);
    var $recomEmployee = $('#' + recomEmployeeId);

    /* setup functions */
    var populateList = function ($element, list, id, value, defaultMessage, selectedId) {
        $element.html('');
        $element.append($("<option></option>").val(-1).text(defaultMessage));
        var concatArray = function (keyList, list, concatWith) {
            var temp = '';
            if (typeof concatWith === 'undefined') {
                concatWith = ' ';
            }
            for (var i in keyList) {
                var listValue = list[keyList[i]];
                if (i == (keyList.length - 1)) {
                    temp = temp + ((listValue === null) ? '' : listValue);
                    continue;
                }
                temp = temp + ((listValue === null) ? '' : listValue) + concatWith;
            }

            return temp;
        };
        for (var i in list) {
            var text = null;
            if (typeof value === 'object') {
                text = concatArray(value, list[i], ' ');
            } else {
                text = list[i][value];
            }
            if (typeof selectedId !== 'undefined' && selectedId != null && selectedId == list[i][id]) {
                $element.append($("<option selected='selected'></option>").val(list[i][id]).text(text));
            } else {
                $element.append($("<option></option>").val(list[i][id]).text(text));
            }
        }
    };
    var search = function (list, where) {
        return list.filter(function (item) {
            for (var i in where) {
                if (!(item[i] === where[i] || where[i] == -1)) {
                    return false;
                }
            }
            return true;
        });
    };
    var onChangeEvent = function ($element, fn) {
        $element.on('change', function () {
            var $this = $(this);
            fn($this);
        });
    };
    /* setup functions */

    /* initialize dropdowns */
    populateList($recomCompany, document.searchValues['company'], 'COMPANY_ID', 'COMPANY_NAME', 'All Company');
    populateList($recomBranch, document.searchValues['branch'], 'BRANCH_ID', 'BRANCH_NAME', 'All Branch');
    populateList($recomLocation, document.searchValues['location'], 'LOCATION_ID', 'LOCATION_EDESC', 'All Location');
    populateList($recomDepartment, document.searchValues['department'], 'DEPARTMENT_ID', 'DEPARTMENT_NAME', 'All Department');
    populateList($recomDesignation, document.searchValues['designation'], 'DESIGNATION_ID', 'DESIGNATION_TITLE', 'All Designation');
    populateList($recomEmployee, document.searchValues['employee'], 'EMPLOYEE_ID', ['FIRST_NAME', 'MIDDLE_NAME', 'LAST_NAME'], 'All Employee');
    /* initialize dropdowns */

    /* setup change events */
    onChangeEvent($recomCompany, function ($this) {
//        populateList($recomBranch, search(document.searchValues['branch'], {'COMPANY_ID': $this.val()}), 'BRANCH_ID', 'BRANCH_NAME', 'All Branch');
//        populateList($recomDepartment, search(document.searchValues['department'], {'COMPANY_ID': $this.val()}), 'DEPARTMENT_ID', 'DEPARTMENT_NAME', 'All Department');
//        populateList($recomDesignation, search(document.searchValues['designation'], {'COMPANY_ID': $this.val()}), 'DESIGNATION_ID', 'DESIGNATION_TITLE', 'All Designation');

        populateList($recomEmployee, search(document.searchValues['employee'], {'COMPANY_ID': $recomCompany.val(), 'BRANCH_ID': $recomBranch.val(),'LOCATION_ID': $recomLocation.val(), 'DEPARTMENT_ID': $recomDepartment.val(), 'DESIGNATION_ID': $recomDesignation.val()}), 'EMPLOYEE_ID', ['FIRST_NAME', 'MIDDLE_NAME', 'LAST_NAME'], 'All Employee');

    });

    onChangeEvent($recomBranch, function ($this) {
//        populateList($recomDepartment, search(document.searchValues['department'], {'BRANCH_ID': $this.val()}), 'DEPARTMENT_ID', 'DEPARTMENT_NAME', 'All Department');
        populateList($recomEmployee, search(document.searchValues['employee'], {'COMPANY_ID': $recomCompany.val(), 'BRANCH_ID': $recomBranch.val(),'LOCATION_ID': $recomLocation.val(),'DEPARTMENT_ID': $recomDepartment.val(), 'DESIGNATION_ID': $recomDesignation.val()}), 'EMPLOYEE_ID', ['FIRST_NAME', 'MIDDLE_NAME', 'LAST_NAME'], 'All Employee');
    });

    onChangeEvent($recomDepartment, function ($this) {
        populateList($recomEmployee, search(document.searchValues['employee'], {'COMPANY_ID': $recomCompany.val(), 'BRANCH_ID': $recomBranch.val(), 'LOCATION_ID': $recomLocation.val(),'DEPARTMENT_ID': $recomDepartment.val(), 'DESIGNATION_ID': $recomDesignation.val()}), 'EMPLOYEE_ID', ['FIRST_NAME', 'MIDDLE_NAME', 'LAST_NAME'], 'All Employee');
    });
    onChangeEvent($recomDesignation, function ($this) {
        populateList($recomEmployee, search(document.searchValues['employee'], {'COMPANY_ID': $recomCompany.val(), 'BRANCH_ID': $recomBranch.val(), 'LOCATION_ID': $recomLocation.val(),'DEPARTMENT_ID': $recomDepartment.val(), 'DESIGNATION_ID': $recomDesignation.val()}), 'EMPLOYEE_ID', ['FIRST_NAME', 'MIDDLE_NAME', 'LAST_NAME'], 'All Employee');
    });
    /* setup change events */
};