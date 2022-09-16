(function ($, app) {
    'use strict';
    
    
     $(document).ready(function () {
         
    app.addDatePicker($('#purchaseDate'));
    app.addDatePicker($('#expiaryDate'));
        $('select').select2();
        
        var inputFieldId = "assetCode";
        var formId = "assetSetup-form";
        var tableName =  "HRIS_ASSET_SETUP";
        var columnName = "ASSET_CODE";
        var checkColumnName = "ASSET_ID";
        var selfId = $("#assetSetupId").val();
        if (typeof(selfId) == "undefined"){
            selfId=0;
        }
        window.app.checkUniqueConstraints(inputFieldId,formId,tableName,columnName,checkColumnName,selfId, function () {
            App.blockUI({target: "#hris-page-content"});
        });       
        window.app.checkUniqueConstraints("assetEdesc",formId,tableName,"ASSET_EDESC",checkColumnName,selfId);
        window.app.checkUniqueConstraints("assetNdesc",formId,tableName,"ASSET_NDESC",checkColumnName,selfId);
        
        
        var $logo = $("#form-logo");
        
        var $myAwesomeDropzone = $('#my-awesome-dropzone');
        var $uploadedImage = $('#uploadedImage');
        
        
        var imageData = {
            fileCode: null,
            fileName: null,
            oldFileName: null
        };
        
        console.log(document.imageData);
        if (typeof document.imageData !== 'undefined' && document.imageData != null) {
//        console.log(document.imageData);
            imageData = document.imageData;
        }
        
        
         var toggle = function () {
            if (imageData.fileName == null) {
                $myAwesomeDropzone.show();
                $uploadedImage.hide();
                $('#uploadFile').text("Upload");
            } else {
                $($uploadedImage.children()[0]).attr('src', document.basePath + "/uploads/" + imageData.fileName);
                $logo.val(imageData.fileCode);
                console.log($logo.val());
                $myAwesomeDropzone.hide();
                $uploadedImage.show();
                $('#uploadFile').text("Edit");
            }
        }
        toggle();
        
         var dropZone = null;
         
         Dropzone.options.myAwesomeDropzone = {
            maxFiles: 1,
            acceptedFiles: 'image/*',
            autoProcessQueue: false,
            addRemoveLinks: true,
            init: function () {
                dropZone = this;
                this.on('success', function (file, success) {
                    imageData = success.data;
                    $logo.val(imageData.fileCode);
                    console.log($logo.val());
                    toggle();
                });
            }
        };
        
        $('#uploadFile').on('click', function () {
            if ($(this).text() == "Edit") {
                imageData.fileName = null;
                toggle();
            } else {
                dropZone.processQueue();
            }
        });


        
        
        
    });

    })(window.jQuery, window.app);