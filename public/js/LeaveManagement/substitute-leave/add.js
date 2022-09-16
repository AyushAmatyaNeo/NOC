(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('Select').select2();

        var monthList = null;
        var $fiscalYear = $('#fiscalYearId');
        var $month = $('#monthId');
        var $employeeId = $('#form-employeeId');
 
        
        let $table = $('#kendo_table');
        // ===================Initializing Table=======================
        app.initializeKendoGrid($table, [
            {
                title: 'Select All',
                headerTemplate: "<input type='checkbox' id='header-chb' class='k-checkbox header-checkbox'><label class='k-checkbox-label' for='header-chb'></label>",
                template: "<input type='checkbox' id='#:ATTENDANCE_DT#' class='k-checkbox row-checkbox'><label class='k-checkbox-label' for='#:ATTENDANCE_DT_AD#'></label>",
                width: 40
            },
            { field: "EMPLOYEE_CODE", title: "Employee Code" },
            { field: "FULL_NAME", title: "Employee Name" },
            { field: "ATTENDANCE_DT", title: "Date"},  
            { field: "IN_TIME", title: "In Time" },
            { field: "OUT_TIME", title: "Out Time" },
            { field: "OVERALL_STATUS_DETAIL", title: "Overall Status" }
            
            
        ]);

        $('#view').on('click', function () {
            const searchData = {
                employeeId : $employeeId.val(),
                monthId : $month.val()
            };
            app.serverRequest(document.getWohWodListLink, searchData).then(function (success) {
                app.renderKendoGrid($table, success.data);
                selectItems = {};
                var data = success.data;
                for (var i in data) {
                    selectItems[data[i]['ATTENDANCE_DT_AD']] = {'checked': false, 'employeeId': data[i]['EMPLOYEE_ID'], 'status': data[i]['OVERALL_STATUS']};
                }
            }, function (failure) {
                App.unblockUI("#hris-page-content");
            });
        });
        

        app.setFiscalMonth($fiscalYear, $month, function (years, months, currentMonth) {
            monthList = months;
        });

        $month.select2();

        var selectItems = {};
        var $classifyBtnDiv = $('#classifyBtnDiv');
        var $bulkBtns = $(".btnClassify");
        $table.on('click', '.k-checkbox', function () {
            var checked = this.checked;
            var row = $(this).closest("tr");
            var grid = $table.data("kendoGrid");
            var dataItem = grid.dataItem(row);
            selectItems[dataItem['ATTENDANCE_DT_AD']].checked = checked;
            if (checked) {
                row.addClass("k-state-selected");
                $classifyBtnDiv.show();
            } else {
                row.removeClass("k-state-selected");
                var atleastOne = false;
                for (var key in selectItems) {
                    if (selectItems[key]['checked']) {
                        atleastOne = true;
                        return;
                    }
                }
                if (atleastOne) {
                    $classifyBtnDiv.show();
                } else {
                    $classifyBtnDiv.hide();
                }

            }
        });

        $bulkBtns.bind("click", function () {
            var btnId = $(this).attr('id');
            var selectedValues = [];
            for (var i in selectItems) {
                if (selectItems[i].checked) {
                    selectedValues.push({date: i, employeeId: selectItems[i]['employeeId'], status: selectItems[i]['status']});
                }
            }

            App.blockUI({target: "#hris-page-content"});
            app.pullDataById(
                    document.classifyURL,
                    {data: selectedValues, btnAction: btnId}
            ).then(function (success) {
                App.unblockUI("#hris-page-content");
                window.location.reload(true);
            }, function (failure) {
                App.unblockUI("#hris-page-content");
            });
        });
    
    
        
        
    });
})(window.jQuery, window.app);


