(function ($) {
    'use strict';

    $(document).ready(function () {
        console.log(document.setup);
        $("#assetSetupTable").kendoGrid({
            excel: {
                fileName: "AssetSetupList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.setup,
                page: 1,
            },
            height: 450,
            scrollable: true,
            sortable: true,
            filterable: true,
            pageable: true,
            rowTemplate: kendo.template($("#rowTemplate").html()),
            columns: [
                {field: "ASSET_EDESC", title: "Asset", width: 120},
                {field: "ASSET_GROUP_EDESC", title: "Asset Group", width: 140},
                {field: "BRAND_NAME", title: "Brand", width: 130},
                {field: "MODEL_NO", title: "Model No ", width: 140},
                {field: "QUANTITY", title: "Quantity ", width: 140},
                {field: "QUANTITY_BALANCE", title: " Stock Qty ", width: 140},
                {title: "Action", width: 140}
            ],
        });
        $("#export").click(function (e) {
            var grid = $("#assetSetupTable").data("kendoGrid");
            grid.saveAsExcel();
        });


        //saerch in kendo grid
        
        app.searchTable('assetSetupTable',['ASSET_EDESC','ASSET_GROUP_EDESC','BRAND_NAME','MODEL_NO','QUANTITY']);
        
        app.pdfExport(
                'assetSetupTable',
                {
                    'ASSET_EDESC': 'Asset',
                    'ASSET_GROUP_EDESC': 'Asset Group',
                    'BRAND_NAME': 'Brand Name',
                    'MODEL_NO': 'Model No',
                    'QUANTITY': 'Quantity',
                    'QUANTITY_BALANCE': 'Stock Quantity'
                });

//        $("#kendoSearchField").keyup(function () {
//            var val = $('#kendoSearchField').val();
//            console.log(val);
//            $("#assetSetupTable").data("kendoGrid").dataSource.filter({
//                logic: "or",
//                filters: [
//                    {
//                        field: "ASSET_EDESC",
//                        operator: "contains",
//                        value: val
//                    },
//                    {
//                        field: "ASSET_GROUP_EDESC",
//                        operator: "contains",
//                        value: val
//                    },
//                    {
//                        field: "BRAND_NAME",
//                        operator: "contains",
//                        value: val
//                    },
//                    {
//                        field: "MODEL_NO",
//                        operator: "contains",
//                        value: val
//                    },
//                    {
//                        field: "QUANTITY",
//                        operator: "contains",
//                        value: val
//                    },
//                ]
//            });
//        });


    });

})(window.jQuery);