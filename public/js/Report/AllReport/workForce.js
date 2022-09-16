(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#bestShiftTable');
        var editAction = document.acl.ALLOW_UPDATE == 'Y' ? '<a class="btn-edit" title="Edit" href="' + document.editLink + '/#:ID#" style="height:17px;"> <i class="fa fa-edit"></i></a>' : '';
        var deleteAction = document.acl.ALLOW_DELETE == 'Y' ? '<a class="confirmation btn-delete" title="Delete" href="' + document.deleteLink + '/#:ID#" style="height:17px;"><i class="fa fa-trash-o"></i></a>' : '';
        var action = editAction + deleteAction;
        app.initializeKendoGrid($table, [
            {field: "COMPANY", title: "Company", width: 100},
            {field: "BRANCH", title: "Branch", width: 100},
            {field: "DEPARTMENT", title: "Department", width: 100},
            {field: "DESIGNATION", title: "Designation", width: 120, },
            {field: "POSITION", title: "Position", width: 120, },
            {field: "SERVICE", title: "Service Type", width: 120, },
            {field: "TOTAL", title: "Total", width: 120, },
            {field: "OCCUPIED", title: "Occupied", width: 120, },
            {field: "VACENT", title: "Vacent", width: 120, },
            {field: "ID", title: "Action", width: 120, template: action}
        ], null, null, null, 'Shift Group List');

        app.searchTable('bestShiftTable', ['CASE_NAME']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                'CASE_NAME': 'Group Name',
                'START_DATE': 'Start Date',
                'END_DATE': 'End Date'
            }, 'Shift Group List');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
               'CASE_NAME': 'Group Name',
                'START_DATE': 'Start Date',
                'END_DATE': 'End Date'
            }, 'Shift Group List');
        });
        
        var wokForceDummyData= [
	{
            "COMPANY": "IT Nepal",
			"BRANCH": "Head Office",
			"DEPARTMENT": "Dot net",
			"DESIGNATION": "Dveloper",
			"POSITION": "Senior Developer",
			"TOTAL": "2",
			"OCCUPIED": "2",
			"VACENT": "0",
			"ID": "1",  
                        "SERVICE":"PERMANENT",
     }, 
	 {
            "COMPANY": "IT Nepal",
			"BRANCH": "Head Office",
			"DEPARTMENT": "Human Resource",
			"DESIGNATION": "Human Resource Manager",
			"POSITION": "Manager",
			"TOTAL": "1",
			"OCCUPIED": "0",
			"VACENT": "1",
			"ID": "2",
                        "SERVICE":"CONTRACT",
     }, 
	 {
            "COMPANY": "IT Nepal",
			"BRANCH": "Head Office",
			"DEPARTMENT": "PHP",
			"DESIGNATION": "Dveloper",
			"POSITION": "Junior Developer",
			"TOTAL": "5",
			"OCCUPIED": "3",
			"VACENT": "2",
			"ID": "3",
                        "SERVICE":"PERMANENT",
     }, 
	 {
            "COMPANY": "IT Nepal",
			"BRANCH": "Head Office",
			"DEPARTMENT": "Deployment",
			"DESIGNATION": "Deployment Executive",
			"POSITION": "Deployment Executive",
			"TOTAL": "10",
			"OCCUPIED": "6",
			"VACENT": "4",
			"ID": "4",
                        "SERVICE":"PERMANENT",
     }, 
	 
    ];

            app.renderKendoGrid($table, wokForceDummyData);

    });
})(window.jQuery);