(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.datePickerWithNepali("newsDate", "nepaliDate");
        app.datePickerWithNepali("newsExpiryDate", "nepaliDateExpiry");
        //to change edit values
        $('#companyId').val(document.newsToList['companyId']).trigger('change.select2');
        $('#branchId').val(document.newsToList['branchId']).trigger('change.select2');
        $('#departmentId').val(document.newsToList['departmentId']).trigger('change.select2');
        $('#designationId').val(document.newsToList['designationId']).trigger('change.select2');
        $('#positionId').val(document.newsToList['positionId']).trigger('change.select2');
        $('#serviceTypeId').val(document.newsToList['serviceTypeId']).trigger('change.select2');
        $('#serviceEventTypeId').val(document.newsToList['serviceEventTypeId']).trigger('change.select2');
        $('#employeeType').val(document.newsToList['employeeType']).trigger('change.select2');
        $('#employeeId').val(document.newsToList['employeeId']).trigger('change.select2');
        $('#genderId').val(document.newsToList['genderId']).trigger('change.select2');


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

        var $fileListTable = $('#fileDetailsTbl');

        var fileDetails = function () {
            window.app.pullDataById(document.pullNewsFile, {
            }).then(function (success) {
                $fileListTable.find("tr:not(:first)").remove();
                $.each(success.data, function (index, value) {
                    $fileListTable.append('<tr><input  type="hidden" name="fileUploadList[]" value="' + value.NEWS_FILE_ID + '"><td>' + value.FILE_NAME + '</td>'
                            +'<td><a href="'+document.basePath+'/uploads/news/'+value.FILE_PATH+'"><i class="fa fa-download"></i></a></td>'
                            +'<td><button type="button" class="btn btn-danger deleteFile">DELETE</button></td></tr>');
                });
            }, function (failure) {
            });
        }

        fileDetails();


        $fileListTable.on("click", "td .deleteFile", function () {
             var selectedtr = $(this).parent().parent();
             selectedtr.remove();
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

    });
})(window.jQuery, window.app);