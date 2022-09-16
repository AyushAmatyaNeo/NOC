(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var $tableContainer = $("#reportTable");

        var $year = $('#fiscalYear');
        var $month = $('#fiscalMonth');

        app.setFiscalMonth($year, $month);




        var extractDetailData = function (rawData, departmentId) {
            var data = {};
            var column = {};
            for (var i in rawData) {
                if (typeof data[rawData[i].EMPLOYEE_ID] !== 'undefined') {
                    data[rawData[i].EMPLOYEE_ID].DAYS['C' + rawData[i].DAY_COUNT] =
                            JSON.stringify({
                                IS_ABSENT: rawData[i].IS_ABSENT,
                                IS_PRESENT: rawData[i].IS_PRESENT,
                                ON_LEAVE: rawData[i].ON_LEAVE,
                                IS_DAYOFF: rawData[i].IS_DAYOFF
                            });
                    data[rawData[i].EMPLOYEE_ID].TOTAL.IS_ABSENT = data[rawData[i].EMPLOYEE_ID].TOTAL.IS_ABSENT + parseFloat(rawData[i].IS_ABSENT);
                    data[rawData[i].EMPLOYEE_ID].TOTAL.IS_PRESENT = data[rawData[i].EMPLOYEE_ID].TOTAL.IS_PRESENT + parseFloat(rawData[i].IS_PRESENT);
                    data[rawData[i].EMPLOYEE_ID].TOTAL.ON_LEAVE = data[rawData[i].EMPLOYEE_ID].TOTAL.ON_LEAVE + parseFloat(rawData[i].ON_LEAVE);
                    data[rawData[i].EMPLOYEE_ID].TOTAL.IS_DAYOFF = data[rawData[i].EMPLOYEE_ID].TOTAL.IS_DAYOFF + parseFloat(rawData[i].IS_DAYOFF);
                } else {
                    data[rawData[i].EMPLOYEE_ID] = {
                        EMPLOYEE_ID: rawData[i].EMPLOYEE_ID,
                        FULL_NAME: rawData[i].FULL_NAME,
                        DAYS: {},
                        TOTAL: {
                            IS_ABSENT: parseFloat(rawData[i].IS_ABSENT),
                            IS_PRESENT: parseFloat(rawData[i].IS_PRESENT),
                            ON_LEAVE: parseFloat(rawData[i].ON_LEAVE),
                            IS_DAYOFF: parseFloat(rawData[i].IS_DAYOFF)
                        }
                    };
                    data[rawData[i].EMPLOYEE_ID].DAYS['C' + rawData[i].DAY_COUNT] =
                            JSON.stringify({
                                IS_ABSENT: rawData[i].IS_ABSENT,
                                IS_PRESENT: rawData[i].IS_PRESENT,
                                ON_LEAVE: rawData[i].ON_LEAVE,
                                IS_DAYOFF: rawData[i].IS_DAYOFF
                            });

                }
//                if (typeof column['C' + rawData[i].DAY_COUNT] === 'undefined') {
//                    var temp = 'C' + rawData[i].DAY_COUNT;
//                    column[temp] = {
//                        field: temp,
//                        title: "" + rawData[i].DAY_COUNT,
//                        template: '<span data="#: ' + temp + ' #" class="daily-attendance"></span>'
//                    }
//
//                }
            }
            var returnData = {rows: [], cols: []};

            returnData.cols.push({
                field: 'employee',
                title: 'employees'
            });
            for (var i = 1; i < 33; i++) {
                var temp = 'C' + i;
                returnData.cols.push({
                    field: temp,
                    title: "" + i,
                    template: '<span data="#: ' + temp + ' #" class="daily-attendance"></span>'
                });
            }
            returnData.cols.push({
                field: 'total',
                title: 'Total',
                template: '<div data="#: total #" class="btn-group widget-btn-list total-attendance">' +
                        '<a class="btn widget-btn custom-btn-present totalbtn"></a>' +
                        '<a class="btn widget-btn custom-btn-absent totalbtn"></a>' +
                        '<a class="btn widget-btn custom-btn-leave totalbtn"></a>' +
                        '</div>'});

            for (var k in data) {
                var row = data[k].DAYS;
                for (var i = 1; i < 33; i++) {
                    if (typeof row['C' + i] === 'undefined') {
                        row['C' + i] = null;
                    }
                }
                row['employee'] = data[k].FULL_NAME;
                returnData.rows.push(row);
                row['total'] = JSON.stringify(data[k].TOTAL);
            }
            return returnData;
        };
        var displayDataInBtnGroup = function (selector) {
            $(selector).each(function (k, group) {
                var $group = $(group);

                var data = $group.attr('data');
                if (data == 'null') {

                } else {
                    data = JSON.parse(data);
                    if (data.IS_PRESENT == 1) {
                        $group.html('P');
                        $group.parent().addClass('bg-green');
                    } else {
                        if (data.IS_ABSENT == 1) {
                            $group.html('A');
                            $group.parent().addClass('bg-red1 textcolor1');

                        } else {
                            if (data.ON_LEAVE == 1) {
                                $group.html('L');
                                $group.parent().addClass('bg-blue1 textcolor2');

                            } else {
                                $group.html('H');
                                $group.parent().addClass('bg-white1 textcolor3 ');
                            }

                        }

                    }
                }

//                $group.html((data.IS_PRESENT == 1) ? 'P' : ((data.IS_ABSENT == 1) ? 'A' : (data.ON_LEAVE == 1) ? 'L' : 'H'));
//                $group.parent().addClass('bg-red');
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
                $absent.html(data['IS_ABSENT']);
                $leave.html(data['ON_LEAVE']);

                var total = presentDays + absentDays + leaveDays;

                $present.attr('title', Number((presentDays * 100 / total).toFixed(1)));
                $absent.attr('title', Number((absentDays * 100 / total).toFixed(1)));
                $leave.attr('title', Number((leaveDays * 100 / total).toFixed(1)));
            });

        };
        var firstTime = true;
        var initializeReport = function (monthId, departmentId) {
            if (firstTime) {
                App.blockUI({target: "#hris-page-content"});

            } else {
                App.blockUI({target: "#reportTable"});

            }
            app.pullDataById(document.wsDepartmentWiseDailyReport, {departmentId: departmentId, monthId: monthId}).then(function (response) {
                if (firstTime) {
                    App.unblockUI("#hris-page-content");
                    firstTime = false;
                } else {
                    App.unblockUI("#reportTable");
                }
                console.log('departmentWiseEmployeeMonthlyR', response);
                var extractedDetailData = extractDetailData(response.data, departmentId);
                console.log('extractedDetailData', extractedDetailData);
//                $tableContainer.remove();
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
                displayDataInBtnGroup('.daily-attendance');
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

        $('select').select2();
        var $monthList = $('#monthList');
        var $departmentList = $('#departmentList');
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
        var monthId = document.monthId;
        var departmentId = document.departmentId;

        populateList($departmentList, comBraDepList['DEPARTMENT_LIST'], 'DEPARTMENT_ID', 'DEPARTMENT_NAME', "SELECT DEPARTMENT");


        $generateReport.on('click', function () {
            var departmentId = $departmentList.val();
            var monthId = $('#fiscalMonth').val();

            if (departmentId == -1 || monthId == '') {
                app.errorMessage("No Department Selected", "Notification");
            } else {
                initializeReport(monthId, departmentId);
            }
        });



        if (monthId != 0 && departmentId != 0) {
//            initializeReport(monthId, departmentId);
//            $monthList.val(monthId);
//            $departmentList.val(departmentId);
        }

    });
})(window.jQuery, window.app);