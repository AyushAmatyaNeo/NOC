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
                title: 'Cancel',
                template: "<input type='checkbox' #:CANCEL_STATUS# disabled id='#:ATTENDANCE_DT#' class='k-checkbox row-checkbox'><label class='k-checkbox-label' for='#:ATTENDANCE_DT#'></label>",
                width: 65, locked: true
            },
            {
                title: 'Claim Satta Bida',
                template: "<input type='checkbox' #:CHECKBOX_STATUS# disabled id='#:ATTENDANCE_DT#' class='k-checkbox row-checkbox'><label class='k-checkbox-label' for='#:ATTENDANCE_DT#'></label>",
                width: 120, locked: true
            },
            { field: "DAY_DETAIL", title: "Day Detail", width:"200px", locked: true },
            // { field: "EMPLOYEE_CODE", title: "Employee Code", width:"80px", locked: true },
            // { field: "FULL_NAME", title: "Employee Name", width:"120px", locked: true },
            { field: "ATTENDANCE_DT", title: "Attendance Date (AD)", width:"100px", locked: true},  
            { field: "ATTENDANCE_DT_BS", title: "Attendance Date (BS)", width:"100px", locked: true}, 
            {
                title: 'OT Remarks',
                template: "<textarea required class='form-control readonly otRemarks' name = 'otRemarks' id='otRemarks' style='width:88%;'> #:OT_REMARKS# </textarea>",
                width: 120, locked: true 
            }, 
            { field: "IN_TIME", title: "In Time", width:"100px" },
            { field: "OUT_TIME", title:"Out Time", width:"100px"},
            { field: "TOTAL_HOUR", title:"Total Hour", width:"100px"},
            { field: "OT_HOUR", title:"Overtime Hour", width:"100px"},
            { field: "LUNCH_ALLOWANCE", title:"Lunch Expense", width:"100px"},
            { field: "NIGHT_ALLOWANCE", title:"Night Expense", width:"100px"},
            { field: "LOCKING_ALLOWANCE", title:"Locking Expense", width:"100px"}
        ], null, null, null, 'Overtime Claim List');
        app.renderKendoGrid($table, document.subDetails);
        var data = document.subDetails;
        console.log(data);
        for (var i in data){
            var appendData = `
            <tr>
                    <td style=" border: 1px solid black; border-collapse: collapse;padding:5px;">`+data[i]['DAY_DETAIL']+`</td>
                    <td style=" border: 1px solid black; border-collapse: collapse;padding:5px;">`+data[i]['ATTENDANCE_DT']+`</td>
                    <td style=" border: 1px solid black; border-collapse: collapse;padding:5px;">`+data[i]['ATTENDANCE_DT_BS']+`</td>
                    <td style=" border: 1px solid black; border-collapse: collapse;padding:5px;">`+data[i]['OT_REMARKS']+`</td>
                    <td style=" border: 1px solid black; border-collapse: collapse;padding:5px;">`+data[i]['IN_TIME']+`</td>
                    <td style=" border: 1px solid black; border-collapse: collapse;padding:5px;">`+data[i]['OUT_TIME']+`</td>
                    <td style=" border: 1px solid black; border-collapse: collapse;padding:5px;">`+data[i]['TOTAL_HOUR']+`</td>
                    <td style=" border: 1px solid black; border-collapse: collapse;padding:5px;">`+data[i]['OT_HOUR']+`</td>
                    <td style=" border: 1px solid black; border-collapse: collapse;padding:5px;">`+data[i]['LUNCH_ALLOWANCE']+`</td>
                    <td style=" border: 1px solid black; border-collapse: collapse;padding:5px;">`+data[i]['NIGHT_ALLOWANCE']+`</td>
                    <td style=" border: 1px solid black; border-collapse: collapse;padding:5px;">`+data[i]['LOCKING_ALLOWANCE']+`</td>
            </tr>`
            // <td style=" border: 1px solid black; border-collapse: collapse;">`+data[i]['LOCKING_EXPENSE']+`</td>
            $('#overtimeDetail tbody').append(appendData);
        }
        $('select').select2();
        $search.on('click', function () {
            const searchData = {
                monthId : $monthId.val()
            };
            app.serverRequest(document.getAllOvertimeDetailLink, searchData).then(function (success) {
                app.renderKendoGrid($table, success.data);
                selectItems = {};
                var data = success.data;
                for (var i in data) {
                    selectItems[data[i]['ATTENDANCE_DT']] = {'checked': false, 
                                                            'otHour': data[i]['OT_HOUR'],
                                                            'inTime': data[i]['IN_TIME'],
                                                            'outTime': data[i]['OUT_TIME']};
                }
                if(success.data){
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
            'DAY_DETAIL': 'Day Detail',
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
                        return;
                    }
                }
            }
        });

        $btnOvertime.bind("click", function () {
            var dataForLeave = [];
            var dataForOvertime = [];
            var monthId = $monthId.val();
            for (var i in selectItems) {
                if (selectItems[i].checked) {
                    dataForLeave.push({date: i, 
                                    otHour: selectItems[i]['otHour'],
                                    inTime:selectItems[i]['inTime'],
                                    outTime:selectItems[i]['outTime']});
                }else{
                    dataForOvertime.push({date: i, otHour: selectItems[i]['otHour'],
                                        inTime:selectItems[i]['inTime'],
                                        outTime:selectItems[i]['outTime']});
                }
            }
            App.blockUI({target: "#hris-page-content"});
            app.pullDataById(
                    '',
                    {dataForLeave: dataForLeave, dataForOvertime: dataForOvertime, monthId:monthId}
            ).then(function (success) {
                App.unblockUI("#hris-page-content");
                // window.location.reload(true);
            }, function (failure) {
                App.unblockUI("#hris-page-content");
            });
        });

    });
})(window.jQuery, window.app);


