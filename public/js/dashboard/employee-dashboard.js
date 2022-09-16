(function ($, app) {
    'use strict';
    $(document).ready(function () {
        // Index.init();
        // Index.initCalendar();

        $("img.lazy").lazyload({
//            effect: "fadeIn",
            threshold: 5000
        });

        if (!$().fullCalendar) {
            return;
        }

        var date = new Date();
        var d = date.getDate();
        var m = date.getMonth();
        var y = date.getFullYear();

        var h = {};

        if ($('#calendar').width() <= 400) {
            $('#calendar').addClass("mobile");
            h = {
                left: 'title, prev, next',
                center: '',
                right: 'today,month,agendaWeek,agendaDay'
            };
        } else {
            $('#calendar').removeClass("mobile");
            if (Metronic.isRTL()) {
                h = {
                    right: 'title',
                    center: '',
                    left: 'prev,next,today,month,agendaWeek,agendaDay'
                };
            } else {
                h = {
                    left: 'title',
                    center: '',
                    right: 'prev,next,today,month,agendaWeek,agendaDay'
                };
            }
        }

        var isViewLoading = true;
        $('#calendar').fullCalendar('destroy'); // destroy the calendar
        $('#calendar').fullCalendar({//re-initialize the calendar
            //defaultDate: '2017-03-12',
            disableDragging: false,
            viewRender: function (view, element) {
                $('#calendar .html-loading').remove();
                var intervalId = setInterval(function () {
                    var $dataHtml = element.find('.fc-content-skeleton');
                    if (!isViewLoading && $dataHtml) {
                        clearInterval(intervalId);
                        $dataHtml.find('tbody').each(function (k, v) {
                            var $attnRow = $(v).find('tr:eq(0)');
                            if ('undefined' != typeof ($attnRow)) {
                                $attnRow.find('.fc-title').html(function (i, inOutTxt) {
                                    var inOutTime = inOutTxt.split(' ');
                                    if (inOutTime.length) {
                                        var output = "";
                                        if (inOutTime[0]) {
                                            output += '<span class="fc-title-in" style="padding:1px 3px;">' + inOutTime[0] + '</span>';
                                        }
                                        if (inOutTime[1]) {
                                            output += '<span class="fc-title-out" style="padding:1px 3px;">' + inOutTime[1] + '</span>';
                                        }
                                        if (output) {
                                            return output;
                                        }
                                    }
                                })
                            }
                            var $eventRow = $(v).find('tr:gt(0)');
                            if ('undefined' != typeof ($eventRow)) {
                                $eventRow.find('.fc-title').css('color', '#fff');
                            }
                        });
                    }
                }, 100);
            },
            eventLimit: true,
            header: h,
            editable: false,
            cache: false,
            data: {},
            events: {
                url: document.calendarJsonFeedUrl,
                type: 'POST',
                error: function () {

                }
            },
            loading: function (isLoading, view) {
                isViewLoading = isLoading;
            }
        });
    });

    /*************** BIRTHDAY TAB CLICK EVENT ***************/
    $('.task-list').slimScroll({
        height: '200px'
    });
    $('.tab-pane-birthday').slimScroll({
        height: '300px'
    });
    $('.upcomingholidays').slimScroll({
        height: '218px'
    });

    $('.ln-nav-tab-birthday').on('click', function (e) {
        e.preventDefault();
        $('.ln-birthday').removeClass('active');
        $('.ln-birthday a').attr('aria-expanded', 'false');
        $(this).attr('aria-expanded', 'true');
        $(this).parent('li').addClass('active');
        if ($(this).is('#ln-birthday-today')) {
            $('#tab-birthday-upcoming').hide().removeClass('active');
            $('#tab-birthday-today').show().addClass('active');
        } else {
            $('#tab-birthday-today').hide().removeClass('active');
            $('#tab-birthday-upcoming').show().addClass('active');
        }
    });

    $('#task-save').on('click', function (e) {
        var taskDate = $.trim($('#inp-task-dt').val());
        var taskName = $.trim($('#inp-task-name').val());
        if (taskDate && taskName) {
            var taskLi =
                    '<li>' +
                    '<div class="task-list-content clearfix">' +
                    '<h4>' + taskName + '</h4>' +
                    '<div class="positon">' + $('.employee-details span:eq(1)').text() + '</div>' +
                    '<div class="name">' + $('.employee-details h4').text() + '</div>' +
                    '</div>' +
                    '</li>';

            if ($('.task-list').length) {
                $('.task-list ul').append(taskLi);
            } else {
                $('.notask').remove();
                $('.portlet-inner-task-wrapper').append('<div class="task-list"><ul>' + taskLi + '</ul></div>')
            }
        }
        $('#inp-task-dt').val('');
        $('#inp-task-name').val('');
    });

    ComponentsPickers.init();


//        console.log('sdfdsf');


    window.app.pullDataById(document.getEmpDashboardUrl, {
        action: 'fetchEmployeeDashBoardDetails',
    }).then(function (success) {
        var empData = success.data;
        if (empData['PRESENT_DAY'] != null) {
            $('#employeePresentDays').text(empData['PRESENT_DAY']);  //present 
        }
        if (empData['LEAVE'] != null) {
            $('#employeeLeaveDays').text(empData['LEAVE']);  //on leave
        }
        if (empData['TRAINING'] != null) {
            $('#employeeTrainingDays').text(empData['TRAINING']);  //on training
        }
        if (empData['TOUR'] != null) {
            $('#employeeTravelDays').text(empData['TOUR']);  // on tour
        }
        if (empData['WOH'] != null) {
            $('#employeeWOHDays').text(empData['WOH']);  // on woh
        }
        if (empData['LATE_IN'] != null) {
            $('#employeeLateInDays').text(empData['LATE_IN']);  //  late in
        }
        if (empData['EARLY_OUT'] != null) {
            $('#employeeEarlyOutDays').text(empData['EARLY_OUT']);  //  early out
        }
        if (empData['MISSED_PUNCH'] != null) {
            $('#employeeMissPunch').text(empData['MISSED_PUNCH']);  //  miss punch
        }
        
        if (empData['FULL_NAME']!= null) {
        $('#employeeFullName').text(empData['FULL_NAME']);   //full name
        }

        
        if (empData['EMAIL_OFFICIAL'] != null) {
            $('#employeeOfficialEmail').text(empData['EMAIL_OFFICIAL']);  //  email
        } else {
            $('#employeeOfficialEmail').next('br').remove();
        }
        if (empData['DESIGNATION_TITLE'] != null) {
            $('#employeeDesignationTitle').text(empData['DESIGNATION_TITLE']);  //  designation Title
        } else {
            $('#employeeDesignationTitle').next('br').remove();
        }
        if (empData['FILE_PATH'] != null) {
            $('#employeeImage').attr("src", document.basePath + '/uploads/' + empData['FILE_PATH']);
        }


        var year = ' ';
        var month = ' ';
        var days = ' ';
        if (empData['SERVICE_YEARS'] != 0 && empData['SERVICE_YEARS'] != null) {
            if (empData['SERVICE_YEARS'] == 1) {
                year = empData['SERVICE_YEARS'] + ' Year ';
            } else {
                year = empData['SERVICE_YEARS'] + ' Years ';
            }
        }
        if (empData['SERVICE_MONTHS'] != 0 && empData['SERVICE_MONTHS'] != null) {
            if (empData['SERVICE_MONTHS'] == 1) {
                month = empData['SERVICE_MONTHS'] + ' Month ';
            } else {
                month = empData['SERVICE_MONTHS'] + ' Months ';
            }
        }
        if (empData['SERVICE_DAYS'] != 0 && empData['SERVICE_DAYS'] != null) {
            if (empData['SERVICE_DAYS'] == 0) {
                days = empData['SERVICE_DAYS'] + ' Day';
            } else {
                days = empData['SERVICE_DAYS'] + ' Days';
            }
        }


        var empServiceDate = "At work for : " + year + month + days;
        $('#employeeServiceDate').text(empServiceDate);  //  service




    }, function (failure) {
        console.log(failure);
    });


})(window.jQuery, window.app);