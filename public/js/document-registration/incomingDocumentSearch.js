(function ($, app) {
    $(document).ready(function () {
        $('select').select2();
        //========================================= Action Column Buttion Initializing ============================
        var viewAction = `<a class="btn-delete" title="View" href="${document.viewLink}/#:REG_DRAFT_ID#" style="height:17px;"><i class="fa fa-search-plus"></i></a>`;
        var editAction = `#if(PROCESS_EDESC != "${document.endProcess}"){#
                                <a class="btn-edit" title="Edit" href="${document.editLink}/#:REG_DRAFT_ID#" style="height:17px;"> <i class="fa fa-edit"></i></a>
                            #}# `;
        var deleteAction = `#if(PROCESS_EDESC != "${document.endProcess}"){#
                            <a class="confirmation btn-delete" title="Delete" href="${document.deleteLink}/#:REG_DRAFT_ID#" style="height:17px;"><i class="fa fa-trash-o"></i></a>
                            #}# `;
        var forwardAction = `#if(PROCESS_EDESC != "${document.endProcess}"){#
                            <a class="btn-delete" title="Forward" href="${document.forwardLink}/#:REG_DRAFT_ID#" style="height:17px;"><i class="fa fa-paper-plane"></i></a>
                            #}# `;
        var acknowledgeAction = `#if(PROCESS_EDESC != "${document.endProcess}"){#
                                <a class="btn-delete" title="Acknowledge" href="${document.acknowledgeLink}/#:REG_DRAFT_ID#" style="height:17px;"><i class="fa fa-check"></i></a>
                                #}# `;
        var action = viewAction + editAction + deleteAction + forwardAction + acknowledgeAction;
        //===========================================================================================================

        //=================Defined table from id====================== 
        let $incomingDocumentsTable = $('#incomingDocumentsTable');
        // ===================Initializing Table=======================
        app.initializeKendoGrid($incomingDocumentsTable, [
            { field: "REG_TEMP_CODE", title: "Reg No.", locked: true, width: 180 },
            { field: "OFFICE_EDESC", title: "From Office", locked: true, width: 120 }, // REFERENCED FROM OFFICE_EDESC FROM DC_OFFICES
            // { field: "FULL_NAME", title: "Receiver", locked: true, width: 150 }, // REFERENCED FROM EMPLOYEE TABLE FROM HRIS_EMPLOYEES
            { field: "LETTER_REF_NO", title: "Letter Ref No.", width: 100 },
            { field: "LETTER_REF_DATE", title: "Letter Ref Date", width: 100 },
            { field: "LETTER_REF_MITI", title: "Letter Ref Miti", width: 100 },
            { field: "COMPLETION_DATE", title: "Completion Date", width: 100 },
            { field: "DEPARTMENT_NAME", title: "Receiving Department",  width: 180 }, 
            { field: "LOCATION_EDESC", title: "Receiving Location",  width: 180 },// REFERENCED FROM DEPARTMENT_NAME FROM HRIS_DEPARTMENTS
            { field: "PROCESS_EDESC", title: "Process Description",  width: 100 }, // REFERENCED FROM OFFICE_EDESC FROM DC_PROCESSES
            //{ field: "RESPONSE_FLAG", title: "Response", locked: true, width: 100 },
            {field: "REG_DRAFT_ID", title: "Action", width: 185, template: action},
            { field: "DOCUMENT_DATE", title: "Received Date", width: 150 },
            { field: "DESCRIPTION", title: "Description", width: 150 },
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
        //=========================================================================

        //================================ Search button Click Function ======================
        var $searchedData = $('#search');
        $searchedData.on('click', function () {
            const searchData = {
                registrationNum : $registrationNumber.val(),
                senderOrg : $senderOrganization.val(),
                letterReferenceNum : $letterReferenceNumber.val(),
                receivingDept : $receivingDepartment.val(),
                receiverName : $receiverName.val(),
                responseFlag : $responseFlag.val(),
                fromDate : $fromDate.val(),
                toDate : $toDate.val(),
                desc : $description.val(),
                toLocationCode:$location.val(),
            };
            app.serverRequest(document.getAllIncomingDatabyIdAction, searchData).then(function (success) {
                App.unblockUI("#hris-page-content");
                console.log(success.data);
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
