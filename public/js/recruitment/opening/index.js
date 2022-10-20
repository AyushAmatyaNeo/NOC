(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.datePickerWithNepali('Start_dt', 'nepaliFromDate');
        app.datePickerWithNepali('End_dt', 'nepaliToDate');
        $("#reset").on("click", function () {
            if (typeof document.ids !== "undefined") {
                $.each(document.ids, function (key, value) {
                    $("#" + key).val(value).change();
                });
            }
        });
        var $table = $('#OpeningTable');
        // console.log(document.openingList);
        app.populateSelect($('#OpeningId'), document.openingList , 'OPENING_ID', 'OPENING_NO', null,null);
        var action = `
            <div class="clearfix">
                <a class="btn btn-icon-only green" href="${document.viewLink}/#:OPENING_ID#" style="height:17px;" title="View Detail">
                    <i class="fa fa-search"></i>
                </a>
                
                <a class="btn btn-icon-only yellow" href="${document.editLink}/#:OPENING_ID#" style="height:17px;" title="Edit">
                    <i class="fa fa-edit"></i>
                </a>
                
                
                <a  class="btn btn-icon-only red confirmation" href="${document.deleteLink}/#:OPENING_ID#" style="height:17px;" title="Delete">
                    <i class="fa fa-times"></i>
                </a>
                
            </div>
        `;

        app.initializeKendoGrid($table, [
            {field: "OPENING_NO", title: "Opening Number",width: 80},
            {title: "Instruction",
                columns: [{
                        field: "INSTRUCTION_EDESC",
                        title: "English", width: 300
                    },
                    {field: "INSTRUCTION_NDESC",
                        title: "Nepali", width: 300
                    }],headerAttributes: { style: "text-align: center" }},
            
            {field: "VACANCY_TOTAL_NO", title: "Vacancy Size",width: 80},
            {field: "RESERVATION_NO", title: "Reservation Size",width: 80},
            {title: "Date",
                columns: [{
                        field: "START_DATE",
                        title: "Start Date",width: 120
                    },
                    {field: "END_DATE",
                        title: "End Date",width: 120
                    },
                    {field: "EXTENDED_DATE",
                    title: "Extended Date",width: 120
                }],headerAttributes: { style: "text-align: center" }},
            {field: "OPENING_ID", title: "Action", template: action,width: 150}

        ],null, null, null, 'Vacancy List');


        $('#search').on('click', function () {
            // var Start_dt     = $('#Start_dt').val();
            // var End_dt       = $('#End_dt').val();
            var OpeningId    = $('#OpeningId').val();     

            app.pullDataById('', {
                // 'Start_dt'  : Start_dt,
                // 'End_dt'    : End_dt,
                'OpeningId' : OpeningId,                
               
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
