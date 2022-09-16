(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var $registrationDate = $('#registrationDate');
        $('select').select2();
        var $officeDiv = $('#officeDiv');
        var $officeId = $('#fromOfficeId');
        app.datePickerWithNepali('registrationDate', 'nepaliRegistrationDate');
        app.datePickerWithNepali('receivingLetterReferenceDate', 'nepaliReceivingLetterReferenceDate');
        app.datePickerWithNepali('documentDate', 'nepaliDocumentDate');
        app.datePickerWithNepali('completionDate', 'nepaliCompletionDate');
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
                            + '<td><a target="blank" href="' + document.basePath + '/uploads/dc_documents/' + success.data.FILE_IN_DIR_NAME + '"><i class="fa fa-download"></i></a></td>'
                            + '<td><button type="button" class="btn btn-danger deleteFile">DELETE</button></td></tr>');
                
                
                    
                }
            }, function (failure) {
            });
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

        // $("#completionDt").hide(); 
        var responseFlag = $("#responseFlag").val();
        if(responseFlag == 'N'){ 
            $("#completionDt").hide().prop('required',false); 
            $("#completionDate").prop('required',false);
        } else{
                 $("#completionDt").show(); $("#completionDate").attr("required", "required");}

        
        
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




        $("#responseFlag").change(function(){
            var responseFlag = $("#responseFlag").val();
            if(responseFlag == 'N'){ 
                $("#completionDt").hide().prop('required',false); 
                $("#completionDate").prop('required',false);
            } else{
                     $("#completionDt").show(); $("#completionDate").attr("required", "required");}
        });

        $("#choiceFlag").change(function(){
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
        });

        $("#choiceFlagKS").change(function(){
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

        $officeId.on("change",function(){
            app.serverRequest(document.getOfficeCode,{
                office_id:$officeId.val()
            }). then(function(response){
                if(response.data == 'D01'){
                    $officeDiv.show();
                }else{
                    $officeDiv.hide();
                }
            }, function(error){
                console.log(error);
            });
        });
        app.serverRequest(document.getOfficeCode,{
            office_id:$officeId.val()
        }). then(function(response){
            if(response.data == 'D01'){
                $officeDiv.show();
            }else{
                $officeDiv.hide();
            }
        }, function(error){
            console.log(error);
        });

    });
    
})(window.jQuery, window.app);


