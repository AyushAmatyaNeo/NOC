(function ($, app) {
    $(document).ready(function () {
        $('select').select2();
        //=================Defined table from id====================== 
        let $karyaSampadanTable = $('#karyaSampadanTable');
        var $search = $('#search');
        // ===================Initializing Table=======================
        app.initializeKendoGrid($karyaSampadanTable, [
            { field: "FULL_NAME", title: "Employee Name", locked: true, width: 150 },
            { field: "FISCAL_YEAR_NAME", title: "Fiscal Year", locked: true, width: 150 },
            { field: "LETTER_REF_NO", title: "Letter Ref No.", locked: true, width: 150 },
            { field: "DESCRIPTION", title: "Description", width: 320 },
            { field: "REMARKS", title: "Remarks", width: 320 },
        ],null,null,null,'KaryaSampadan');

        
        //============================ Tools Button Click Function : Exporting Table Data into xlsx/pdf =====================
        var fc = {
            "FULL_NAME": "full name",
            "FISCAL_YEAR_NAME": "fiscal year",
            "LETTER_REF_NO": "Letter Reference No.",
            "DESCRIPTION": "description",
            "REMARKS": "remarks"
            
        };
        $('#excelExport').on('click', function () {
            app.excelExport($karyaSampadanTable, fc, 'KaryaSampadan.xlsx');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($karyaSampadanTable, fc, 'KaryaSampadan.pdf');
        });
        //=====================================================================

        // ====================== Search of Kendo search text =================
        app.searchTable('karyaSampadanTable', ['FULL_NAME', 'FISCAL_YEAR_NAME','LETTER_REF_NO','DESCRIPTION','REMARKS'], false);
        //=====================================================================
      
        
        app.serverRequest('', {}).then(function (success) {
            ;
            App.unblockUI("#hris-page-content");
            app.renderKendoGrid($karyaSampadanTable, success.data);
        }, function (failure) {
            App.unblockUI("#hris-page-content");
        });


        // ====================== Filter Search =================
        $search.on('click', function () {
            var data = document.searchManager.getSearchValues();
            app.serverRequest('', data).then(function (response) {
                if (response.success) {
                    app.renderKendoGrid($karyaSampadanTable, response.data);
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });
        });

    }); 
})(window.jQuery, window.app);
