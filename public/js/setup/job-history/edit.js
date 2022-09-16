(function ($, app) {
    $(document).ready(function () {
        $('select').select2();
        app.startEndDatePickerWithNepali('nepaliStartDate1', 'startDate', 'nepaliEndDate1', 'endDate');
        app.datePickerWithNepali('eventDate', 'eventDateNepali');

        var $employeeId = $("#employeeId");
        var $serviceEventTypeId = $("#serviceEventTypeId");

        var $toServiceTypeId = $('#toServiceTypeId');
        var $toCompanyId = $('#toCompanyId');
        var $toBranchId = $('#toBranchId');
        var $toDepartmentId = $('#toDepartmentId');
        var $toDesignationId = $('#toDesignationId');
        var $toPositionId = $('#toPositionId');

        $employeeId.parent().css('pointer-events', 'none');
        var showHistory = function (employeeId) {
            app.pullDataById(document.wsGetHistoryList, {employeeId}).then(function (response) {
                // console.log(response);
                if (response.success) {
                    var data = [];
                    var services = response.data;

                    $.each(services, function (key, item) {
                        data.push({
                            time: item['START_DATE_BS'],
                            header: item['SERVICE_EVENT_TYPE_NAME'],
                            body: [{
                                    tag: 'div',
                                    content: `
                                            <table class="table">
                                            <tr><td>Company</td><td>${item['COMPANY_NAME']}</td></tr>
                                            <tr><td>Branch</td><td>${item['BRANCH_NAME']}</td></tr>
                                            <tr><td>Department</td><td>${item['DEPARTMENT_NAME']}</td></tr>
                                            <tr><td>Designation</td><td>${item['DESIGNATION_TITLE']}</td></tr>
                                            <tr><td>Position</td><td>${item['POSITION_NAME']}</td></tr>
                                            <tr><td>Service Type</td><td>${item['SERVICE_TYPE_NAME']}</td></tr>
                                            <tr><td>Salary</td><td>${item['TO_SALARY']}</td></tr>
                                            </table>`
                                }],
                        });
                    });
                    $('#myTimeline').albeTimeline(data, {formatDate:'YYYY-MM-dd'});

                }
            }, function () {

            });
        };
        showHistory($employeeId.val());
        // app.setDropZone($('#fileId'), $('#dropZone'), document.uploadFileLink);   // old code for display image name in service status Update
        app.setDropZoneServiceUpdate($('#fileId'), $('#dropZoneService'), document.uploadFileLink);

        $("#toFunctionalLevelId, #toPositionId").change(function(){
            $("#roles").val("Fetching roles...");
            let q = {};
            q.functionalLevelId = $("#toFunctionalLevelId").val();
            q.positionId = $("#toPositionId").val();

            app.pullDataById(document.getRolesData, q).then(function(data){
                $("#form-roles").val(data.data);
            });
        });

        // Get uploaded file details
        // window.app.pullDataById(document.getFileDetailLink, {
        // }).then(function (response) {    
        //     console.log(response)
        // }, function (failure) {
        //     console.log('No file found!');
        // });
    });
})(window.jQuery, window.app);


