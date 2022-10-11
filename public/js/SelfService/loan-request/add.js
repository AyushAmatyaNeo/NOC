(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        var $form = $('#loan-form');
        var $submitBtn = $('#submitBtn');
        var $testBtn = $('#testBtn');
        var $test = $('#test');

        $('#test').on('click', function(){
            console.log(document.getElementsByClassName('installment').value);

        });

        $testBtn.on('click',function(){
            const number = document.getElementsByClassName('number');
            const numberArr = [...number].map(input => input.value);
            const amount = document.getElementsByClassName('amount'); 
            const amountArr = [...amount].map(input => input.value);
            // console.log(amountArr);
            const interest = document.getElementsByClassName('interest');
            const interestArr = [...interest].map(input => input.value);
            const principal = document.getElementsByClassName('principal');
            const principalArr = [...principal].map(input => input.value);
            const installment = document.getElementsByClassName('installment');
            const installmentArr = [...installment].map(input => input.value);
            const principalRemaining = document.getElementsByClassName('principal');
            const principalRemainingArr = [...principalRemaining].map(input => input.value);
            
            const searchData = {
                loanId: $("#loanId").val(),
                loanDate:$('#loanDate').val(),
                reason:$('#reason').val(),
                appliedLoan:$(".appliedLoan").val(),
                period: $("#period").val(),
                repaymentInstallments : $(".repaymentInstallments").val(),
                monthlyInterestRate:$("#monthlyInterestRate").val(),
                monthlyInstallmentAmount:$("#monthlyInstallmentAmount").val(),
                totalAmount:$('#totalAmount').val(),
                totalDeductionWithoutCit:$('#totalDeductionWithoutCit').val(),
                receivedPercent:$('#receivedPercent').val(),
                receivedWOcit:$('#receivedWOcit').val(),
                permissibleDeduction:$('#permissibleDeduction').val(),
                principalRepaid:$('#principalRemaining').val(),
                cit:$('#cit').val(),
                epf:$('#epf').val(),
                vehiclePurchaseLoan:$('#vehiclePurchaseLoan').val(),
                incomeTax:$('#incomeTax').val(),
                medicalLoan:$('#medicalLoan').val(),
                sst:$('#sst').val(),
                cycleLoan:$('#cycleLoan').val(),
                educationLoan:$('#educationLoan').val(),
                ewf:$('#ewf').val(),
                familyInsuranceLoan:$('#familyInsuranceLoan').val(),
                landLoan:$('#landLoan').val(),
                modernTechnology:$('#modernTechnology').val(),
                motorCycleLoan:$('#motorCycleLoan').val(),
                socialLoan:$('#socialLoan').val(),
                hml:$('#hml').val(),
                number:numberArr,
                loanAmount:amountArr,
                interestAmount:interestArr,
                principalAmount:principalArr,
                installmentAmount:installmentArr,
                principalRemainingAmount:principalRemainingArr,
                interestRate:$('#interestRate').val(),
                employeeCode :$('#employeeCode').val(),
                employeeName :$('#employeeName').val(),
                basicSalary:$('#basicSalary').val(),
                basicGrade :$('#basicGrade').val(),
                netAmnt:$('#netAmnt').val(),
                salaryGrade:$('#salaryGrade').val(),
             };
             app.serverRequest(document.loanData, searchData).then(function (success){
                window.location.href = document.urlTest;
             });
        });

        app.datePickerWithNepali("loanDate","nepaliDate");
        /* prevent past event post */
//        $('#form-loanDate').datepicker("setStartDate", new Date());
        $('#form-loanDate').datepicker("setStartDate",);
        /* end of  prevent past event post */
        app.setLoadingOnSubmit("loanApprove-form");
        app.setLoadingOnSubmit("loan-form");

        $('#comment').hide();

        $("#loanId").on('input change', function(){
            var loanId = $("#loanId").val();
            for(let i = 0; i < document.rateDetails.length; i++){
                if(document.rateDetails[i].LOAN_ID == loanId){
                    var appliedLoan = document.rateDetails[i].MAX_AMOUNT;
                    var interestRate = document.rateDetails[i].INTEREST_RATE;
                    var period = document.rateDetails[i].REPAYMENT_PERIOD;
                    var repaymentInstallments = document.rateDetails[i].REPAYMENT_PERIOD*12;
                    var monthlyInterestRate = parseFloat(document.rateDetails[i].INTEREST_RATE/12);
                    var totalSalary = parseFloat($("#totalSalary").val());
                    var rate = (1 + monthlyInterestRate / 100) ** repaymentInstallments ;
                    var monthlyInstallmentAmount = (appliedLoan * (monthlyInterestRate/100) *rate)/(rate-1);
                    var cit = parseFloat($("#cit").val());
                    var totalDeduction =parseFloat($('#totalDeductionWithoutCit').val())+ parseFloat(monthlyInstallmentAmount.toFixed(2)) +  parseFloat($("#cit").val());
                    $(".interestRate").val(interestRate);
                    document.rateDetails[i].IS_RATE_FLEXIBLE == 'Y' ? $("#interestRate").removeAttr('readonly') : $("#interestRate").attr('readonly', 'readonly') ;
                    $(".appliedLoan").val(appliedLoan);
                    $("#period").val(period);
                    $(".repaymentInstallments").val(repaymentInstallments);
                    $("#monthlyInterestRate").val(monthlyInterestRate.toFixed(2));
                    $("#monthlyInstallmentAmount").val(monthlyInstallmentAmount.toFixed(2));
                    $('#totalAmount').val(parseFloat($('#totalAmount').val())+ parseFloat(monthlyInstallmentAmount.toFixed(2)));
                    $('#totalDeductionWithoutCit').val(parseFloat($('#totalDeductionWithoutCit').val())+ parseFloat(monthlyInstallmentAmount.toFixed(2)));
                    $('#receivedPercent').val(((totalSalary - totalDeduction)/totalSalary*100).toFixed(2) + ' %');
                    $('#receivedWOcit').val(((totalSalary - totalDeduction + cit)/totalSalary*100).toFixed(2) + ' %');
                    $('#permissibleDeduction').val(-((totalSalary * 0.25) - totalSalary + totalDeduction - cit).toFixed(2));
                    

                    if (((totalSalary - totalDeduction)/totalSalary*100).toFixed(2) < 25){
                        console.log('first');
                        document.getElementById("testBtn").disabled = true;
                        $('#comment').show();}
                    else{
                        document.getElementById("testBtn").disabled = false;
                        console.log('second');
                        $('#comment').hide();
                    }
                    
                    $('#loanEmiTable tbody tr').remove();

                    for (let i = 0; i < repaymentInstallments ; i++) {
                        var interestAmount= ((appliedLoan * interestRate)/1200).toFixed(2);
                        var principalRepaid = (monthlyInstallmentAmount - interestAmount).toFixed(2) ;
                        var principalRemaining = appliedLoan - principalRepaid;
                        var appendData = ` 
                        <tr>
                        <td>
                            <div style="width:50px">
                                <input value = `+(i+1)+` style="width:100%" type="text" name="number[]"   class="number" readonly>       
                            </div>
                        </td>
                        <td>
                            <div style="width:170px">
                                <input value = `+appliedLoan+`  style="width:100%" type="text" name="amount[]"   class="amount" readonly>       
                            </div>
                        </td>
                        <td>
                            <div style="width:90px">
                                <input value = `+monthlyInstallmentAmount.toFixed(2)+` style="width:100%" type="text" name="installment[]"   class="installment" readonly id="installment">       
                            </div>
                        </td>
                        <td>
                            <div style="width:90px">
                                <input  value = `+interestAmount+` style="width:100%" type="text" name="interest[]"   class="interest" readonly>       
                            </div>
                        </td>
                        <td>
                            <div style="width:90px">
                                <input value = `+principalRepaid+` style="width:100%" type="text" name="principal[]"   class="principal" readonly>       
                            </div>
                        </td>
                        <td>
                            <div style="width:90px">
                                <input value = `+principalRemaining.toFixed(0)+` style="width:100%" type="text" name="principalRemaining[]"   class="principalRemaining" readonly>       
                            </div>
                        </td>
                    </tr>
                        `;
                        $('#loanEmiTable tbody').append(appendData);
                        appliedLoan = principalRemaining.toFixed(2);
                        interestAmount = ((principalRemaining * monthlyInterestRate) * 1200).toFixed(2) ;
                        
                     }  
                       
                    break;
                }
            }
        });

        var elements = document.getElementsByClassName("installment");

        $("#cit").on('change', function(){
            var cit = parseFloat($("#cit").val());
            var totalDeduction = parseFloat($("#totalDeductionWithoutCit").val()) + cit ;
            var totalSalary = parseFloat($("#totalSalary").val());
            $('#receivedPercent').val(((totalSalary - totalDeduction)/totalSalary*100).toFixed(2) + ' %');
            $('#receivedWOcit').val(((totalSalary - totalDeduction + cit)/totalSalary*100).toFixed(2) + ' %');
            $('#totalAmount').val(totalDeduction);
            if (((totalSalary - totalDeduction)/totalSalary*100).toFixed(2) < 25){
                document.getElementById("testBtn").disabled = true;
                console.log('if');
                $('#comment').show();}
                else{
                    document.getElementById("testBtn").disabled = false;
                    console.log('else');
                    $('#comment').hide();
                }
        });



        var employeeId = $('#employeeId').val();
        window.app.floatingProfile.setDataFromRemote(employeeId);

        $('#addDocument').on('click', function () {
            if(tbItem>1){
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
            window.app.pullDataById(document.pushFileLink, {
                'fileName': data.fileName,
                'oldFileName': data.oldFileName
            }).then(function (success) {
                if (success.success) {

                    tbItem=tbItem+1;
                    $('#fileDetailsTbl').append('<tr>'
                            + '<input type="hidden" name="fileUploadList" value="' + success.data.fileName + '"><td>' + success.data.oldFileName + '</td>'
                            + '<td><a target="blank" href="' + document.basePath + '/uploads/loan_files/' + success.data.fileName + '"><i class="fa fa-download"></i></a></td>'
                            + '<td><button type="button" class="btn btn-danger deleteFile">DELETE</button></td></tr>');
                }
            }, function (failure) {
            });
        }

        $('#uploadCancelBtn').on('click', function () {
            $('#documentUploadModel').modal('hide');
        });

        $('#fileDetailsTbl').on('click', '.deleteFile', function () {
            tbItem=tbItem-1;
            var selectedtr = $(this).parent().parent();
            selectedtr.remove();
            var rowCount1 = document.getElementById('fileDetailsTbl').rows.length;
        });

        window.app.serverRequest(document.pullFilebyId, {
            'id': document.idForFile
        }).then(function (success) {
            if (success.success) {
                console.log(success.data['FILE_PATH']);
                if(success.data.FILE_PATH == null){ return; }
                $('#fileDetailsTbl').append('<tr>'
                            +'<input type="hidden" name="fileUploadList[]" value="1"><td>' + success.data['FILE_PATH'] + '</td>'
                            + '<td><a target="blank" href="' + document.basePath + '/uploads/loan_files/' + success.data['FILE_PATH']+ '"><i class="fa fa-download"></i></a></td>');

            }
        }, function (failure) { 
            console.log('failed');
        });



        $testBtn.on('click', function(){
            if ($('#receivedPercent').val() < 25){
                console.log($('#receivedPercent'));
                $form.prop('error-message', 'It is Below 25%');
                app.showMessage('It is Below 25%', 'error');
                app.showMessage('It is Below 25%', 'error');
                return false;
            }
            var amnt = $('#receivedPercent').val() > 25;
            var empId = $('#employeeId').val();
            var loanId = $("#loanId").val();
            var period = $("#period").val();
            var loanAmount =  $(".appliedLoan").val();
            var monthlyImstallmentRate =  $("#monthlyInstallmentAmount").val();
            var cit  = parseFloat($("#cit").val());
            // checkForErrors(empId, loanAmount,period,monthlyImstallmentRate,loanId,cit);
        });

        


        var checkForErrors = function (empId, loanAmount, period,loanId, installment, citVal) {
            
            app.pullDataById(document.wsValidateLoanRequest, {empId:empId, loanAmount:loanAmount, period:period,loanId:loanId, installment:installment, citVal:citVal}).then(function (response) {
                if (response.data['ERROR'] == "ALLOW") {
                    $form.prop('valid', 'true');
                    $form.prop('error-message', '');
                } else {
                    $form.prop('valid', 'false');
                    $form.prop('error-message', response.data['ERROR']);
                    app.showMessage(response.data['ERROR'], 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });
        }

        app.setLoadingOnSubmit("loan-form", function ($form) {
            // console.log('afd');
            if ($form.prop('valid') === 'true') {
                return true;
            } else {
                app.showMessage($form.prop('error-message'), 'error');
                return false;
            }
        });

        $('#employeeId').on('change', function(){
            console.log('asdf');
        });
    });


})(window.jQuery, window.app);

