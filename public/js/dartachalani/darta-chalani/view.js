(function ($, app) {
    'use strict';
    $(document).ready(function () {
        app.datePickerWithNepali('draftDt', 'nepaliStartDate1');
        app.datePickerWithNepali('documentDt', 'nepaliStartDate2');
        app.datePickerWithNepali('dispatchDt', 'nepaliDispatchDt');
        

        var myDropzone;
        Dropzone.autoDiscover = false;
        
        if($("#responseFlag").val() == 'N'){
            $("#letterRefNos").hide(); 
        }
        
        $("#responseFlag").change(function(){
            var responseFlag = $("#responseFlag").val();
            if(responseFlag == 'N'){ 
                $("#letterRefNos").hide().prop('required',false); 
                $("#letterRefNo").prop('required',false);
            } else{
                     $("#letterRefNos").show(); $("#letterRefNo").attr("required", "required");}
        });
        
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
            'id': document.idForFile
        }).then(function (success) {
            if (success.success) {
                if(success.data[0].FILE_PATH == null || success.data[0].FILE_PATH == ""){ return; }
                $('#fileDetailsTbl').append('<tr>'
                            +'<input type="hidden" name="fileUploadList[]" value="1"><td>' + success.data[0]['FILE_PATH'] + '</td>'
                            + '<td><a target="blank" href="' + document.basePath + '/uploads/dartachalani_docs/' + success.data[0]['FILE_PATH']+ '"><i class="fa fa-download"></i></a></td>');

            }
        }, function (failure) { 
            console.log('failed');
        });
        app.serverRequest(document.getOfficeCode,{
            office_id:$('#toOfficeCode').val()
        }). then(function(response){
            if(response.data == 'D01'){
                $('#officeDiv').show();
            }else{
                $('#officeDiv').hide();
            }
        }, function(error){
            console.log(error);
        });

    });
    
})(window.jQuery, window.app);


