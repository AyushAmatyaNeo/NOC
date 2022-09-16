(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        var $tableContainer = $("#reportTable");

        var $year = $('#fiscalYear');
        var $month = $('#fiscalMonth');
        var branchName = 'ALL';
        var monthName = '';
        app.setFiscalMonth($year, $month);

        $.each(document.searchManager.getIds(), function (key, value) {
            $('#' + value).select2();
        });

        app.searchTable('reportTable', ['code', 'employee'], false);

        var extractDetailData = function (rawData, days) {
            var data = {};
            var column = {};
            for (var i in rawData) {
                if (typeof data[rawData[i].EMPLOYEE_ID] !== 'undefined') {
                    data[rawData[i].EMPLOYEE_ID].DAYS['C' + rawData[i].DAY_COUNT] =
                            JSON.stringify({
                                IS_ABSENT: rawData[i].IS_ABSENT,
                                IS_PRESENT: rawData[i].IS_PRESENT,
                                ON_LEAVE: rawData[i].ON_LEAVE,
                                IS_DAYOFF: rawData[i].IS_DAYOFF,
                                IN_TIME: rawData[i].IN_TIME,
                                OUT_TIME: rawData[i].OUT_TIME,
                                TOTAL_HOUR: rawData[i].TOTAL_HOUR,
                                EMPLOYEE_CODE: rawData[i].EMPLOYEE_CODE,
                                HOLIDAY: rawData[i].HOLIDAY,
                                DEPARTMENT_NAME: rawData[i].DEPARTMENT_NAME,
                                TRAVEL: rawData[i].TRAVEL,
                            });
                    data[rawData[i].EMPLOYEE_ID].TOTAL.IS_ABSENT = data[rawData[i].EMPLOYEE_ID].TOTAL.IS_ABSENT + parseFloat(rawData[i].IS_ABSENT);
                    data[rawData[i].EMPLOYEE_ID].TOTAL.IS_PRESENT = data[rawData[i].EMPLOYEE_ID].TOTAL.IS_PRESENT + parseFloat(rawData[i].IS_PRESENT);
                    data[rawData[i].EMPLOYEE_ID].TOTAL.ON_LEAVE = data[rawData[i].EMPLOYEE_ID].TOTAL.ON_LEAVE + parseFloat(rawData[i].ON_LEAVE);
                    data[rawData[i].EMPLOYEE_ID].TOTAL.IS_DAYOFF = data[rawData[i].EMPLOYEE_ID].TOTAL.IS_DAYOFF + parseFloat(rawData[i].IS_DAYOFF);
                    data[rawData[i].EMPLOYEE_ID].TOTAL.HOLIDAY_WORK = data[rawData[i].EMPLOYEE_ID].TOTAL.HOLIDAY_WORK + parseFloat(rawData[i].HOLIDAY_WORK);
                    data[rawData[i].EMPLOYEE_ID].TOTAL.HOLIDAY = data[rawData[i].EMPLOYEE_ID].TOTAL.HOLIDAY + parseFloat(rawData[i].HOLIDAY);
                    data[rawData[i].EMPLOYEE_ID].TOTAL.TRAVEL = data[rawData[i].EMPLOYEE_ID].TOTAL.TRAVEL + parseFloat(rawData[i].TRAVEL);

                    if(isNaN(data[rawData[i].EMPLOYEE_ID].TOTAL.TOTAL_HOUR)) 
                    {
                    data[rawData[i].EMPLOYEE_ID].TOTAL.TOTAL_HOUR = 0;
                    }
                    if(rawData[i].TOTAL_HOUR != null && rawData[i].TOTAL_HOUR != ''){
                        var minutes = parseInt(rawData[i].TOTAL_HOUR.match(/(.*):/g).pop().replace(":", "")) * 60 + parseInt(rawData[i].TOTAL_HOUR.match(/:(.*)/g).pop().replace(":", ""));
                        data[rawData[i].EMPLOYEE_ID].TOTAL.TOTAL_HOUR = parseInt(data[rawData[i].EMPLOYEE_ID].TOTAL.TOTAL_HOUR) + parseInt(minutes);
                    }
                    console.log(data[rawData[i].EMPLOYEE_ID].TOTAL.TOTAL_HOUR);
                } else {
                    data[rawData[i].EMPLOYEE_ID] = {
                        EMPLOYEE_CODE: rawData[i].EMPLOYEE_CODE,
                        EMPLOYEE_ID: rawData[i].EMPLOYEE_ID,
                        FULL_NAME: rawData[i].FULL_NAME,
                        DEPARTMENT_NAME: rawData[i].DEPARTMENT_NAME,
                        DAYS: {},
                        TOTAL: {
                            IS_ABSENT: parseFloat(rawData[i].IS_ABSENT),
                            IS_PRESENT: parseFloat(rawData[i].IS_PRESENT),
                            ON_LEAVE: parseFloat(rawData[i].ON_LEAVE),
                            IS_DAYOFF: parseFloat(rawData[i].IS_DAYOFF),
                            HOLIDAY_WORK: parseFloat(rawData[i].HOLIDAY_WORK),
                            TOTAL_HOUR: parseFloat(rawData[i].TOTAL_HOUR),
                            HOLIDAY: parseFloat(rawData[i].HOLIDAY),
                            TRAVEL: parseFloat(rawData[i].TRAVEL),
                        }
                    };
                    data[rawData[i].EMPLOYEE_ID].DAYS['C' + rawData[i].DAY_COUNT] =
                            JSON.stringify({
                                IS_ABSENT: rawData[i].IS_ABSENT,
                                IS_PRESENT: rawData[i].IS_PRESENT,
                                ON_LEAVE: rawData[i].ON_LEAVE,
                                IS_DAYOFF: rawData[i].IS_DAYOFF,
                                HOLIDAY_WORK: rawData[i].HOLIDAY_WORK,
                                IN_TIME: rawData[i].IN_TIME,
                                OUT_TIME: rawData[i].OUT_TIME,
                                TOTAL_HOUR: rawData[i].TOTAL_HOUR,
                                EMPLOYEE_CODE: rawData[i].EMPLOYEE_CODE,
                                HOLIDAY: rawData[i].HOLIDAY,
                                TRAVEL: rawData[i].TRAVEL,

                            });

                }
            }
            var returnData = {rows: [], cols: []};

            returnData.cols.push({
                field: 'EMPLOYEE_CODE',
                title: 'Code',
                template: '<span><b>#=code#</b></span>'
            });

            returnData.cols.push({
                field: 'Employee',
                title: 'EMPLOYEES',
                template: '<span><b>#=employee#</b></span>'
            });

            returnData.cols.push({
                field: 'DEPARTMENT_NAME',
                title: 'Department',
                template: '<span style="text-align: left">#=department#</span>'
            });

            returnData.cols.push({
                title: 'Time',
                // width: 400,
                template: '<span style="text-align: center"><i>In_time</br>Out_time</br>Total_hour</i></span>'
            });

            for (var i = 1; i <= days[0].TOTAL_DAYS; i++) {
                var temp = 'C' + i;
                returnData.cols.push({
                    field: temp,
                    title: "" + i,
                    template: '<span " data="#: ' + temp + ' #" class="daily-attendance"></span>'
                });
            }

            returnData.cols.push({
                field: 'present',
                title: 'P',
                template: '<div data="#: total #" class="btn-group widget-btn-list present-attendance">' +
                    '<a class="btn widget-btn custom-btn-present totalbtn"></a>' +
                    '</div>'});

            returnData.cols.push({
                field: 'absent',
                title: 'A',
                template: '<div data="#: total #" class="btn-group widget-btn-list absent-attendance">' +
                    '<a class="btn widget-btn custom-btn-absent totalbtn"></a>' +
                    '</div>'});

            returnData.cols.push({
                field: 'leave',
                title: 'L/H',
                template: '<div data="#: total #" class="btn-group widget-btn-list leave-attendance">' +
                    '<a class="btn widget-btn custom-btn-present totalbtn"></a>' +
                    '</div>'});

            returnData.cols.push({
                field: 'holidaywork',
                title: 'WH',
                template: '<div data="#: total #" class="btn-group widget-btn-list holidaywork-attendance">' +
                    '<a class="btn widget-btn custom-btn-present totalbtn"></a>' +
                    '</div>'});

            returnData.cols.push({
                field: 'total',
                title: 'Total',
                template: '<div data="#: total #" class="btn-group widget-btn-list total-attendance">' +
                    '<a class="btn widget-btn custom-btn-leave totalbtn"></a>' +
                    '</div>'});

            returnData.cols.push({
                field: 'totalhour',
                title: 'Total Hour',
                template: '<div data="#: total #" class="btn-group widget-btn-list totalhour-attendance">' +
                    '<a class="btn widget-btn custom-btn-leave totalbtn"></a>' +
                    '</div>'});

            for (var k in data) {
                var row = data[k].DAYS;
                for (var i = 1; i <= days[0].TOTAL_DAYS; i++) {
                    if (typeof row['C' + i] === 'undefined') {
                        row['C' + i] = null;
                    }
                }
                row['employee'] = data[k].FULL_NAME;
                row['code'] = (data[k].EMPLOYEE_CODE == null) ? '' : data[k].EMPLOYEE_CODE;
                row['department'] = data[k].DEPARTMENT_NAME;
                returnData.rows.push(row);
                row['present'] = JSON.stringify(data[k].TOTAL.IS_PRESENT);
                row['absent'] = JSON.stringify(data[k].TOTAL.IS_ABSENT);
                row['dayoff'] = JSON.stringify(data[k].TOTAL.IS_DAYOFF);
                row['leave'] = JSON.stringify(data[k].TOTAL.ON_LEAVE);
                row['holidaywork'] = JSON.stringify(data[k].TOTAL.HOLIDAY_WORK);
                row['total'] = JSON.stringify(data[k].TOTAL);
                row['totalhour'] = JSON.stringify(data[k].TOTAL_HOUR);
                row['holiday'] = JSON.stringify(data[k].HOLIDAY);
                row['travel'] = JSON.stringify(data[k].TRAVEL);
            }
            return returnData;
        };


        var displayTotalInGrid = function (selector) {
            $(selector).each(function (k, group) {
                var $group = $(group);
                var data = JSON.parse($group.attr('data'));
                var $childrens = $group.children();
                var $data = $($childrens[0]);

                var travelDays = parseFloat(data['TRAVEL']);
                var presentDays = parseFloat(data['IS_PRESENT']) + travelDays;
                var absentDays = parseFloat(data['IS_ABSENT']);
                var leaveDays =  parseFloat(data['ON_LEAVE']) + parseFloat(data['IS_DAYOFF']) + parseFloat(data['HOLIDAY']);
                var holidayWork = parseFloat(data['HOLIDAY_WORK']);

                var totalhour = parseFloat(data['TOTAL_HOUR']);
                totalhour = Math.floor(totalhour / 60) + ":" + totalhour % 60;

                var actualeave = (leaveDays > holidayWork) ? (leaveDays-holidayWork) : (holidayWork - leaveDays);

                var totalPresent = presentDays + leaveDays + holidayWork;
                var actualPresent = (presentDays>0)? totalPresent : presentDays + leaveDays;

                var total = presentDays + absentDays + leaveDays;


                if(selector == '.present-attendance'){
                    $data.html(presentDays);
                    $data.attr('title', Number((presentDays * 100 / total).toFixed(1)));
                } else if(selector == '.absent-attendance'){
                    $data.html(absentDays);
                    $data.attr('title', Number((absentDays * 100 / total).toFixed(1)));
                } else if(selector == '.total-attendance'){
                    $data.html(actualPresent);
                    $data.attr('title', Number((actualPresent * 100 / total).toFixed(1)));
                } else if(selector == '.leave-attendance'){
                    $data.html(leaveDays);
                    $data.attr('title', Number((leaveDays * 100 / total).toFixed(1)));
                } else if(selector == '.holidaywork-attendance') {
                    $data.html(holidayWork);
                    $data.attr('title', Number((holidayWork * 100 / total).toFixed(1)));
                } else if(selector == '.totalhour-attendance'){
                    $data.html(totalhour);
                    $data.attr('title', Number((totalhour * 100 / total).toFixed(1)));
                } else {}

            });

        };

        var initializeReport = function (q) {
            $tableContainer.empty();
            app.pullDataById(document.wsBranchWiseDailyReport, q).then(function (response) {
                console.log('branchWiseEmployeeMonthlyR', response);
                var extractedDetailData = extractDetailData(response.data, response.days);
                if(response.branchName != -1){
                    branchName = response.branchName[0].BRANCH_NAME;
                    console.log(branchName);
                }
                monthName = response.dates[0].MONTH_EDESC;
                console.log(monthName);
                console.log('extractedDetailData', extractedDetailData);
                $tableContainer.kendoGrid({
                    dataSource: {
                        data: extractedDetailData.rows,
                        pageSize: 500
                    },
                    dataBound: function (e) {
                        var grid = e.sender;
                        if (grid.dataSource.total() === 0) {
                            var colCount = grid.columns.length;
                            $(e.sender.wrapper)
                                    .find('tbody')
                                    .append('<tr class="kendo-data-row"><td colspan="' + colCount + '" class="no-data">There is no data to show in the grid.</td></tr>');
                        }

                        $('.daily-attendance').each(function (k, group) {
                            var $group = $(group);

                            var data = $group.attr('data');
                            if (data == 'null') {

                            } else {
                                data = JSON.parse(data);
                                if (data.IS_PRESENT == 1 || data.IN_TIME != null) {
                                    $group.html(((data.IN_TIME == null) ? '-' : data.IN_TIME) + '</br>' + ((data.OUT_TIME == null) ? '-' : data.OUT_TIME) + '</br>' + ((data.TOTAL_HOUR == null) ? '-' : data.TOTAL_HOUR));
                                    $group.parent().addClass('bg-success text-center');
                                } else {
                                    if (data.IS_ABSENT == 1) {
                                        $group.html('A');
                                        $group.parent().addClass('bg-red1 textcolor1 text-center');

                                    } else {
                                        if (data.ON_LEAVE == 1) {
                                            $group.html('L');
                                            $group.parent().addClass('bg-blue1 textcolor2 text-center');

                                        }else if (data.TRAVEL == 1) {
                                            $group.html('T');
                                            $group.parent().addClass('bg-white1 textcolor3 ');
                                        } else {
                                            $group.html('H');
                                            $group.parent().addClass('bg-white1 textcolor3 text-center');
                                        }

                                    }

                                }
                            }

                        });

                    },
                    scrollable: false,
                    sortable: false,
                    pageable: false,
                    columns: extractedDetailData.cols
                });

                displayTotalInGrid('.present-attendance');
                displayTotalInGrid('.absent-attendance');
                displayTotalInGrid('.leave-attendance');
                displayTotalInGrid('.holidaywork-attendance');
                displayTotalInGrid('.total-attendance');
                displayTotalInGrid('.totalhour-attendance');

            }, function (error) {
                console.log('departmentWiseEmployeeMonthlyE', error);
            });
        };

        $('select').select2();
        var $monthList = $('#monthList');
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
        var monthList = document.monthList;
        var monthId = document.monthId;

        populateList($monthList, monthList, 'MONTH_ID', 'MONTH_EDESC', "Select Month", monthId);


        $generateReport.on('click', function () {
            var monthId = $('#fiscalMonth').val();
            var q = document.searchManager.getSearchValues();
            q['monthId'] = monthId;

            console.log(q);

            initializeReport(q);

        });


        $('#excelExport').on('click', function () {
            $tableContainer.table2excel({
                exclude: ".noExl",
                name: "Branch wise daily In Out",
                filename: "Branch wise daily In Out"
            });
        });

        // $('#exportPdf').on('click', function () {
        //     kendo.drawing.drawDOM($tableContainer).then(function (group) {
        //         kendo.drawing.pdf.saveAs(group, "Branch wise daily In Out.pdf");
        //     });
        // });

        $("#printAsPDF").click(function (e) {
            var divToPrint = document.getElementById('reportTable');

            var newWin = window.open('', 'Print-Window');

            newWin.document.open();
            newWin.document.write('<html><body onload="window.print()"><div style="text-align: center;"><p style="font-size:20px;" ><b>'+document.preference.companyName+'</b></p><p style="font-size:20px;"><b>'+document.preference.companyAddress+'</b></p></div><div style="text-align: left;"><p style="font-size:15px;"><b>Branch: '+branchName+'<br/>Month: '+monthName+'</b></p></div>' + divToPrint.innerHTML + '<br/></body><style>table {border-collapse: collapse;}table, th, td {border: 1px solid black;text-align: center;} table td:nth-child(2){ text-align: left; }</style></html>');
            newWin.document.close();

            branchName = 'ALL';
            // setTimeout(function () {
            //     newWin.close();
            // }, 1000);
        });


    });
})(window.jQuery, window.app);