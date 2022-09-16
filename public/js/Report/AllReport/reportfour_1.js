(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var $tableContainer = $("#reportTable");
        var extractDetailData = function (rawData, employeeId) {
            var data = {};
            var column = {};

            for (var i in rawData) {
                if (typeof data[rawData[i].MONTH_ID] !== 'undefined') {
                    data[rawData[i].MONTH_ID].MONTHS['C' + rawData[i].DAY_COUNT] =
                            JSON.stringify({
                                IS_ABSENT: rawData[i].IS_ABSENT,
                                IS_PRESENT: rawData[i].IS_PRESENT,
                                ON_LEAVE: rawData[i].ON_LEAVE
                            });
                    data[rawData[i].MONTH_ID].TOTAL.IS_ABSENT = data[rawData[i].MONTH_ID].TOTAL.IS_ABSENT + parseFloat(rawData[i].IS_ABSENT);
                    data[rawData[i].MONTH_ID].TOTAL.IS_PRESENT = data[rawData[i].MONTH_ID].TOTAL.IS_PRESENT + parseFloat(rawData[i].IS_PRESENT);
                    data[rawData[i].MONTH_ID].TOTAL.ON_LEAVE = data[rawData[i].MONTH_ID].TOTAL.ON_LEAVE + parseFloat(rawData[i].ON_LEAVE);
                    data[rawData[i].MONTH_ID].TOTAL.IS_DAYOFF = data[rawData[i].MONTH_ID].TOTAL.IS_DAYOFF + parseFloat(rawData[i].IS_DAYOFF);

                } else {
                    data[rawData[i].MONTH_ID] = {
                        MONTH_ID: rawData[i].MONTH_ID,
                        MONTH_EDESC: rawData[i].MONTH_EDESC,
                        MONTHS: {},
                        TOTAL: {
                            IS_ABSENT: parseFloat(rawData[i].IS_ABSENT),
                            IS_PRESENT: parseFloat(rawData[i].IS_PRESENT),
                            ON_LEAVE: parseFloat(rawData[i].ON_LEAVE),
                            IS_DAYOFF: parseFloat(rawData[i].IS_DAYOFF)
                        }
                    };
                    data[rawData[i].MONTH_ID].MONTHS['C' + rawData[i].DAY_COUNT] =
                            JSON.stringify({
                                IS_ABSENT: rawData[i].IS_ABSENT,
                                IS_PRESENT: rawData[i].IS_PRESENT,
                                ON_LEAVE: rawData[i].ON_LEAVE
                            });

                }
                if (typeof column[rawData[i].DAY_COUNT] === 'undefined') {
                    var temp = 'C' + rawData[i].DAY_COUNT;
                    column[rawData[i].DAY_COUNT] = {
                        field: temp,
                        title: rawData[i].DAY_COUNT,
                        template: '<span data="#: ' + temp + ' #" class="custom-btn-group"></span>'
                    }
                }
            }


            var returnData = {rows: [], cols: []};

            returnData.cols.push({field: 'month', title: 'Month'});
            for (var k in column) {
                returnData.cols.push(column[k]);
            }
            returnData.cols.push({
                field: 'total',
                title: 'Total',
                width: 400,
                template: '<div data="#: total #" class="btn-group widget-btn-list total-attendance">' +
                        '<a class="btn  widget-btn custom-btn-present totalbtn"></a>' +
                        '<a class="btn  widget-btn custom-btn-absent totalbtn"></a>' +
                        '<a class="btn  widget-btn custom-btn-leave totalbtn"></a>' +
                        '</div>'});


            for (var k in data) {
                var row = data[k].MONTHS;
                for (var i in column) {
                    if (typeof row[column[i]['field']] === 'undefined') {
                        row[column[i]['field']] = null;
                    }
                }

                row['month'] = data[k].MONTH_EDESC;
                returnData.rows.push(row);
                row['total'] = JSON.stringify(data[k].TOTAL);
            }

            return returnData;
        };
        var displayDataInBtnGroup = function (selector) {
            $(selector).each(function (k, group) {
                var $group = $(group);

                var data = $group.attr('data');
                if (data == "null") {

                } else {
                    data = JSON.parse(data);
                    if (data['IS_PRESENT'] == 1) {
                        $group.html('P');
                        $group.parent().addClass('bg-green');
                    } else {
                        if (data['IS_ABSENT'] == 1) {
                            $group.html('A');
                            $group.parent().addClass('bg-red1 textcolor1');

                        } else {
                            if (data['ON_LEAVE'] == 1) {
                                $group.html('L');
                                $group.parent().addClass('bg-blue1 textcolor2');

                            } else {
                                $group.html('H');
                                $group.parent().addClass('bg-white1 textcolor3');
                            }

                        }

                    }
                }
            });

        };
        var firstTime = true;
        var initializeReport = function (employeeId) {
            if (firstTime) {
                App.blockUI({target: "#hris-page-content"});

            } else {
                App.blockUI({target: "#reportTable"});

            }
            app.pullDataById(document.wsEmployeeWiseDailyReport, {employeeId: employeeId}).then(function (response) {
                if (firstTime) {
                    App.unblockUI("#hris-page-content");
                    firstTime = false;
                } else {
                    App.unblockUI("#reportTable");
                }
                console.log('departmentWiseEmployeeMonthlyR', response);
                var extractedDetailData = extractDetailData(response.data, employeeId);
                console.log('extractedDetailData', extractedDetailData);
                $tableContainer.kendoGrid({
                    dataSource: {
                        data: extractedDetailData.rows,
                        pageSize: 20
                    },
                    scrollable: false,
                    sortable: false,
                    pageable: false,
                    columns: extractedDetailData.cols
                });
                displayDataInBtnGroup('.custom-btn-group');
                displayTotalInGrid('.total-attendance');

            }, function (error) {
                if (firstTime) {
                    App.unblockUI("#hris-page-content");
                    firstTime = false;
                } else {
                    App.unblockUI("#reportTable");
                }
                console.log('departmentWiseEmployeeMonthlyE', error);
            });
        };
        var displayTotalInGrid = function (selector) {
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
        $('select').select2();
        var $employeeList = $('#employeeList');
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

        var employeeList = document.employeeList;

        populateList($employeeList, employeeList, 'EMPLOYEE_ID', 'FULL_NAME', "Select Employee");





        $generateReport.on('click', function () {
            var employeeId = $employeeList.val();
            if (employeeId == -1) {
                app.errorMessage("No Employee Selected", "Notification");
            } else {
                initializeReport(employeeId);
            }
        });

        var employeeId = document.employeeId;
        if (employeeId != 0) {
            initializeReport(employeeId);
            $employeeList.val(employeeId);
        }


    });
})(window.jQuery, window.app);