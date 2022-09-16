(function ($) {
    'use strict';
    $(document).ready(function () {

        $("#serviceEventTypeTable").kendoGrid({
            excel: {
                fileName: "ServiceEventTypeList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.serviceEventTypes,
                pageSize: 20
            },
            height: 450,
            scrollable: true,
            sortable: true,
            filterable: true,
            pageable: {
                input: true,
                numeric: false
            },
            rowTemplate: kendo.template($("#rowTemplate").html()),
            columns: [
                {field: "SERVICE_EVENT_TYPE_CODE", title: "Service Event Type Code",width:120},
                {field: "SERVICE_EVENT_TYPE_NAME", title: "Service Event Type Name",width:200},
            ]
        });
        
        app.searchTable('serviceEventTypeTable',['SERVICE_EVENT_TYPE_CODE','SERVICE_EVENT_TYPE_NAME']);
        
        app.pdfExport(
                'serviceEventTypeTable',
                {
                    'SERVICE_EVENT_TYPE_NAME': 'Service Event Type'
                }
        );
        
        $("#export").click(function (e) {
            var grid = $("#serviceEventTypeTable").data("kendoGrid");
            grid.saveAsExcel();
        });

    });
})(window.jQuery, window.app);