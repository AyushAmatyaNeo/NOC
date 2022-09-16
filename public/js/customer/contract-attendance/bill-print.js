(function ($) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        var $cutomerSelect = $('#customerSelect');
        var $monthSelect = $('#monthSelect');
        app.populateSelect($cutomerSelect, document.customerList, 'CUSTOMER_ID', 'CUSTOMER_ENAME', 'Select An Customer', '');
        app.populateSelect($monthSelect, document.monthList, 'MONTH_ID', 'MONTH_EDESC', 'Select An Month', '');

        $('#hiddenDiv').hide();

        $('#printContract').on('click', function () {
            app.exportDomToPdf2('printDiv');
        });


        $('#billBtn').on('click', function () {
            var selectedCustomerVal = $cutomerSelect.val();
            var selectedMonthVal = $monthSelect.val();

            if (selectedCustomerVal == '' || selectedMonthVal == '') {
                app.errorMessage('Customer or Month is not selected ', 'error');
                return;
            }
            $("#employeeTbl tbody").remove();








            app.serverRequest(document.pullMonthlyBillCustomerWise, {
                customerId: selectedCustomerVal,
                monthId: selectedMonthVal

            }
            ).then(function (response) {

                console.log(response);
                var responseData = response.data.attendnaceDetails;

                var headerData = `<tr>
                                    <th>S.N</th>
                                    <th>Employee</th>
                                    <th>Location</th>
                                    <th>Designation</th>
                                    <th>PresentDays</th>
                                    <th>MonthlyDays</th>
                                    <th>Rate</th>
                                    <th>Amt</th>
                
                                </tr>`;

                if (responseData.length > 0) {
                    $('#hiddenDiv').show();
                    $('#employeeTbl').append(headerData);
                    $('.customerName').html($('#customerSelect :selected').text());
                    $('#monthName').html($('#monthSelect :selected').text());
                } else {
                    $('#hiddenDiv').hide();
                    $('.customerName').html('');
                    $('#monthName').html('');
                    app.showMessage('No Available Date For The Month', 'info', 'Data');
                }


                var sn = 1;
                var totalAmt = 0;
                $.each(responseData, function (key, value) {
                    var appendData = `<tr>
                                    <td>` + sn + `</td>
                                    <td>` + value.FULL_NAME + `</td>
                                    <td>` + value.LOCATION_NAME + `</td>
                                    <td>` + value.DESIGNATION_TITLE + `</td>
                                    <td>` + value.PRESENT_DAYS + `</td>
                                    <td>` + value.DAYS_IN_MONTH + `</td>
                                    <td>` + value.RATE + `</td>
                                    <td>` + Math.trunc((value.RATE / value.DAYS_IN_MONTH) * value.PRESENT_DAYS) + `</td>
                                </tr>`;
                    $('#employeeTbl').append(appendData);
                    totalAmt += Math.ceil((value.RATE / 30) * value.PRESENT_DAYS);
                    sn++;


                });

                var appendfooterData = `<tr>
                                    <th rowspan='6'>Total:</th>
                                    <th>` + totalAmt + `</th>
                                </tr>`;
                $('#employeeTbl').append(appendfooterData);




            });

        });



    });
})(window.jQuery);