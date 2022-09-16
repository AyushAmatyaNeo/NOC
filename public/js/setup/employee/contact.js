(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();


        var $employeeTable = $('#employeeTable');
        var $search = $('#search');

        var viewAction = '<a class="btn-edit" title="View" href="' + document.viewLink + '/#:EMPLOYEE_ID#/1" style="height:17px;"><i class="fa fa-search-plus"></i></a> ';
        var editAction = '<a class="btn-edit" title="Edit" href="' + document.editLink + '/#:EMPLOYEE_ID#/1" style="height:17px;"> <i class="fa fa-edit"></i></a>';
        var deleteAction = '<a class="confirmation btn-delete" title="Delete" href="' + document.deleteLink + '/#:EMPLOYEE_ID#" id="bs_#:EMPLOYEE_ID #" style="height:17px;"><i class="fa fa-trash-o"></i></a>';
        var action = viewAction + editAction + deleteAction;
        app.initializeKendoGrid($employeeTable, [
            {field: "EMPLOYEE_CODE", title: "Code", template: '<td>#: (EMPLOYEE_CODE == null) ? ' - ' : EMPLOYEE_CODE #</td>'},
            {field: "FULL_NAME", title: "Full Name"},
            {field: "MOBILE_NO", title: "Mobile No"},
           // {field: "BIRTH_DATE", title: "Birth Date"},
            //{field: "COMPANY_NAME", title: "Company"},
            {field: "BRANCH_NAME", title: "Branch"},
            {field: "DEPARTMENT_NAME", title: "Department"},
            {field: "DESIGNATION_TITLE", title: "Designation"},
             {field:"TELEPHONE_NO",title:"Telephone"},
             {field:"EMAIL_OFFICIAL",title:"Email Official"},
            {field:"EXTENSION_NO",title:"Extention No"},
             
           // {field: "EMPLOYEE_ID", title: "Action", template: action}
        ]);
        app.searchTable('employeeTable', ['EMPLOYEE_CODE', 'FULL_NAME', 'MOBILE_NO','BRANCH_NAME', 'DEPARTMENT_NAME', 'DESIGNATION_TITLE','TELEPHONE_NO','EMAIL_OFFICIAL'], false);

        var map = {
            'EMPLOYEE_CODE': 'Employee Code',
            'FULL_NAME': 'Employee',
            'GENDER_NAME': 'Gender',
            'COMPANY_NAME': 'Company',
            'BRANCH_NAME': 'Branch',
            'DEPARTMENT_NAME': 'Department',
            'DESIGNATION_TITLE': 'Designation',
            'LEVEL_NO': 'Level',
            'POSITION_NAME': 'Position',
            'JOIN_DATE': 'Join Date',
            'BIRTH_DATE': 'Birth Date',
            'COUNTRY_NAME': 'Country',
            'RELIGION_NAME': 'Relgion',
            'BLOOD_GROUP_CODE': 'Blood Group',
            'MOBILE_NO': 'Mobile',
            'TELEPHONE_NO': 'Telephone',
            'SOCIAL_ACTIVITY': 'Social Activity',
            'EXTENSION_NO': 'Extension No',
            'EMAIL_OFFICIAL': 'Email Official',
            'EMAIL_PERSONAL': 'Email Personal',
            'SOCIAL_NETWORK': 'Social Network',
            'ADDR_PERM_HOUSE_NO': 'Permanent House No',
            'ADDR_PERM_WARD_NO': 'Permanent Ward No',
            'ADDR_PERM_STREET_ADDRESS': 'Permanent Street Address',
            'ADDR_PERM_ZONE_NAME': 'Permanenet Zone',
            'ADDR_PERM_DISTRICT_NAME': 'Permanent District',
            'VDC_MUNICIPALITY_NAME': ' Permanent Municipality',
            'ADDR_TEMP_HOUSE_NO': 'Temp House No',
            'ADDR_TEMP_WARD_NO': 'Temp Ward No',
            'ADDR_TEMP_STREET_ADDRESS': 'Temp Street Address',
            'ADDR_TEMP_ZONE_NAME': 'Temp Zone',
            'ADDR_TEMP_DISTRICT_NAME': 'Temp District',
            'VDC_MUNICIPALITY_NAME_TEMP': 'Temp Municipality',
            'EMRG_CONTACT_NAME': 'Emergency Contact Name',
            'EMERG_CONTACT_RELATIONSHIP': 'Emergency Contact Relationship',
            'EMERG_CONTACT_ADDRESS': 'Emergency Contact Address',
            'EMERG_CONTACT_NO': 'Emergency Contact No',
            'FAM_FATHER_NAME': 'Father Name',
            'FAM_FATHER_OCCUPATION': 'Father Occupation',
            'FAM_GRAND_FATHER_NAME': 'Grand Father Name',
            'MARITAL_STATUS': 'Maritual Status',
            'FAM_SPOUSE_NAME': 'Spouse Name',
            'FAM_SPOUSE_OCCUPATION': 'Spouse Occupation',
            'FAM_MOTHER_NAME': 'Mother Name',
            'FAM_GRAND_MOTHER_NAME': 'Grand Mother Name',
            'FAM_SPOUSE_BIRTH_DATE': 'Spouse BirthDate',
            'FAM_SPOUSE_WEDDING_ANNIVERSARY': 'Spouse Wedding Anniversary',
            'ID_CARD_NO': 'ID Card No',
            'ID_LBRF': 'ID Lb Rf.',
            'ID_BAR_CODE': 'Bar Code',
            'ID_PROVIDENT_FUND_NO': 'Provident Fund No',
            'ID_DRIVING_LICENCE_NO': 'Driving Licence No',
            'ID_DRIVING_LICENCE_EXPIRY': 'Driving licence Expiary',
            'ID_DRIVING_LICENCE_TYPE': 'Driving Licence Type',
            'ID_PASSPORT_NO': 'Passport No',
            'ID_CITIZENSHIP_NO': 'Citizenship No',
            'ID_CIT_ISSUE_PLACE_NAME': 'Citizenship Issued Place',
            'ID_THUMB_ID': 'Thumb Id',
            'ID_PAN_NO': 'Pan No',
            'ID_ACCOUNT_NO': 'Account No',
            'ID_RETIREMENT_NO': 'Retirement No',
            'ID_CITIZENSHIP_ISSUE_DATE': 'Citizenship Issue Date',
            'ID_PASSPORT_EXPIRY': 'Passport Expiry',
            'SALARY': 'Salary',
            'SALARY_PF': 'Salary Pf',
            'SERVICE_TYPE_NAME': 'Service Type',
            'SERVICE_EVENT_TYPE_NAME': 'Service Event Type',
        };
        $('#excelExport').on('click', function () {
            app.excelExport($employeeTable, map, 'Employee List.xlsx');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($employeeTable, map, 'Employee List.pdf');
        });

        $search.on('click', function () {
            var data = document.searchManager.getSearchValues();
            app.pullDataById(document.pullEmployeeListForEmployeeTableLink, data).then(function (response) {
                if (response.success) {
                    app.renderKendoGrid($employeeTable, response.data);
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });
        });

    });
})(window.jQuery, window.app);