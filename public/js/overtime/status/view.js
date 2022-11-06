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

        app.initializeKendoGrid($table, [
            {
                title: 'Cancel',
                template: "<input type='checkbox' #:CANCEL_STATUS# id='#:ID#' class='k-checkbox row-checkbox cancleClaim'><label class='k-checkbox-label' for='#:ID#'></label>",
                width: 65, locked: true
            },
            {
                title: 'Claim Satta Bida',
                template: "<input type='checkbox' #:CHECKBOX_STATUS# disabled id='#:ATTENDANCE_DT#' class='k-checkbox row-checkbox'><label class='k-checkbox-label' for='#:ATTENDANCE_DT#'></label>",
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
            { field: "ATTENDANCE_DT_BS", title: "Attendance Date (BS)", width:"100px", locked: true},  
            {
                title: 'OT Remarks',
                template: "<textarea required readonly class='form-control otRemarks' name = 'otRemarks' id='otRemarks' style='width:88%;'> #:OT_REMARKS# </textarea>",
                width: 120, locked: true 
            },
            { field: "IN_TIME", title: "In Time", width:"100px" },
            { field: "OUT_TIME", title:"Out Time", width:"100px"},
            { field: "TOTAL_HOUR", title:"Total Hour", width:"100px"},
            { field: "OT_HOUR", title:"Overtime Hour", width:"100px"},
            { field: "LUNCH_ALLOWANCE", title:"Lunch Expense", width:"100px"},
            { field: "NIGHT_ALLOWANCE", title:"Night Expense", width:"100px"},
            { field: "LOCKING_ALLOWANCE", title:"Locking Expense", width:"100px"}
        ]);

        app.renderKendoGrid($table, document.subDetails);
        var selectItems = {};
        for (var i in document.subDetails) {
            selectItems[document.subDetails[i]['ID']] = {'checked': (document.subDetails[i]['CANCELED_BY_RA']=='Y')?true:false, 
                                                    'otHour': document.subDetails[i]['OT_HOUR'],
                                                    'inTime': document.subDetails[i]['IN_TIME'],
                                                    'outTime': document.subDetails[i]['OUT_TIME'],
                                                    'typeFlag': document.subDetails[i]['TYPE_FLAG'],
                                                    'lunchAllowance': document.subDetails[i]['LUNCH_ALLOWANCE'],
                                                    'nightAllowance': document.subDetails[i]['NIGHT_ALLOWANCE'],
                                                    'lockingAllowance': document.subDetails[i]['LOCKING_ALLOWANCE'],
                                                    'leaveReward': document.subDetails[i]['LEAVE_REWARD'],
                                                    'dashianTiharLeaveReward': document.subDetails[i]['DASHAIN_TIHAR_LEAVE_REWARD'],
                                                    'otDays': document.subDetails[i]['OT_DAYS'],
                                                    'festiveOtDays': document.subDetails[i]['BONUS_MULTI'] * document.subDetails[i]['OT_DAYS'],
                                                    'grandOtDays': ( document.subDetails[i]['BONUS_MULTI'] * document.subDetails[i]['OT_DAYS'] ) + document.subDetails[i]['OT_DAYS'] 
                                                };
        }

        // $('.cancleClaim').on('change', function() {
        //     var claimedHour = 0;
        //     var claimedLeave = 0;
        //     var lunchAllowance = 0;
        //     var otDays = 0;
        //     var dashianTiharLeaveReward = 0;
        //     console.log(selectItems);
        //     for (var i in selectItems) {
        //         if(!selectItems[i]['checked']){
        //             if(selectItems[i]['typeFlag'] == 'L'){
        //                 claimedLeave += parseFloat(selectItems[i]['leaveReward']);
        //                 dashianTiharLeaveReward += parseFloat(selectItems[i]['dashianTiharLeaveReward']);
        //             }else if(selectItems[i]['typeFlag'] == 'O'){
        //                 claimedHour += parseFloat(selectItems[i]['otHour']);
        //                 lunchAllowance += parseFloat(selectItems[i]['lunchAllowance']);
        //                 otDays += 1;
        //             }
        //         }
        //     }
        //     $('#appOtHours').val(claimedHour.toFixed(2));
        //     $('#totalLeave').val(claimedLeave);
        //     $('#dashainTiharBida').val(dashianTiharLeaveReward);
        //     $('#totalSattaBida').val(claimedLeave+dashianTiharLeaveReward);
        //     $('#lunchAllowance').val(lunchAllowance);
        //     $('#appOtDays').val(otDays);
        // });
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

        $table.on('click', '.k-checkbox', function () {
            var checked = this.checked;
            var row = $(this).closest("tr");
            var grid = $table.data("kendoGrid");
            var dataItem = grid.dataItem(row);
            selectItems[dataItem['ID']].checked = checked;
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
            var claimedHour = 0;
            var claimedLeave = 0;
            var lunchAllowance = 0;
            var nightAllowance = 0;
            var lockingAllowance = 0;
            var otDays = 0;
            var festiveOtDays = 0;
            var totalOtDays = 0;
            var dashianTiharLeaveReward = 0;
            for (var i in selectItems) {
                if(!selectItems[i]['checked']){
                    if(selectItems[i]['typeFlag'] == 'L'){
                        claimedLeave += parseFloat(selectItems[i]['leaveReward']);
                        dashianTiharLeaveReward += parseFloat(selectItems[i]['dashianTiharLeaveReward']);
                    }else if(selectItems[i]['typeFlag'] == 'O'){
                        claimedHour += parseFloat(selectItems[i]['otHour']);
                        lunchAllowance += parseFloat(selectItems[i]['lunchAllowance']);
                        otDays += parseFloat(selectItems[i]['otDays']);
                        festiveOtDays += parseFloat(selectItems[i]['festiveOtDays']);
                        nightAllowance += parseFloat(selectItems[i]['nightAllowance']);
                        lockingAllowance += parseFloat(selectItems[i]['lockingAllowance']);
                    }
                }
            }
            $('#appOtHours').val(claimedHour.toFixed(2));
            $('#totalLeave').val(claimedLeave);
            $('#dashainTiharBida').val(dashianTiharLeaveReward);
            $('#totalSattaBida').val(claimedLeave+dashianTiharLeaveReward);
            $('#lunchAllowance').val(lunchAllowance);
            $('#appOtDays').val(otDays);
            $('#appNightAllowance').val(nightAllowance);
            $('#appLockingAllowance').val(lockingAllowance);
            $('#festiveOtDays').val(festiveOtDays);
            $('#grandTotalOtDays').val(festiveOtDays + otDays);
        });

        $('.btnApproveReject').bind("click", function () {
            var btnId = $(this).attr('id');
            var totalOT = $('#totalOT').val();
            var totalLeave = $('#totalLeave').val();
            var recommenderRemarks = $('#recommenderRemarks').val();
            var approverRemarks = $('#approverRemarks').val();
            var raRemarks = $('#raRemarks').val();
            var appOtDays = $('#appOtDays').val();
            var appOtHours =$('#appOtHours').val();
            var appLunchAllowance =$('#lunchAllowance').val();
            var appLockingAllowance =$('#appLockingAllowance').val();
            var appNightAllowance =$('#appNightAllowance').val();
            var appSattaBida = $('#totalLeave').val();
            var appTiharBida = $('#dashainTiharBida').val();
            var appTotalBida = $('#totalSattaBida').val();
            var festiveOtDays = $('#festiveOtDays').val();
            var grandTotalOtDays = $('#grandTotalOtDays').val();
            // App.blockUI({target: "#hris-page-content"});
            app.pullDataById(
                    document.approveRejectLink,
                    {
                        subDetail:selectItems, 
                        btnId: btnId, 
                        totalOT:totalOT,
                        totalLeave:totalLeave,
                        recommenderRemarks: recommenderRemarks,
                        approverRemarks: approverRemarks,
                        raRemarks: raRemarks,
                        appOtDays: appOtDays,
                        appOtHours: appOtHours,
                        appLunchAllowance: appLunchAllowance,
                        appLockingAllowance: appLockingAllowance,
                        appNightAllowance: appNightAllowance,
                        appSattaBida: appSattaBida,
                        appTiharBida: appTiharBida,
                        appTotalBida: appTotalBida,
                        festiveOtDays: festiveOtDays,
                        grandTotalOtDays: grandTotalOtDays,
                    }
            ).then(function (success) {
                App.unblockUI("#hris-page-content");
                // window.location.href = "../../../status";
            }, function (failure) {
                App.unblockUI("#hris-page-content");
            });
        });

        var $print = $('#print');
        $print.on('click', function () {
            app.exportDomToPdf('printableArea', document.urlCss);
        });

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
                    <td style=" border: 1px solid black; border-collapse: collapse;padding:5px;">`+data[i]['OT_DAYS']+`</td>
                    <td style=" border: 1px solid black; border-collapse: collapse;padding:5px;">`+data[i]['LUNCH_ALLOWANCE']+`</td>
                    <td style=" border: 1px solid black; border-collapse: collapse;padding:5px;">`+data[i]['NIGHT_ALLOWANCE']+`</td>
                    <td style=" border: 1px solid black; border-collapse: collapse;padding:5px;">`+data[i]['LOCKING_ALLOWANCE']+`</td>
            </tr>`
            // <td style=" border: 1px solid black; border-collapse: collapse;">`+data[i]['LOCKING_EXPENSE']+`</td>
            $('#overtimeDetail tbody').append(appendData);
        }

    });
})(window.jQuery, window.app);


