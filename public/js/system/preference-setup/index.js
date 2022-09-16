(function ($) {
    'use strict';
    $(document).ready(function () {
        $("#preferenceTable").kendoGrid({
            excel: {
                fileName: "Preference.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.preferenceSetupList,
                page: 1,
            },
            height: 450,
            scrollable: true,
            sortable: true,
            filterable: true,
            pageable: true,
            rowTemplate: kendo.template($("#rowTemplate").html()),
            columns: [
                {field: "COMPANY_NAME", title: "Company", width: 150},
                {field: "PREFERENCE_NAME", title: "Preference Name", width: 150},
                {field: "PREFERENCE_CONSTRAINT", title: "Preference Constraint", width: 170},
                {field: "CONSTRAINT_VALUE", title: "Constraint Value", width: 140},
                {field: "PREFERENCE_CONDITION", title: "Preference Condition", width: 170},
                {title: "Action", width: 120}
            ],
        });
        
        app.searchTable('preferenceTable',['COMPANY_NAME','PREFERENCE_NAME','PREFERENCE_CONSTRAINT','CONSTRAINT_VALUE','PREFERENCE_CONDITION']);
        
        app.pdfExport(
                'preferenceTable',
                {
                    'COMPANY_NAME': 'Company',
                    'PREFERENCE_NAME': 'Preference',
                    'PREFERENCE_CONSTRAINT': 'Constraint',
                    'CONSTRAINT_VALUE': 'Value',
                    'PREFERENCE_CONDITION': 'Condition'
                }
        );
        
        $("#export").click(function (e) {
            var grid = $("#preferenceTable").data("kendoGrid");
            grid.saveAsExcel();
        });
    });
})(window.jQuery);