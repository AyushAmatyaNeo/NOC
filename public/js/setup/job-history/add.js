(function ($, app) {
    $(document).ready(function () {
        $('select').select2();
        app.startEndDatePickerWithNepali('nepaliStartDate1', 'startDate', 'nepaliEndDate1', 'endDate');
        app.datePickerWithNepali('eventDate', 'eventDateNepali');

        var $employeeId = $("#employeeId");
        var $serviceEventTypeId = $("#serviceEventTypeId");

        var $toCompanyId = $('#toCompanyId');
        var $toBranchId = $('#toBranchId');
        var $toDepartmentId = $('#toDepartmentId');
        var $toDesignationId = $('#toDesignationId');
        var $toPositionId = $('#toPositionId');
        var $toServiceTypeId = $('#toServiceTypeId');
        var $toSalary = $("#toSalary");
        var $startDate = $('#startDate');
        var $isRetired = $('#isRetired');
        var $isDisabled = $('#isDisabled');
        var $toFunctionalLevelId = $('#toFunctionalLevelId');
        app.floatingProfile.setDataFromRemote($employeeId.val());

        var getPreviousHistory = function (startDate, employeeId) {
            if (typeof startDate === "undefined" || typeof employeeId === "undefined" || startDate == null || employeeId == null || employeeId == -1) {
                return;
            }

            app.pullDataById(document.wsGetPreviousHistory, {
                employeeId: employeeId,
                startDate: startDate
            }).then(function (response) {
                var data = response.data;
                if (typeof data === "undefined" || data == null) {
                    return;
                }
                $serviceEventTypeId.val(data['SERVICE_EVENT_TYPE_ID']).trigger('change.select2');
                $toCompanyId.val(data.TO_COMPANY_ID).trigger('change.select2');
                $toBranchId.val(data.TO_BRANCH_ID).trigger('change.select2');
                $toDepartmentId.val(data.TO_DEPARTMENT_ID).trigger('change.select2');
                $toDesignationId.val(data.TO_DESIGNATION_ID).trigger('change.select2');
                $toPositionId.val(data.TO_POSITION_ID).trigger('change.select2');
                $toServiceTypeId.val(data.TO_SERVICE_TYPE_ID).trigger('change.select2');
                $toSalary.val(data.TO_SALARY);
                $isRetired.prop("checked", data.RETIRED_FLAG === "Y");
                $isDisabled.prop("checked", data.DISABLED_FLAG === "Y");


            }, function (error) {
                console.log(error)
            });
        };

        var getCurrentDetails = function (employeeId) {
            if (typeof employeeId === "undefined" || employeeId == null || employeeId == -1) {
                return;
            }

            app.pullDataById(document.getCurrentDetails, {
                employeeId: employeeId,
            }).then(function (response) {
                var data = response.data;
                console.log(data);
                if (typeof data === "undefined" || data == null) {
                    return;
                }
                $serviceEventTypeId.val(data['SERVICE_EVENT_TYPE_ID']).trigger('change.select2');
                $toCompanyId.val(data.COMPANY_ID).trigger('change.select2');
                $toBranchId.val(data.BRANCH_ID).trigger('change.select2');
                $toDepartmentId.val(data.DEPARTMENT_ID).trigger('change.select2');
                $toDesignationId.val(data.DESIGNATION_ID).trigger('change.select2');
                $toPositionId.val(data.POSITION_ID).trigger('change.select2');
                $toServiceTypeId.val(data.SERVICE_TYPE_ID).trigger('change.select2');
                $toSalary.val(data.SALARY);
                $toFunctionalLevelId.val(data.FUNCTIONAL_LEVEL_ID).trigger('change.select2');
                $('#toLocationId').val(data.LOCATION_ID).trigger('change.select2');
                $isRetired.prop("checked", data.RETIRED_FLAG === "Y");
                $isDisabled.prop("checked", data.DISABLED_FLAG === "Y");
                $('#toActingPositionId').val(data.ACTING_POSITION_ID).trigger('change.select2');
                $('#toActingFunctionalLevelId').val(data.ACTING_FUNCTIONAL_LEVEL_ID).trigger('change.select2');
                $('#oldGradeSankhya').val(data.GRADE_SANKHYA);
                $('#oldOneDayGradeAmount').val(data.ONE_DAY_GRADE_AMT);
                $('#oldGradeAmount').val(data.GRADE_SANKHYA * data.ONE_DAY_GRADE_AMT);
                $('#newGradeSankhya').val(data.GRADE_SANKHYA);
                $('#newOneDayGradeAmount').val(data.ONE_DAY_GRADE_AMT);
                $('#newGradeAmount').val(data.GRADE_SANKHYA * data.ONE_DAY_GRADE_AMT);
                $('#gradePosition').val(document.getPositions[data.POSITION_ID]);
                $('#gradeServiceType').val(document.getServiceType[data.SERVICE_TYPE_ID]);
                $('#gradeFunctionalLevel').val(document.getFunctionalLevels[data.FUNCTIONAL_LEVEL_ID]);
                $('#gradeSalary').val(data.GRADE_SALARY);
                $('#gradeOneDayAmount').val(data.ONE_DAY_GRADE_AMT);
                $('#gradeCeiling').val(data.GRADE_CEILING);
                // console.log(document.getPositions);
                // console.log(document.getPositions[data.POSITION_ID]);
            }, function (error) {
                console.log(error)
            });
        };


        $serviceEventTypeId.on('change', function(){
            if ($serviceEventTypeId.val() == "11"){
                $('#toCompanyId').prop('required',false);
                $('#toPositionId').prop('required',false);
                $('#toDesignationId').prop('required',false);
                $('#toSalary').prop('required',false);
                $('#toServiceTypeId').prop('required',false);
                $('#toBranchId').prop('required',false);
                $('#toDepartmentId').prop('required',false);
                $('#toFunctionalLevelId').prop('required',false);
                $('#isRetired').prop('required',true);


            }
            else {
                $('#toCompanyId').prop('required',true);
                $('#toPositionId').prop('required',true);
                $('#toDesignationId').prop('required',true);
                $('#toSalary').prop('required',true);
                $('#toServiceTypeId').prop('required',true);
                $('#toBranchId').prop('required',true);
                $('#toDepartmentId').prop('required',true);
                $('#toFunctionalLevelId').prop('required',true);
            }
        });

        $employeeId.on('change', function () {
            var $this = $(this);
            var value = $this.val();
            app.floatingProfile.setDataFromRemote(value);
            // getPreviousHistory($startDate.val(), value);
            getCurrentDetails(value);
            showHistory(value);
        });
        $startDate.on('change', function () {
            var $this = $(this);
            var value = $this.val();
            // getPreviousHistory(value, $employeeId.val());
        });



        var showHistory = function (employeeId) {
            app.pullDataById(document.wsGetHistoryList, {employeeId}).then(function (response) {
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
                                            <tr><td>Service Type</td><td>${item['SERVICE_TYPE_NAME']}</td></tr>`
                                            +(item['TO_SALARY']?`<tr><td>Salary</td><td>${item['TO_SALARY']}</td></tr>
                                            </table>`:`</table>`)
                                }],
                        });
                    });
                    $('#myTimeline').albeTimeline(data, {formatDate:'YYYY-MM-dd'})
                    
                    ;
                    

                }
            }, function () {

            });
        };
        showHistory($employeeId.val());
        app.setDropZone($('#fileId'), $('#dropZone'), document.uploadFileLink);

        $("#toFunctionalLevelId, #toPositionId").change(function(){
            $("#roles").val("Fetching roles...");
            let q = {};
            q.functionalLevelId = $("#toFunctionalLevelId").val();
            q.positionId = $("#toPositionId").val();

            app.pullDataById(document.getRolesData, q).then(function(data){
                $("#form-roles").val(data.data);
            });
        });

        $(document).on('change','#toPositionId, #toServiceTypeId, #toFunctionalLevelId, #toActingPositionId, #toActingFunctionalLevelId', function () {
            var toPositionIdVal = $('#toActingPositionId').val()?$('#toActingPositionId').val():$('#toPositionId').val();
            var toFunctionalLevelIdVal = $('#toActingFunctionalLevelId').val()?$('#toActingFunctionalLevelId').val():$('#toFunctionalLevelId').val();
            var toServiceTypeId = $('#toServiceTypeId').val();
            var oldGradeAmount = $('#oldGradeAmount').val();
            console.log(toPositionIdVal);
            getNewGradeAmount(oldGradeAmount, toServiceTypeId, toFunctionalLevelIdVal, toPositionIdVal);

        });

        var getNewGradeAmount = function (oldGradeAmount, toServiceTypeId, toFunctionalLevelIdVal, toPositionIdVal) {
            if (typeof toServiceTypeId === "undefined" || toServiceTypeId == null || toServiceTypeId == -1 || typeof toFunctionalLevelIdVal === "undefined" || toFunctionalLevelIdVal == null || toFunctionalLevelIdVal == -1 || typeof toPositionIdVal === "undefined" || toPositionIdVal == null || toPositionIdVal == -1 || toPositionIdVal == '' || toServiceTypeId == '' || toFunctionalLevelIdVal == '') {
                $('#newGradeSankhya').val('');
                $('#newOneDayGradeAmount').val('');
                $('#newGradeAmount').val('');
                return;
            }
            app.pullDataById(document.getNewGradeAmount, {
                toPositionIdVal:toPositionIdVal,
                toFunctionalLevelIdVal:toFunctionalLevelIdVal,
                toServiceTypeId:toServiceTypeId,
                oldGradeAmount:oldGradeAmount
            }).then(function (response) {
                if(response.success){
                    var data = response.data;
                    if (typeof data === "undefined" || data == null) {
                        return;
                    }
                    $('#newGradeSankhya').val(data.newGradeSankhya);
                    $('#newOneDayGradeAmount').val(data.newOneDayGradeAmount);
                    $('#newGradeAmount').val(data.newGradeSankhya * data.newOneDayGradeAmount);
                    $('#gradeSalary').val(data.other_data['BASIC_SALARY']);
                    $('#gradeOneDayAmount').val(data.newOneDayGradeAmount);
                    $('#gradeCeiling').val(data.other_data['GRADE_CEILING_NO']);
                    $('#gradePosition').val(document.getPositions[data.other_data['POSITION_ID']]);
                    $('#gradeServiceType').val(document.getServiceType[data.other_data['SERVICE_TYPE_ID']]);
                    $('#gradeFunctionalLevel').val(data.other_data['FUNCTIONAL_LEVEL_ID']);
                }else{
                    $('#newGradeSankhya').val('');
                    $('#newOneDayGradeAmount').val('');
                    $('#newGradeAmount').val('');
                    $('#gradeSalary').val('');
                    $('#gradeOneDayAmount').val('');
                    $('#gradeCeiling').val('');
                    $('#gradePosition').val('');
                    $('#gradeServiceType').val('');
                    $('#gradeFunctionalLevel').val('');
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                console.log(error);
            });
            
        };

    });
})(window.jQuery, window.app);


