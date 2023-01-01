(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#jobResponsibilityTable');
        var editAction = document.acl.ALLOW_UPDATE == 'Y' ? '<a class="btn-edit" title="Edit" href="' + document.editLink + '/#:ID#" style="height:17px;"> <i class="fa fa-edit"></i></a>' : '';
        var deleteAction = document.acl.ALLOW_DELETE == 'Y' ? '<a class="confirmation btn-delete" title="Delete" href="' + document.deleteLink + '/#:ID#" style="height:17px;"><i class="fa fa-trash-o"></i></a>' : '';
        var terminateAction = '<a class="btn btn-icon-only red confirmation" title="Terminate" href="' + document.terminateLink + '/#:ID#" style="height:12px;"><i class="fa fa-times"></i></a>';
        var action = terminateAction + deleteAction;
        app.initializeKendoGrid($table, [
            {field: "EMPLOYEE_CODE", title: "Employee Code", width: 80},
            {field: "FULL_NAME", title: "Employee Name", width: 150},
            {field: "JOB_RES_ENG_NAME", title: "Job Responsibility Name (English)", width: 200},
            {field: "JOB_RES_NEP_NAME", title: "Job Responsibility Name (Nepali)", width: 120},
            {field: "ASSIGNED_BY", title: "Assigned By", width: 150},
            {field: "START_DATE", title: "Start Date", width: 80},
            {field: "END_DATE", title: "End Date", width: 80},
            {field: "ID", title: "Action", width: 120, template: 
            `
            <span class="clearfix">                              
                #if(END_DATE =='Ongoing'){#
                    <a  class="btn btn-icon-only red confirmation" href="${document.terminateLink}/#:ID#" style="height:17px;" title="Cancel">
                        <i class="fa fa-times"></i>
                    </a>
                #}#
                <a class="confirmation btn-delete" href="${document.deleteLink}/#: ID #" style="height:17px; width:13px" title="Delete">
                    <i class="fa fa-trash-o"></i>
                </a>
            </span>`}
        ], null, null, null, 'Job Responsibility Assign List');

        app.searchTable('jobResponsibilityTable', ['FULL_NAME','JOB_RES_ENG_NAME', 'JOB_RES_NEP_NAME']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                'EMPLOYEE_CODE': 'Employee Code',
                'FULL_NAME': 'Employee Name',
                'JOB_RES_ENG_NAME': 'Job Responsibility Name (English)',
                'JOB_RES_NEP_NAME': 'Job Responsibility Name (Nepali)',
                'ASSIGNED_BY':'Assigned By',
                'START_DATE':'Start Date',
                'END_DATE':'End Date',
            }, 'Job Responsibility Assign List');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
                'DESIGNATION_TITLE': 'Name',
                'COMPANY_NAME': 'Company',
                'BASIC_SALARY': 'Basic Salary',
            }, 'Designation List');
        });


        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
    });
})(window.jQuery);
