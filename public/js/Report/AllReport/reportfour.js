
function setTemplate(temp) {
    var returnvalue = '';
    if (temp == null || temp == 'null' || typeof temp =='undefined' ) {
        var checkLeaveVal ='';
    }else{
        var checkLeaveVal = temp.slice(0, 2);
    }
    if (temp == 'PR') {
        returnvalue = 'blue';
    } 
    else if (temp == 'AB') {
        returnvalue = 'red';
    } else if (checkLeaveVal  == "L-" || checkLeaveVal=="HL") {
        returnvalue = 'green';
    } else if (temp == 'DO') {
        returnvalue = 'yellow';
    } else if (temp == 'HD') {
        returnvalue = 'purple';
    } else if (temp == 'WD') {
        returnvalue = 'purple-soft';
    } else if (temp == 'WH') {
        returnvalue = 'yellow-soft';
    }
    return returnvalue;
}

function setAbbr(temp){
    var returnvalue = '';
    if (temp == null || temp == 'null' || typeof temp =='undefined' ) {
        var checkLeaveVal ='';
    }else{
        var checkLeaveVal = temp.slice(0, 2);
    }
    if (temp == 'PR') {
        returnvalue = 'Present';
    } 
    else if (temp == 'AB') {
        returnvalue = 'Absent';
    } else if (checkLeaveVal  == "L-") {
        returnvalue = 'On Leave';
    } else if (checkLeaveVal  == "HL") {
        returnvalue = 'On Half Leave';
    } else if (temp == 'DO') {
        returnvalue = 'Day Off';
    } else if (temp == 'HD') {
        returnvalue = 'Holiday';
    } else if (temp == 'WD') {
        returnvalue = 'Work On Day Off';
    } else if (temp == 'WH') {
        returnvalue = 'Work On Holiday';
    }
    return returnvalue;
}

(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        
        var $employeeList = $('#employeeList');
        var $generateReport = $('#generateReport');
        var $table = $('#reportTable');
        
         var exportVals;
         var columns=generateColsForKendo(32);
         
          app.initializeKendoGrid($table, columns, null, null, 'Employee Wise Attendance Report.xlsx');
        
        
        app.populateSelect($employeeList,document.employeeList, 'EMPLOYEE_ID', 'FULL_NAME','Select','');
        
        
        
        $generateReport.on('click',function(){
           var selectedEmp=$employeeList.val(); 
           var selectedFiscalYear=$('#fiscalYearId').val();
           
           console.log('emp',selectedEmp);
           console.log('fiscal',selectedFiscalYear);
           
           if(selectedEmp==null||selectedEmp==''){
               app.showMessage('select Employee','info','');
               return;
           }
           if(selectedFiscalYear==null||selectedFiscalYear==''){
               app.showMessage('select Fiscal Year','info','');
               return;
           }
           
           app.serverRequest('', {
           employeeId:selectedEmp,
           fiscalYearId:selectedFiscalYear
            }).then(function (response) {
                console.log(response);
                if(response.success==true){
                app.renderKendoGrid($table, response.data);
                }
            }, function (error) {

            });
           
        });
        
        
           function generateColsForKendo(dayCount) {
              exportVals={
            'MONTH_DTL': 'Month',
            'PRESENT': 'Present',
            'ABSENT': 'Absent',
            'LEAVE': 'Leave',
            'DAYOFF': 'Day Off',
            'HOLIDAY': 'Holiday',
            'WORK_DAYOFF': 'Work on Dayoff',
            'WORK_HOLIDAY': 'Work On Holiday',
        };
            var cols = [];
            cols.push({
                field: 'MONTH_DTL',
                title: "Month",
                locked: true,
                template: '<span>#:MONTH_DTL#</span>',
                width: 150
            });
            for (var i = 1; i <= dayCount; i++) {
                var temp = 'D' + i;
                exportVals[temp]=i;
                cols.push({
                    field: temp,
                    title: "" + i,
                     width: 60,
                     template: '<abbr title="#:setAbbr('+temp+')#"><button type="button" class="btn btn-block #:setTemplate('+temp+')#">#:(' + temp + ' == null) ? " " :'+temp+'#</button></abbr>',
//                     template: '<span  class="#: setTemplate(' + temp + ') #">#:(' + temp + ' == null) ? " " :'+temp+'#</span>',
                });
            }
            
            
            cols.push({
                field: 'PRESENT',
                title: "Present",
                template: '<span>#:PRESENT#</span>',
                width: 100
            });
            cols.push({
                field: 'ABSENT',
                title: "Absent",
                template: '<span>#:ABSENT#</span>',
                width: 100
            });
            cols.push({
                field: 'LEAVE',
                title: "Leave",
                template: '<span>#:LEAVE#</span>',
                width: 100
            });
            cols.push({
                field: 'DAYOFF',
                title: "Dayoff",
                template: '<span>#:DAYOFF#</span>',
                width: 100
            });
            cols.push({
                field: 'HOLIDAY',
                title: "Holiday",
                template: '<span>#:HOLIDAY#</span>',
                width: 100
            });
            cols.push({
                field: 'WORK_DAYOFF',
                title: "Work Dayoff",
                template: '<span>#:WORK_DAYOFF#</span>',
                width: 100
            });
            cols.push({
                field: 'WORK_HOLIDAY',
                title: "Work Holiday",
                template: '<span>#:WORK_HOLIDAY#</span>',
                width: 100
            });
            
//            console.log(exportVals);
            return cols;
        }
        
        
         $('#excelExport').on('click', function () {
            app.excelExport($table, exportVals, 'Employee Wise Attendance Report');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, exportVals, 'Employee Wise Attendance Report');
        });

        
        
        
     

    });
})(window.jQuery, window.app);