(function ($) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        
        var $description = $('#description');
        var $toOfficeCode = $('#toOfficeCode');
        var $responseFlag = $('#responseFlag');
        var $remarks = $('#remarks');
        var $departmentId = $('#departmentId');
        var $table = $('#chalaniTable');
        var $processId = $('#processId');
        var $location = $('#toLocationCode');
    


        var viewAction = '<a class="btn-edit" title="View" href="' + document.viewLink + '/#:DISPATCH_DRAFT_ID#" style="height:17px;"> <i class="fa fa-search"></i></a>';
        var editAction = `#if(PROCESS_EDESC !="${document.endProcess}" && PROCESS_EDESC != "Approved"){#<a class="btn-edit" title="Edit" href="` + document.editLink + `/#:DISPATCH_DRAFT_ID#" style="height:17px;"> <i class="fa fa-edit"></i></a>#}#`;
        var deleteAction = `#if(PROCESS_EDESC !="${document.endProcess}" && PROCESS_EDESC != "Approved"){#<a class="confirmation btn-edit" title="Delete" href="` + document.deleteLink + `/#:DISPATCH_DRAFT_ID#" style="height:17px;"><i class="fa fa-trash-o"></i></a>#}#`;
        var forwardAction = `#if(PROCESS_EDESC !="${document.endProcess}" && PROCESS_EDESC != "Approved"){#<a class="btn-edit" title="Forward" href="` + document.forwardLink + `/#:DISPATCH_DRAFT_ID#" style="height:17px;"><i class="fa fa-forward"></i></a>#}#`;
        var acknowledgeAction = `#if(PROCESS_EDESC !="${document.endProcess}"){#<a class="btn-edit" title="Acknowledge" href="` + document.acknowledgeLink + `/#:DISPATCH_DRAFT_ID#" style="height:17px;"><i class="fa fa-check"></i></a>#}#`;

        var action = viewAction + editAction + deleteAction + forwardAction + acknowledgeAction;
        app.initializeKendoGrid($table, [
            { field: "DRAFT_DATE", title: "Draft Date", width: 100 },
            { field: "DRAFT_MITI", title: "Draft Miti", width: 100 },
            // { field: "DOCUMENT_DATE", title: "Document Date", width: 150 },
            // { field: "REMARKS", title: "Remarks", width: 150 },
            { field: "PROCESS_EDESC", title: "Process", width: 120 },
            { field: "DEPARTMENT_NAME", title: "Department", width: 180 },
            { field: "LOCATION_EDESC", title: "Location", width: 180 },
            { field: "RESPONSE_FLAG", title: "Response", width: 85 },
            { field: "DISPATCH_DRAFT_ID", title: "Action", width: 110, template: action },
            { field: "DESCRIPTION", title: "Description", width: 180 },
        ], null, null, null, 'Chalani List');

        app.searchTable('chalaniTable', ['DISPATCH_TEMP_CODE', 'DESCRIPTION', 'DRAFT_DATE', 'REMARKS', 'DEPARTMENT_NAME', 'PROCESS_EDESC','LOCATION_EDESC'], false);
        var map = {
            'DISPATCH_TEMP_CODE': 'Dispatch Temp Code',
            'DESCRIPTION': 'Description',
            'DRAFT_DATE': 'Draft Date',
            'DOCUMENT_DATE': 'Document Date',
            'RESPONSE_FLAG': 'Response Flag',
            'REMARKS': 'Remarks',
            'PROCESS_EDESC': 'Process',
            'DEPARTMENT_NAME': 'Department'
        };

        $('#search').on('click', function () {

            var data = {
                departmentId: $departmentId.val(),
                description: $description.val(),
                toOfficeCode: $toOfficeCode.val(),
                responseFlag: $responseFlag.val(),
                remarks: $remarks.val(),
                processId: $processId.val(),
                toLocationCode:$location.val(),
                
               
            };
            app.serverRequest(document.getSearchResults, data).then(function (success) {
                App.unblockUI("#hris-page-content");
                app.renderKendoGrid($table, success.data);
            }, function (failure) {
                App.unblockUI("#hris-page-content");
            });

        });


        $('#excelExport').on('click', function () {
            app.exportToExcel($table, map, 'Chalani List');
        });

        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, map, 'Chalani List');
        });
        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {
        });

        //======================= Reset button click funtion ==========================
        var $resetSearchData = $('#reset')
            $resetSearchData.on('click', function () {
                $('.form-control').val("");
            });
    });
})(window.jQuery);