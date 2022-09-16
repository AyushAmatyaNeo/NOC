(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        var $tableContainer = $("#reportTable");

        var $year = $('#fiscalYear');
        var $month = $('#fiscalMonth');
        var monthName = 'ALL';
        var branchName = '';

        app.setFiscalMonth($year, $month);

        $.each(document.searchManager.getIds(), function (key, value) {
            $('#' + value).select2();
        });

        app.searchTable('reportTable', ['code', 'employee'], false);

        var extractDetailData = function (rawData, days) {
            console.log(rawData);
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
                                HOLIDAY_WORK: rawData[i].HOLIDAY_WORK,
                                EMPLOYEE_CODE: rawData[i].EMPLOYEE_CODE,
                                DEPARTMENT_NAME: rawData[i].DEPARTMENT_NAME,
                                HOLIDAY: rawData[i].HOLIDAY,
                                TRAVEL: rawData[i].TRAVEL,
                            });
                    data[rawData[i].EMPLOYEE_ID].TOTAL.IS_ABSENT = data[rawData[i].EMPLOYEE_ID].TOTAL.IS_ABSENT + parseFloat(rawData[i].IS_ABSENT);
                    data[rawData[i].EMPLOYEE_ID].TOTAL.IS_PRESENT = data[rawData[i].EMPLOYEE_ID].TOTAL.IS_PRESENT + parseFloat(rawData[i].IS_PRESENT);
                    data[rawData[i].EMPLOYEE_ID].TOTAL.ON_LEAVE = data[rawData[i].EMPLOYEE_ID].TOTAL.ON_LEAVE + parseFloat(rawData[i].ON_LEAVE);
                    data[rawData[i].EMPLOYEE_ID].TOTAL.IS_DAYOFF = data[rawData[i].EMPLOYEE_ID].TOTAL.IS_DAYOFF + parseFloat(rawData[i].IS_DAYOFF);
                    data[rawData[i].EMPLOYEE_ID].TOTAL.HOLIDAY_WORK = data[rawData[i].EMPLOYEE_ID].TOTAL.HOLIDAY_WORK + parseFloat(rawData[i].HOLIDAY_WORK);
                    data[rawData[i].EMPLOYEE_ID].TOTAL.HOLIDAY = data[rawData[i].EMPLOYEE_ID].TOTAL.HOLIDAY + parseFloat(rawData[i].HOLIDAY);
                    data[rawData[i].EMPLOYEE_ID].TOTAL.TRAVEL = data[rawData[i].EMPLOYEE_ID].TOTAL.TRAVEL + parseFloat(rawData[i].TRAVEL);
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
                field: 'employee',
                title: 'Employees',
                template: '<span style="text-align: left">#=employee#</span>'
            });

            returnData.cols.push({
                field: 'DEPARTMENT_NAME',
                title: 'Department',
                template: '<span style="text-align: left">#=department#</span>'
            });
            for (var i = 1; i <= days[0].TOTAL_DAYS; i++) {
                var temp = 'C' + i;
                returnData.cols.push({
                    field: temp,
                    title: "" + i,
                    template: '<span data="#: ' + temp + ' #" class="daily-attendance"></span>'
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

            for (var k in data) {
                console.log(data);
                // debugger;
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
                row['holiday'] = JSON.stringify(data[k].HOLIDAY);
                row['travel'] = JSON.stringify(data[k].TRAVEL);
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
                    if (data.IS_PRESENT == 1 ) {
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

                            } else if (data.HOLIDAY_WORK == 1){
                                $group.html('WH');
                                $group.parent().addClass('bg-white1 textcolor3 ');
                            } else if (data.HOLIDAY == 1 || data.IS_DAYOFF == 1){
                                $group.html('H');
                                $group.parent().addClass('bg-white1 textcolor3 ');
                            } else if (data.TRAVEL == 1){
                                $group.html('T');
                                $group.parent().addClass('bg-white1 textcolor3 ');
                            } else {

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
                var $data = $($childrens[0]);

                var travelDays = parseFloat(data['TRAVEL']);
                var presentDays = parseFloat(data['IS_PRESENT']) + travelDays;
                var absentDays = parseFloat(data['IS_ABSENT']);
                var leaveDayoffHoliday =  parseFloat(data['ON_LEAVE']) + parseFloat(data['IS_DAYOFF']) + parseFloat(data['HOLIDAY']);
                var holidayWork = parseFloat(data['HOLIDAY_WORK']);

                // var actualeave = (leaveDays > holidayWork) ? (leaveDays-holidayWork) : (holidayWork - leaveDays);

                var actualLeaves = leaveDayoffHoliday;

                if(presentDays == 0) {
                    actualLeaves = 0;
                }

                var totalPresent = presentDays + actualLeaves + holidayWork ;
                var actualPresent = (presentDays>0)? totalPresent : presentDays + actualLeaves;

                var total = presentDays + absentDays + leaveDayoffHoliday;


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
                    $data.html(leaveDayoffHoliday);
                    $data.attr('title', Number((leaveDayoffHoliday * 100 / total).toFixed(1)));
                } else if(selector == '.holidaywork-attendance'){
                    $data.html(holidayWork);
                    $data.attr('title', Number((holidayWork * 100 / total).toFixed(1)));
                } else {}

            });

        };

        // var firstTime = true;
        var initializeReport = function (q) {
            $tableContainer.empty();
            // if (firstTime) {
            //     App.blockUI({target: "#hris-page-content"});
            //
            // } else {
            //     App.blockUI({target: "#reportTable"});
            //
            // }
            app.pullDataById(document.wsBranchWiseDailyReport, q).then(function (response) {
                // if (firstTime) {
                //     App.unblockUI("#hris-page-content");
                //     firstTime = false;
                // } else {
                //     App.unblockUI("#reportTable");
                // }
                console.log('branchWiseEmployeeMonthlyR', response);
                var extractedDetailData = extractDetailData(response.data, response.days);
                if(response.branchName != -1){
                    branchName = response.branchName[0].BRANCH_NAME;
                    console.log(branchName);
                }
                monthName = response.dates[0].MONTH_EDESC;
                console.log(monthName);
                console.log('extractedDetailData', extractedDetailData);
//                $tableContainer.remove();
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
                displayDataInBtnGroup('.daily-attendance');
                displayTotalInGrid('.present-attendance');
                displayTotalInGrid('.absent-attendance');
                displayTotalInGrid('.leave-attendance');
                displayTotalInGrid('.holidaywork-attendance');
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
        var $branchList = $('#branchList');
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



        if (monthId != 0 && branchId != 0) {
//            initializeReport(monthId, branchId);
//            $monthList.val(monthId);
//            $branchList.val(branchId);
        }

        var today = new Date();
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0');
        var yyyy = today.getFullYear();
        var today = dd + '/' + mm + '/' + yyyy;

        $('#excelExport').on('click', function () {
            $tableContainer.table2excel({
                exclude: ".noExl",
                name: "Branch wise daily",
                filename: "Branch wise daily"
            });
        });



        $("#printAsPDF").click(function (e) {
            var divToPrint = document.getElementById('reportTable');
            // divToPrint.innerHTML += `<colgroup>
            //         <col span="1" style="text-align: left">
            //         </colgroup> `;

            var newWin = window.open('', 'Print-Window');
            newWin.document.open();
            newWin.document.write('<html><body onload="window.print()"> <div style="text-align: center;"><p style="font-size:20px;"><b>'+document.preference.companyName+'</b></p><p style="font-size:18px;"><b>'+document.preference.companyAddress+'</b></p></div><div style="text-align: left;"><p style="font-size:15px;"><b>Branch: '+branchName+'<br/>Month: '+monthName+'</b></p></div>' + divToPrint.innerHTML + '<br/><div><span style="display: inline;">Generated By: '+document.name+'</span><span style="display: inline; float: right;">Generated Date: '+today+'</span></div></body><style>table {border-collapse: collapse;}table, th, td {border: 1px solid black;text-align: center;} table td:nth-child(2){ text-align: left; }</style></html>');
            newWin.document.close();

            branchName = 'ALL';
        });



    });
})(window.jQuery, window.app);