(function ($) {
    'use strict';
    $(document).ready(function () {
        console.log(document.headings);
        $("#defaultRatingTable").kendoGrid({
            excel: {
                fileName: "AppraisalDefaultRatingList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.defaultRating,
                page: 1,
            },
            height: 450,
            scrollable: true,
            sortable: true,
            filterable: true,
            pageable: true,
            rowTemplate: kendo.template($("#rowTemplate").html()),
            columns: [
                {field: "DEFAULT_VALUE", title: "Default Rating",width:120},
                {field: "MIN_VALUE", title: "Min. Value",width:120},
                {field: "MAX_VALUE", title: "Max. Value",width:100},
                {field: "APPRAISAL_TYPE_EDESC", title: "Appraisal Type",width:100},
                {field: "DESIGNATION_LIST", title: "Designation List",width:100},
                {title: "Action",width:100}
            ],
        });
        
        app.searchTable('defaultRating',['DEFAULT_VALUE','MIN_VALUE','MAX_VALUE','APPRAISAL_TYPE_EDESC']);
        
        app.pdfExport(
                'defaultRating',
                {
                    'DEFAULT_VALUE': 'Default Rating',
                    'MIN_VALUE': 'Min. Value',
                    'MAX_VALUE': 'Max. Value',
                    'APPRAISAL_TYPE_EDESC': 'Appraisal type',
                });
        
        $("#export").click(function (e) {
            var rows = [{
                    cells: [
                        {value: "Default Rating"},
                        {value: "Min. Value"},
                        {value: "Max. Value"},
                        {value: "Appraial Type"},
                        {value: "Designation List"},
                        {value: "Position List"},
                    ]
                }];
            var dataSource = $("#defaultRatingTable").data("kendoGrid").dataSource;
            var filteredDataSource = new kendo.data.DataSource({
                data: dataSource.data(),
                filter: dataSource.filter()
            });

            filteredDataSource.read();
            var data = filteredDataSource.view();

            for (var i = 0; i < data.length; i++) {
                var dataItem = data[i];
                rows.push({
                    cells: [
                        {value: dataItem.DEFAULT_VALUE},
                        {value: dataItem.MIN_VALUE},
                        {value: dataItem.MAX_VALUE},
                        {value: dataItem.APPRAISAL_TYPE_EDESC},
                        {value: (dataItem.DESIGNATION_LIST).join(", ")},
                        {value: (dataItem.POSITION_LIST).join(", ")},
                    ]
                });
            }
            excelExport(rows);
            e.preventDefault();
        });

        function excelExport(rows) {
            var workbook = new kendo.ooxml.Workbook({
                sheets: [
                    {
                        columns: [
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                        ],
                        title: "Appraisal Default Rating List",
                        rows: rows
                    }
                ]
            });
            kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "AppraisalDefaultRatingList.xlsx"});
        }
    });
})(window.jQuery);