(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var $tableContainer = $("#report");
        var extractDetailData = function (rawData, branchId) {
            var data = {};
            var column = {};

            for (var i in rawData) {
                console.log('data', rawData[i]);
                if (typeof data[rawData[i].EMPLOYEE_ID] !== 'undefined') {
                    data[rawData[i].EMPLOYEE_ID].MONTHS[rawData[i].MONTH_EDESC] =
                            JSON.stringify({
                                IS_ABSENT: rawData[i].IS_ABSENT,
                                IS_PRESENT: rawData[i].IS_PRESENT,
                                ON_LEAVE: rawData[i].ON_LEAVE
                            });
                } else {
                    data[rawData[i].EMPLOYEE_ID] = {
                        EMPLOYEE_ID: rawData[i].EMPLOYEE_ID,
                        FULL_NAME: rawData[i].FULL_NAME,
                        MONTHS: {}
                    };
                    data[rawData[i].EMPLOYEE_ID].MONTHS[rawData[i].MONTH_EDESC] =
                            JSON.stringify({
                                IS_ABSENT: rawData[i].IS_ABSENT,
                                IS_PRESENT: rawData[i].IS_PRESENT,
                                ON_LEAVE: rawData[i].ON_LEAVE
                            });

                }
                if (typeof column[rawData[i].MONTH_ID] === 'undefined') {
                    var temp = rawData[i].MONTH_EDESC;
                    column[rawData[i].MONTH_ID] = {
                        field: temp,
                        title: rawData[i].MONTH_EDESC,
                        template: '<div data="#: ' + temp + ' #" class="btn-group widget-btn-list custom-btn-group ' + branchId + '">' +
                                '<a class="btn  widget-btn custom-btn-present totalbtn"></a>' +
                                '<a class="btn  widget-btn custom-btn-absent totalbtn"></a>' +
                                '<a class="btn widget-btn custom-btn-leave totalbtn"></a>' +
                                '</div>'
                    }

                }
            }
            var returnData = {rows: [], cols: []};
            returnData.cols.push({
                field: 'employeeId',
                title: 'Id',
                width: 30
            });
            returnData.cols.push({
                field: 'employee',
                title: 'employees',
                template: '<a href="' + document.linkToReportFour + '/#:employeeId#">#: employee# </a>'
            });
            for (var k in column) {
                returnData.cols.push(column[k]);
            }

            for (var k in data) {
                var row = data[k].MONTHS;
                row['employee'] = data[k].FULL_NAME;
                row['employeeId'] = data[k].EMPLOYEE_ID;
                returnData.rows.push(row);
            }
            return returnData;
        };
        var displayDataInBtnGroup = function (selector) {
            $(selector).each(function (k, group) {
                var $group = $(group);
                var data = JSON.parse($group.attr('data'));
                var $childrens = $group.children();
                var $present = $($childrens[0]);
                var $absent = $($childrens[1]);
                var $leave = $($childrens[2]);

                var presentDays = parseFloat(data['IS_PRESENT']);
                var absentDays = parseFloat(data['IS_ABSENT']);
                var leaveDays = parseFloat(data['ON_LEAVE']);

                $present.html(data['IS_PRESENT']);
                $absent.html( data['IS_ABSENT']);
                $leave.html(data['ON_LEAVE']);

                var total = presentDays + absentDays + leaveDays;

                $present.attr('title',Number((presentDays * 100 / total).toFixed(1)) );
                $absent.attr('title',Number((absentDays * 100 / total).toFixed(1)));
                $leave.attr('title',Number((leaveDays * 100 / total).toFixed(1))) ;
            });

        };
        var firstTime = true;
        var initializeReport = function (branchId) {
            App.blockUI({target: "#hris-page-content"});
            app.pullDataById(document.wsBranchWise, {branchId: branchId}).then(function (response) {
                App.unblockUI("#hris-page-content");
                var extractedDetailData = extractDetailData(response.data, branchId);
                $tableContainer.kendoGrid({
                    dataSource: {
                        data: extractedDetailData.rows,
                        pageSize: 500
                    },
                    scrollable: false,
                    sortable: false,
                    pageable: false,
                    columns: extractedDetailData.cols
                });
                displayDataInBtnGroup('.custom-btn-group.' + branchId);


            }, function (error) {
                if (firstTime) {
                    App.unblockUI("#hris-page-content");
                    firstTime = false;
                } else {
                    App.unblockUI("#departmentMonthReport");
                }
                console.log('departmentWiseEmployeeMonthlyE', error);
            });
        };

        $('select').select2();
        var branchList = $('#branchList');
        var $generateReport = $('#generateReport');

        var populateList = function ($element, list, id, value, defaultMessage, selectedId) {
            $element.html('');
            $element.append($("<option></option>").val(-1).text(defaultMessage));
            for (var i in list) {
                if (typeof selectedId !== 'undefined' && selectedId != null && selectedId == list[i][id]) {
                    $element.append($("<option selected='selected'></option>").val(list[i][id]).text(list[i][value]));
                } else {
                    $element.append($("<option></option>").val(list[i][id]).text(list[i][value]));
                }
            }
        }

        var comBraDepList = document.comBraDepList;

        populateList(branchList, comBraDepList['BRANCH_LIST'], 'BRANCH_ID', 'BRANCH_NAME', "Select Branch");

        $generateReport.on('click', function () {
            var branchId = branchList.val();
            if (branchId == -1) {
                app.errorMessage("No Branch Selected", "Notification");
            } else {
                initializeReport(branchId);
            }
        });
        var branchId = document.branchId;
        if (branchId != 0) {
            initializeReport(branchId);
            branchList.val(branchId);
        }
    });
})(window.jQuery, window.app);