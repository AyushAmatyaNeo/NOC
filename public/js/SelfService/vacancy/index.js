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
                <a href="${document.viewLink}/#:VACANCY_ID#" class="btn default btn-sm blue">
                <i class="fa fa-eye icon-black"></i> View </a>  
            </div>
        `;

        // <a href="${document.applyLink}/#:VACANCY_ID#" class="btn default btn-sm green">
        //         <i class="fa fa-arrow-right icon-black"></i> Apply</a>

      

        app.initializeKendoGrid($table, [
            {field: "VACANCY_NO", title: "Vacancy No",width: 60},
            {field: "OPENING_ID", title: "Opening No",width: 60},
            {field: "AD_NO", title: "Ad Number",width: 100},
            {field: "VACANCY_TYPE", title: "Vacancy Type",width: 100},
            {field: "DEPARTMENT_ID", title: "Department",width: 140},
            {field: "QUALIFICATION_ID", title: "Qualification",width: 120},
            {field: "POSITION_ID", title: "Position",width: 80},
            {field: "FUNCTIONAL_LEVEL_EDESC", title: "Functional Level",width: 80},
            {field: "VACANCY_RESERVATION_NO", title: "Reservation No",width: 80},    
            {field: "VACANCY_ID", title: "Action", template: action,width: 150},
            


        ],null, null, null, 'Vacancy List');

        $('#search').on('click', function () {
            var openingId        = $('#openingId').val();
            var QualificationId  = $('#qualificationId').val();
            var AdNo             = $('#adnumber').val();
            var DepartmentId     = $('#departmentId').val();
            var positionId       = $('#positionId').val();
            app.pullDataById('', {
                'openingId' : openingId,
                'QualificationId' : QualificationId,
                'AdNo' : AdNo,
                'DepartmentId' : DepartmentId,
                'positionId'  : positionId
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
