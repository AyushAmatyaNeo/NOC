(function ($,app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        var $payIdInt = $("#payIdInt");
        var $payIdAmt = $("#payIdAmt");
        var inputFieldId = "form-loanName";
        var formId = "loan-form";
        var tableName =  "HRIS_LOAN_MASTER_SETUP";
        var columnName = "LOAN_NAME";
        app.datePickerWithNepali('validFrom', 'validFromNepali');
        app.datePickerWithNepali('validUpto', 'validUptoNepali');

        var checkColumnName = "LOAN_ID";
        var selfId = $("#loanID").val();
        if (typeof(selfId) == "undefined"){
            selfId=0;
        }
        window.app.checkUniqueConstraints(inputFieldId,formId,tableName,columnName,checkColumnName,selfId, function () {
            App.blockUI({target: "#hris-page-content"});
        });
        window.app.checkUniqueConstraints("form-loanCode",formId,tableName,"LOAN_CODE",checkColumnName,selfId);
        
        if(document.is_rate_flexible != null){
            $('#isRateFlexible option[value="'+document.is_rate_flexible+'"]').prop('selected', true).change();
        }

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
                            id: null,
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
            $('#fileDetailsTbl').append('<tr>'
                    + '<input type="hidden" name="fileUploadList[]" value="' + data.fileName + '"><td>' + data.oldFileName + '</td>'
                    + '<td><a target="_blank" href="' + document.basePath + '/uploads/loan_files/' + data.fileName + '"><i class="fa fa-download"></i></a></td>'
                    + '<td><button type="button" class="btn btn-danger deleteFile">DELETE</button></td></tr>');
        }

        $('#uploadCancelBtn').on('click', function () {
            $('#documentUploadModel').modal('hide');
        });

        $('#fileDetailsTbl').on('click', '.deleteFile', function () {
            var selectedtr = $(this).parent().parent();
            selectedtr.remove();
            var rowCount1 = document.getElementById('fileDetailsTbl').rows.length;
        });

        $('#fileDetailsTbl').on('click', '.deleteFile', function () {
            if(confirm("Confirm delete?")){
                var selectedtr = $(this).parent().parent();
                var name = $(this).closest("tr").find('td:eq(3)').text();
                console.log(name);
                app.pullDataById(document.deleteFileLink, {
                    'name': name
                }).then(function (success) {
                    app.showMessage('File deleted', 'success');
                    selectedtr.remove();
                })
            }
        });

        //pay codes tagging with payroll
        app.populateSelect($payIdInt, document.pay_codes, 'PAY_ID', 'PAY_EDESC');
        app.populateSelect($payIdAmt, document.pay_codes, 'PAY_ID', 'PAY_EDESC');
    });
})(window.jQuery,window.app);

angular.module('hris',[])
    .controller('loanRestrictionController',function($scope,$http){
        $scope.view = function(){
            $scope.salaryRangeFrom = 8000;
        }
    });
