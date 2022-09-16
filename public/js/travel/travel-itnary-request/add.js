(function ($, app) {
    'use strict';
    $(document).ready(function () {
        app.startEndDatePickerWithNepali('nepaliStartDate1', 'fromDt', 'nepaliEndDate1', 'toDt')
        $('select#form-transportType').select2();
        $('select#travelSubstitute').select2();
        $('select#form-employeeId').select2();
        $('select#travelEmpSub').select2();
        $('.memberSelect').select2();
        var employeeId = $('#employeeId').val();
        app.floatingProfile.setDataFromRemote(employeeId);

        var $print = $('#print');
        $print.on('click', function () {
            app.exportDomToPdf('printableArea', document.urlCss);
        });
        
        var $noOfDays = $('#noOfDays');
        var $fromDate = $('#fromDt');
        var $toDate = $('#toDt');
        var $nepaliFromDate = $('#nepaliStartDate1');
        var $nepaliToDate = $('#nepaliEndDate1');
        
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
  

     
        
//         to add bill member start

        var $memberDetails = $('#memberDetails');
        var $memberAddBtn = $('.memberAddBtn');
        
        
        $memberAddBtn.on('click', function () {
            var appendData = `
            <tr>
//                <td>
                                        <select class="memberSelect" name="employeeId[]" required="required">
                                            
                                        </select>
                                    </td>
//            <td><input class="memberDelBtn btn btn-danger" type="button" value="Del -" style="padding:3px;"></td>
            </tr>
//            
            `;
            $('#memberDetails tbody').append(appendData);
            app.populateSelect($('#memberDetails tbody').find('.memberSelect:last'), document.employeeList, 'EMPLOYEE_ID', 'FULL_NAME', 'Select An Employee', '');
            $('#memberDetails tbody').find('.memberSelect:last').select2();
            
        });
        
        $memberDetails.on('click', '.memberDelBtn', function () {
            var selectedtr = $(this).parent().parent();
            selectedtr.remove();
        });
        
        
//         to a dd bill member end

            app.populateSelect($('#form-transportType'), document.transportTypes , 'TRANSPORT_CODE', 'TRANSPORT_NAME', null,null);
            
            app.populateSelect($('.mot'), document.transportTypes , 'TRANSPORT_CODE', 'TRANSPORT_NAME', null,null);


        
        
        
        // to add itranary details start
        
        var $itnaryDtl = $('#itnaryDetails');
        var $itnaryDtlAddBtn = $('.deatilAddBtn');
        
        
        $itnaryDtlAddBtn.on('click', function () {
            console.log('clciked');
            var appendData = `
                            <tr>
                                <td>
                                    <div style="overflow:hidden">
                                        <input style="width:100%" type="text" name="depDate[]" required="required"  class="depDate">
                                    </div>
                                </td>
                                <td>
                                    <div style="overflow:hidden">
                                        <input style="width:100%" type="text" name="depTime[]"   class="depTime"  data-format="HH:mm" data-template="HH:mm">       
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
                                        <input style="width:100%" type="text" name="arrTime[]"   class="arrTime"  data-format="HH:mm" data-template="HH:mm">
                                    </div>
                                </td>       
                                <td>
                                    <div style="overflow:hidden">
                                        <textarea style="width:100%" rows="4" cols="50" name="detRemarks[]"   class="detRemarks"></textarea>
                                    </div>
                                </td>
                                <td><input class="itnaryDtlDelBtn btn btn-danger" type="button" value="Del -" style="padding:3px;"></td>
                            </tr>
            `;
            
            $('#itnaryDetails tbody').append(appendData);
            
            app.addComboTimePicker(
                    $('#itnaryDetails tbody').find('.depTime:last'),
                    $('#itnaryDetails tbody').find('.arrTime:last')
                    );
            
            app.addDatePicker(
                    $('#itnaryDetails tbody').find('.depDate:last'),
                    $('#itnaryDetails tbody').find('.arrDate:last')
                    );
             
            app.populateSelect($('#itnaryDetails tbody').find('.mot:last'), document.transportTypes , 'TRANSPORT_CODE', 'TRANSPORT_NAME', null,null);

            
            
            
//            app.populateSelect($('#itnaryDetails tbody').find('.memberSelect:last'), document.employeeList, 'EMPLOYEE_ID', 'FULL_NAME', 'Select An Employee', '');
//            $('#itnaryDetails tbody').find('.memberSelect:last').select2();
            
        });
        
        
         $itnaryDtl.on('click', '.itnaryDtlDelBtn', function () {
            var selectedtr = $(this).parent().parent();
            selectedtr.remove();
        });
        
        
        app.addComboTimePicker($('.depTime'), $('.arrTime'));
        app.addDatePicker($('.depDate:last'),$('.arrDate:last'));
        
        // to add itranary details end
        
        
        
        var displayErrorMsg = function (object) {
            var selectedVal = object.val()
            var $parent = object.parent();
            if (selectedVal == "") {
                var $errorElement = $('</br><span class="errorMsg" aria-required="true">Field is Required</span>');
                if (!($parent.find('span.errorMsg').length > 0)) {
                    $parent.append($errorElement);
                }
                return 'error';
            } else {
                if ($parent.find('span.errorMsg').length > 0) {
                    $parent.find('span.errorMsg').remove();
                    $parent.find('br').remove();
                }
                return 'no error';
            }
        }
        
        
        $('#travelItnaryForm').submit(function () {
            var error = [];

            $('.depTime').each(function (index) {
                var errorResult = displayErrorMsg($(this));
                if (errorResult == 'error') {
                    error.push('error');
                }
            });

            $('.arrTime').each(function (index) {
                var errorResult = displayErrorMsg($(this));
                if (errorResult == 'error') {
                    error.push('error');
                }
            });
            
            
            var empList = [];
            $('.memberSelect').each(function (index) {
                let selectedVal = $(this).val();
                let intSelectedVal = (selectedVal > 0) ? parseInt(selectedVal) : 0;
                console.log($.inArray(intSelectedVal, empList));
                if ($.inArray(intSelectedVal, empList) >= 0 || intSelectedVal == 0) {
                    app.errorMessage("Same Member For Travel Selected", "error");
                    error.push('error');
                } else {
                    empList.push(intSelectedVal);
                }
            });
            
            
            if (error.length > 0) {
                return false;
            } else {
                App.blockUI({target: "#hris-page-content"});
                return true;
            }

        });
        
        
        

    });
})(window.jQuery, window.app);
