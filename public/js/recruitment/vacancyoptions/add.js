(function ($, app) {
    'use strict';
    $(document).ready(function () {
        app.startEndDatePickerWithNepali('nepaliStartDate1', 'fromDt', 'nepaliEndDate1', 'toDt')
        $('select.optionId').select2();
        $('select.OpenInternal').select2();
        $('select#form-employeeId').select2();
        var employeeId = $('#employeeId').val();
        app.floatingProfile.setDataFromRemote(employeeId);

        var $print = $('#print');
        $print.on('click', function () {
            app.exportDomToPdf('printableArea', document.urlCss);
        });
    
        var OpenInternal = [{key: "OPEN", value: "OPEN"}, {key: "INTERNAL", value: "INTERNAL"}];
        var $OI = $(".OpenInternal");
        app.populateSelect($OI, OpenInternal, 'key', 'value');
    
        
//         to add Options List

            app.populateSelect($('.optionId'), document.OptionLists , 'OPTION_ID', 'OPTION_EDESC', null,null);
        
        
        // to add  details start
        
        var $itnaryDtl = $('#vacancyoptionsDetails');
        var $itnaryDtlAddBtn = $('.deatilAddBtn');

        $("#VacancyId").change(function(){
            let vid = $("#VacancyId").val();

            for(let i in document.positions){
                if(document.positions[i].VACANCY_ID == vid){
                    $("#position").html(" "+ document.positions[i].DESIGNATION_TITLE);
                    $("#position").css({"border-style":"ridge","font-size":"12px","padding-left": "6px"});
                }
                
            }
        });
        
        // Check Remaining value Start ----------------
        // $("#VacancyId").change(function(){
        //     let vid = $("#VacancyId").val();
        //     // let openquota = 0;
        //     // let totalQuota_open = 0;
        //     // console.log(document.Quota_openleft);
        //     for(let i in document.Quota_openleft){
        //         if(document.Quota_openleft[i].VACANCY_ID == vid && document.Quota_openleft[i].OPEN_INTERNAL === 'OPEN'){
                   
        //                 openquota += parseInt(document.Quota_openleft[i].QUOTA);
        //                 // openquota ++;
                        
        //             // $("#Openleft").html(" "+ openquota);
        //             // $("#Openleft").css({"border-style":"ridge","font-size":"12px","padding-left": "12px"});
        //         }
        //     }
        //     for(let i in document.Quota_open){
        //         if(document.Quota_open[i].VACANCY_ID == vid){
        //             totalQuota_open =  document.Quota_open[i].QUOTA_OPEN;
                   
        //         }
        //     }
        //         let RemainingOpenData = totalQuota_open - openquota

        //         $("#Openleft").html(" "+ RemainingOpenData);
        //         $("#Openleft").css({"border-style":"ridge","font-size":"12px","padding-left": "12px"});
            

        // });
        // CheckRemaining value End-------------------
        // console.log(document.Quota_open);
        
        //OPEN
        $("#VacancyId").change(function(){
            let vid = $("#VacancyId").val();
            let openquota = 0;
            let totalQuota_open = 0;
            for(let i in document.Quota_open){
                if(document.Quota_open[i].VACANCY_ID == vid){
                    totalQuota_open =  document.Quota_open[i].QUOTA_OPEN
                    // $("#quotaopen").html(" "+ document.Quota_open[i].QUOTA_OPEN);
                    // $("#quotaopen").css({"border-style":"ridge","font-size":"12px","padding-left": "12px"});
                }
            }
            for(let i in document.Quota_openleft){
                if(document.Quota_openleft[i].VACANCY_ID == vid && document.Quota_openleft[i].OPEN_INTERNAL === 'OPEN'){
                   
                        openquota += parseInt(document.Quota_openleft[i].QUOTA);
                }
            }
            let RemainingOpenData = totalQuota_open - openquota ;
            
                $("#quotaopen").html(" "+ RemainingOpenData);
                $("#quotaopen").css({"border-style":"ridge","font-size":"12px","padding-left": "12px"});

        });
        //INTERNAL
        $("#VacancyId").change(function(){
            let vid = $("#VacancyId").val();
            let internalquota = 0;
            let totalQuota_internal = 0;
            for(let i in document.Quota_internal){
                if(document.Quota_internal[i].VACANCY_ID == vid){
                    totalQuota_internal =  document.Quota_internal[i].QUOTA_INTERNAL;
                    // $("#quotaInternal").html(" "+ document.Quota_internal[i].QUOTA_INTERNAL);
                    // $("#quotaInternal").css({"border-style":"ridge","font-size":"12px","padding-left": "12px"});
                }
            }
            for(let i in document.Quota_openleft){
                if(document.Quota_openleft[i].VACANCY_ID == vid && document.Quota_openleft[i].OPEN_INTERNAL == 'INTERNAL'){
                   
                        internalquota += parseInt(document.Quota_openleft[i].QUOTA);
                        // openquota ++;                        
                    // $("#Openleft").html(" "+ openquota);
                    // $("#Openleft").css({"border-style":"ridge","font-size":"12px","padding-left": "12px"});
                }
            }
                let RemainingInternalData = totalQuota_internal - internalquota ;
                console.log(internalquota);
                $("#quotaInternal").html(" "+ RemainingInternalData);
                $("#quotaInternal").css({"border-style":"ridge","font-size":"12px","padding-left": "12px"});
        });
        // Open/Internal Value Check START
        function checkQuota(){
            var oi =  $("select[name='OpenInternal[]']")
            .map(function(){return $(this).val();}).get();
            var internalQuota = parseInt($("#quotaInternal").html());
            // console.log(internalQuota);
            var openQuota = parseInt($("#quotaopen").html());
            var quotavalue = $("input[name='Quota[]']")
              .map(function(){return $(this).val();}).get();
            var totalInternalValue = 0; 
            var totalOpenValue = 0;
            for (var i = 0; i < quotavalue.length; i++)
            {
                if(oi[i] == "INTERNAL")
                {
                    totalInternalValue += parseInt(quotavalue[i])||0;
                    // console.log(totalInternalValue);
                    
                }
                if (oi[i] == "OPEN")
                {
                    totalOpenValue += parseInt(quotavalue[i])||0;
                }
                
            }

            if(totalInternalValue > internalQuota ){
                $("#submit").attr("disabled", "disabled");
                $("#quotaInternal").css("border-color", "red");
            }
            // else{
            //     $("#submit").removeAttr("disabled");
            //     $("#quotaInternal").css("border-color", "");
            // }
            else if(totalOpenValue > openQuota ){
                $("#submit").attr("disabled", "disabled");
                $("#quotaopen").css("border-color", "red");
            }
            else if((openQuota+internalQuota) <= (totalInternalValue+totalOpenValue)){
                $("#addrow").attr("disabled", "disabled");
            }
            else{
                $("#submit").removeAttr("disabled");
                $("#quotaopen").css("border-color", "");
                $("#quotaInternal").css("border-color", "");
            }
        }
        $(document).on("input", ".Quota", function(){
            checkQuota();
        });

        $(document).on("change", ".OpenInternal", function(){
            checkQuota();
        });
        
        // Open/Internal Value Check END
        
        $itnaryDtlAddBtn.on('click', function () {
            // console.log('clciked');
            var appendData = `
                            <tr>
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
                            </tr>
            `;
            
            $('#vacancyoptionsDetails tbody').append(appendData); 
            $('select.optionId').select2();
            $('select.OpenInternal').select2();
            app.populateSelect($('#vacancyoptionsDetails tbody').find('.optionId:last'), document.OptionLists , 'OPTION_ID', 'OPTION_EDESC', null,null);
            app.populateSelect($('#vacancyoptionsDetails tbody').find('.OpenInternal:last'), OpenInternal, 'key', 'value');
            
        });
        
        
         $itnaryDtl.on('click', '.vacancyOptionDebtn', function () {
            var selectedtr = $(this).parent().parent();
            selectedtr.remove();
            checkQuota();
        });
        
        
      
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
        $('#vacancyoptions').submit(function () {
            var error = [];

            $('.OptionId').each(function (index) {
                var errorResult = displayErrorMsg($(this));
                if (errorResult == 'error') {
                    error.push('error');
                }
            });

            $('.OpenInternal').each(function (index) {
                var errorResult = displayErrorMsg($(this));
                if (errorResult == 'error') {
                    error.push('error');
                }
            });
            
            
            if (error.length > 0) {
                return false;
            } else {
                App.blockUI({target: "#hris-page-content"});
                return true;
            }

        });
        // Check Remaining Quota
        
       
    });
})(window.jQuery, window.app);
