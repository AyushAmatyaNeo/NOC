(function ($, app) {
    $(document).ready(function () {
        $('select').select2();
        var monthList = null;
        var $fiscalYear = $('#fiscalYearId');
        var $month = $('#monthId');
        var $employeeId = $('#form-employeeId');
        app.setFiscalMonth($fiscalYear, $month, function (years, months, currentMonth) {
            monthList = months;
        });

        $month.select2();
        //========================================= Action Column Buttion Initializing ============================
        var action = `
        <div class = "clearfix">
        <a class = "btn btn-primary green" href="${document.viewLink}/#:GRATUITY_ID#" style="height:17px;" title="View Detail">
        <i class="fa fa-search"></i></a>
        <a class = "btn btn-primary green" href="${document.recalculateLink}/#:GRATUITY_ID#" style="height:17px;" title="Recalculate">
        <i class="fa fa-refresh"></i></a>
        <a class = "confirmation btn btn-delete red" href="${document.deleteLink}/#:GRATUITY_ID#" style="height:17px;" title="Delete"">
        <i class="fa fa-trash-o"></i></a>`;
        //===========================================================================================================

        //=================Defined table from id====================== 
        let $table = $('#table');
        // ===================Initializing Table=======================
        app.initializeKendoGrid($table, [
            { field: "EMPLOYEE_CODE", title: "Employee Code" },
            { field: "FULL_NAME", title: "Employee Name" },
            { field: "TOTAL_WOD", title: "Total Work On Day Off"},  
            { field: "TOTAL_WOH", title: "Total Work On Holiday" },
            { field:"TOTAL_SUBSTITUTE_LEAVE", title:"Classified Substitute Leave"}
            
            
        ]);

        
        //============================ Tools Button Click Function : Exporting Table Data into xlsx/pdf =====================
        var fc = {
            "REG_DRAFT_ID": "Registration No.",
            "OFFICE_EDESC": "From Office",
            //"FULL_NAME": "Receiver",
            "LETTER_REF_NO": "Letter Reference No.",
            "DEPARTMENT_NAME": "Receiving Department",
            "LOCATION_EDESC": "Receiving Location",
            "PROCESS_EDESC": "Process Description", 
            "RESPONSE_FLAG": "Response",
            "DESCRIPTION": "Description"
        
        };
        $('#excelExport').on('click', function () {
            app.excelExport($table, fc, 'Darta.xlsx');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, fc, 'Darta.pdf');
        });
        //=====================================================================

        // ====================== Search of Kendo search text =================
        app.searchTable('incomingDocumentsTable', ['REG_TEMP_CODE', 'OFFICE_EDESC','LETTER_REF_NO','DEPARTMENT_NAME','LOCATION_EDESC'], false);
        
        //===================================================================
        
        //================== Creating Variable of input data of index.html ===============================
        //=========================================================================

        //================================ Search button Click Function ======================
        var $searchedData = $('#search');
        $searchedData.on('click', function () {
            const searchData = {
                employeeId : $employeeId.val(),
                monthId : $month.val()
            };
            app.serverRequest(document.getAllSubtituteLeaveDataLink, searchData).then(function (success) {
                app.renderKendoGrid($table, success.data);
            }, function (failure) {
                App.unblockUI("#hris-page-content");
            });
        });

        // ==========================Changing Eng-Nep Date Function===============================
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, false);
        //============================= Mapping Data to Incoming Data Table =================
        app.serverRequest(document.getAllIncoming, '').then(function (success) {
            // App.unblockUI("#hris-page-content");
            app.renderKendoGrid($incomingDocumentsTable, success.data);
        }, function (failure) {
            App.unblockUI("#hris-page-content");
        });

        //======================= Reset button click funtion ==========================
        var $resetSearchData = $('#reset')
            $resetSearchData.on('click', function () {
                $('.form-control').val("");
            });
    }); 
})(window.jQuery, window.app);
