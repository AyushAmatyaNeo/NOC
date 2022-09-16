(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        // app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, true);
        $("#reset").on("click", function () {
            if (typeof document.ids !== "undefined") {
                $.each(document.ids, function (key, value) {
                    $("#" + key).val(value).change();
                });
            }
        });
        app.populateSelect($('#qualificationId'), document.qualificationList , 'ACADEMIC_DEGREE_ID', 'ACADEMIC_DEGREE_NAME', null,null);
        app.populateSelect($('#adnumber'), document.adnumberList , 'VACANCY_ID', 'AD_NO', null,null);
        app.populateSelect($('#departmentId'), document.DepartmentList , 'DEPARTMENT_ID', 'DEPARTMENT_NAME', null,null);
        app.populateSelect($('#positionId'), document.positionList , 'DESIGNATION_ID', 'DESIGNATION_TITLE', null,null);
        app.populateSelect($('#openingId'), document.openingList , 'OPENING_ID', 'OPENING_NO', null,null);
        // console.log(document.adnumberList);
        var $table = $('#VacancyTable');
        var action = `
            <div class="clearfix">
                <a class="btn btn-icon-only green" href="${document.viewLink}/#:VACANCY_ID#" style="height:17px;" title="View Detail">
                    <i class="fa fa-search"></i>
                </a>
                
                <a class="btn btn-icon-only yellow" href="${document.editLink}/#:VACANCY_ID#" style="height:17px;" title="Edit">
                    <i class="fa fa-edit"></i>
                </a>
                
                
                <a  class="btn btn-icon-only red confirmation" href="${document.deleteLink}/#:VACANCY_ID#" style="height:17px;" title="Delete">
                    <i class="fa fa-times"></i>
                </a>
                
            </div>
        `;

        app.initializeKendoGrid($table, [
            {field: "VACANCY_NO", title: "Vacancy No",width: 60},
            {field: "OPENING_ID", title: "Opening No",width: 60},
            {field: "AD_NO", title: "Ad Number",width: 100},
            {field: "VACANCY_TYPE", title: "Vacancy Type",width: 100},
            {field: "DEPARTMENT_ID", title: "Department",width: 140},
            {field: "QUALIFICATION_ID", title: "Qualification",width: 120},
            {field: "POSITION_ID", title: "Designation",width: 80},
            {field: "VACANCY_RESERVATION_NO", title: "Reservation No",width: 80},    
            {field: "VACANCY_ID", title: "Action", template: action,width: 150}


        ],null, null, null, 'Vacancy List');

        $('#search').on('click', function () {
            var openingId        = $('#openingId').val();
            var QualificationId  = $('#qualificationId').val();
            var AdNo             = $('#adnumber').val();
            var DepartmentId     = $('#departmentId').val();
            var positionId       = $('#positionId').val();
            var vacancy_type       = $('#vacancy_type').val();
            console.log(vacancy_type);
            app.pullDataById('', {
                'openingId' : openingId,
                'QualificationId' : QualificationId,
                'AdNo' : AdNo,
                'DepartmentId' : DepartmentId,
                'positionId'  : positionId,
                'vacancy_type' : vacancy_type
            }).then(function (response) {
                if (response.success) {
                    app.renderKendoGrid($table, response.data);
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });

        });


        app.searchTable($table ['VACANCY_ID']);
        var exportMap = {                
                'OPENING_ID': 'OpeningId',
                'VACANCY_ID' : 'VacancyId',
                'VACANCY_EDESC': 'VacancyEdesc',
                'PAYMENT_FLAG': 'PaymentFlag',
                'ETHNICITY_FLAG': 'EthnicityFlag',
                'JOB_SPECIFICATION': 'JobSpecification',
                // 'AGE': 'Age',
                // 'GENDER' : 'Gender',
                'QUALIFICATION': 'Qualification',
                'NO_OF_QUOTA': 'NoOfQuota',
                'STATUS' : 'Status'
        };
        $('#excelExport').on('click', function () {
            app.excelExport($table, exportMap, 'Vacancy_List.xlsx');
        });

        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, exportMap, 'Vacancy_List.pdf');
        });

    });
})(window.jQuery, window.app);
