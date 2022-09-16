(function ($, app) {
    'use strict';
    $(document).ready(function () {
        //DropZone Code
        $('select').select2();
        var $form = $('#RecruitmentVacancy');
        var myDropzone;
        Dropzone.autoDiscover = false;
        myDropzone = new Dropzone("div#dropZoneContainer", {
            url: document.ViewUrl,
            autoProcessQueue: false,
            maxFiles: 1,
            addRemoveLinks: true,
            init: function () {
                this.on("success", function (file, success) {
                    if (success.success) {
                        pullimageUpload(success.data);
                    }
                });
                this.on("complete", function (file) {
                    this.removeAllFiles(true);
                });
            }
        });

        $('#viewOnlyDocument').on('click', function (data) {
            window.app.pullDataById(document.pullVacancyFileLink, {
                'fileid': data.fileId,
                'fileName': data.fileName,
                'fileNameDir': data.fileNameDir
            }).then(function (success) {
                // console.log(success);
                if (success.success) {
                    $('#fileDetailsTbl').append('<tr>'
                        + '<input name="fileUploadList[]" value="' + success.data.fileId + '"><td>' + success.data.fileName + '</td>'
                        + '<td><a target="blank" href="' + document.basePath + '/uploads/noc_documents/' + success.data.fileNameDir + '"><i class="fa fa-download"></i></a></td>');

                } else {
                    $('#fileDetailsTbl').append(`<tr><td><p>No File</p></td></tr>`);
                }
            }, function (failure) {
            });
        });

        var pullimageUpload = function (data) {
            window.app.pullDataById(document.pullVacancyFileLink, {
                'filePath': data['fileName'],
                'fileName': data.oldFileName
            }).then(function (success) {
                if (success.success) {
                    $('#fileDetailsTbl').append('<tr>'
                        + '<input type="hidden" name="fileUploadList[]" value="' + success.data.FILE_ID + '"><td>' + success.data.FILE_NAME + '</td>'
                        + '<td><a target="blank" href="' + document.basePath + '/uploads/noc_documents/' + success.data.FILE_IN_DIR_NAME + '"><i class="fa fa-download"></i></a></td>'
                        + '<td><button type="button" class="btn btn-danger deleteFile">DELETE</button></td></tr>');

                }
            }, function (failure) {
                print_r('No File Attached.');
            });
        }

        // Other Code
        app.startEndDatePickerWithNepali('nepaliStartDate1', 'form-fromDate', 'nepaliEndDate1', 'form-toDate')
        var vacancyId = $('#VacancyId').val();
        window.app.floatingProfile.setDataFromRemote(vacancyId);

        var $print = $('#print');
        $print.on('click', function () {
            app.exportDomToPdf('printableArea', document.urlCss);
        });
    });
})(window.jQuery, window.app);
