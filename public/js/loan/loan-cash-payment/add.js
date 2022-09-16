(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.datePickerWithNepali("paidDate","nepaliDate");
        $("#totalPaid").val($("#principlePaid").val());
        var principlePaid, interestPaid = 0, totalPaid;
        
        $("#paymentAmount").on('change paste input',function(){
            totalPaid = parseInt($("#paymentAmount").val());
            principlePaid = totalPaid - interestPaid;
            totalPaid != '' ? $("#principleAmount").val(principlePaid) : $("#principleAmount").val('') ;
        });

        $("#interest").on('change paste input',function(){
            interestPaid = parseInt($("#interest").val());
            totalPaid = parseInt($("#paymentAmount").val());
            principlePaid = totalPaid - interestPaid;
            totalPaid != '' ? $("#principleAmount").val(principlePaid) : $("#principleAmount").val('') ;
        });

        $('#calculate-interest').on('click', function(){
            var days = $("#days").val();
            var rate = $("#rate").val();
            interestPaid = parseInt(($('#unpaidTotal').val()*rate/100)/365*days);
            $("#interest").val(interestPaid||'');
            totalPaid = $("#paymentAmount").val();
            principlePaid = totalPaid - interestPaid;
            totalPaid != '' ? $("#principleAmount").val(principlePaid) : $("#principleAmount").val('') ;
        });

        $('#paidDate').datepicker("setStartDate", new Date());
    });
})(window.jQuery, window.app);

