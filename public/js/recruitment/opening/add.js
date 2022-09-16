(function ($,app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        // app.startEndDatePicker('startDate', 'endDate');
        app.datePickerWithNepali('Start_dt', 'nepaliStartDate11');
        app.datePickerWithNepali('End_dt', 'nepaliEndDate12');
        app.datePickerWithNepali('Extended_dt', 'nepaliExtendedDate');

        app.startEndDatePickerWithNepali('nepaliStartDate', 'Start_dt', 'nepaliEndDate','End_dt');
        app.startEndDatePickerWithNepali('nepaliEndDate', 'End_dt', 'nepaliExtendedDate','Extended_dt');
        var inputFieldId = "OpeningNo";
        var formId = "OpeningForm";
        var tableName =  "HRIS_REC_OPENINGS";
        var columnName = "OPENING_NO";
        var checkColumnName = "OPENING_ID";
        var selfId = $("#openingId").val();
        if (typeof(selfId) == "undefined"){
            selfId=0;
        }   
        window.app.checkUniqueConstraints(inputFieldId,formId,tableName,columnName,checkColumnName,selfId);
        //-----------------------------------------------------------------------------------------------------------------------------------------//
        // File Upload
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

        var imageUpload = function (data) {
            window.app.pullDataById(document.pushVacancyFileLink, {
                'filePath': data.fileName,
                'fileName': data.oldFileName
            }).then(function (success) {
                if (success.success) {
                    $('#fileDetailsTbl').append('<tr>'
                            + '<input type="hidden" name="fileUploadList[]" value="' + success.data.FILE_ID + '"><td>' + success.data.FILE_NAME + '</td>'
                            + '<td><a target="blank" href="' + document.basePath + '/uploads/noc_documents/' + success.data.FILE_IN_DIR_NAME + '"><i class="fa fa-download"></i></a></td>'
                            + '<td><button type="button" class="btn btn-danger deleteFile">DELETE</button></td></tr>');
                    
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
})(window.jQuery,window.app);