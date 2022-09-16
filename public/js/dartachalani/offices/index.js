(function ($) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        
        var $officeCode = $('#officeCode');
        var $officeEDESC = $('#officeEDESC');
        var $officeNDESC = $('#officeNDESC');
        
        var $table = $('#officesTable');
        var editAction = '<a class="btn-edit" title="Edit" href="' + document.editLink + '/#:OFFICE_ID#" style="height:17px;"> <i class="fa fa-edit"></i></a>';
        var deleteAction = '<a class="confirmation btn-delete" title="Delete" href="' + document.deleteLink + '/#:OFFICE_ID#" style="height:17px;"><i class="fa fa-trash-o"></i></a>';
        var action = editAction + deleteAction;
        app.initializeKendoGrid($table, [
            { field: "OFFICE_CODE", title: "Office Code", width: 150 },
            { field: "OFFICE_EDESC", title: "Office Name (English)", width: 150 },
            { field: "OFFICE_NDESC", title: "Office Name (Nepali)", width: 150 },
            //{ field: "REMARKS", title: "Remarks", width: 150 },
            { field: "OFFICE_ID", title: "Action", width: 120, template: action }
        ], null, null, null, 'Offices List');

        app.searchTable('officesTable', ['OFFICE_CODE', 'OFFICE_EDESC', 'OFFICE_NDESC'], false);
        var map = {
            'OFFICE_CODE': 'Office Code',
            'OFFICE_EDESC': 'Office Eng. Desc.',
            'OFFICE_NDESC': 'Office Nep. Desc.'
        };

        $('#search').on('click', function () {

            var data = {
                officeCode: $officeCode.val(),
                officeEDESC: $officeEDESC.val(),
                // officeNDESC: $officeNDESC.val()
                
            };
            if(data.officeEDESC == "----"){
                data.officeEDESC = null;
            }

            console.log(data);
            app.serverRequest(document.getSearchResults, data).then(function (success) {
                App.unblockUI("#hris-page-content");
                console.log(success.data);
                app.renderKendoGrid($table, success.data);
            }, function (failure) {
                App.unblockUI("#hris-page-content");
            });

        });

        //officeDesc start
        // $('#officeEDESC').on('change', function(){
        //      var data = {
        //         officeCode: $officeCode.val(),
        //         officeEDESC: $officeEDESC.val(),
        //         // officeNDESC: $officeNDESC.val()
                
        //     };

        //     console.log(data);
        //     app.serverRequest(document.getSearchResults, data).then(function (success) {
        //         App.unblockUI("#hris-page-content");
        //         console.log(success.data);
        //         app.renderKendoGrid($table, success.data);
        //     }, function (failure) {
        //         App.unblockUI("#hris-page-content");
        //     });
        // }); //ends officeDesc

        $('#reset').on('click', function(){
            $('.form-control').val("");

            //populate all values on form reset
            var data = {
                officeCode: $officeCode.val(),
                officeEDESC: $officeEDESC.val(),
                // officeNDESC: $officeNDESC.val()
                
            };

            console.log(data);
            app.serverRequest(document.getSearchResults, data).then(function (success) {
                App.unblockUI("#hris-page-content");
                console.log(success.data);
                app.renderKendoGrid($table, success.data);
            }, function (failure) {
                App.unblockUI("#hris-page-content");
            });

        });


        $('#excelExport').on('click', function () {
            app.exportToExcel($table, map, 'Offices List');
        });

        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, map, 'Offices List');
        });
        
        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {
        });
        
    });
})(window.jQuery);