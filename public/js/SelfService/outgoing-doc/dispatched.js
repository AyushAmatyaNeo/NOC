(function ($,app) {
    $(document).ready(function () {
        $('select').select2();
        //================================= Action Button =================
        var viewAction = '<a class="btn-delete" title="View" href="' + document.viewLink + '/#:DISPATCH_ID#" style="height:17px;"><i class="fa fa-search-plus"></i></a>';
        //========================================================

        //======================== Initializing Kendo Table ====================
        var $table = $('#dispatchedTable');
        app.initializeKendoGrid($table, [
            //{ field: "DISPATCH_CODE", title: "Dispatch Code", locked: true, width: 140 },
            { field: "LETTER_NUMBER", title: "Letter No.", locked: true, width: 180 }, // REFERENCED FROM OFFICE_EDESC FROM DC_OFFICES
            //{ field: "FULL_NAME", title: "Receiver", locked: true, width: 150 }, // REFERENCED FROM EMPLOYEE TABLE FROM HRIS_EMPLOYEES
            { field: "DEPARTMENT_NAME", title: "From Department", locked: true, width: 180 },
            { field: "LOCATION_EDESC", title: "From Location", locked: true, width: 180 },
            {field: "letter_ref_no", title: "Letter Ref No.",  width: 160 },
            // REFERENCED FROM DEPARTMENT_NAME FROM HRIS_DEPARTMENTS
            { field: "OFFICE_EDESC", title: "To Office",  width: 120 }, // REFERENCED FROM OFFICE_EDESC FROM DC_PROCESSES
            { field: "REG_ID", title: "Reg No.",  width: 100 },
            {field: "DISPATCH_ID", title: "Action", width: 80, template: viewAction},
            { field: "DISPATCH_DATE", title: "Dispatch Date", width: 120 },
            { field: "NEPALI_DATE", title: "Dispatch Miti", width: 120 },
            { field: "DESCRIPTION", title: "Description", width: 150 }, 
            //{field: "RESPONSE_FLAG", title: "Response", width: 120},
            
        ]);
        //======================================================================
        
        //====================== Search Input Field in Kendo Table ================
        app.searchTable('dispatchedTable', ['DISPATCH_CODE', 'LETTER_NUMBER','DEPARTMENT_NAME','DESCRIPTION', 'OFFICE_EDESC', 'REG_ID', 'LETTER_REF_NO','LOCATION_EDESC'], false);
        //=======================================================================

        //===================== Mapping Data into kendo table ============
        app.serverRequest(document.getTableData, '').then(function (success) {
            // App.unblockUI("#hris-page-content");
            app.renderKendoGrid($table, success.data);
        }, function (failure) {
            App.unblockUI("#hris-page-content");
        });

        //=========================== Search Function =====================
        var $letterNo = $('#letterNo');
        var $fromDept = $('#fromDept');
        var $description = $('#description');
        var $toOfficeCode = $('#toOfficeCode');
        var $location = $('#toLocationCode');
      
        

        var search = $('#search');

        search.on('click',function(){
            const searchData = {
                letterNumber : $letterNo.val(),
                fromDepartment : $fromDept.val(),
                descrip : $description.val(),
                toOfficeCod : $toOfficeCode.val(),
                toLocationCode :$location.val(),
               
            };
            console.log(searchData);
            app.serverRequest(document.getSearchDataById, searchData).then(function (success) {
                App.unblockUI("#hris-page-content");
                app.renderKendoGrid($table, success.data);
            }, function (failure) {
                App.unblockUI("#hris-page-content");
            });

        });
        //============================= Reset Button Function ====================
        var $resetSearched = $('#reset');
        $resetSearched.on('click', function () {
            $('.form-control').val("");
            $('#toLocationCode').val("");
        });
        //========================================================================

        //============= Fetch File into Dispatched Doc.html Function ================
        // var myDropzone;
        // Dropzone.autoDiscover = false;
        
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

        app.serverRequest(document.pullFilebyId, {
            'id': document.id
        }).then(function (success) {
            if (success.success) {
                imageUpload({fileName: success.data['FILE_IN_DIR_NAME'], oldFileName: success.data['FILE_NAME']});
            }
        }, function (failure) { 
            console.log('failed');
        });

    });

        // myDropzone = new Dropzone("div#dropZoneContainer", {
        //     url: document.uploadUrl,
        //     autoProcessQueue: false,
        //     maxFiles: 1,
        //     addRemoveLinks: true,
        //     init: function () {
        //         this.on("success", function (file, success) {
        //             if (success.success) {
        //                 console.log(success.data);
        //                 imageUpload(success.data);
        //             }
        //         });
        //         this.on("complete", function (file) {
        //             this.removeAllFiles(true);
        //         });
        //     }
        // });
        
        // var imageUpload = function (data) {
        //     window.app.pullDataById(document.pushDCFileLink, {
        //         'filePath': data.fileName,
        //         'fileName': data.oldFileName
        //     }).then(function (success) {
        //         if (success.success) {
                    
        //             tbItem=tbItem+1;
        //             $('#fileDetailsTbl').append('<tr>'
        //                     +'<input type="hidden" name="fileUploadList[]" value="' + success.data.FILE_ID + '"><td>' + success.data.FILE_NAME + '</td>'
        //                     + '<td><a target="blank" href="' + document.basePath + '/uploads/dc_documents/' + success.data.FILE_IN_DIR_NAME + '"><i class="fa fa-download"></i></a></td>');
                
                
                    
        //         }
        //     }, function (failure) {
        //     });
        // }
        // app.serverRequest(document.pullFilebyId, {
        //     'id': document.id
        // }).then(function (success) {
        //     if (success.success) {
        //         imageUpload({fileName: success.data['FILE_IN_DIR_NAME'], oldFileName: success.data['FILE_NAME']});
        //     }
        // }, function (failure) { 
        //     console.log('failed');
        // });


    // });
})(window.jQuery,window.app);