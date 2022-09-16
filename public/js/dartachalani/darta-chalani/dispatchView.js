(function ($, app) {
    'use strict';
    $(document).ready(function () {
        
        var myDropzone;
        Dropzone.autoDiscover = false;
        
        myDropzone = new Dropzone("div#dropZoneContainer", {
            url: document.uploadUrl,
            autoProcessQueue: false,
            maxFiles: 1,
            addRemoveLinks: true,
            init: function () {
                this.on("success", function (file, success) {
                    if (success.success) {
                        console.log(success.data);
                        imageUpload(success.data);
                    }
                });
                this.on("complete", function (file) {
                    this.removeAllFiles(true);
                });
            }
        });

        window.app.serverRequest(document.pullFilebyId, {
            'id': document.id
        }).then(function (success) {
            // console.log(success.data[0]);
            // debugger;
            if (success.success) {
                $('#fileDetailsTbl').append('<tr>'
                            +'<input type="hidden" name="fileUploadList[]" value="1"><td>' + success.data[0]['FILE_NAME'] + '</td>'
                            + '<td><a target="blank" href="' + document.basePath + '/uploads/dartachalani_docs/' + success.data[0]['FILE_IN_DIR_NAME']+ '"><i class="fa fa-download"></i></a></td>');
            }
        }, function (failure) { 
            console.log('failed');
        });
    });
    
})(window.jQuery, window.app);


