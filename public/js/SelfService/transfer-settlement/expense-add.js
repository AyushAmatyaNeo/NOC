(function ($, app) {
    
    $(document).ready(function () {

        $('#recommenderId').select2();
        $('#approverId').select2();

        let total = 150;
        // $("#domesticConfigTable").hide();

        internationalPlaces = [
            {
                "CODE": "LISTED CITIES",
                "NAME": "Listed Cities"
            },
            {
                "CODE": "OTHER INDIA CITIES",
                "NAME": "Other India Cities"
            },
            {
                "CODE": "OTHER COUNTRIES",
                "NAME": "Other Countries"
            }
        ]
        transportTypes = [
            {
                "CODE": "WALKING",
                "NAME": "Walking"
            },
            {
                "CODE": "TRAVEL",
                "NAME": "Transportation"
            }
        ];

        transportList = [
            {
                "CODE": "On Foot",
                "NAME": "On Foot"
            },
            {
                "CODE": "Office Vehicle",
                "NAME": "Office Vehicle"
            },
            {
                "CODE": "Train",
                "NAME": "Train"
            },
            {
                "CODE": "Airplane",
                "NAME": "Airplane"
            },
            {
                "CODE": "Cruise",
                "NAME": "Cruise"
            },
            {
                "CODE": "Taxi",
                "NAME": "Taxi"
            },
            {
                "CODE": "Bus",
                "NAME": "Bus"
            }
        ];
        
        all_data=document.currencyList;

        $(document).on('change', ".familyMember", function (){
            if($(this).closest("tr").find("td div input.isForFamily").val() == 'N'){
                $(this).closest("tr").find("td div input.isForFamily").val('Y');
            }else{
                $(this).closest("tr").find("td div input.isForFamily").val('N');
            }
            const dates = document.getElementsByClassName('depDate');
            const datesArr = [...dates].map(input => input.value);
            const isFamily = document.getElementsByClassName('isForFamily');
            const isFamilyArr = [...isFamily].map(input => input.value);
            checkForErrors(datesArr, isFamilyArr);
        });

        // all_data_json = JSON.parse(all_data);
        var dt = new Date($('#eventDate').val());
        dt.setDate( dt.getDate());
        var date = dt.getDate();
        var month = dt.getMonth(); //Be careful! January is 0 not 1
        var year = dt.getFullYear();

        var dt2 = new Date($('#returnedDate').val());
        dt2.setDate( dt2.getDate() + 7 );
        var date2 = dt2.getDate();
        var month2 = dt2.getMonth(); //Be careful! January is 0 not 1
        var year2 = dt2.getFullYear();

        var months = ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'];

        var extendedStartDate = (date + "-" +(months[month]) + "-" + year);
        var extendedEndDate = (date2 + "-" +(months[month2]) + "-" + year2);
        

        app.startEndDatePickerWithNepali('', 'eventDate', '', 'returnedDate');
        // app.addComboTimePicker($('.depTime'), $('.arrTime'));
        app.populateSelect($('.mot'), transportTypes, 'CODE', 'NAME', '-select-',null, 1, true);
        app.populateSelect($('.transport'), transportList, 'CODE', 'NAME', '-select-',null, 1, true);
        app.populateSelect($('.internationalTransport'), transportList, 'CODE', 'NAME', '-select-',null, 1, true);
        app.addDatePicker($('.depDate:last'), $('.arrDate:last'));
        app.addDatePicker($('.depDateInternational:last'), $('.arrDateInternational:last'));
        
        // $('.depDate:last').datepicker('setEndDate', app.getSystemDate(response.data.serverDate));


             $('.depDate:last').datepicker('setStartDate', app.getSystemDate(extendedStartDate));
             $('.depDate:last').datepicker('setEndDate', new Date());

            // $('.arrDate:last').datepicker('setStartDate', app.getSystemDate(extendedStartDate));
            // $('.arrDate:last').datepicker('setEndDate', app.getSystemDate(extendedEndDate));
            
            // console.log($('#departureDate').val());
            // $('#departureDate').val(date + "-" +(month + 1) + "-" + year);


            // $('.arrDateInternational:last').datepicker('setStartDate', app.getSystemDate(extendedStartDate));
            // $('.arrDateInternational:last').datepicker('setEndDate', app.getSystemDate(extendedEndDate));




        // app.addComboTimePicker($('.depTimeInternational'), $('.arrTimeInternational'));
        app.populateSelect($('.motInternational'), internationalPlaces, 'CODE', 'NAME', '-select-',null, 1, true);
        app.populateSelect($('.currency'), all_data, 'code', 'code', '-select-',null, 1, true);
        

        // $(".depDate:first").on('change', function () {
        //     var diff =  Math.floor(( Date.parse($(".arrDate:first").val()) - Date.parse($(".depDate:first").val()) ) / 86400000);
        //     $(".noOfDays:first").val(diff + 1);
        // });
        // $(document).on('change', '.otherExpenses', function(){
        //     $(this).closest("tr").find("td div input.total").val($(this).closest("tr").find("td div input.otherExpenses").val());
        // });
        
        var clicked = false;
        var dates = [];

        $(document).on('change', ".depDate, .arrDate", function () {
            const dates = document.getElementsByClassName('depDate');
            const datesArr = [...dates].map(input => input.value);
            const isFamily = document.getElementsByClassName('isForFamily');
            const isFamilyArr = [...isFamily].map(input => input.value);
            checkForErrors(datesArr, isFamilyArr);
            // var diff = Math.floor(( Date.parse($(this).closest("tr").find("td div input.arrDate").val()) - Date.parse($(this).closest("tr").find("td div input.depDate").val()) ) / 86400000);
            $(this).closest("tr").find("td div input.noOfDays").val(1);   
        });

        $('form').bind('submit', function () {
            $(this).find(':disabled').removeAttr('disabled');
        });
        
        var numberOfRows = parseInt(document.getRowNumber);
        $('.deatilAddBtn').on('click', function () {
            numberOfRows = numberOfRows+1;
            if(numberOfRows > 3){
                $('#dtlAddBtn').attr('disabled','disabled');
            }else{
                $('#dtlAddBtn').removeAttr('disabled');
            }
            var appendData = `
            <tr>
                <td><input class="dtlDelBtn btn btn-danger" type="button" value="Del -" style="padding:3px;"></td>
                <td>
                    <div style="width:90px">
                    <input type="hidden" name="isForFamily[]" class="isForFamily" id="isForFamily" value = "N">
                        <input  type="checkbox" name="familyMember[]"  class="familyMember" id="familyMember">
                    </div>
                </td>
                <td>
                <div style="width:90px">
                    <input style="width:100%" type="text" name="familyName[]"  class="familyName" value ="Self">       
                </div>
            </td>
                <td>
                    <div style="overflow:hidden">
                        <input style="width:100%" type="text" name="depDate[]" required="required"  class="depDate" id="depDate">
                    </div>
                </td>
                <td>
                    <div style="overflow:hidden">
                        <input style="width:100%" type="text" name="locFrom[]" required="required"  class="locFrom">       
                    </div>
                </td>
                <td>
                    <div style="overflow:hidden">
                        <input style="width:100%" type="text" name="locto[]" required="required"  class="locto">       
                    </div>
                </td>       

                <td>
                    <div style="overflow:hidden">
                        <input style="width:100%" type="number" readonly name="noOfDays[]" required="required"  class="noOfDays">       
                    </div>
                </td>

                <td>
                    <div style="overflow:hidden">
                        <select class='transport' name='transport[]' >
                        </select>
                    </div>
                </td>
                <td>
                    <div style="width:90px">
                        <input style="width:100%" type="text" name="transportClass[]"  class="transportClass">       
                    </div>
                </td>
                <td>
                    <div style="width:80px">
                        <input style="width:100%" type="number" name="rate1[]"  class="rate1">       
                    </div>
                </td>
                <td>
                    <div style="width:80px">
                        <input style="width:100%" type="number" name="miles[]"  class="miles">       
                    </div>
                </td>
                <td>
                    <div style="width:80px">
                        <input style="width:100%" type="number" name="rate2[]"  class="rate2">       
                    </div>
                </td>
                <td>
                    <div style="width:150px">
                        <textarea style="width:100%" rows="4" cols="50" name="otherExpenseDetail[]"  class="otherExpenseDetail"></textarea>
                    </div>
                </td>
                <td>
                    <div style="overflow:hidden">
                        <input style="width:100%" type="number" name="otherExpenses[]"  class="otherExpenses">       
                    </div>
                </td>
                <td>
                    <div style="width:150px">
                        <textarea style="width:100%" rows="4" cols="50" name="detPurpose[]"  class="detPurpose"></textarea>
                    </div>
                </td>       
                <td>
                    <div style="overflow:hidden">
                        <textarea style="width:100%" rows="4" cols="50" name="detRemarks[]"  class="detRemarks"></textarea>
                    </div>
                </td>

                

            </tr>
            `;
            
            $('#domesticConfigTable tbody').append(appendData);
            
            // app.addComboTimePicker(
            //         $('#domesticConfigTable tbody').find('.depTime:last'),
            //         $('#domesticConfigTable tbody').find('.arrTime:last')
            //         );
            
            app.addDatePicker(
                    $('#domesticConfigTable tbody').find('.depDate:last'),
                    $('#domesticConfigTable tbody').find('.arrDate:last')
                    );


                    $('.depDate:last').datepicker('setStartDate', app.getSystemDate(extendedStartDate));
                    $('.depDate:last').datepicker('setEndDate', new Date());




            app.populateSelect($('#domesticConfigTable tbody').find('.mot:last'),transportTypes, 'CODE', 'NAME', '-select-',null, 1, true);

            app.populateSelect($('#domesticConfigTable tbody').find('.transport:last'),transportList, 'CODE', 'NAME', '-select-',null, 1, true);

            // $('#domesticConfigTable tbody').find(".depDate:last").on('change', function () {
            //     var diff =  Math.floor(( Date.parse($('#domesticConfigTable tbody').find(".arrDate:last").val()) - Date.parse($('#domesticConfigTable tbody').find(".depDate:last").val()) ) / 86400000);
            //     $('#domesticConfigTable tbody').find(".noOfDays:last").val(diff + 1);
            // });
    
            // $('#domesticConfigTable tbody').find(".arrDate:last").on('change', function () {
            //     var diff =  Math.floor(( Date.parse($('#domesticConfigTable tbody').find(".arrDate:last").val()) - Date.parse($('#domesticConfigTable tbody').find(".depDate:last").val()) ) / 86400000);
            //     $('#domesticConfigTable tbody').find(".noOfDays:last").val(diff + 1);
            // });
            
        });

        
        if(numberOfRows > 3){
            $('#dtlAddBtn').attr('disabled','disabled');
        }else{
            $('#dtlAddBtn').removeAttr('disabled');;
        }
        
        $('#domesticConfigTable').on('click', '.dtlDelBtn', function () {
            numberOfRows = numberOfRows-1;
            if(numberOfRows > 3){
                $('#dtlAddBtn').attr('disabled','disabled');
            }else{
                $('#dtlAddBtn').removeAttr('disabled');;
            }
            var selectedtr = $(this).parent().parent();
            selectedtr.remove();
        });


        $('#addDocument').on('click', function () {
            $('#documentUploadModel').modal('show');
        });

        $('#uploadCancelBtn').on('click', function () {
            $('#documentUploadModel').modal('hide');
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

        var imageUpload = function (data) {
            window.app.pullDataById(document.pushDCFileLink, {
                'filePath': data.fileName,
                'fileName': data.oldFileName
            }).then(function (success) {
                if (success.success) {
                    
                    $('#fileDetailsTbl').append('<tr>'
                            +'<input type="hidden" name="fileUploadList[]" value="' + success.data.FILE_ID + '"><td>' + success.data.FILE_NAME + '</td>'
                            + '<td><a target="blank" href="' + document.basePath + '/uploads/transfer_settlement_documents/' + success.data.FILE_IN_DIR_NAME + '"><i class="fa fa-download"></i></a></td>'
                            + '<td><button type="button" class="btn btn-danger deleteFile">DELETE</button></td></tr>');

                }
            }, function (failure) {
            });
        }

        $('#fileDetailsTbl').on('click', '.deleteFile', function () {  
            var selectedtr = $(this).parent().parent();
            selectedtr.remove();
            var rowCount1 = document.getElementById('fileDetailsTbl').rows.length;

        });
        var $form = $('#jobHistory-form');
        var isvalid = 'Y';
        $('#submitBtn').on('click', function () {
            const dates = document.getElementsByClassName('depDate');
            const datesArr = [...dates].map(input => input.value);
            const isFamily = document.getElementsByClassName('isForFamily');
            const isFamilyArr = [...isFamily].map(input => input.value);
            checkForErrors(datesArr, isFamilyArr);
            if(isvalid=='N'){
                return false;
            }
        });

        var checkForErrors = function (datesArr, isFamilyArr) {
            app.pullDataById(document.validateSettlementExpense, {dates: datesArr, isFamily: isFamilyArr, jobHistoryId: document.id}).then(function (response) {
                console.log(response.data);
                if(response.data != ''){
                    $form.prop('valid', 'false');
                    $form.prop('error-message', response.data);
                    app.showMessage(response.data, 'error');
                    isvalid = 'N';
                    return 'test';
                }else{
                    isvalid = 'Y';
                    return 'asdf';
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });
        }
    });
})(window.jQuery, window.app);

