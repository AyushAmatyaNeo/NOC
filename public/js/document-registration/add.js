(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        var $registrationDate = $('#registrationDate');
        var $officeDiv = $('#officeDiv');
        var $DeptDiv = $('#DeptDiv');
        var $officeId = $('#fromOfficeId');
        $('select').select2();
        app.datePickerWithNepali('registrationDate', 'nepaliRegistrationDate');
        app.datePickerWithNepali('receivingLetterReferenceDate', 'nepaliReceivingLetterReferenceDate');
        app.datePickerWithNepali('documentDate', 'nepaliDocumentDate');
        app.datePickerWithNepali('completionDate', 'nepaliCompletionDate');
        app.getServerDate().then(function (response) {
            $registrationDate.val(response.data.serverDate);
            $('#nepaliRegistrationDate').val(nepaliDatePickerExt.fromEnglishToNepali(response.data.serverDate));
            
        });

        $(document).on("change", '#registrationDate', function(){
            var selectedDate = $(this).val();
            selectedDate = Date.parse(selectedDate);
            var date = Date.parse(document.date);
            var diff = date - selectedDate;
            if(diff < -1){
                $(this).val('');
                app.showMessage('Cannot input future date', 'error');
            }
        });

        $(document).on("change", '#receivingLetterReferenceDate', function(){
            var selectedDate = $(this).val();
            selectedDate = Date.parse(selectedDate);
            var date = Date.parse(document.date);
            var diff = date - selectedDate;
            if(diff < -1){
                $(this).val('');
                app.showMessage('Cannot input future date', 'error');
            }
        });

        $(document).on("change", '#documentDate', function(){
            var selectedDate = $(this).val();
            selectedDate = Date.parse(selectedDate);
            var date = Date.parse(document.date);
            var diff = date - selectedDate;
            if(diff < -1){
                $(this).val('');
                app.showMessage('Cannot input future date', 'error');
            }
        });

        // $("#completionDate").hide(); 
        
        // $("#completionDate").change(function(){
        //     var completionDate = $("#completionDate").val();
        //     if(responseFlag == 'N'){ 
        //         $("#completionDate").hide().prop('required',false); 
        //         $("#completionDate").prop('required',false);
        //     } else{
        //              $("#completionDate").show(); $("#completionDate").attr("required", "required");}
        // });

        
        $("#completionDt").hide(); 
        
        $("#responseFlag").change(function(){
            var responseFlag = $("#responseFlag").val();
            if(responseFlag == 'N'){ 
                $("#completionDt").hide().prop('required',false); 
                $("#completionDate").prop('required',false);
            } else{
                     $("#completionDt").show(); $("#completionDate").attr("required", "required");}
        });

        
        
        $("#sbFisYr").hide(); 
        
        $("#choiceFlag").change(function(){
            var choiceFlag = $("#choiceFlag").val();
            if(choiceFlag == 'N'){ 
                $("#sbFisYr").hide().prop('required',false); 
                $("#sbFiscalYear").prop('required',false);
            } else{
                     $("#sbFisYr").show(); $("#sbFiscalYear").attr("required", "required");}
        });

        $("#employeee").hide(); 
        
        $("#choiceFlag").change(function(){
            var choiceFlag = $("#choiceFlag").val();
            if(choiceFlag == 'N'){ 
                $("#employeee").hide().prop('required',false); 
                $("#employee").prop('required',false);
            } else{
                     $("#employeee").show(); $("#employee").attr("required", "required");}
        });

        // ------------------------------------------------------------------------------------
        $("#ksFisYr").hide(); 
        
        $("#choiceFlagKS").change(function(){
            var choiceFlagKS = $("#choiceFlagKS").val();
            if(choiceFlagKS == 'N'){ 
                $("#ksFisYr").hide().prop('required',false); 
                $("#ksFiscalYear").prop('required',false);
            } else{
                     $("#ksFisYr").show(); $("#ksFiscalYear").attr("required", "required");}
        });

        $("#employeeee").hide(); 
        
        $("#choiceFlagKS").change(function(){
            var choiceFlagKS = $("#choiceFlagKS").val();
            if(choiceFlagKS == 'N'){ 
                $("#employeeee").hide().prop('required',false); 
                $("#employee2").prop('required',false);
            } else{
                     $("#employeeee").show(); $("#employee2").attr("required", "required");}
        });

        // ------------------------------------------------------------------------------------
        $("#responseFlagged").show();
        $("#choiceFlagKS").change(function(){
            var choiceFlagKS = $("#choiceFlagKS").val();
            if(choiceFlagKS == 'Y'){ 
                $("#responseFlagged").hide().prop('required',false);
                $('#responseFlag').val('N');
                $("#completionDt").hide().prop('required',false); 
                $("#completionDate").prop('required',false); 
            } else{
                     $("#responseFlagged").show();}
            // console.log($('#responseFlag').val());
        });
        $("#choiceFlag").change(function(){
            var choiceFlag = $("#choiceFlag").val();
            if(choiceFlag == 'Y'){ 
                $("#responseFlagged").hide().prop('required',false);
                $('#responseFlag').val('N');
                $("#completionDt").hide().prop('required',false); 
                $("#completionDate").prop('required',false); 
            } else{
                     $("#responseFlagged").show();}
        });
        // ------------------------------------------------------------------------------------




        // $("#nepaliCompletionDate").hide(); 
        
        // $("#nepaliCompletionDate").change(function(){
        //     var responseFlag = $("#nepaliCompletionDate").val();
        //     if(responseFlag == 'N'){ 
        //         $("#nepaliCompletionDate").hide().prop('required',false); 
        //         $("#completionDate").prop('required',false);
        //     } else{
        //              $("#nepaliCompletionDate").show(); $("#nepaliCompletionDate").attr("required", "required");}
        // });




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
                            +'<input type="hidden" name="fileUploadList[]" value="' + success.data.FILE_ID + '"><td>' + success.data.FILE_IN_DIR_NAME + '</td>'
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
            var selectedtr = $(this).parent().parent();
            selectedtr.remove();
            var rowCount1 = document.getElementById('fileDetailsTbl').rows.length;

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
                if(response.data == 'HO'){
                    $DeptDiv.show();
                }else{
                    $DeptDiv.hide();
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
            if(response.data == 'HO'){
                $DeptDiv.show();
            }else{
                $DeptDiv.hide();
            }
        }, function(error){
            console.log(error);
        });
        

    });
    

    
})(window.jQuery, window.app);


