(function ($, app) {
    'use strict';
    $(document).ready(function () {
        app.startEndDatePickerWithNepali('nepaliStartDate1', 'fromDt', 'nepaliEndDate1', 'toDt')
       
        var employeeId = $('#employeeId').val();
        app.floatingProfile.setDataFromRemote(employeeId);

        var $print = $('#print');
        $print.on('click', function () {
            app.exportDomToPdf('printableArea', document.urlCss);
        });
        
        
        // to view Vacany Options dtl start

         
        $.each([], function (key, value) {
            
            var appendData = 
                            `<tr>
                                <td>
                                    <div style="overflow:hidden">
                                        <select class='optionId' name='OptionId[]'  required="required">
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div style="overflow:hidden">
                                        <select class='OpenInternal' name='OpenInternal[]' required="required">
                                        </select>
                                    </div>
                                </td>  
                                <td>
                                    <div style="overflow:hidden">
                                        <input style="width:100%" type="Number" name="Quota[]"   class="Quota">       
                                    </div>
                                </td>
                                <td>
                                    <div style="overflow:hidden">
                                        <input style="width:100%" type="Number" name="NormalAmt[]" required="required"  class="NormalAmt">       
                                    </div>
                                </td>
                                <td>
                                    <div style="overflow:hidden">
                                        <input style="width:100%" type="Number" name="LateAmt[]" required="required"  class="LateAmt">       
                                    </div>
                                </td>     
                                <td>
                                    <div style="overflow:hidden">
                                        <textarea style="width:100%" rows="3" cols="40" name="Remarks[]"  class="Remarks"></textarea>
                                    </div>
                                </td>
                                <td><input class="vacancyOptionDebtn btn btn-danger" type="button" value="Del -" style="padding:3px;"></td>
                            </tr>`;
            
            $('#vacancyoptionsDetails tbody').append(appendData);
                
            app.populateSelect($('#vacancyoptionsDetails tbody').find('.OptionId:last'), document.OptionList, 'OPTION_ID', 'OPTION_EDESC', null, null,value.OPTION_EDESC);
            // console.log(document.OptionList);
             


// //    to populate values start
//   $('#itnaryDetails tbody').find('.locFrom:last').val(value.LOCATION_FROM);
//   $('#itnaryDetails tbody').find('.locto:last').val(value.LOCATION_TO);
//   $('#itnaryDetails tbody').find('.detRemarks:last').val(value.REMARKS);
            
            
//    to populate values end
            
            
        });

        // to add itranary details end
        
        
        $('input').prop("disabled", true);
        $('select').prop("disabled", true);
        $('textarea').prop("disabled", true);
        
    });
})(window.jQuery, window.app);
