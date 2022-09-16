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
       

        // var OpenInternal = [{key: "OPEN", value: "OPEN"}, {key: "INTERNAL", value: "INTERNAL"}];
        // var $OI = $(".OpenInternal");
        // app.populateSelect($OI, OpenInternal, 'key', 'value');

        // var openingid = []
        // var $OID = $(".optionId");
        // app.populateSelect($OID, openingid, 'key', 'value');
        // var optionId = $('.optionId').val('OPTION_EDESC');
        
//         to add Options List

            // app.populateSelect($('.optionId'), document.OptionLists , 'OPTION_ID', 'OPTION_EDESC', null,null);
            // app.populateSelect($('.Quota'), document.details , 'QUOTA', details['QUOTA'], null,null);
        
        
        // to add itranary details start
        
        // var $itnaryDtl = $('#vacancyoptionsDetails'); 
        var e = document.getElementById("ddlViewBy").value;
        // var strUser = e.options[e.selectedIndex].text;          
        
        // Vacancy Quota Check Start
        function checkQuota(){
            var oi =  $("select[name='OpenInternal[]']")
            .map(function(){return $(this).val();}).get();
            var internalQuota = parseInt($("#quotaInternal").html());
            console.log(internalQuota);
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
        // vacancy Quota Check End
        
        
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
        
        
        

    });
})(window.jQuery, window.app);
