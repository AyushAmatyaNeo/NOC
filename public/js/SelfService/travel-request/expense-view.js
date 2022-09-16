(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var $print = $('#print');
        $print.on('click', function () {
            app.exportDomToPdf('printableArea', document.urlCss);
        });

        var imageUpload = function (data) {
            window.app.pullDataById(document.pushDCFileLink, {
                'filePath': data.fileName,
                'fileName': data.oldFileName
            }).then(function (success) {
                if (success.success) {
                    $('#fileDetailsTbl').append('<tr>'
                            +'<input type="hidden" name="fileUploadList[]" value="' + success.data.FILE_ID + '"><td>' + success.data.FILE_NAME + '</td>'
                            + '<td><a target="blank" href="' + document.basePath + '/uploads/travel_documents/' + success.data.FILE_IN_DIR_NAME + '"><i class="fa fa-download"></i></a></td>');
                
                
                    
                }
            }, function (failure) {
            });
        }

        window.app.serverRequest(document.pullFilebyId, {
            'id': document.id
        }).then(function (success) {
            if (success.success) {
                var i;
                for (i = 0; i < success.data.length; i++) {
                    imageUpload({fileName: success.data[i]['FILE_IN_DIR_NAME'], oldFileName: success.data[i]['FILE_NAME']});
                }
                
            }
        }, function (failure) { 
            console.log('failed');
        });
    });
})(window.jQuery, window.app);
