(function ($, app) {
    'use strict';
    $(document).ready(function () {
        app.startEndDatePickerWithNepali('nepaliStartDate1', 'fromDt', 'nepaliEndDate1', 'toDt')
        app.setLoadingOnSubmit("travelItnary-form");
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
        
        function getDateFormat(date) {
            var m_names = new Array("Jan", "Feb", "Mar",
                    "Apr", "May", "Jun", "Jul", "Aug", "Sep",
                    "Oct", "Nov", "Dec");


            var d = new Date(date);
            var curr_date = d.getDate();
            var curr_month = d.getMonth();
            var curr_year = d.getFullYear();
            return curr_date + "-" + m_names[curr_month]
                    + "-" + curr_year;

        }
        
        
        // to view itnary member start
//        console.log(document.itnaryMembersDtl);
        
        $.each([], function (key, value) {
//            console.log(value);
var appendData = `
            <tr>
                <td>
                                        <select class="memberSelect" name="employeeId[]">
                                            
                                        </select>
                                    </td>
            </tr>
            `;
            $('#memberDetails tbody').append(appendData);
            app.populateSelect($('#memberDetails tbody').find('.memberSelect:last'), document.employeeList, 'EMPLOYEE_ID', 'FULL_NAME', null, null,value.EMPLOYEE_ID);
            $('#memberDetails tbody').find('.memberSelect:last').select2();
        });
        
        
        // to view itnary memebr end
        
        
        
        // to view itnary dtl start
         
        $.each([], function (key, value) {
            
            var appendData = `
                            <tr>
                                <td>
                                    <div style="overflow:hidden">
                                        <input style="width:100%" type="text" name="depDate[]" required="required"  class="depDate">
                                    </div>
                                </td>
                                <td>
                                    <div style="overflow:hidden">
                                        <input style="width:100%" type="text" name="depTime[]" required="required"  class="depTime"  data-format="HH:mm" data-template="HH:mm">       
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
                                        <input style="width:100%" type="text" name="arrTime[]" required="required"  class="arrTime"  data-format="HH:mm" data-template="HH:mm">
                                    </div>
                                </td>       
                                <td>
                                    <div style="overflow:hidden">
                                        <textarea style="width:100%" rows="4" cols="30" name="detRemarks[]"   class="detRemarks"></textarea>
                                    </div>
                                </td>
                            </tr>
            `;
            
            $('#itnaryDetails tbody').append(appendData);
            
            
            $('#itnaryDetails tbody').find('.depTime:last').combodate({
                    minuteStep: 1,
                    firstItem: 'name',
                    value: value.DEPARTURE_TIME
                });
                
            $('#itnaryDetails tbody').find('.arrTime:last').combodate({
                    minuteStep: 1,
                    firstItem: 'name',
                    value: value.ARRIVE_TIME
                });
            
            
            app.addDatePicker(
                    $('#itnaryDetails tbody').find('.depDate:last'),
                    $('#itnaryDetails tbody').find('.arrDate:last')
                    );
            
            $('#itnaryDetails tbody').find('.depDate:last').datepicker("update", getDateFormat(value.DEPARTURE_DT));
            $('#itnaryDetails tbody').find('.arrDate:last').datepicker("update", getDateFormat(value.ARRIVE_DT));
            
             
            app.populateSelect($('#itnaryDetails tbody').find('.mot:last'), document.transportTypes , 'TRANSPORT_CODE', 'TRANSPORT_NAME', null,null,value.TRANSPORT_TYPE);


//    to populate values start
  $('#itnaryDetails tbody').find('.locFrom:last').val(value.LOCATION_FROM);
  $('#itnaryDetails tbody').find('.locto:last').val(value.LOCATION_TO);
  $('#itnaryDetails tbody').find('.detRemarks:last').val(value.REMARKS);
            
            
//    to populate values end
            
            
        });
        
        
        // to view itnary dtl end
  

     
        
//         to add bill member start

        var $memberDetails = $('#memberDetails');
        var $memberAddBtn = $('.memberAddBtn');
        
        
        $memberAddBtn.on('click', function () {
            console.log('clciked');
            var appendData = `
            <tr>
                <td>
                                        <select class="memberSelect" name="employeeId[]">
                                            
                                        </select>
                                    </td>
            <td><input class="memberDelBtn btn btn-danger" type="button" value="Del -" style="padding:3px;"></td>
            </tr>            
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

//            app.populateSelect($('#form-transportType'), document.transportTypes , 'TRANSPORT_CODE', 'TRANSPORT_NAME', null,null);
            
//            app.populateSelect($('.mot'), document.transportTypes , 'TRANSPORT_CODE', 'TRANSPORT_NAME', null,null);


        
        
        
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
                                        <input style="width:100%" type="text" name="depTime[]" required="required"  class="depTime"  data-format="HH:mm" data-template="HH:mm">       
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
                                        <input style="width:100%" type="text" name="arrTime[]" required="required"  class="arrTime"  data-format="HH:mm" data-template="HH:mm">
                                    </div>
                                </td>       
                                <td>
                                    <div style="overflow:hidden">
                                        <textarea style="width:100%" rows="4" cols="30" name="detRemarks[]"   class="detRemarks"></textarea>
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
        
        
        $('input').prop("disabled", true);
        $('select').prop("disabled", true);
        $('textarea').prop("disabled", true);
        
        $('#pdfExport').on('click', function () {
            kendo.drawing.drawDOM($("#itnaryview")).then(function (group) {
                kendo.drawing.pdf.saveAs(group, "ItineraryDetails.pdf");
            });
        });
    });
})(window.jQuery, window.app);
