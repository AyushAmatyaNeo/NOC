(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#onboardtable');

        var actiontemplateConfig = `<a class="btn-edit" title="View" href="${document.viewLink}/#:APPLICATION_ID#" style="height:17px;">
        <i class="fa fa-search-plus"></i></a> `;
        app.initializeKendoGrid($table, [
            {
                title: 'Select All',                
                headerTemplate: "<input type='checkbox' id='header-chb' class='k-checkbox header-checkbox'><label class='k-checkbox-label' for='header-chb'></label>",
                template: "<input type='checkbox' id='#:APPLICATION_ID#'  class='k-checkbox row-checkbox'><label class='k-checkbox-label' for='#:APPLICATION_ID#'></label>",
                width: 50
            },
            {field: "PROFILE_IMG", title: "Photo",  width: 70,
                template: "<div class = 'user-photo' " +
                    "style='background-image: url(#:PROFILE_IMG#);'></div>"
                    },
            {field: "FULL_NAME",title:"Full Name", width: 120,
                template:"#= FIRST_NAME # #= MIDDLE_NAME # #= LAST_NAME #"
            },
            {field: "VACANCY_TYPE", title: "Vacancy Type",  width: 80},
            {field: "APPLICATION_AMOUNT", title: "Amount",  width: 50},
            {field: "AD_NO", title: "Ad No",width: 80},
            {field: "SERVICE_TYPE_ID", title: "Service Type",width: 80},  
            {field: "SERVICE_EVENTS_ID", title: "Service Event",width: 80},
            {field: "POSITION_ID", title: "Designation",width: 100},
            {field: "DEPARTMENT_ID", title: "Department",width: 120},
            {field: "STAGE_ID", title: "Stage",width: 80},        
            {field: "APPLICATION_ID", title: "Action", width: 60,  template: actiontemplateConfig}
        ], null, null, null, 'Department List');

        app.searchTable('onboardtable', ['DEPARTMENT_NAME', 'COMPANY_NAME', 'BRANCH_NAME']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                'DEPARTMENT_NAME': 'Name',
                'COMPANY_NAME': 'Company',
                'BRANCH_NAME': 'Branch',
            }, 'Department List');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
                'DEPARTMENT_NAME': 'Name',
                'COMPANY_NAME': 'Company',
                'BRANCH_NAME': 'Branch',
            }, 'Department List');
        });


        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });


         // Select Option Show 
         var selectItems = {};
         var $bulkBtnContainer = $('#acceptRejectDivOnboard');
         var $bulkBtns = $(".btnApproveOnboard");
         var ids = [];
         $table.on('click', '.k-checkbox', function () {
            
             var checked = this.checked;
             var row = $(this).closest("tr");
             var grid = $table.data("kendoGrid");
             var dataItem = grid.dataItem(row);

             if (selectItems[dataItem['APPLICATION_ID']] === undefined) {
                 selectItems[dataItem['APPLICATION_ID']] = {'checked': checked};
             } else {
                 selectItems[dataItem['APPLICATION_ID']]['checked'] = checked;
             }
             if (checked) {
                 row.addClass("k-state-selected");
                 $bulkBtnContainer.show();

             } else {
                 var atleastOne = false;
             }
          
         });
         $(".btnApproveOnboard").on('click', function () {

            $.each( selectItems, function( key, value ) {
                if (value.checked == true) {
                    ids.push(key);
                } 
            });
            var stids = ids.toString();
            if (ids != null ) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: document.onboardEmployee,
               
                data: {
                    id : ids,
                },
                success: function(data) {
                   window.location.reload(true);
                }
            });
            }
         });

    });
})(window.jQuery);