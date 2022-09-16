(function ($, app) {
    //    'use strict';
        $(document).ready(function () {
            $('select').select2();    
            
            $(document).on('change','.VacancyReservationNo',CheckReservationNo);


            function CheckReservationNo(){
                var total = 0;
                var oid = $("#OpeningId").val();
                console.log(oid)
                for (let i in document.OpeningVacancyNo) {
                    if (document.OpeningVacancyNo[i].OPENING_ID == oid) {
                        var totalrservation = document.OpeningVacancyNo[i].RESERVATION_NO;
                        console.log(totalrservation);
                        totalrservation = (totalrservation == null) ? 0 : totalrservation;
                    }
                }
                jQuery.extend({
                    getValues: function (url) {
                        $.ajax({
                            url: url,
                            data: { 'oid': oid },
                            type: 'POST',
                            async: false,
                            success: function (data) {
                                result = data;
                            }
                        });
                        return result;
                    }
                });
                var total = parseInt($('.VacancyReservationNo').val());
                // $(".VacancyReservationNo").each(function() {
                //     var quantity = parseInt($(this).val());
                    
                //     total += quantity;
                //     // console.log(total);
                // });
                var Reservation_data = $.getValues(document.CheckReserNo);
                Reservation_data.data = (Reservation_data.data == null) ? 0 : Reservation_data.data;
                var rem_quota = (totalrservation - Reservation_data.data);
                var remaining_reservation = (totalrservation - Reservation_data.data - total);
        
                if(remaining_reservation < 0){
                    toastr.error('Reservation Number exceeded..!!');
                    $('#rem_Reservation_no').html('Remaining Reservation: '+ rem_quota)
                    $('input[name="submit"]').attr('disabled', true);
                    $('#rem_Reservation_no').css('border','2px solid red')
                }else{
                    // $('#rem_Reservation_no').html('Remaining Reservation No: '+ rem_quota)
                    $('#rem_Reservation_no').html('');
                    $('input[name="submit"]').attr('disabled', false);
                    $('#rem_Reservation_no').css({ "border-style": "none", "font-size": "", "padding-left": "","border": '' })
                }
                console.log('Remainng quota - ' + remaining_reservation);
            }
        });
       
    })(window.jQuery, window.app);
    
    
    