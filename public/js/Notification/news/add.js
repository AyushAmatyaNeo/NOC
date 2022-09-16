(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.datePickerWithNepali("newsDate", "nepaliDate");
        app.datePickerWithNepali("newsExpiryDate", "nepaliDateExpiry");


        var myDropzone;
        Dropzone.autoDiscover = false;
        myDropzone = new Dropzone("div#dropZoneContainer", {
                url: document.uploadUrl,
                autoProcessQueue: false,
                maxFiles: 1,
                addRemoveLinks: true,
                dictDefaultMessage: 'Click / Drop files here to upload',
                init: function () {
                this.on("success", function (file, success) { 
                    if (success.success) {
                        imageUpload(success.data);
                    }
                });
                this.on("complete", function (file) {
                    this.removeAllFiles(true);
                });

            }
            });
        
        $('#addDocument').on('click', function () {
            $('#documentUploadModel').modal('show');
        });

 

        $('#uploadSubmitBtn').on('click', function () {
            if (myDropzone.files.length == 0) {
                $('#uploadErr').show();
                return;
            } else {
                $('#uploadErr').hide();
            }
            $('#documentUploadModel').modal('hide');
            myDropzone.processQueue();
        });

 
        $('#uploadCancelBtn').on('click', function () {
            $('#documentUploadModel').modal('hide');
        });



        var imageUpload = function (data) {
            window.app.pullDataById(document.pushNewsFileLink, {
                'newsTypeId': null,
                'filePath': data.fileName,
                'fileName': data.oldFileName
            }).then(function (success) {
                if (success.success) {
                    $('#fileDetailsTbl').append('<tr>'
                    +'<input  type="hidden" name="fileUploadList[]" value="'+success.data.newsFileId+'"><td>' + data.oldFileName + '</td>'
                    +'<td><a href="'+document.basePath+'/uploads/news/'+success.data.filePath+'"><i class="fa fa-download"></i></a></td>'
                    +'<td><button type="button" class="btn btn-danger deleteFile">DELETE</button></td></tr>');
                    
                    
                    
                }
            }, function (failure) {
            });
        }
        
        
        $('#fileDetailsTbl').on('click','.deleteFile', function() {
             var selectedtr = $(this).parent().parent();
             selectedtr.remove();
        });
        

    });
})(window.jQuery, window.app);
