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
        // console.log(document.openings);
        app.populateSelect($('#adnumberId'), document.adno , 'VACANCY_ID', 'AD_NO', null,null);
        app.populateSelect($('#openingId'), document.openings , 'OPENING_ID', 'OPENING_NO', null,null);
        var $search = $('#search');
        var $table = $('#VacancystageTable');
        var action = `
            <div class="clearfix">
                <a class="btn btn-icon-only green" href="${document.viewLink}/#:REC_VACANCY_STAGE_ID#" style="height:17px;" title="View Detail">
                    <i class="fa fa-search"></i>
                </a>
                </div>
                `;
                // <a class="btn btn-icon-only yellow" href="${document.editLink}/#:REC_VACANCY_STAGE_ID#" style="height:17px;" title="Edit">
                //     <i class="fa fa-edit"></i>
                // </a>                
                
                // <a  class="btn btn-icon-only red confirmation" href="${document.deleteLink}/#:REC_VACANCY_STAGE_ID#" style="height:17px;" title="Delete">
                //     <i class="fa fa-times"></i>
                // </a>                

        app.initializeKendoGrid($table, [
            {
                title: 'Select All',
                headerTemplate: "<input type='checkbox' id='header-chb' class='k-checkbox header-checkbox'><label class='k-checkbox-label' for='header-chb'></label>",
                template: "<input type='checkbox' id='#:REC_VACANCY_STAGE_ID#'  class='k-checkbox row-checkbox'><label class='k-checkbox-label' for='#:REC_VACANCY_STAGE_ID#'></label>",
                width: 50,
            },
            {field: "OPENING_NO", title: "Opening Number"},
            {field: "AD_NO", title: "Ad Number"},
            {field: "REC_STAGE_ID", title: "Current Stage"},
            {field: "ORDER_NO", title: "Order Numner"},
            {field: "REC_VACANCY_STAGE_ID", title: "Action", template: action}


        ],null, null, null, 'VacancyStage List');


        $('#search').on('click', function () {

            var adnumberId  = $('#adnumberId').val();
            var openingId  = $('#openingId').val();

            app.pullDataById('', {
                'adnumberId' : adnumberId,
                'openingId'   : openingId
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


        app.searchTable($table ['VACANCY_ID']);
        var exportMap = {                
                'OPENING_ID': 'OpeningId',
                'VACANCY_ID' : 'VacancyId',
                'QUOTA': 'Quota',
                'OPEN_INTERNAL': 'OpenInternal',
                'NORMAL_AMT': 'NormalAmt',
                'LATE_AMT': 'LateAmt',
                'REMARKS' : 'Remarks',
                'STATUS' : 'Status'
        };
        $('#excelExport').on('click', function () {
            app.excelExport($table, exportMap, 'Vacancy_Options.xlsx');
        });

        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, exportMap, 'Vacancy_Options.pdf');
        });
        var selectItems = {};
        var $bulkBtnContainer = $('#acceptRejectDiv');
        var $bulkBtns = $(".btnApproveReject");
        $table.on('click', '.k-checkbox', function () {
            var checked = this.checked;
            var row = $(this).closest("tr");
            var grid = $table.data("kendoGrid");
            var dataItem = grid.dataItem(row);
            if (selectItems[dataItem['REC_VACANCY_STAGE_ID']] === undefined) {
                selectItems[dataItem['REC_VACANCY_STAGE_ID']] = {'checked': checked, 'employeeId': dataItem['EMPLOYEE_ID']};
            } else {
                selectItems[dataItem['REC_VACANCY_STAGE_ID']]['checked'] = checked;
            }
            if (checked) {
                row.addClass("k-state-selected");
                $bulkBtnContainer.show();
            } else {
                var atleastOne = false;
            }
        });
        $bulkBtns.bind("click", function () { 
            var btnId = $(this).attr('id');
            var selectedValues = [];
            var StageId = $("#StageId").val();
            console.log(StageId);
            $bulkBtnContainer.hide();
            for (var i in selectItems) {
                if (selectItems[i].checked) {
                    selectedValues.push({
                        StageId: StageId,
                        id: i,
                    });
                }
            }
            app.bulkServerRequest(document.bulkStageWS, selectedValues, function () {
                $search.trigger('click');
            }, function (data, error) {
                
            }); 
        });
    });
})(window.jQuery, window.app);
