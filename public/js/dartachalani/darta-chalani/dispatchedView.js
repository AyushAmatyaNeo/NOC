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
                        app.serverRequest(document.pushFileLink, {
                            id: document.id,
                            file: success.data.fileName,
                            fileName: success.data.oldFileName
                        });
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
            window.app.pullDataById(document.pullDispatchFilebyId, {
                'id': document.id
            }).then(function (success) {
                console.log(success);
                if (success.success) {
                    $('#fileDetailsTbl').append('<tr>'
                            + '<input value="' + success.data[0].FILE_NAME + '"><td>' + success.data[0].FILE_NAME + '</td>'
                            + '<td><a target="_blank" href="' + document.basePath + '/uploads/dartachalani_docs/' + success.data[0].FILE_PATH + '"><i class="fa fa-download"></i></a></td>'
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

        window.app.pullDataById(document.pullFilesbyId, {
                'id': document.id
            }).then(function (success) {
                if (success.success) {
                    console.log(success.data);
                    for(let i = 0; i < success.data.length; i++){
                        $('#fileDetailsTbl').append('<tr>'
                            +'<input value="' + success.data[i].FILE_PATH + '"><td>' + success.data[i].FILE_PATH+ '</td>'
                            + '<td><a target="_blank" href="' + document.basePath + '/uploads/dartachalani_docs/' + success.data[i].FILE_PATH + '"><i class="fa fa-download"></i></a></td>');
                    }
                }
            }, function (failure) {
            });
    });

    var $table = $("#table");

    app.initializeKendoGrid($table, [
        {field: "DISPATCH_DRAFT_ID", title: "Dispatch Draft ID", width: 100},
        {field: "DEPARTMENT_NAME", title: "Department", width: 150},
        {field: "PROCESS_EDESC", title: "Process", width: 150},
        {field: "FULL_NAME", title: "Employee", width: 150},
        {field: "DOC_DATE", title: "Document Date", width: 150},
        {field: "FILE_PATH", title: "File", width: 150,
        template: '#if(FILE_PATH!=0){#<a target="_blank" href="../../uploads/dartachalani_docs/#:FILE_PATH#">View</a>#}#'
            }
    ], null, null, null);

    app.serverRequest(document.getHistoryLink, '').then(function (response) {
        if (response.success) {
            console.log(response.data);
            app.renderKendoGrid($table, response.data);
        } else {
            app.showMessage(response.error, 'error');
        }
    }, function (error) {
        app.showMessage(error, 'error');
    });
})(window.jQuery, window.app);
