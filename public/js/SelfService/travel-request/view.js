(function ($, app) {

    $(document).ready(function () {
        $('select#form-transportTypeList').select2();
        app.startEndDatePickerWithNepali('nepaliStartDate1', 'form-fromDate', 'nepaliEndDate1', 'form-toDate')
        var employeeId = $('#employeeId').val();
        window.app.floatingProfile.setDataFromRemote(employeeId);

        var $print = $('#print');
        $print.on('click', function () {
            app.exportDomToPdf('printableArea', document.urlCss);
        });
        
        var $noOfDays = $('#noOfDays');
        var $fromDate = $('#form-fromDate');
        var $toDate = $('#form-toDate');
        var $nepaliFromDate = $('#nepaliStartDate1');
        var $nepaliToDate = $('#nepaliEndDate1');
        
        var diff =  Math.floor(( Date.parse($toDate.val()) - Date.parse($fromDate.val()) ) / 86400000);
        $noOfDays.val(diff + 1);
        
        $fromDate.on('change', function () {
            var diff =  Math.floor(( Date.parse($toDate.val()) - Date.parse($fromDate.val()) ) / 86400000);
            $noOfDays.val(diff + 1);
        });
        
        $toDate.on('change', function () {
            var diff =  Math.floor(( Date.parse($toDate.val()) - Date.parse($fromDate.val()) ) / 86400000);
            $noOfDays.val(diff + 1);
        });
        
        $nepaliFromDate.on('change', function () {
            var diff =  Math.floor(( Date.parse($toDate.val()) - Date.parse($fromDate.val()) ) / 86400000);
            $noOfDays.val(diff + 1);
        });
        
        $nepaliToDate.on('change', function () {
            var diff =  Math.floor(( Date.parse($toDate.val()) - Date.parse($fromDate.val()) ) / 86400000);
            $noOfDays.val(diff + 1);
        });

        let total = 150;
        // $("#domesticConfigTable").hide();

        $("#addDomesticBtn").on('click', function (){
            $("#domesticConfigTable").show();
            $(".arrDate:first").attr("required", "required");
            $(".depDate:first").attr("required", "required");
            $(".locFrom:first").attr("required", "required");
            $(".locto:first").attr("required", "required");
        });
        $("#deleteDomesticBtn").on('click', function (){
            $("#domesticConfigTable").hide();
            $(".arrDate:first").prop('required',false);
            $(".depDate:first").prop('required',false);
            $(".locFrom:first").prop('required',false);
            $(".locto:first").prop('required',false);
        });
          
        // $("#internationalConfigTable").hide();

        $("#addInternationalBtn").on('click', function (){
            $("#internationalConfigTable").show();
            $(".arrDateInternational:first").attr("required", "required");
            $(".depDateInternational:first").attr("required", "required");
            $(".locFromInternational:first").attr("required", "required");
            $(".loctoInternational:first").attr("required", "required")
        });
        $("#deleteInternationalBtn").on('click', function (){
            $("#internationalConfigTable").hide();
            $(".arrDateInternational:first").prop('required',false);
            $(".depDateInternational:first").prop('required',false);
            $(".locFromInternational:first").prop('required',false);
            $(".loctoInternational:first").prop('required',false);
        });

        var internationalPlaces = [
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
        var transportTypes = [
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
        
        var all_data=document.currencyList;

        // all_data_json = JSON.parse(all_data);
        var dt = new Date($('#form-fromDate').val());
        dt.setDate( dt.getDate() - 7 );
        var date = dt.getDate();
        var month = dt.getMonth(); //Be careful! January is 0 not 1
        var year = dt.getFullYear();

        var dt2 = new Date($('#form-toDate').val());
        dt2.setDate( dt2.getDate() + 7 );
        var date2 = dt2.getDate();
        var month2 = dt2.getMonth(); //Be careful! January is 0 not 1
        var year2 = dt2.getFullYear();

        var months = ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'];

        var extendedStartDate = (date + "-" +(months[month]) + "-" + year);
        var extendedEndDate = (date2 + "-" +(months[month2]) + "-" + year2);

        console.log($('#form-fromDate').val());
        console.log(extendedEndDate);

        // all_data_json = JSON.parse(all_data);


        app.startEndDatePickerWithNepali('', 'departureDate', '', 'returnedDate');
        // app.addComboTimePicker($('.depTime'), $('.arrTime'));
        // app.populateSelect($('.mot'), transportTypes, 'CODE', 'NAME', '-select-',null, 1, true);
        app.addDatePicker($('.depDate'), $('.arrDate'));

        // app.addComboTimePicker($('.depTimeInternational'), $('.arrTimeInternational'));
        // app.populateSelect($('.motInternational'), internationalPlaces, 'CODE', 'NAME', '-select-',null, 1, true);
        app.populateSelect($('.currency'), all_data, 'code', 'code', '-select-',null, 1, true);
        app.addDatePicker($('.depDateInternational'), $('.arrDateInternational'));

        // console.log($('#form-fromDate').val());
        $('.depDate').datepicker('setStartDate', app.getSystemDate(extendedStartDate));
            $('.depDate').datepicker('setEndDate', app.getSystemDate(extendedEndDate));

            $('.arrDate').datepicker('setStartDate', app.getSystemDate(extendedStartDate));
            $('.arrDate').datepicker('setEndDate', app.getSystemDate(extendedEndDate));

            $('.depDateInternational').datepicker('setStartDate', app.getSystemDate(extendedStartDate));
            $('.depDateInternational').datepicker('setEndDate', app.getSystemDate(extendedEndDate));

            $('.arrDateInternational').datepicker('setStartDate', app.getSystemDate(extendedStartDate));
            $('.arrDateInternational').datepicker('setEndDate', app.getSystemDate(extendedEndDate));

        // $(".depDate:first").on('change', function () {
        //     var diff =  Math.floor(( Date.parse($(".arrDate:first").val()) - Date.parse($(".depDate:first").val()) ) / 86400000);
        //     $(".noOfDays:first").val(diff + 1);
        // });
        // $(document).on('change', '.otherExpenses', function(){
        //     $(this).closest("tr").find("td div input.total").val($(this).closest("tr").find("td div input.otherExpenses").val());
        // });
        $(document).on('change', '.depDateInternational, .arrDateInternational', function () {
            var diff = Math.floor(( Date.parse($(this).closest("tr").find("td div input.arrDateInternational").val()) - Date.parse($(this).closest("tr").find("td div input.depDateInternational").val()) ) / 86400000);
            if(clicked == false){
                if($(this).closest("tr").find("td div input.arrDateInternational").val() == $('#returnedDate').val()){
                    $(this).closest("tr").find("td input.applyHalfDay").click();
                    $(this).closest("tr").find("td div input.noOfDaysInternational").val(diff + 1 - 0.5);
                }else{
                    $(this).closest("tr").find("td div input.noOfDaysInternational").val(diff + 1);
                }
            }else{
                if($(this).closest("tr").find("td input.applyHalfDay").prop('checked')==true){
                    $(this).closest("tr").find("td div input.noOfDaysInternational").val(diff + 1 - 0.5);
                }else{
                    $(this).closest("tr").find("td div input.noOfDaysInternational").val(diff + 1);
                }
            }
            // if($(this).closest("tr").find("td div input.arrDateInternational").val() == $('#returnedDate').val()){
            //     $(this).closest("tr").find("td div input.noOfDaysInternational").val(diff + 1 - 0.5);
            // }else{
            //     $(this).closest("tr").find("td div input.noOfDaysInternational").val(diff + 1);
            // }
            // $(this).closest("tr").find("td div input.noOfDaysInternational").val(diff + 1);
        });

        var clicked = false;
        $(document).on('change', ".depDate, .arrDate", function () {
            var diff = Math.floor(( Date.parse($(this).closest("tr").find("td div input.arrDate").val()) - Date.parse($(this).closest("tr").find("td div input.depDate").val()) ) / 86400000);
            console.log($(this).closest("tr").find("td input.applyHalfDay").prop('checked'));
            if(clicked == false){
                if($(this).closest("tr").find("td div input.arrDate").val() == $('#returnedDate').val()){
                    $(this).closest("tr").find("td input.applyHalfDay").click();
                    $(this).closest("tr").find("td div input.noOfDays").val(diff + 1 - 0.5);
                }else{
                    $(this).closest("tr").find("td div input.noOfDays").val(diff + 1);
                }
            }else{
                if($(this).closest("tr").find("td input.applyHalfDay").prop('checked')==true){
                    $(this).closest("tr").find("td div input.noOfDays").val(diff + 1 - 0.5);
                }else{
                    $(this).closest("tr").find("td div input.noOfDays").val(diff + 1);
                }
            }
            
            
        });
        $(document).on('change', ".applyHalfDay", function (){
            clicked = true;
            $('input.applyHalfDay').prop('checked',false);
            $(this).closest("tr").find("td input.applyHalfDay").prop('checked',true);
            $('.depDate').trigger("change");
            $('.depDateInternational').trigger("change");
            // document.getElementById('applyHalfDay').checked = true;
            // $(this).closest("tr").find("td input.applyHalfDay").prop('checked');
        });
        

        // $(document).on('change', ".noOfDays, .kmWalked, .mot, .depDate, .arrDate", function () {
            
        //     console.log($(this).closest("tr").find("td div select.mot").val());
        //     if($(this).closest("tr").find("td div select.mot").val() == "WALKING" && $(this).closest("tr").find("td div input.kmWalked").val() != null){
        //         app.serverRequest(document.getLineTotal, {
        //             travelType: "DOMESTIC",
        //             mot: $(this).closest("tr").find("td div select.mot").val(),
        //             unit: $(this).closest("tr").find("td div input.kmWalked").val()
        //         }).then(function (response) {
        //             total = response.data;
        //             console.log(total);
        //         }, function (error) {
        //             console.log(error);
        //         });
        //     }else if($(this).closest("tr").find("td div select.mot").val() == "TRAVEL" && $(this).closest("tr").find("td div input.noOfDays").val() != null){
        //         app.serverRequest(document.getLineTotal, {
        //             travelType: "DOMESTIC",
        //             mot: $(this).closest("tr").find("td div select.mot").val(),
        //             unit: $(this).closest("tr").find("td div input.noOfDays").val()
        //         }).then(function (response) {
        //             total = response.data;
        //         }, function (error) {
        //             console.log(error);
        //         });
        //     }
        //     $(this).closest("tr").find("td div input.total").val(total);
        // });
        
        // $(".arrDate:first").on('change', function () {
        //     var diff =  Math.floor(( Date.parse($(".arrDate:first").val()) - Date.parse($(".depDate:first").val()) ) / 86400000);
        //     $(".noOfDays:first").val(diff + 1);
        // });

        // $(".depDateInternational:first").on('change', function () {
        //     var diff =  Math.floor(( Date.parse($(".arrDateInternational:first").val()) - Date.parse($(".depDateInternational:first").val()) ) / 86400000);
        //     $(".noOfDaysInternational:first").val(diff + 1);
        // });

        // $(".arrDateInternational:first").on('change', function () {
        //     var diff =  Math.floor(( Date.parse($(".arrDateInternational:first").val()) - Date.parse($(".depDateInternational:first").val()) ) / 86400000);
        //     $(".noOfDaysInternational:first").val(diff + 1);
        // });

        $('form').bind('submit', function () {
            $(this).find(':disabled').removeAttr('disabled');
        });

        $('.deatilAddBtn').on('click', function () {
            var appendData = `
            <tr>
                <td><input class="dtlDelBtn btn btn-danger" type="button" value="Del -" style="padding:3px;"></td>
                <td>
                <div style="width:90px">
                    <input  type="checkbox" name="applyHalfDay[]"  class="applyHalfDay" id="applyHalfDay">
                </div>
                </td>
                <td>
                    <div style="overflow:hidden">
                        <input style="width:100%" type="text" name="depDate[]" required="required"  class="depDate">
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
                        <select class='mot' name='mot[]' required="required">
                        </select>
                    </div>
                </td>       
                <td>
                    <div style="overflow:hidden">
                        <input style="width:100%" type="text" name="arrDate[]" required="required"  class="arrDate">       
                    </div>
                </td>
                <td>
                    <div style="overflow:hidden">
                        <input style="width:100%" type="number" readonly name="noOfDays[]" required="required"  class="noOfDays">       
                    </div>
                </td>
                <td>
                    <div style="overflow:hidden">
                        <input style="width:100%" type="number" name="kmWalked[]"  class="kmWalked">       
                    </div>
                </td> 
                <td>
                    <div style="overflow:hidden">
                        <select class='transport' name='transport[]' >
                        </select>
                    </div>
                </td>
                <td>
                    <div style="width:120px">
                        <input style="width:100%" type="text" name="transportClass[]"  class="transportClass">       
                    </div>
                </td>
                <td>
                    <div style="width:120px">
                        <input style="width:100%" type="number" name="rate1[]"  class="rate1">       
                    </div>
                </td>
                <td>
                    <div style="width:120px">
                        <input style="width:100%" type="number" name="miles[]"  class="miles">       
                    </div>
                </td>
                <td>
                    <div style="width:120px">
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
            
            app.addComboTimePicker(
                    $('#domesticConfigTable tbody').find('.depTime:last'),
                    $('#domesticConfigTable tbody').find('.arrTime:last')
                    );
            
            app.addDatePicker(
                    $('#domesticConfigTable tbody').find('.depDate:last'),
                    $('#domesticConfigTable tbody').find('.arrDate:last')
                    );

                    $('#domesticConfigTable tbody').find('.depDate:last').datepicker('setStartDate', app.getSystemDate(extendedStartDate));
                    $('#domesticConfigTable tbody').find('.depDate:last').datepicker('setEndDate', app.getSystemDate(extendedEndDate));
        
                    $('#domesticConfigTable tbody').find('.arrDate:last').datepicker('setStartDate', app.getSystemDate(extendedStartDate));
                    $('#domesticConfigTable tbody').find('.arrDate:last').datepicker('setEndDate', app.getSystemDate(extendedEndDate));
    
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

        $('.deatilAddBtnInternational').on('click', function () {
            var appendData = `
            <tr>
                <td><input class="dtlDelBtnInternational btn btn-danger" type="button" value="Del -" style="padding:3px;"></td>
                <td>
                <div style="width:90px">
                    <input  type="checkbox" name="applyHalfDay[]"  class="applyHalfDay" id="applyHalfDay">
                </div>
                </td>
                <td>
                    <div style="overflow:hidden">
                        <input style="width:100%" type="text" name="depDateInternational[]" required="required"  class="depDateInternational">
                    </div>
                </td>
                <td>
                    <div style="overflow:hidden">
                        <input style="width:100%" type="text" name="locFromInternational[]" required="required"  class="locFromInternational">       
                    </div>
                </td>
                <td>
                    <div style="overflow:hidden">
                        <input style="width:100%" type="text" name="loctoInternational[]" required="required"  class="loctoInternational">       
                    </div>
                </td>
                <td>
                    <div style="overflow:hidden">
                        <select class='motInternational' name='motInternational[]' required="required">
                        </select>
                    </div>
                </td>       
                <td>
                    <div style="overflow:hidden">
                        <input style="width:100%" type="text" name="arrDateInternational[]" required="required"  class="arrDateInternational">       
                    </div>
                </td>
                <td>
                    <div style="overflow:hidden">
                        <input style="width:100%" type="number" readonly name="noOfDaysInternational[]" required="required"  class="noOfDaysInternational">       
                    </div>
                </td> 
                <td>
                    <div style="overflow:hidden">
                        <select class='internationalTransport' name='internationalTransport[]' >
                        </select>
                    </div>
                </td>
                <td>
                    <div style="width:120px">
                        <input style="width:100%" type="text" name="internationalTransportClass[]"  class="internationalTransportClass">       
                    </div>
                </td>
                <td>
                    <div style="width:120px">
                        <input style="width:100%" type="number" name="internationalRate1[]"  class="internationalRate1">       
                    </div>
                </td>
                <td>
                    <div style="width:120px">
                        <input style="width:100%" type="number" name="internationalMiles[]"  class="internationalMiles">       
                    </div>
                </td>
                <td>
                    <div style="width:120px">
                        <input style="width:100%" type="number" name="internationalRate2[]"  class="internationalRate2">       
                    </div>
                </td>
                <td>
                    <div style="width:150px">
                        <textarea style="width:100%" rows="4" cols="50" name="internationalOtherExpenseDetail[]"  class="internationalOtherExpenseDetail"></textarea>
                    </div>
                </td>
                <td>
                    <div style="overflow:hidden">
                        <input style="width:100%" type="number" name="otherExpensesInternational[]"  class="otherExpensesInternational">       
                    </div>
                </td>
                <td>
                    <div style="width:150px">
                        <textarea style="width:100%" rows="4" cols="50" name="internationalDetPurpose[]"  class="internationalDetPurpose"></textarea>
                    </div>
                </td>
                <td>
                    <div style="overflow:hidden">
                        <select class='currency' name='currency[]' required="required">
                        </select>
                    </div>
                </td> 
                <td>
                    <div style="overflow:hidden">
                        <input style="width:100%" type="number" name="exchangeRateInternational[]"  class="exchangeRateInternational">       
                    </div>
                </td>       
                <td>
                    <div style="overflow:hidden">
                        <textarea style="width:100%" rows="4" cols="50" name="detRemarksInternational[]"  class="detRemarksInternational"></textarea>
                    </div>
                </td>

                

            </tr>
            `;
            
            $('#internationalConfigTable tbody').append(appendData);
            
            app.addComboTimePicker(
                    $('#internationalConfigTable tbody').find('.depTimeInternational:last'),
                    $('#internationalConfigTable tbody').find('.arrTimeInternational:last')
                    );
            
            app.addDatePicker(
                    $('#internationalConfigTable tbody').find('.depDateInternational:last'),
                    $('#internationalConfigTable tbody').find('.arrDateInternational:last')
                    );
             
                    $('#internationalConfigTable tbody').find('.depDateInternational:last').datepicker('setStartDate', app.getSystemDate(extendedStartDate));
                    $('#internationalConfigTable tbody').find('.depDateInternational:last').datepicker('setEndDate', app.getSystemDate(extendedEndDate));
        
                    $('#internationalConfigTable tbody').find('.arrDateInternational:last').datepicker('setStartDate', app.getSystemDate(extendedStartDate));
                    $('#internationalConfigTable tbody').find('.arrDateInternational:last').datepicker('setEndDate', app.getSystemDate(extendedEndDate));
        

            app.populateSelect($('#internationalConfigTable tbody').find('.motInternational:last'), internationalPlaces, 'CODE', 'NAME', '-select-',null, 1, true);
            app.populateSelect($('#internationalConfigTable tbody').find('.currency'), all_data, 'code', 'code', '-select-',null, 1, true);
            app.populateSelect($('#internationalConfigTable tbody').find('.internationalTransport:last'),transportList, 'CODE', 'NAME', '-select-',null, 1, true);

            // $('#internationalConfigTable tbody').find(".depDateInternational:last").on('change', function () {
            //     var diff =  Math.floor(( Date.parse($('#internationalConfigTable tbody').find(".arrDateInternational:last").val()) - Date.parse($('#internationalConfigTable tbody').find(".depDateInternational:last").val()) ) / 86400000);
            //     $('#internationalConfigTable tbody').find(".noOfDaysInternational:last").val(diff + 1);
            // });
    
            // $('#internationalConfigTable tbody').find(".arrDateInternational:last").on('change', function () {
            //     var diff =  Math.floor(( Date.parse($('#internationalConfigTable tbody').find(".arrDateInternational:last").val()) - Date.parse($('#internationalConfigTable tbody').find(".depDateInternational:last").val()) ) / 86400000);
            //     $('#internationalConfigTable tbody').find(".noOfDaysInternational:last").val(diff + 1);
            // });
            
        });

        $('#domesticConfigTable').on('click', '.dtlDelBtn', function () {
            var selectedtr = $(this).parent().parent();
            selectedtr.remove();
        });

        $('#internationalConfigTable').on('click', '.dtlDelBtnInternational', function () {
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
                            + '<td><a target="blank" href="' + document.basePath + '/uploads/travel_documents/' + success.data.FILE_IN_DIR_NAME + '"><i class="fa fa-download"></i></a></td>'
                            + ((document.status!='AP')? '<td><button type="button" class="btn btn-danger deleteFile">DELETE</button></td></tr>':'</tr>'));

                }
            }, function (failure) {
            });
        }

        $('#fileDetailsTbl').on('click', '.deleteFile', function () {  
            var selectedtr = $(this).parent().parent();
            selectedtr.remove();
            var rowCount1 = document.getElementById('fileDetailsTbl').rows.length;

        });

        window.app.serverRequest(document.pullFilebyId, {
            'id': document.id
        }).then(function (success) {
            if (success.success) {
                var i;
                for (i = 0; i < success.data.length; i++) {
                    imageUpload({fileName: success.data[i]['FILE_IN_DIR_NAME'], oldFileName: success.data[i]['FILE_NAME']});
                }
                
            }
        }, function (failure) { 
            console.log('failed');
        });
        
    });
})(window.jQuery, window.app);
