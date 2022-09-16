(function ($, app) {
    $(document).ready(function () {
        $('select').select2();
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
        let $incomingDocumentsTable = $('#incomingDocumentsTable');
        // ===================Initializing Table=======================
        app.initializeKendoGrid($incomingDocumentsTable, [
            { field: "EMPLOYEE_CODE", title: "Employee Code" },
            { field: "FULL_NAME", title: "Employee Name" },
            { field: "EXTRA_SERVICE_YR", title: "Extra Service Year"},  
            { field: "TOTAL_AMOUNT", title: "Amount" },
            { field:"GRATUITY_ID", title:"Action", template:action}
            
            
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
            app.excelExport($incomingDocumentsTable, fc, 'Darta.xlsx');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($incomingDocumentsTable, fc, 'Darta.pdf');
        });
        //=====================================================================

        // ====================== Search of Kendo search text =================
        app.searchTable('incomingDocumentsTable', ['REG_TEMP_CODE', 'OFFICE_EDESC','LETTER_REF_NO','DEPARTMENT_NAME','LOCATION_EDESC'], false);
        
        //===================================================================
        
        //================== Creating Variable of input data of index.html ===============================
        var $registrationNumber = $('#registrationNum');
        var $senderOrganization = $('#senderOrg');
        var $letterReferenceNumber = $('#letterReferenceNum');
        var $receivingDepartment = $('#receivingDept');
        var $receiverName = $('#receiverName');
        var $responseFlag = $('#responseFlag');
        var $fromDate = $('#fromDate');
        var $toDate = $('#toDate');
        var $description = $('#desc');
        var $location = $('#toLocationCode');
        var $employeeId = $('#employeeId');
        //=========================================================================

        //================================ Search button Click Function ======================
        var $searchedData = $('#search');
        $searchedData.on('click', function () {
            console.log($employeeId.val());
            const searchData = {
                employeeId : $employeeId.val(),
            };
            app.serverRequest(document.getAllGratuityDataAction, searchData).then(function (success) {
                app.renderKendoGrid($incomingDocumentsTable, success.data);
            }, function (failure) {
                App.unblockUI("#hris-page-content");
            });
        });

        //================================ Importing data into select field =================================
        
        // var orgs = [];
        // for(var key in document.orgs){
        //     orgs.push({'VALUES' : document.orgs[key].SENDER_ORG, 'COLUMNS' : document.orgs[key].SENDER_ORG});
        // }
        // app.populateSelect($senderOrganization, orgs, 'VALUES', 'COLUMNS');

        // var dept = [];
        // for(var key in document.dept){
        //     dept.push({'VALUES' : document.dept[key].DEPARTMENT_NAME, 'COLUMNS' : document.dept[key].DEPARTMENT_NAME});
        // }
        // app.populateSelect($receivingDepartment, dept, 'VALUES', 'COLUMNS');

        // var response = [];
        // for(var key in document.response){
        //     response.push({'VALUES' : document.response[key].RESPONSE_FLAG, 'COLUMNS' : document.response[key].RESPONSE_FLAG});
        // }
        // app.populateSelect($responseFlag, response, 'VALUES', 'COLUMNS');

        //====================================================================================


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
