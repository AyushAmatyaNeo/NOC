(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        var $search = $('#search');

        var $dateList = $('#dateList');

        var selectedDates = [];

        var $monthsNepali = $('#monthsNepali');



        $search.on('click', function () {
//            console.log(selectedDates);
//            console.log(JSON.stringify(selectedDates));
            app.pullDataById(document.getHireFireReportWS, {'data': JSON.stringify(selectedDates)}).then(function (response) {
                if (response.success) {
                    $(".infomation-data").html('');
//                    console.log(response.data);
                    $.each(response.data, function (index, value) {
                        var employeeData = hiredEmployees(value.DATA);
                        var appendData = '<div class="databox">'
                                + '<div class="month"><h3>' + value.NAME + '</h3><div>'
                                + '<h4 class="title">Hire - ' + value.TOTAL + '</h4>'
                                + '<div class="infobox">'
                                + '<label class="name fontbold">Name</label>'
                                + '<label class="date fontbold">Join Date</label>'
                                + '</div>'+
                                employeeData
                                + '</div>';
                        $(".infomation-data").append(appendData);
//              console.log(value);
                    });

                } else {
                    console.log(response.error);
                }
            }, function (error) {
                console.log(error);
            });

        });

        $monthsNepali.on('change', function () {
            var tempSelectedDate = [];
            var option = $('option:selected', this);
            option.each(function () {
                var $this = $(this);
                if ($this.length) {
                    var selText = $this.text();
                    var tempFromDate=$this.attr('data-fromDate');
                    var tempToDate=$this.attr('data-toDate');
                    var tempArr = {};
                    tempArr['name'] = selText;
                    tempArr['fromDate'] = tempFromDate;
                    tempArr['toDate'] = tempToDate;
                    tempSelectedDate.unshift(tempArr);
                }
            });
            $dateList.empty();
            selectedDates = tempSelectedDate;

            if (tempSelectedDate.length !== 0) {
                $dateList.append("<tr><th>Name</th><th>FromDate</th><th>ToDate</th><tr>");
            }

            $.each(tempSelectedDate, function (index, value) {
                $dateList.append("<tr><th>" + value.name + "</th><th>" + value.fromDate + "</th><th>" + value.toDate + "</th><tr>");
            });



        })


        $("#reset").on("click", function () {
            if (typeof document.ids !== "undefined") {
                $.each(document.ids, function (key, value) {
                    $("#" + key).val(value).change();
                });
            }
        });

        function hiredEmployees(data) {
            var tempHtml = '';
            $.each(data, function (index, value) {
//                console.log(value);
                tempHtml += '<div class="infodata">'
                        + '<label class="name">'
                        + value.FULL_NAME
                        + '</label>'
                        + ' <label class="date">'
                        +  value.JOIN_DATE
                        + '</label>'
                        + '</div>';
            });
            return tempHtml;
        }


    });
})(window.jQuery, window.app);

