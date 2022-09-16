(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        // app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, true);
        $("#reset").on("click", function () {
            if (typeof document.ids !== "undefined") {
                $.each(document.ids, function (key, value) {
                    $("#" + key).val(value).change();
                });
            }
        });

        var $table = $('#levelTable');
        var action = `
            <div class="clearfix">
                <a class="btn btn-icon-only green" href="${document.viewLink}/#:VACANCY_LEVEL_ID#" style="height:17px;" title="View Detail">
                    <i class="fa fa-search"></i>
                </a>
                
                <a class="btn btn-icon-only yellow" href="${document.editLink}/#:VACANCY_LEVEL_ID#" style="height:17px;" title="Edit">
                    <i class="fa fa-edit"></i>
                </a>
                
                
                <a  class="btn btn-icon-only red confirmation" href="${document.deleteLink}/#:VACANCY_LEVEL_ID#" style="height:17px;" title="Delete">
                    <i class="fa fa-times"></i>
                </a>
                
            </div>
        `;
        app.initializeKendoGrid($table, [
            {field: "FUNCTIONAL_LEVEL_NO", title: "Level",width: 100},
            {field: "POSITION_ID", title: "Designation",width: 100},
            {title: "Level Amount",
                columns: [{
                        field: "NORMAL_AMOUNT",
                        title: "Normal Amount",width: 100
                    },
                    {field: "LATE_AMOUNT",
                        title: "Late Amount",width: 100
                    },
                    {field: "INCLUSION_AMOUNT",
                    title: "Inclusion Amount",width: 100
                }]},
            {title: "Level Age",
            columns: [{
                    field: "MIN_AGE",
                    title: "Minimum Age",width: 100
                },
                {field: "MAX_AGE",
                    title: "Maximum Age",width: 100
                }]},
                    
            {field: "FUNCTIONAL_LEVEL_ID", title: "Action", template: action,width: 150}


        ],null, null, null, 'Level List');


        $('#search').on('click', function () {
            var vacancylevel  = $('#vacancylevel').val();


            app.pullDataById('', {
                'vacancylevel' : vacancylevel,
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


        app.searchTable($table ['REC_STAGE_ID']);
        var exportMap = {                
                'ORDER_NO': 'OrderNo',
                'STAGE_EDESC' : 'StageEdesc',
                'STAGE_NDESC': 'StageNdesc',
                'PAYMENT_FLAG': 'PaymentFlag',
                'ETHNICITY_FLAG': 'EthnicityFlag',
                'STATUS' : 'Status'
        };
        $('#excelExport').on('click', function () {
            app.excelExport($table, exportMap, 'Stage_List.xlsx');
        });

        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, exportMap, 'Stage_List.pdf');
        });

    });
})(window.jQuery, window.app);
