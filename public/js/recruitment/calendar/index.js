(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.datePickerWithNepali('Start_dt', 'nepaliFromDate');
        app.datePickerWithNepali('End_dt', 'nepaliToDate');
        // app.populateSelect($('#OpeningNo'), JSON.parse(document.openings) , 'OPENING_ID', 'OPENING_NO', null,null);
        // console.log(document.openings);
        var Vacancy_data = JSON.parse(document.details);
        // console.log(Vacancy_data);
        var $table = $('#calendarTable');
        // Full calendar Event
        var calendar = $('#calendarTable').fullCalendar({
            editable:true,
        header:{
        left:'prev,next today',
        center:'title',
        right:'month,agendaWeek,agendaDay'
        },
        eventClick: function(event) {
            if (event.url) {
                window.open(event.url, "_blank");
                return false;
            }
        },
        events: Vacancy_data,
        eventMouseover: function(Vacancy_data, jsEvent, view) {
            $('.fc-title', this).append('<div class="fc-vacancy"><p> Vacancy Size: '+Vacancy_data.vacancy_no+'</p></div>');
        },
        eventMouseout:function(){
            $('.fc-vacancy').remove();
        },
        
        });
        //Nepali days convert
        $(window).on('load',ChangeDay);
        $(document).on("click", ".fc-next-button", ChangeDay);
        $(document).on("click", ".fc-prev-button", ChangeDay);
        function ChangeDay(){
            var sunday = $('.fc-sun').eq(0).text();
            $('.fc-sun > span').eq(0).append(' /  आइतबार');
            var sunday = $('.fc-mon').eq(0).text();
            $('.fc-mon > span').eq(0).append(' /  सोमबार');
            var sunday = $('.fc-tue').eq(0).text();
            $('.fc-tue > span').eq(0).append(' /  मंगलबार');
            var sunday = $('.fc-wed').eq(0).text();
            $('.fc-wed > span').eq(0).append(' /  बुधबार');
            var sunday = $('.fc-thu').eq(0).text();
            $('.fc-thu > span').eq(0).append(' /  बिहिबार');
            var sunday = $('.fc-fri').eq(0).text();
            $('.fc-fri > span').eq(0).append(' /  शुक्रबार');
            var sunday = $('.fc-sat').eq(0).text();
            $('.fc-sat > span').eq(0).append(' /  शनिबार');       
        };
        // Convert Date and append to cell
        $(window).on("load", changeDate);
        function changeDate(){
            var days = $(document).find('.fc-day').length;
            var daysData = [];
            for(var $i=0;$i < days; $i++){
                var date = $('.fc-week .fc-bg table tbody tr td').eq($i).attr('data-date');
                daysData.push(date);
            }
            
            // console.log((daysData));
        }

        // Nepali Date function
        var n =  getNepaliDateByLabel("2021-08-29");
        console.log(n);
        function getNepaliDateByLabel(elementObj) {
            var x = elementObj;        
            if (x.length > 4) {
                var dateChunks = x.split('-');
                console.log(dateChunks);
                var monthStr = parseInt(dateChunks[1]);
                var dateStr = parseInt(dateChunks[2]);
                var yearStr = parseInt(dateChunks[0]); 

                var nepyearStr = (yearStr + 56);
                var nepmonthStr = monthStr ;
                var nepdateStr = dateStr+ 10;

                console.log(nepyearStr+' '+nepmonthStr+' '+nepdateStr);
            }
        
        }

        // Kendo Report section:
        $('#search').on('click', function () {
            var Start_dt     = $('#Start_dt').val();
            var End_dt       = $('#End_dt').val();
            var OpeningNo    = $('#OpeningNo').val();     

            app.pullDataById('', {
                'Start_dt'  : Start_dt,
                'End_dt'    : End_dt,
                'OpeningNo' : OpeningNo,                
               
            }).then(function (response) {
                if (response.success) {
                    app.renderKendoGrid($table, response.data);
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });

        });
        app.searchTable($table ['OPENING_ID']);
        var exportMap = {                
                'OPENING_ID': 'OpeningId',
                'OPENING_NO' : 'OpeningNo',
                'START_DATE': 'Start_dt',
                'END_DATE': 'End_dt',
                'INSTRUCTION_EDESC': 'Instruction_edesc',
                'INSTRUCTION_NDESC': 'Instruction_ndesc',
                'STATUS' : 'status'
        };
        $('#excelExport').on('click', function () {
            app.excelExport($table, exportMap, 'Opening_report.xlsx');
        });

        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, exportMap, 'Opening_report.pdf');
        });
    })

})(window.jQuery, window.app);