(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#assetGroupTable');
        var editAction = document.acl.ALLOW_UPDATE == 'Y' ? '<a class="btn-edit" title="Edit" href="' + document.editLink + '/#:ASSET_GROUP_ID#" style="height:17px;"> <i class="fa fa-edit"></i></a>' : '';
        var deleteAction = document.acl.ALLOW_DELETE == 'Y' ? '<a class="confirmation btn-delete" title="Delete" href="' + document.deleteLink + '/#:ASSET_GROUP_ID#" style="height:17px;"><i class="fa fa-trash-o"></i></a>' : '';
        var action = editAction + deleteAction;
        app.initializeKendoGrid($table, [
            {field: "ASSET_GROUP_EDESC", title: "Asset Group", width: 400},
            {field: "ASSET_GROUP_ID", title: "Action", width: 120, template: action}
        ], null, null, null, 'AssetGroupList');

        app.searchTable('assetGroupTable', ['ASSET_GROUP_EDESC']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                'ASSET_GROUP_EDESC': 'Asset Group'
            }, 'AssetGroupList');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
                'ASSET_GROUP_EDESC': 'Asset Group'
            }, 'AssetGroupList');
        });


        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
    });
})(window.jQuery);