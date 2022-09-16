(function ($, app) {
    //    'use strict';
        $(document).ready(function () {
            $('select').select2();    
            // var End_dt = $('#End_dt').val();
            // var nepaliStartDate =  $(document).on('load',)
            $('#Start_dt').on('change',function(){
                // var Start_dt = $('#Start_dt').val();
                app.startEndDatePickerWithNepali('nepaliStartDate', 'Start_dt', 'nepaliEndDate','End_dt');
            });
            $('#End_dt').on('change',function(){
                // var Start_dt = $('#Start_dt').val();
                app.startEndDatePickerWithNepali('nepaliEndDate', 'End_dt', 'nepaliExtendedDate','Extended_dt');
            });
            app.startEndDatePickerWithNepali('nepaliStartDate', 'Start_dt', 'nepaliEndDate','End_dt');
            app.startEndDatePickerWithNepali('nepaliEndDate', 'End_dt', 'nepaliExtendedDate','Extended_dt');
            var $form = $('#RecruitmentVacancy');
            
            var myDropzone;
            Dropzone.autoDiscover = false;
            myDropzone = new Dropzone("div#dropZoneContainer", {
                url: document.EditUrl,
                autoProcessQueue: false,
                maxFiles: 1,
                addRemoveLinks: true,
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
            
            // VIEW Document event
            $('#viewDocument').on('click', function (data) {
                window.app.pullDataById(document.pullVacancyFileLink, {
                    'fileid': data.fileId,
                    'fileName': data.fileName,
                    'fileNameDir': data.fileNameDir
                }).then(function (success) {
                    if (success.success) {
                        $('#fileDetailsTbl').append('<tr>'
                                + '<input name="fileUploadList[]" value="' + success.data.fileId + '"><td>' + success.data.fileName + '</td>'
                                + '<td><a target="blank" href="' + document.basePath + '/uploads/noc_documents/' + success.data.fileNameDir + '"><i class="fa fa-download"></i></a></td>'
                                + '<td><button type="button" class="btn btn-danger deleteFile">DELETE</button></td>'
                                + '<td style="visibility: hidden;">'+success.data.fileNameDir+'</td></tr>');
                        
                    }
                }, function (failure) {
                });
            });

            //Delete file uploads
            $('#fileDetailsTbl').on('click', '.deleteFile', function () {
                if(confirm("Confirm delete?")){
                    var selectedtr = $(this).parent().parent();
                    var name = $(this).closest("tr").find('td:eq(3)').text();
                    // console.log(name);
                    app.pullDataById(document.deleteFileLink, {
                        'name': name
                    }).then(function (success) {
                        app.showMessage('File deleted', 'success');
                        selectedtr.remove();
                    })
                }
            });
            //END delete
    
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

            
            var imageUpload = function (data) {
                window.app.pullDataById(document.updateVacancyFileLink, {
                    'filePath': data.fileName,
                    'fileName': data.oldFileName,
                    'linkId' : data.Vid,
                    'fileNameDir': data.fileNameDir
                }).then(function (success) {
                    if (success.success) {
                        $('#fileDetailsTbl').append('<tr>'
                                + '<input type="hidden" name="fileUploadList[]" value="' + success.data.FILE_ID + '"><td>' + success.data.FILE_NAME + '</td>'
                                + '<td><a target="blank" href="' + document.basePath + '/uploads/noc_documents/' + success.data.FILE_IN_DIR_NAME + '"><i class="fa fa-download"></i></a></td>'
                                + '<td><button type="button" class="btn btn-danger deleteFile">DELETE</button></td>'
                                + '<td style="visibility: hidden;">'+success.data.fileNameDir+'</td></tr>');
                        
                    }
                }, function (failure) {
                });
            }
    
            $('#uploadCancelBtn').on('click', function () {
                $('#documentUploadModel').modal('hide');
            });
    
            $('#fileDetailsTbl').on('click', '.deleteFile', function () {
                var selectedtr = $(this).parent().parent();
                selectedtr.remove();
                var rowCount1 = document.getElementById('fileDetailsTbl').rows.length;
            });
            
        });
    
    })(window.jQuery, window.app);
    
    
    