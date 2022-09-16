(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.datePickerWithNepali('fromDate', 'nepaliFromDate');
        app.datePickerWithNepali('toDate', 'nepaliToDate');
       
        var $employeeTable = $('#employeeTable');
        var $search = $('#search');
  
        var columns = [
            {field: "EMPLOYEE_CODE", title: "Code", locked: true, width: 100},
            {field: "FULL_NAME", title: "Full Name", locked: true, width: 120},
            {field: "BIRTH_DATE_AD", title: "Birth Date", width: 100},
            {field: "JOIN_DATE_AD", title: "Join Date", width: 100},
            {field: "FUNCTIONAL_TYPE_EDESC", title: "Functional Type", width: 120},
            {field: "DEPARTMENT_NAME", title: "Department", width: 120},
            {field: "DESIGNATION_TITLE", title: "Designation", width: 120},
            {field: "POSITION_NAME", title: "Position", width: 120}
        ];

        app.initializeKendoGrid($employeeTable, columns, null, null, null, 'Birthday List.xlsx');

        app.searchTable('employeeTable', ['EMPLOYEE_CODE', 'FULL_NAME', 'MOBILE_NO', 'BIRTH_DATE', 'COMPANY_NAME', 'BRANCH_NAME', 'DEPARTMENT_NAME', 'DESIGNATION_TITLE'], false);
  
        var map = {
            'EMPLOYEE_CODE': 'Employee Code',
            'FULL_NAME': 'Employee',
            'BIRTH_DATE_AD': 'Birth Date(AD)',
            'FUNCTIONAL_TYPE_EDESC': 'Functional Type',
            'DEPARTMENT_NAME': 'Department',
            'DESIGNATION_TITLE': 'Designation',
            'POSITION_NAME': 'Position'
        }; 

        var exportColumnParameters = [];
        for(var key in map){
            exportColumnParameters.push({'VALUES' : key, 'COLUMNS' : map[key]});
        }
        var $exparams = $('#exparamsId');
        app.populateSelect($exparams, exportColumnParameters, 'VALUES', 'COLUMNS');
        $exparams.val(Object.keys(map));

        $('#excelExport').on('click', function () {
            var fc = app.filterExportColumns($("#exparamsId").val(), map);
            app.excelExport($employeeTable, fc, 'Birthday Employee List.xlsx');        
        });
        $('#pdfExport').on('click', function () {
            var fc = app.filterExportColumns($("#exparamsId").val(), map);
            app.exportToPDF($employeeTable, fc, 'Birthday Employee List.pdf');
        });
       
        $search.on('click', function () {
            var data = document.searchManager.getSearchValues();
            var fromDate = $('#fromDate').val();
            var toDate = $('#toDate').val();
            data.fromDate = fromDate;
            data.toDate = toDate;
            
            app.serverRequest(document.pullEmployeeListForEmployeeTableLink, data).then(function (response) {
                if (response.success) {
                    $employeeTable.empty();
                    var columnParameters = $exparams.val();
                    var columns_bk = [];
                    for(let i = 0; i < columns.length; i++){
                        for(let j = 0; j < columnParameters.length; j++){
                            if(columns[i].field == columnParameters[j]){
                                columns_bk.push(columns[i]);
                            }
                        }
                    }
                    app.initializeKendoGrid($employeeTable, columns_bk, null, null, null, 'Job Duration Report.xlsx');
                    app.renderKendoGrid($employeeTable, response.data);
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });  
        });
        
//        $("#reset").on("click", function () {
//            $(".form-control").val("");
//            document.searchManager.reset();
//        });
    }); 
})(window.jQuery, window.app);