(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        // app.datePickerWithNepali('Start_dt', 'nepaliFromDate');
        // app.datePickerWithNepali('End_dt', 'nepaliToDate');
        $("#reset").on("click", function () {
            if (typeof document.ids !== "undefined") {
                $.each(document.ids, function (key, value) {
                    $("#" + key).val(value).change();
                });
            }
        });
        var $search = $('#search');
        // console.log(document.Stages);
        app.populateSelect($('#OpeningNo'), document.openings , 'OPENING_ID', 'OPENING_NO', null,null);
        app.populateSelect($('#adnumberId'), document.adno , 'VACANCY_ID', 'AD_NO', null,null);
        app.populateSelect($('#appliedadnumber'), document.adno , 'AD_NO', 'AD_NO', null,null);
        app.populateSelect($('#skillsId'), document.Skills , 'SKILL_ID', 'SKILL_NAME', null,null);
        app.populateSelect($('#inclusionId'), document.InclusionList , 'OPTION_ID', 'OPTION_EDESC', null,null);
        app.populateSelect($('#department'), document.DepartmentList , 'DEPARTMENT_ID', 'DEPARTMENT_NAME', null,null);
        app.populateSelect($('#designation'), document.designations , 'DESIGNATION_ID', 'DESIGNATION_TITLE', null,null);
        app.populateSelect($('#stageId'), document.Stages , 'REC_STAGE_ID', 'STAGE_EDESC', null,null);
        app.populateSelect($('#stage'), document.Stages , 'REC_STAGE_ID', 'STAGE_EDESC', null,null);
        app.populateSelect($('#gender'), [{"GENDER_ID":"1","GENDER_NAME":"MALE"},{"GENDER_ID":"2","GENDER_NAME":"FEMALE"}] , 'GENDER_ID', 'GENDER_NAME', null,null); 
        var $table = $('#applicationTable');
        var action = `
            <div class="clearfix">
                <a class="btn btn-icon-only green" href="${document.viewLink}/#:APPLICATION_ID#" style="height:17px;" title="View Detail">
                    <i class="fa fa-search"></i>
                </a>
            </div>
        `;

        // console.log(document.profileurl);
        // New Kendo table
        var actiontemplateConfig = `<a class="btn-edit" title="View" href="${document.viewLink}/#:APPLICATION_ID#" style="height:17px;">
                                    <i class="fa fa-search-plus"></i></a> `;
        app.initializeKendoGrid($table, [
            {field: "VACANCY_AD_NO", title: "AD No",width: 80, locked: true},
            {field: "MULTIPLE_AD_NO", title: "Applied AD No",width: 80, locked: true},
            {field: "REGISTRATION_NO", title: "S.No",  width: 100, locked: true},
            // {field: "PROFILE_IMG", title: "Photo",  width: 100},
            {field: "PROFILE_IMG", title: "Photo",  width: 70, locked: true,
                template: "<div class = 'user-photo' " +
                    "style='background-image: url(#:PROFILE_IMG#);'></div>"
                    },
            {field: "FULL_NAME",title:"Full Name", width: 140, locked: true},
            {field: "NOC",title:"NOC Employeed ?", width: 50, locked: false},
            {field: "VACANCY_TYPE", title: "Vacancy Type",  width: 130,locked: true},
            {field: "APPLICATION_AMOUNT", title: "Amount",  width: 60, locked: true},
            {field: "PAYMENT", title: "Payment",  width: 100, locked: true},
            {field: "PAYMENT_STATUS", title: "Payment Status",  width: 100, locked: true},
            {field: "APPLICATION_ID", title: "Action", width: 60,  template: actiontemplateConfig, locked: true},
            {field: "EMPLOYEE_CODE", title: "Employee Code",  width: 100,lock: true},
            {field: "DESIGNATION_TITLE", title: "Current Designation",  width: 100,lock: true},
            {field: "MOBILE_NO", title: "Mobile No",width: 80, locked: false},  
            {field: "APPLIED_DATE_AD", title: "Applied Date (AD)",width: 80, locked: false},  
            {field: "APPLIED_DATE_BS", title: "Applied Date (BS)",width: 80, locked: false},  
            {field: "SERVICE_TYPE_ID", title: "Service Type",width: 80, locked: false},  
            {field: "SERVICE_EVENTS_ID", title: "Service Event",width: 100, locked: false},
            {field: "POSITION_ID", title: "Designation",width: 100, locked: false},
            {field: "DEPARTMENT_ID", title: "Department",width: 120, locked: false},
            {field: "STAGE_ID", title: "Stage",width: 80, locked: false},        
        ], null, null, null, 'User List');
        app.searchTable($table, ['AD_NO', 'APPLICATION_ID', 'FULL_NAME', 'VACANCY_TYPE', 'APPLICATION_AMOUNT', 'PAYMENT_STATUS', 'SERVICE_TYPE_ID', 'SERVICE_EVENTS_ID', 'POSITION_ID', 'DEPARTMENT_ID', 'STAGE_ID','APPLIED_DATE_AD','APPLIED_DATE_BS'], false);


        $('#search').on('click', function () {
            var OpeningNo  = $('#OpeningNo').val();
            var adnumberId  = $('#adnumberId').val();
            var appliedadnumber = $('#appliedadnumber').val();
            var department  = $('#department').val();
            var designation  = $('#designation').val();
            var gender       = $('#gender').val();
            var stageId     = $('#stage').val();
            var vacancy_type = $('#vacancy_type').val();
            var paymentPaid = $('#paymentPaid').val();
            var paymentVerified = $('#paymentVerified').val();
            document.body.style.cursor='wait';
            // console.log(stageId);
            app.pullDataById('', {
                'OpeningNo' : OpeningNo,
                'adnumberId' : adnumberId,
                'appliedadnumber' : appliedadnumber,
                'department' : department,
                'designation' : designation,
                'gender' : gender,
                'stageId'  : stageId,
                'vacancy_type' : vacancy_type,
                'paymentPaid' : paymentPaid,
                'paymentVerified' : paymentVerified,
            }).then(function (response) {
                if (response.success) {
                    document.body.style.cursor='default';
                    console.log(response);
                    app.renderKendoGrid($table, response.data);
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });

        });

        var exportMap = {   
            'VACANCY_AD_NO': 'AD No.',
            'MULTIPLE_AD_NO': 'Applied AD No.',
            'REGISTRATION_NO': 'S No.',
            'FULL_NAME': 'Full Name',
            'DOB': 'DOB',
            'CITIZENSHIP_NO': 'Citizenship No',
            'CTZ_ISSUE_DATE': 'Citizenship Issued Date',
            'CTZ_ISSUE_DISTRICT_ID' : 'Citizenship Issued District',
            'VACANCY_TYPE': 'Vacancy Type',
            'MOBILE_NO' : 'Mobile Number',
            'APPLICATION_AMOUNT': 'Application Amount',
            'PAYMENT': 'Payment',
            'PAYMENT_STATUS': 'Payment Status',
            'EMPLOYEE_CODE': 'Employee Code',
            'DESIGNATION_TITLE': 'Current Designation',
            'SERVICE_TYPE_ID': 'Service Type',
            'SERVICE_EVENTS_ID': 'Service Event',
            'POSITION_ID': 'Designation',
            'DEPARTMENT_ID': 'Department',
            'STAGE_ID': 'Stage',
            'APPLIED_DATE_AD': 'Applied Date(AD)',
            'APPLIED_DATE_BS': 'Applied Date(BS)',
            'FATHER_NAME': 'Father Name',
            'GRANDFATHER_NAME': 'Grand Father Name',
            'MOTHER_NAME': 'Mother Name',
        };
        $('#excelExport').on('click', function () {
            app.excelExport($table, exportMap, 'User Application.xlsx');
        });

        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, exportMap, 'User Application.pdf');
        });
        // Select Option Show 
        var selectItems = {};
        var $bulkBtnContainer = $('#acceptRejectDiv');
        var $bulkBtns = $(".btnApproveReject");
        $table.on('click', '.k-checkbox', function () {
            var checked = this.checked;
            var row = $(this).closest("tr");
            var grid = $table.data("kendoGrid");
            var dataItem = grid.dataItem(row);
            if (selectItems[dataItem['APPLICATION_ID']] === undefined) {
                selectItems[dataItem['APPLICATION_ID']] = {'checked': checked, 'employeeId': dataItem['EMPLOYEE_ID']};
            } else {
                selectItems[dataItem['APPLICATION_ID']]['checked'] = checked;
            }
            if (checked) {
                row.addClass("k-state-selected");
                $bulkBtnContainer.show();
            } else {
                var atleastOne = false;
            }
        });
        $bulkBtns.bind("click", function () { 
            var btnId = $(this).attr('id');
            var selectedValues = [];
            var StageId = $("#StageId").val();
            var remarks = $("#remarks").val();
            $bulkBtnContainer.hide();
            for (var i in selectItems) {
                if (selectItems[i].checked) {
                    selectedValues.push({
                        StageId: StageId,
                        remarks : remarks,
                        id: i,
                    });
                }
            }
            app.bulkServerRequest(document.bulkStageIdWS, selectedValues, function () {
                $search.trigger('click');
            }, function (data, error) {
                
            }); 
        });
    });
    
})(window.jQuery, window.app);
