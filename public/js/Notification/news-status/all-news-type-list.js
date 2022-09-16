(function ($) {
    'use strict';
    $(document).ready(function () {
        
        console.log(document.viewLink);
        var $table = $('#table');

//var $attachmentTemplate='<span>#:FILE_NAME#</span>';
        var columns = [
            {field: "NEWS_TITLE", title: "Title", width: 150},
            {field: "NEWS_EDESC", title: "Desc", width: 150},
//            {field: ["FILE_NAME","FILE_PATH"], title: "ATTACHMENT", template: $attachmentTemplate, width: 150},
            {field: ["NEWS_ID"], title: "Action", width: 120, template: `<span><a class="btn-edit" href="` + document.viewLink + `/#: NEWS_ID #" style="height:17px;" title="view detail">
                            <i class="fa fa-search-plus"></i>
                            </a></span>`}
        ];
        var map = {
            'NEWS_TITLE': 'NEWS_TITLE',
            'NEWS_EDESC': 'Desc'
        }
        app.initializeKendoGrid($table, columns, "News List.xlsx");

        app.searchTable($table, ['NEWS_TITLE','NEWS_EDESC']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, map, 'News  List.xlsx');
        });
        
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, map, 'News  List.pdf');
        });

        app.pullDataById("", {}).then(function (response) {
            console.log(response.data);
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
    });
})(window.jQuery);