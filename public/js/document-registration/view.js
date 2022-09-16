(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var $registrationDate = $('#registrationDate');
        var $recievingLetterRefDate = $('#receivingLetterReferenceDate');
        var $locationDiv = $('#locationDiv');
        var $receivingDepartment = $('#receivingDepartment');
        $locationDiv.hide();
        $('select').select2();
        app.datePickerWithNepali('registrationDate', 'nepaliRegistrationDate');
        app.datePickerWithNepali('receivingLetterReferenceDate', 'nepaliReceivingLetterReferenceDate');
        app.datePickerWithNepali('documentDate', 'nepaliDocumentDate');
        
        app.getServerDate().then(function (response) {
            $registrationDate.val(response.data.serverDate);
            $('#nepaliRegistrationDate').val(nepaliDatePickerExt.fromEnglishToNepali(response.data.serverDate));
            
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
                        console.log(success.data);
                        imageUpload(success.data);
                    }
                });
                this.on("complete", function (file) {
                    this.removeAllFiles(true);
                });
            }
        });
        
        $('#addDocument').on('click', function () {
            
            if(tbItem>0){
                window.alert("You can upload only 1 file");
            }else{
                $('#documentUploadModel').modal('show');
            }
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
        var tbItem=0;
        
        var imageUpload = function (data) {
            window.app.pullDataById(document.pushDCFileLink, {
                'filePath': data.fileName,
                'fileName': data.oldFileName
            }).then(function (success) {
                if (success.success) {
                    
                    tbItem=tbItem+1;
                    $('#fileDetailsTbl').append('<tr>'
                            +'<input type="hidden" name="fileUploadList[]" value="' + success.data.FILE_ID + '"><td>' + success.data.FILE_NAME + '</td>'
                            + '<td><a target="blank" href="' + document.basePath + '/uploads/dc_documents/' + success.data.FILE_IN_DIR_NAME + '"><i class="fa fa-download"></i></a></td>');
                
                
                    
                }
            }, function (failure) {
            });
        }


        var choiceFlag = $("#choiceFlag").val();
        if(choiceFlag == 'N'){ 
            $("#sbFisYr").hide().prop('required',false); 
            $("#sbFiscalYear").prop('required',false);
            $("#employeee").hide().prop('required',false); 
            $("#employee").prop('required',false);
        } else{
                $("#sbFisYr").show(); $("#sbFiscalYear").attr("required", "required");
                $("#employeee").show(); $("#employee").attr("required", "required");
                }
        var choiceFlagKS = $("#choiceFlagKS").val();
        if(choiceFlagKS == 'N'){ 
            $("#ksFisYr").hide().prop('required',false); 
            $("#ksFiscalYear").prop('required',false);
            $("#employeeee").hide().prop('required',false); 
            $("#employee2").prop('required',false);
        } else{
                $("#ksFisYr").show(); $("#ksFiscalYear").attr("required", "required");
                $("#employeeee").show(); $("#employee2").attr("required", "required");
            }
        

        $('#uploadCancelBtn').on('click', function () {
            $('#documentUploadModel').modal('hide');
        });

        $('#fileDetailsTbl').on('click', '.deleteFile', function () {
            tbItem=tbItem-1;
            app.serverRequest(document.deleteFileFromTable, {
                'id': document.id
            });
            var selectedtr = $(this).parent().parent();
            selectedtr.remove();
            var rowCount1 = document.getElementById('fileDetailsTbl').rows.length;

        });

        window.app.serverRequest(document.pullFilebyId, {
            'id': document.id
        }).then(function (success) {
            if (success.success) {
                imageUpload({fileName: success.data['FILE_IN_DIR_NAME'], oldFileName: success.data['FILE_NAME']});
            }
        }, function (failure) { 
            console.log('failed');
        });

        $receivingDepartment.on("change", function(){
            app.serverRequest(document.getDepartmentCode, {
                deartment_id: $receivingDepartment.val()
            }).then(function (response) {
                if(response.data == 'NA'){
                    $locationDiv.show();
                }else{
                    $locationDiv.hide();
                }
            }, function (error) {
                console.log(error);
            });
        });
        var $officeDiv = $('#officeDiv');
        var $officeId = $('#fromOtherOfficeName');
        if($officeId.val()){
            $officeDiv.show();
        }else{
            $officeDiv.hide();
        }
        

    });
    
})(window.jQuery, window.app);


