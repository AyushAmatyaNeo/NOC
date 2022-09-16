(function ($, app) {
    $(document).ready(function () {
        $('select').select2();
        app.datePickerWithNepali('fromDate', 'nepaliFromDate');
        app.datePickerWithNepali('toDate', 'nepaliToDate');


        var $shiftAssignTable = $('#shiftAssignTable');
        var $search = $('#search');
        var $bulkActionDiv = $('#bulkActionDiv');
        var $shiftId = $('#shiftId');
        var $fromDate = $('#fromDate');
        var $nepaliFromDate = $('#nepaliFromDate');
        var $toDate = $('#toDate');
        var $nepaliToDate = $('#nepaliToDate');
        var $bulkAdd = $('#bulkAdd');


        var grid = app.initializeKendoGrid($shiftAssignTable, [
            {field: "COMPANY_NAME", title: "Company", width: 150},
            {field: "BRANCH_NAME", title: "Branch", width: 150},
            {field: "DEPARTMENT_NAME", title: "Department", width: 150},
            {field: "POSITION_NAME", title: "Position", width: 150},
            {field: "DESIGNATION_TITLE", title: "Designation", width: 150},
            {field: "SERVICE_TYPE_NAME", title: "Service Type", width: 150},
            {field: "EMPLOYEE_TYPE", title: "Employee Type", width: 150},
            {field: "FULL_NAME", title: "Name", width: 150},
        ], function (e) {
            app.serverRequest(document.employeeShiftsWS, {employeeId: e.data.EMPLOYEE_ID}).then(function (response) {
                if (!response.success) {
                    app.showMessage(response.error, 'error');
                    return;
                }
                $("<div/>").appendTo(e.detailCell).kendoGrid({
                    dataSource: {
                        data: response.data,
                        pageSize: 20
                    },
                    scrollable: false,
                    sortable: false,
                    pageable: false,
                    columns: [
                        {field: "SHIFT_ENAME", title: "Shift"},
                        {title: "From Date", columns: [
                                {field: "FROM_DATE_AD", title: "AD"},
                                {field: "FROM_DATE_BS", title: "BS"},
                            ]},
                        {title: "To Date", columns: [
                                {field: "TO_DATE_AD", title: "AD"},
                                {field: "TO_DATE_BS", title: "BS"},
                            ]}
                    ],
                    dataBound: function (e) {
                        var grid = e.sender;
                        if (grid.dataSource.total() === 0) {
                            var colCount = grid.columns.length;
                            $(e.sender.wrapper)
                                    .find('tbody')
                                    .append('<tr class="kendo-data-row"><td colspan="' + colCount + '" class="no-data">There is no data to show in the grid.</td></tr>');
                        }
                    },
                });
            }, function (error) {
            });
        }, {id: 'EMPLOYEE_ID', atLast: false, fn: function (selected) {
                if (selected) {
                    $bulkActionDiv.show();
                } else {
                    $bulkActionDiv.hide();
                }
            }});

        app.searchTable('shiftAssignTable', ['COMPANY_NAME', 'BRANCH_NAME', 'DEPARTMENT_NAME', 'POSITION_NAME', 'DESIGNATION_TITLE', 'FULL_NAME', 'SERVICE_TYPE_NAME', 'EMPLOYEE_TYPE'], true);

        $search.on('click', function () {
            grid.clearSelected();
            $shiftId.val(-1).trigger('change.select2');
            $fromDate.val('');
            $nepaliFromDate.val('');
            $toDate.val('');
            $nepaliToDate.val('');
            $bulkActionDiv.hide();
            var search = document.searchManager.getSearchValues();
            app.serverRequest(document.employeeListWS, search).then(function (response) {
                app.renderKendoGrid($shiftAssignTable, response.data);
            }, function (error) {
                app.showMessage(error, 'error');
            });
        });

        app.populateSelect($shiftId, document.shiftList, 'SHIFT_ID', 'SHIFT_ENAME', 'Select Shift', -1);
        $shiftId.on('change', function () {
            var $this = $(this);
            var value = $this.val();
            var filList = document.shiftList.filter(function (shift) {
                return shift['SHIFT_ID'] == value;
            });

            if (filList.length > 0) {
                $fromDate.val(filList[0]['START_DATE']);
                app.pullDataById(document.getNepaliDate, {'engDate':filList[0]['START_DATE']}).then(function(success){
                    if(success.success){
                        $nepaliFromDate.val(success.data);
                    }
                });                
                $toDate.val(filList[0]['END_DATE']);
                app.pullDataById(document.getNepaliDate, {'engDate':filList[0]['END_DATE']}).then(function(success){
                    if(success.success){
                        $nepaliToDate.val(success.data);
                    }
                });                
            }

        });
        $bulkAdd.on('click', function () {
            var shiftId = $shiftId.val();
            if (shiftId == -1) {
                app.showMessage("Select Shift First.", "error");
                $shiftId.focus();
                return;
            }
            var fromDate = $fromDate.val();
            if (fromDate == "") {
                app.showMessage("From Date is required.", "error");
                $fromDate.focus();
                return;
            }
            var employeeShift = grid.getSelected();
            var toDate = $toDate.val();
            var employeeShiftIds = [];

            for (var i in employeeShift) {
                employeeShiftIds.push(employeeShift[i]['EMPLOYEE_ID']);
            }


            (function (employeeIdList) {
                var counter = 0;
                var length = employeeIdList.length;
                var addShift = function (employeeId) {
                    app.serverRequest(document.addWs, {
                        shiftId: shiftId,
                        fromDate: fromDate,
                        toDate: toDate,
                        employeeIds: [employeeId]
                    }).then(function (response) {
                        NProgress.set((counter + 1) / length);
                        if (!response.success) {
                            app.showMessage("Shift Assign for Employee Id : " + employeeId + "Failed.", 'error');
                        }
                        counter++;
                        if (counter < length) {
                            addShift(employeeIdList[counter]);
                        } else {
                            app.showMessage("Shift Assigned.");
                            $search.trigger('click');
                        }
                    }, function (error) {

                    });

                };
                NProgress.start();
                addShift(employeeIdList[counter]);


            })(employeeShiftIds);
        });


    });
})(window.jQuery, window.app);
