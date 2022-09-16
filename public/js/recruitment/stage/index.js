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

        var $table = $('#StageTable');
        var action = `
            <div class="clearfix">
                <a class="btn btn-icon-only green" href="${document.viewLink}/#:REC_STAGE_ID#" style="height:17px;" title="View Detail">
                    <i class="fa fa-search"></i>
                </a>
                
                <a class="btn btn-icon-only yellow" href="${document.editLink}/#:REC_STAGE_ID#" style="height:17px;" title="Edit">
                    <i class="fa fa-edit"></i>
                </a>
                
                
                <a  class="btn btn-icon-only red confirmation" href="${document.deleteLink}/#:REC_STAGE_ID#" style="height:17px;" title="Delete">
                    <i class="fa fa-times"></i>
                </a>
                
            </div>
        `;

        app.initializeKendoGrid($table, [
            {title: "Stage Details",
                columns: [{
                        field: "STAGE_EDESC",
                        title: "English",width: 350
                    },
                    {field: "STAGE_NDESC",
                        title: "Nepali",width: 350
                    }]},
            {field: "ORDER_NO", title: "Order Number",width: 100},
            // {field: "REC_STAGE_ID", title: "Action", template: action,width: 150}


        ],null, null, null, 'Stage List');


        $('#search').on('click', function () {
            var stage  = $('#stage').val();


            app.pullDataById('', {
                'stage' : stage,
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
