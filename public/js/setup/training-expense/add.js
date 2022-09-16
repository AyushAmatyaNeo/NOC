(function ($, app) {
    'use strict';
    $(document).ready(function () {
        app.startEndDatePickerWithNepali('nepaliStartDate1', 'fromDt', 'nepaliEndDate1', 'toDt')
        $('select#trainingId').select2();
        $('select#form-employeeId').select2();
        $('select#travelEmpSub').select2();
        $('.memberSelect').select2();
        var employeeId = $('#employeeId').val();
        app.floatingProfile.setDataFromRemote(employeeId);

        var $print = $('#print');
        $print.on('click', function () {
            app.exportDomToPdf('printableArea', document.urlCss);
        });

        
        

        

        // var $summation = $('#sumClick');
        // $summation.on('click', function () {
        //     document.getElementById("sumValue").value = parseFloat(document.getElementById("amount").value);
        // });





        
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
        
        var $itnaryDtl = $('#expenseDetails');
        var $itnaryDtlAddBtn = $('.detailAddBtn');
        
        
        $itnaryDtlAddBtn.on('click', function () {
            var appendData = `
                            <tr>
                                <td>
                                    <div style="overflow:hidden">
                                        <select style="width:100%" required="required" name="expenseHeadId[]"   class="expenseHeadId">
                                        </select>       
                                    </div>
                                </td>
                                <td>
                                    <div style="overflow:hidden">
                                        <select style="width:100%" name="partnerId[]"   class="partnerId">
                                        </select>      
                                    </div>
                                </td>  
                                <td>
                                    <div style="overflow:hidden">
                                        <input style="width:100%" type="Number" name="amount[]"   class="amount" id="amount">       
                                    </div>
                                </td>
                                <td>
                                    <div style="overflow:hidden">
                                        <textarea style="width:100%" rows="3" cols="40" name="description[]"  class="description"></textarea>
                                    </div>
                                </td>
                                <td><input id="itnaryDtlDelBtn" class="itnaryDtlDelBtn btn btn-danger" type="button" value="Del -" style="padding:3px;"></td>
                            </tr>
                                
            `;
            
            $('#expenseDetails tbody').append(appendData);
            
            $('select.expenseHeadId').select2();
            $('select.partnerId').select2();
            app.populateSelect($('#expenseDetails tbody').find('.expenseHeadId:last'), document.ExpenseList , 'EXPENSE_HEAD_ID', 'EXPENSE_NAME', null,null);
            // app.populateSelect($('#vacancyoptionsDetails tbody').find('.OpenInternal:last'), OpenInternal, 'key', 'value');
            
        });
        $('select.expenseHeadId').select2();
        $('select.partnerId').select2();
        app.populateSelect($('#expenseDetails tbody').find('.expenseHeadId:last'), document.ExpenseList , 'EXPENSE_HEAD_ID', 'EXPENSE_NAME', null,null);
        
         $itnaryDtl.on('click', '#itnaryDtlDelBtn', function () {
            var selectedtr = $(this).parent().parent();
            selectedtr.remove();
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
