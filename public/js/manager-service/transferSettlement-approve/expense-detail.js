(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('#approve').on('click', function () {
            var recommendRemarksId = $("#form-recommendedRemarks");
            var approveRemarksId = $("#form-approvedRemarks");

            if (typeof recommendRemarksId !== "undefined") {
                recommendRemarksId.removeAttr("required");
            }
            if (typeof approveRemarksId !== "undefined") {
                approveRemarksId.removeAttr("required");
            }
            App.blockUI({target: "#hris-page-content"});
        });
        var $print = $('#print');
        $print.on('click', function () {
            app.exportDomToPdf('printableArea', document.urlCss);
        });

        $('#addDocument').on('click', function () {
            $('#documentUploadModel').modal('show');
        });

        $('#uploadCancelBtn').on('click', function () {
            $('#documentUploadModel').modal('hide');
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
                        imageUpload(success.data);
                        app.showMessage("Upload successfull", 'success');
                    }
                    else{
                        app.showMessage("File type error", 'error');
                    }
                });
                this.on("complete", function (file) {
                    this.removeAllFiles(true);
                });
            }
        });
        
        var imageUpload = function (data) {
            window.app.pullDataById(document.pushDCFileLink, {
                'filePath': data.fileName,
                'fileName': data.oldFileName
            }).then(function (success) {
                if (success.success) {
                    $('#fileDetailsTbl').append('<tr>'
                            +'<input type="hidden" name="fileUploadList[]" value="' + success.data.FILE_ID + '"><td>' + success.data.FILE_NAME + '</td>'
                            + '<td><a target="blank" href="' + document.basePath + '/uploads/travel_documents/' + success.data.FILE_IN_DIR_NAME + '"><i class="fa fa-download"></i></a></td>'
                            + ((document.status!='AP')? '<td><button type="button" class="btn btn-danger deleteFile">DELETE</button></td></tr>':'</tr>'));
                
                
                    
                }
            }, function (failure) {
            });
        }
        $('#fileDetailsTbl').on('click', '.deleteFile', function () {  
            var selectedtr = $(this).parent().parent();
            selectedtr.remove();
            var rowCount1 = document.getElementById('fileDetailsTbl').rows.length;

        });
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
