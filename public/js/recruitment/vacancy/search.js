
(function ($, app) {
    $(document).ready(function () {
        document.searchManager = {
            company: [],
            branch: [],
            functionalType: [],
            companyListener: null,
            branchListener: null,
            ids: []
            , setCompany: function (company) {
                this.company = company;
            }, getCompany: function () {
                return this.company;
            }, setBranch: function (branch) {
                this.branch = branch;
            }, getBranch: function () {
                return this.company
            }, setCompanyListener: function (listener) {
                this.companyListener = listener;
            }, setBranchListener: function (listener) {
                this.branchListener = listener
            }, setfunctionalTypeListener: function (listener) {
                this.functionalTypeListener = listener;
            }, callCompanyListener: function () {
                if (this.companyListener !== null) {
                    this.companyListener();
                }
            }, callBranchListener: function () {
                if (this.branchListener !== null) {
                    this.branchListener();
                }
            },
            setIds: function (ids) {
                this.ids = ids;
            },
            getIds: function () {
                return this.ids;
            },
            getSearchValues: function () {
                var values = {};
                $.each(this.ids, function (key, value) {
                    if (typeof value !== "undefined") {
                        values[value] = $('#' + value).val();
                    }
                });
                return values;
            },
            reset: function () {
                let acl = document.acl;
                let aclControlVal='F';
                $.each(this.ids, function (key, value) {
                    $('#' + value).val(-1).change();
                });
                for(let i = 0; i < acl['CONTROL'].length; i++){
                    $.each(this.ids, function (key, value) {
//                    console.log(value);
                        let $company = $('#' + 'companyId');
                        let $branch = $('#' + 'branchId');
//                    
                    let populateValues = [];
                    if (typeof acl !== 'undefined') {
                        aclControlVal = acl['CONTROL'];

                        $.each(acl['CONTROL_VALUES'], function (k, v) {
                            if (v.CONTROL == acl['CONTROL'][i]) {
                                populateValues.push(v.VAL);
                            }
                        });
//                console.log(acl['CONTROL']);
                    }  //end if
                        if (typeof value !== "undefined") {
                            if (value == 'companyId' || value == 'branchId' || value == 'designationId' || value == 'departmentId' || value == 'positionId') {
                                switch (aclControlVal[i]) {
                                    case 'F':
                                       // $('#' + value).val(-1).change();
                                        break;
                                    case 'C':
                                        if (value == 'companyId') {
                                            $company.val(populateValues);
                                            $company.trigger('change');
                                        } else {
                                           // $('#' + value).val(-1).change();
                                        }
                                        break;
                                    case 'B':
                                        if (value == 'branchId') {
                                            $branch.val(populateValues);
                                            $branch.trigger('change');
                                        } else {
                                           // $('#' + value).val(-1).change();
                                        }
                                        break;
                                }
                            } else {
                                $('#' + value).val(-1).change();
                            }
                        }
                    
                    });
                }
                
                
                if (this.resetEvent !== null) {
                    this.resetEvent();
                }
            },
            resetEvent: null,
            registerResetEvent: function (fn) {
                this.resetEvent = fn;
            },
            setSearchValues: function (values) {
                $.each(this.ids, function (key, value) {
                    if (typeof values[value] !== "undefined") {
                        $('#' + value).val(values[value]).trigger('change.select2');
                    }
                });
            },
            getSelectedEmployee: function () {
                var selectedValues = $('#employeeId').val();
                var employeeList = this.getEmployee();
                if (selectedValues === null || selectedValues === "-1") {
                    return employeeList;
                }
                return employeeList.filter(function (item) {
                    if (Array.isArray(selectedValues)) {
                        if($.inArray(item['EMPLOYEE_ID'], selectedValues) >= 0){
                            return item;
                        };
                    } else {
                        return item['EMPLOYEE_ID'] == selectedValues;
                    }
                });
            }
        };
        (function () {
            $('.hris-reset-btn').on('click', function () {
                document.searchManager.reset();
                app.resetField();
            });
        })();

        /*
         * Search javascript code starts here
         */
        var changeSearchOption = function (companyId, branchId) {
            document.searchManager.setIds(JSON.parse(JSON.stringify(arguments)));

            var $company = $('#' + companyId);
            var $branch = $('#' + branchId);
            var $functionalType = $('#' + "random-random");
            if (typeof genderId !== 'undefined' && genderId !== null) {
                $gender = $('#' + genderId);
            }

            /* setup functions */
            var populateList = function ($element, list, id, value, defaultMessage, selectedId) {
                $element.html('');
                if (typeof defaultMessage !== "undefined" && !$element.prop('multiple')) {
                    $element.append($("<option></option>").val(-1).text(defaultMessage));
                }
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
                        var value = where[i];
                        if (Array.isArray(value)) {
                            var xc = false;
                            for (var x in value) {
                                if (item[i] === value[x]) {
                                    xc = true;
                                    break;
                                }
                            }
                            if(xc==false){
                                return xc;
                            }
                        } else if (value === null) {

                        } else {
                            if (!(item[i] === value || value == -1)) {
                                return false;
                            }
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

            var employeeSearchAndPopulate = function () {
                var searchParams = {'COMPANY_ID': $company.val(), 'BRANCH_ID': $branch.val()};
                if ($gender.length != 0) {
                    searchParams['GENDER_ID'] = $gender.val();
                }
                if ($employeeType.length != 0) {
                    searchParams['EMPLOYEE_TYPE'] = $employeeType.val();
                }
                if ($location.length != 0) {
                    searchParams['LOCATION_ID'] = $location.val();
                }
                if ($functionalType.length != 0) {
                    searchParams['FUNCTIONAL_TYPE_ID'] = $functionalType.val();
                }
                var employeeList = search(document.searchValues['employee'], searchParams);
                document.searchManager.setEmployee(employeeList);
                populateList($employee, employeeList, 'EMPLOYEE_ID', 'FULL_NAME', 'All Employee');
            };
            /* setup functions */

            /* initialize dropdowns */
            populateList($company, document.searchValues['company'], 'COMPANY_ID', 'COMPANY_NAME', 'All Company');
            populateList($branch, document.searchValues['branch'], 'BRANCH_ID', 'BRANCH_NAME', 'All Branch');

            document.searchManager.setCompany(document.searchValues['company']);
            document.searchManager.setBranch(document.searchValues['branch']);
            
            
           
            }
            /* initialize dropdowns */

            /* setup change events */
            onChangeEvent($company, function ($this) {
                employeeSearchAndPopulate();
                document.searchManager.callCompanyListener();
            });

            onChangeEvent($branch, function ($this) {
                employeeSearchAndPopulate();
                document.searchManager.callBranchListener();
            });
            var acl = document.acl;
            var employeeDetail = document.employeeDetail;
            if (typeof acl !== 'undefined' && typeof employeeDetail !== 'undefined') {

                for(let i = 0; i < acl['CONTROL'].length; i++){
                    var populateValues = [];
                    $.each(acl['CONTROL_VALUES'], function (k, v) {

                        if (v.CONTROL == acl['CONTROL'][i]) {
                            populateValues.push(v.VAL);
                        }
                    });
                
                    switch (acl['CONTROL'][i]) {
                        case 'C':
                            $company.val((populateValues.length<1)?employeeDetail['COMPANY_ID']:populateValues);
                            $company.trigger('change');
                            $company.prop('disabled', true);
                            break;
                        case 'B':
                            $branch.val((populateValues.length<1)?employeeDetail['BRANCH_ID']:populateValues);
                            $branch.trigger('change');
                            $branch.prop('disabled', true);
                            break;
                        case 'DS':
                            $designation.val((populateValues.length<1)?employeeDetail['DESIGNATION_ID']:populateValues);
                            $designation.trigger('change');
                            $designation.prop('disabled', true);
                            break;
                        case 'DP':
                            $department.val((populateValues.length<1)?employeeDetail['DEPARTMENT_ID']:populateValues);
                            $department.trigger('change');
                            $department.prop('disabled', true);
                            break;
                        case 'P':
                            $position.val((populateValues.length<1)?employeeDetail['POSITION_ID']:populateValues);
                            $position.trigger('change');
                            $position.prop('disabled', true);
                            break;
                    }
                }
            }

        };

        if (typeof document.searchValues !== 'undefined') {
            changeSearchOption("companyId", "branchId");
        } else {
            if (typeof document.getSearchDataLink !== "undefined") {
                app.serverRequest(document.getSearchDataLink, {}).then(function (response) {
                    document.searchValues = response.data;
                    changeSearchOption("companyId", "branchId");
                });
            } else {
                throw "No data or url set."
            }
        }


        /* setup change events */

    });
})(window.jQuery, window.app);