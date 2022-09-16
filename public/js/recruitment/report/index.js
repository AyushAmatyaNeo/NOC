(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.datePickerWithNepali('Start_dt', 'nepaliFromDate');
        app.datePickerWithNepali('End_dt', 'nepaliToDate');
        app.populateSelect($('#stageId'), document.Stages , 'REC_STAGE_ID', 'STAGE_EDESC', null,null);
        $("#reset").on("click", function () {
            if (typeof document.ids !== "undefined") {
                $.each(document.ids, function (key, value) {
                    $("#" + key).val(value).change();
                });
            }
        });
        app.populateSelect($('#OpeningNo'), document.openings , 'OPENING_ID', 'OPENING_NO', null,null);
        var $table = $('#table');

        app.initializeKendoGrid($table, [
            {
                title: 'Select All',
                headerTemplate: "<input type='checkbox' id='header-chb' class='k-checkbox header-checkbox'><label class='k-checkbox-label' for='header-chb'></label>",
                template: "<input type='checkbox' id='#:OPENING_ID#'  class='k-checkbox row-checkbox'><label class='k-checkbox-label' for='#:OPENING_ID#'></label>",
                width: 50
            },  
            {field: "OPENING_NO", title: "Opening No.",width: 80},
            {field: "AD_NO", title: "AD No.",width: 80},
            {title: "Date",
                columns: [{
                        field: "START_DATE",
                        title: "Start Date",width: 100
                    },
                    {field: "END_DATE",
                        title: "End Date",width: 100
                    },
                    {field: "EXTENDED_DATE",
                    title: "Ext Date",width: 100
                }]},
            {field: "VACANCY_TYPE", title: "Type",width: 100},
            {field: "VACANCY_RESERVATION_NO", title: "RES.NO.",width: 100},
            {field: "DESIGNATION_TITLE", title: "position",width: 100},
            {field: "STAGE_EDESC", title: "Stage",width: 100},
            // {field: "OPENING_ID", title: "Action", template: 'action',width: 150}

        ],null, null, null, 'Vacancy List');

        $('#search').on('click', function () {
            var Start_dt     = $('#Start_dt').val();
            var End_dt       = $('#End_dt').val();
            var OpeningNo    = $('#OpeningNo').val(); 
            var stageId    = $('#stageId').val();     

            app.pullDataById('', {
                'Start_dt'  : Start_dt,
                'End_dt'    : End_dt,
                'OpeningNo' : OpeningNo,                
                'stageId'   : stageId
            }).then(function (response) {
                if (response.success) {
                    app.renderKendoGrid($table, response.data);
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });

        });

        app.searchTable($table ['OPENING_ID']);
        var exportMap = {                
                'OPENING_ID': 'OpeningId',
                'OPENING_NO' : 'OpeningNo',
                'START_DATE': 'Start_dt',
                'END_DATE': 'End_dt',
                'INSTRUCTION_EDESC': 'Instruction_edesc',
                'INSTRUCTION_NDESC': 'Instruction_ndesc',
                'STATUS' : 'status'
        };
        $('#excelExport').on('click', function () {
            app.excelExport($table, exportMap, 'Opening_List.xlsx');
        });

        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, exportMap, 'Opening_List.pdf');
        });
        
    });
})(window.jQuery, window.app);
