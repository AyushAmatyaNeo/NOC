(function ($, app) {
    $(document).ready(function () {
        
        
        
        var changeSearchOption = function (companyId, branchId, departmentId, designationId, positionId, serviceTypeId, serviceEventTypeId, employeeId) {
            
        
        /*
         * Search javascript code starts here
         */
        var $company = $('#companyId');
        var $branch = $('#branchId');
        var $department = $('#departmentId');
        var $designation = $('#designationId');
        var $position = $('#positionId');
        var $serviceType = $('#serviceTypeId');
        var $employee = $('#employeeId');
        var $employeeType = $('#employeeTypeId');

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
        populateList($company, document.searchValues['company'], 'COMPANY_ID', 'COMPANY_NAME', 'All Company');
        populateList($branch, document.searchValues['branch'], 'BRANCH_ID', 'BRANCH_NAME', 'All Branch');
        populateList($department, document.searchValues['department'], 'DEPARTMENT_ID', 'DEPARTMENT_NAME', 'All Department');
        populateList($designation, document.searchValues['designation'], 'DESIGNATION_ID', 'DESIGNATION_TITLE', 'All Designation');
        populateList($position, document.searchValues['position'], 'POSITION_ID', 'POSITION_NAME', 'All Position');
        populateList($serviceType, document.searchValues['serviceType'], 'SERVICE_TYPE_ID', 'SERVICE_TYPE_NAME', 'All Service Type');
        populateList($employee, document.searchValues['employee'], 'EMPLOYEE_ID', 'FULL_NAME', 'All Employee');
        
        if ($employeeType.length != 0) {
                populateList($employeeType, document.searchValues['employeeType'], 'EMPLOYEE_TYPE_KEY', 'EMPLOYEE_TYPE_VALUE', 'All Employee Type');
            }
        /* initialize dropdowns */

        /* setup change events */
        onChangeEvent($company, function ($this) {
//            populateList($branch, search(document.searchValues['branch'], {'COMPANY_ID': $this.val()}), 'BRANCH_ID', 'BRANCH_NAME', 'All Branch');
//            populateList($department, search(document.searchValues['department'], {'COMPANY_ID': $this.val()}), 'DEPARTMENT_ID', 'DEPARTMENT_NAME', 'All Department');
//            populateList($designation, search(document.searchValues['designation'], {'COMPANY_ID': $this.val()}), 'DESIGNATION_ID', 'DESIGNATION_TITLE', 'All Designation');
//            populateList($position, search(document.searchValues['position'], {'COMPANY_ID': $this.val()}), 'POSITION_ID', 'POSITION_NAME', 'All Position');

            populateList($employee, search(document.searchValues['employee'], {'COMPANY_ID': $company.val(), 'BRANCH_ID': $branch.val(), 'DEPARTMENT_ID': $department.val(), 'DESIGNATION_ID': $designation.val(), 'POSITION_ID': $position.val(), 'SERVICE_TYPE_ID': $serviceType.val()}), 'EMPLOYEE_ID', ['FIRST_NAME', 'MIDDLE_NAME', 'LAST_NAME'], 'All Employee');

        });

        onChangeEvent($branch, function ($this) {
//            populateList($department, search(document.searchValues['department'], {'BRANCH_ID': $this.val()}), 'DEPARTMENT_ID', 'DEPARTMENT_NAME', 'All Department');

            populateList($employee, search(document.searchValues['employee'], {'COMPANY_ID': $company.val(), 'BRANCH_ID': $branch.val(), 'DEPARTMENT_ID': $department.val(), 'DESIGNATION_ID': $designation.val(), 'POSITION_ID': $position.val(), 'SERVICE_TYPE_ID': $serviceType.val()}), 'EMPLOYEE_ID', ['FIRST_NAME', 'MIDDLE_NAME', 'LAST_NAME'], 'All Employee');
        });

        onChangeEvent($department, function ($this) {
            populateList($employee, search(document.searchValues['employee'], {'COMPANY_ID': $company.val(), 'BRANCH_ID': $branch.val(), 'DEPARTMENT_ID': $department.val(), 'DESIGNATION_ID': $designation.val(), 'POSITION_ID': $position.val(), 'SERVICE_TYPE_ID': $serviceType.val()}), 'EMPLOYEE_ID', ['FIRST_NAME', 'MIDDLE_NAME', 'LAST_NAME'], 'All Employee');
        });
        onChangeEvent($designation, function ($this) {
            populateList($employee, search(document.searchValues['employee'], {'COMPANY_ID': $company.val(), 'BRANCH_ID': $branch.val(), 'DEPARTMENT_ID': $department.val(), 'DESIGNATION_ID': $designation.val(), 'POSITION_ID': $position.val(), 'SERVICE_TYPE_ID': $serviceType.val()}), 'EMPLOYEE_ID', ['FIRST_NAME', 'MIDDLE_NAME', 'LAST_NAME'], 'All Employee');
        });
        onChangeEvent($position, function ($this) {
            populateList($employee, search(document.searchValues['employee'], {'COMPANY_ID': $company.val(), 'BRANCH_ID': $branch.val(), 'DEPARTMENT_ID': $department.val(), 'DESIGNATION_ID': $designation.val(), 'POSITION_ID': $position.val(), 'SERVICE_TYPE_ID': $serviceType.val()}), 'EMPLOYEE_ID', ['FIRST_NAME', 'MIDDLE_NAME', 'LAST_NAME'], 'All Employee');
        });
        onChangeEvent($serviceType, function ($this) {
            populateList($employee, search(document.searchValues['employee'], {'COMPANY_ID': $company.val(), 'BRANCH_ID': $branch.val(), 'DEPARTMENT_ID': $department.val(), 'DESIGNATION_ID': $designation.val(), 'POSITION_ID': $position.val(), 'SERVICE_TYPE_ID': $serviceType.val()}), 'EMPLOYEE_ID', ['FIRST_NAME', 'MIDDLE_NAME', 'LAST_NAME'], 'All Employee');
        });
        
         if ($employeeType.length != 0) {
                onChangeEvent($employeeType, function ($this) {
                     populateList($employee, search(document.searchValues['employee'], {'COMPANY_ID': $company.val(), 'BRANCH_ID': $branch.val(), 'DEPARTMENT_ID': $department.val(), 'DESIGNATION_ID': $designation.val(), 'POSITION_ID': $position.val(), 'SERVICE_TYPE_ID': $serviceType.val(),'EMPLOYEE_TYPE': $employeeType.val()}), 'EMPLOYEE_ID', ['FIRST_NAME', 'MIDDLE_NAME', 'LAST_NAME'], 'All Employee');
                });
            }
    }
    
    changeSearchOption("companyId", "branchId", "departmentId", "designationId", "positionId", "serviceTypeId", "serviceEventTypeId", "employeeId");

        /* setup change events */
        
        
        
         $("#reset").on("click", function () {
            changeSearchOption("companyId", "branchId", "departmentId", "designationId", "positionId", "serviceTypeId", "serviceEventTypeId", "employeeId");
            if (typeof document.ids !== "undefined") {
                $.each(document.ids, function (key, value) {
                    $("#" + key).val(value).change();
                });
            }
//            var angularElementId = Object.keys(document.angularElementDetail)[0];
//            var angularSearchFunction = document.angularElementDetail[angularElementId];
//            console.log(angularSearchFunction);
//            angular.element('#'+angularElementId).scope().angularSearchFunction();
        });
        
    });

})(window.jQuery, window.app);