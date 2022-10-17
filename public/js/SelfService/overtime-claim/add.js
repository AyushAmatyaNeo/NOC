/**
 * Created by punam on 9/28/16.
 */
(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var $monthId = $('#monthId');
        var $search = $('#search');
        var $claimBtnDiv = $('#claimBtnDiv');
        let $table = $('#table');
        var $btnOvertime = $('#btnOvertime');

        var $print = $('#print');
        $print.on('click', function () {
            app.exportDomToPdf('printableArea', document.urlCss);
        });

        app.initializeKendoGrid($table, [
            {
                title: 'Claim as Satta Bida',
                template: "<input type='checkbox' #:CHECKBOX_STATUS# id='#:ATTENDANCE_DT#' class='k-checkbox row-checkbox'><label class='k-checkbox-label' for='#:ATTENDANCE_DT#'></label>",
                width: 120, locked: true,
                headerAttributes: {
                    "class": "table-header-cell k-text-right",
                    style: "font-size: 11px"
                  }
            },
            { field: "DAY_DETAIL", title: "Day Detail", width:"200px", locked: true },
            // { field: "EMPLOYEE_CODE", title: "Employee Code", width:"80px", locked: true },
            // { field: "FULL_NAME", title: "Employee Name", width:"120px", locked: true },
            { field: "ATTENDANCE_DT", title: "Attendance Date (AD)", width:"100px", locked: true},  
            { field: "ATTENDANCE_DATE_BS", title: "Attendance Date (BS)", width:"100px", locked: true}, 
            {
                title: 'OT Remarks',
                template: "<textarea required class='form-control otRemarks' name = 'otRemarks' id='otRemarks' style='width:88%;'></textarea>",
                width: 120, locked: true 
            }, 
            { field: "IN_TIME", title: "In Time", width:"100px" },
            { field: "OUT_TIME", title:"Out Time", width:"100px"},
            { field: "LEAVE_REWARD", title:"Satta Bida", width:"100px"},
            { field: "TOTAL_HOUR", title:"Total Hour", width:"100px"},
            { field: "OT_HOUR", title:"Overtime Hour", width:"100px"},
            { field: "LUNCH_EXPENSE", title:"Lunch Expense", width:"100px"},
            { field: "NIGHT_EXPENSE", title:"Night Expense", width:"100px"},
            { field: "LOCKING_EXPENSE", title:"Locking Expense", width:"100px"}
        ], null, null, null, 'Overtime Claim List');

        $(document).on('change', '.otRemarks', function () {
            var row = $(this).closest("tr");
            var grid = $table.data("kendoGrid");
            var dataItem = grid.dataItem(row);
            selectItems[dataItem['ATTENDANCE_DT']].otRemarks = $(this).val();
            console.log('#test'+dataItem['ATTENDANCE_DT']);
            $('#test'+dataItem['ATTENDANCE_DT']).val($(this).val());
        });
        function calculateAllDetails(list){
            var lunchExpenseSum = 0;
            var otHourSum = 0;
            var leaveSum = 0;
            var dashainTiharSattaBida = 0;
            var leaveCount = 0;
            var totalOtDays = 0;
            var nightAllowance = 0;
            var lockAllowance = 0;
            for (var i in list){
                console.log(list);
                if(list[i]['checked']){
                    if(parseFloat(list[i]['otHour'])<6){
                        leaveCount = 0.5;
                    }else{
                        leaveCount = 1;
                    }
                    leaveSum += leaveCount;
                    if(i==holidayDetail['LP']['date'] || i==holidayDetail['GP']['date'] || i==holidayDetail['SAP']['date'] || i==holidayDetail['DH1']['date'] || i==holidayDetail['DH2']['date']){
                        dashainTiharSattaBida += leaveCount;
                    }else if(i==holidayDetail['BT']['date'] || i==holidayDetail['NAW']['date'] || i==holidayDetail['AST']['date'] || i==holidayDetail['DAS']['date']){
                        dashainTiharSattaBida += (leaveCount*2);
                    }
                }else{
                    lunchExpenseSum += parseFloat(list[i]['lunchExpense']);
                    otHourSum += parseFloat(list[i]['otHour']);
                    totalOtDays += parseFloat(list[i]['otDays']);
                    nightAllowance += parseFloat(list[i]['nightExpense']);
                    lockAllowance += parseFloat(list[i]['lockingExpense']);
                }
            }
            $('#lunchExpense').val(lunchExpenseSum.toFixed(2));
            $('#totalOtHour').val(otHourSum.toFixed(2));
            $('#totalLeave').val(leaveSum.toFixed(2));
            $('#dashainTiharLeave').val(dashainTiharSattaBida.toFixed(2));
            $('#grandTotalLeave').val((leaveSum+dashainTiharSattaBida).toFixed(2));
            $('#totalOtDays').val(totalOtDays);
            $('#nightAllowance').val(nightAllowance.toFixed(2));
            $('#lockingAllowance').val(lockAllowance.toFixed(2));
        }
        // $('select').select2();
        var holidayDetail = {};

        $search.on('click', function () {
            const searchData = {
                monthId : $monthId.val()
            };
            $("#overtimeDetail tbody tr").remove();
            app.serverRequest(document.getHolidayDetail, searchData).then(function (success){
                holidayDetail = {};
                var data = success.data;
                for (var i in data){
                    holidayDetail[data[i]['HOLIDAY_CODE']] = {
                        'holidayCode': data[i]['HOLIDAY_CODE'],
                        'date': data[i]['START_DATE']
                    }
                }
            }, function (failure) {
                App.unblockUI("#hris-page-content");
            });
            app.serverRequest(document.getAllOvertimeDetailLink, searchData).then(function (success) {
                selectItems = {};
                var data = success.data;
                var dataForKendo = [];
                for (var i in data) {
                    data[i]['OT_HOUR'] = parseFloat(data[i]['OT_HOUR']).toFixed(2);
                    data[i]['TOTAL_HOUR'] = parseFloat(data[i]['TOTAL_HOUR']).toFixed(2);
                    selectItems[data[i]['ATTENDANCE_DT']] = {'checked': false, 
                                                            'otHour': data[i]['OT_HOUR'],
                                                            'inTime': data[i]['IN_TIME'],
                                                            'outTime': data[i]['OUT_TIME'],
                                                            'totalHour':data[i]['TOTAL_HOUR'],
                                                            'dayCode':data[i]['DAY_CODE'],
                                                            'lunchExpense':data[i]['LUNCH_EXPENSE'],
                                                            'nightExpense':data[i]['NIGHT_EXPENSE'],
                                                            'lockingExpense':data[i]['LOCKING_EXPENSE'],
                                                            'otRemarks':'',
                                                            'otDays':data[i]['OT_DAYS']
                                                        };

                    if(data[i]['CHECKBOX_STATUS']!='disabled'){
                        dataForKendo.push(data[i]);
                    }
                    var appendData = `
                    <tr>
                            <td style=" border: 1px solid black; border-collapse: collapse;">`+data[i]['DAY_DETAIL']+`</td>
                            <td style=" border: 1px solid black; border-collapse: collapse;">`+data[i]['ATTENDANCE_DATE_BS']+`</td>
                            <td style=" border: 1px solid black; border-collapse: collapse;">`+data[i]['ATTENDANCE_DT']+`</td>
                            <td style=" border: 1px solid black; border-collapse: collapse;">`+data[i]['IN_TIME']+`</td>
                            <td style=" border: 1px solid black; border-collapse: collapse;">`+data[i]['OUT_TIME']+`</td>
                            <td style=" border: 1px solid black; border-collapse: collapse;">`+data[i]['TOTAL_HOUR']+`</td>
                            <td style=" border: 1px solid black; border-collapse: collapse;">`+data[i]['OT_HOUR']+`</td>
                            <td style=" border: 1px solid black; border-collapse: collapse;">`+data[i]['LUNCH_EXPENSE']+`</td>
                            <td style=" border: 1px solid black; border-collapse: collapse;">`+data[i]['NIGHT_EXPENSE']+`</td>
                            <td><input id='test`+data[i]['ATTENDANCE_DT']+`' value = ''/></td>
                    </tr>`
                    // <td style=" border: 1px solid black; border-collapse: collapse;">`+data[i]['LOCKING_EXPENSE']+`</td>
                    $('#overtimeDetail tbody').append(appendData);
                }
                calculateAllDetails(selectItems);
                app.renderKendoGrid($table, dataForKendo);
                if(success.data.length > 0){
                    $claimBtnDiv.show();
                }else{
                    $claimBtnDiv.hide();
                }
            }, function (failure) {
                App.unblockUI("#hris-page-content");
            });
        });

        var exportMap = {
            'CANCEL_STATUS': 'Cancled by HR',
            'CHECKBOX_STATUS': 'Claimed Leave',
            'DAY_CODE': 'Day Code',
            'EMPLOYEE_CODE': 'Employee Code',
            'FULL_NAME': 'Full Name',
            'ATTENDANCE_DT': 'Attendance Date',
            'ATTENDANCE_DT_BS': 'Date (BS)',
            'OT_REMARKS': 'Remarks',
            'IN_TIME': 'In Time',
            'OUT_TIME': 'Out Time',
            'TOTAL_HOUR': 'Total Hours',
            'OT_HOUR': 'OT Hours',
            'LUNCH_ALLOWANCE': 'Lunch Allowance',
            'NIGHT_ALLOWANCE': 'Night Allowance',
            'LOCKING_ALLOWANCE': 'Locking Allowance',
        };
        $('#excelExport').on('click', function () {
            app.excelExport($table, exportMap, 'Overtime Claim Detail.xlsx');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, exportMap, 'Overtime Claim Detail.pdf');
        });

        var selectItems = {};
        $table.on('click', '.k-checkbox', function () {
            var checked = this.checked;
            var row = $(this).closest("tr");
            var grid = $table.data("kendoGrid");
            var dataItem = grid.dataItem(row);
            selectItems[dataItem['ATTENDANCE_DT']].checked = checked;
            if (checked) {
                row.addClass("k-state-selected");
            } else {
                row.removeClass("k-state-selected");
                var atleastOne = false;
                for (var key in selectItems) {
                    if (selectItems[key]['checked']) {
                        atleastOne = true;
                    }
                }
            }
            calculateAllDetails(selectItems);
        });

        $btnOvertime.bind("click", function () {
            var dataForLeave = [];
            var dataForOvertime = [];
            var monthId = $monthId.val();
        
            var details = [];
            var valid = true;
            for (var i in selectItems) {
                if(selectItems[i]['otRemarks']=='' && selectItems[i]['dayCode']=='H'){
                    valid = false;
                }
                if (selectItems[i].checked) {
                    dataForLeave.push({date: i, 
                                    otHour: selectItems[i]['otHour'],
                                    inTime:selectItems[i]['inTime'],
                                    outTime:selectItems[i]['outTime'],
                                    totalHour:selectItems[i]['totalHour'],
                                    dayCode:selectItems[i]['dayCode'],
                                    lunchExpense:selectItems[i]['lunchExpense'],
                                    nightExpense:selectItems[i]['nightExpense'],
                                    lockingExpense:selectItems[i]['lockingExpense'],
                                    otRemarks:selectItems[i]['otRemarks']
                                });
                }else{
                    dataForOvertime.push({date: i, otHour: selectItems[i]['otHour'],
                                        inTime:selectItems[i]['inTime'],
                                        outTime:selectItems[i]['outTime'],
                                        totalHour:selectItems[i]['totalHour'],
                                        dayCode:selectItems[i]['dayCode'],
                                        lunchExpense:selectItems[i]['lunchExpense'],
                                        nightExpense:selectItems[i]['nightExpense'],
                                        lockingExpense:selectItems[i]['lockingExpense'],
                                        otRemarks:selectItems[i]['otRemarks']
                                    });
                }
            }
            console.log(valid);
            if(valid == true){
                App.blockUI({target: "#hris-page-content"});
            
                details.push({totalOtDays: $('#totalOtDays').val(),
                            totalOtHour: $('#totalOtHour').val(),
                            lunchExpense: $('#lunchExpense').val(),
                            nightAllowance: $('#nightAllowance').val(),
                            lockingAllowance: $('#lockingAllowance').val(),
                            dashainTiharLeave: $('#dashainTiharLeave').val(),
                            grandTotalLeave: $('#grandTotalLeave').val(),
                            totalLeave: $('#totalLeave').val()
                        });
                app.pullDataById(
                        '',
                        {dataForLeave: dataForLeave, dataForOvertime: dataForOvertime, monthId:monthId, details: details}
                ).then(function (success) {
                    console.log('adsf');
                    App.unblockUI("#hris-page-content");
                    window.location.reload(true);
                }, function (failure) {
                    App.unblockUI("#hris-page-content");
                });
            }else{
                app.showMessage('Please fill remarks or every Overtime Claim!!!', 'error');
            }
            
        });
        
        var monthList = null;
        var $fiscalYear = $('#fiscalYearId');
        var $month = $('#monthId');

        app.setFiscalMonth($fiscalYear, $month, function (years, months, currentMonth) {
            monthList = months;
        });

        
        $month.select2();
        $('#fiscalYearId').select2();
    });
})(window.jQuery, window.app);


