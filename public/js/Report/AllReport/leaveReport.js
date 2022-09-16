(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        var $fromDate = $('#fromDate');
        var $toDate = $('#toDate');
        var $leaveReportTable = $('#leaveReportTable');
        var $search = $('#search');
        var $customWise = $('#customWise');
        
        
        $customWise.on('change',function(){
            var chagnedValue=$(this).val();
            if(chagnedValue=='EMP'){
                $('#EmpOptionsDiv').show();
            }else{
                $('#EmpOptionsDiv').hide();
            }
        })
        
        

        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, true);
        
        var cols = [];
                    cols.push({field: "NAME", title: "NAME"});
                $.each(document.allLeave, function (index, value) {
                    var tempObj = new Object();
                        tempObj.field = value.LEAVE_TRIM_ENAME;
                        tempObj.title = value.LEAVE_ENAME;
                        tempObj.template = "<span>#: ("+value.LEAVE_TRIM_ENAME+"== null) ? '0' : "+value.LEAVE_TRIM_ENAME+"#</span>";
                    cols.push(tempObj);
                });
        
        app.initializeKendoGrid($leaveReportTable, cols, "Leave Report.xlsx");
        
        app.searchTable('leaveReportTable',['NAME']);
        

        $search.on('click', function () {
            var fromDate = $fromDate.val();
            var toDate = $toDate.val();
            var customWise=$customWise.val();

            
            var data = document.searchManager.getSearchValues();
            data['fromDate'] = fromDate;
            data['toDate'] = toDate;
            data['customWise'] = customWise;
            
            app.pullDataById(document.getLeaveReportWS, data).then(function (response) {
                console.log(response.success);
                if (response.success) {
                    
                    app.renderKendoGrid($leaveReportTable, response.data);
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });
        });

    });
})(window.jQuery, window.app);

