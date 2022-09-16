
function setTemplate(temp) {
    var returnvalue = '';
    if (temp == null || temp == 'null' || typeof temp =='undefined' ) {
        var checkLeaveVal ='';
    }else{
        var checkLeaveVal = temp.slice(0, 2);
    }
    if (temp == 'PR') {
        returnvalue = 'blue';
    }
    else if (temp == 'AB') {
        returnvalue = 'red';
    } else if (checkLeaveVal  == "L-" || checkLeaveVal=="HL") {
        returnvalue = 'green';
    } else if (temp == 'DO') {
        returnvalue = 'yellow';
    } else if (temp == 'HD') {
        returnvalue = 'purple';
    } else if (temp == 'WD') {
        returnvalue = 'purple-soft';
    } else if (temp == 'WH') {
        returnvalue = 'yellow-soft';
    } else if (temp == 'TV') {
        returnvalue = 'green-turquoise';
    } else if (temp == 'O') {
        returnvalue = 'grey';
    }
    return returnvalue;
}


function setAbbr(temp){
    var returnvalue = '';
    if (temp == null || temp == 'null' || typeof temp =='undefined' ) {
        var checkLeaveVal ='';
    }else{
        var checkLeaveVal = temp.slice(0, 2);
    }
    if (temp == 'PR') {
        returnvalue = 'Present';
    }
    else if (temp == 'AB') {
        returnvalue = 'Absent';
    } else if (checkLeaveVal  == "L-") {
        returnvalue = 'On Leave';
    } else if (checkLeaveVal  == "HL") {
        returnvalue = 'On Half Leave';
    } else if (temp == 'DO') {
        returnvalue = 'Day Off';
    } else if (temp == 'HD') {
        returnvalue = 'Holiday';
    } else if (temp == 'WD') {
        returnvalue = 'Work On Day Off';
    } else if (temp == 'WH') {
        returnvalue = 'Work On Holiday';
    }
    return returnvalue;
}

(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, true);
        var $table = $("#report");

        var exportVals;

        var $search = $('#search');
        $search.on('click', function () {
            var data = document.searchManager.getSearchValues();
            data['fromDate'] = $('#fromDate').val();
            data['toDate'] = $('#toDate').val();
            var days =  Math.floor(( Date.parse(data['toDate']) - Date.parse(data['fromDate']) ) / 86400000) + 1;
            data['days'] = days;

            const div = document.getElementById('date');
            div.innerHTML = `
            From : `+ data['fromDate'] + `</br>
            To   ` + `  : `+ data['toDate'] + `
            `;

            app.serverRequest('', data).then(function (response) {
                var leaveDetails=response.data.leaveDetails;

                $table.empty();

                var colsss=[];

                colsss.push({
                    field: 'BRANCH_NAME',
                    title: 'Location',
                    template: '<span><b>#=BRANCH_NAME#</b></span>'
                });

                colsss.push({
                    field: 'DESIGNATION_TITLE',
                    title: 'Designation',
                    template: '<span><b>#=DESIGNATION_TITLE#</b></span>'
                });

                colsss.push({
                    field: 'EMPLOYEE_CODE',
                    title: 'Code',
                    template: '<span><b>#=EMPLOYEE_CODE#</b></span>'
                });

                colsss.push({
                    field: 'FULL_NAME',
                    title: 'Employee',
                    template: '<span style="text-align: left">#=FULL_NAME#</span>'
                });

                for (var i = 1; i <= days; i++) {
                    var temp = 'D' + i;
                    colsss.push({
                        field: temp,
                        title: "" + i,
                        template: '<abbr title="#:setAbbr('+temp+')#"><button type="button" style="padding: 8px 0px 7px;" class="btn btn-block #:setTemplate('+temp+')#">#:(' + temp + ' == null) ? " " :'+temp+'#</button></abbr>'
                    });
                }

                // colsss.push({
                //     field: 'IS_PRESENT',
                //     title: 'PR',
                //     template: '<span style="text-align: left">#=IS_PRESENT#</span>'
                // });
                //
                // colsss.push({
                //     field: 'IS_ABSENT',
                //     title: 'AB',
                //     template: '<span style="text-align: left">#=IS_ABSENT#</span>'
                // });
                //
                // colsss.push({
                //     field: 'ON_LEAVE',
                //     title: 'L',
                //     template: '<span style="text-align: left">#=ON_LEAVE#</span>'
                // });
                //
                // colsss.push({
                //     field: 'HOLIDAY',
                //     title: 'HD',
                //     template: '<span style="text-align: left">#=HOLIDAY#</span>'
                // });
                // colsss.push({
                //     field: 'DAYOFF',
                //     title: 'DO',
                //     template: '<span style="text-align: left">#=IS_DAYOFF#</span>'
                // });
                // colsss.push({
                //     field: 'TRAVEL',
                //     title: 'T',
                //     template: '<span style="text-align: left">#=TRAVEL#</span>'
                // });
                //
                // colsss.push({
                //     field: 'TOTAL',
                //     title: 'Total',
                //     template: '<span style="text-align: left">#=TOTAL#</span>'
                // });

                $table.kendoGrid({
                    dataSource: {
                        data: response.data.data,
                        pageSize: 500
                    },
                    scrollable: false,
                    sortable: false,
                    pageable: false,
                    columns: colsss
                });


            }, function (error) {

            });
        });

        $("#exportPDF").click(function (e) {
            $('#report').css({'overflow-x' : 'visible'});
            kendo.drawing.drawDOM($("#fullReport")).then(function (group) {
                kendo.drawing.pdf.saveAs(group, "Whereabout report.pdf");
            });
            $('#report').css({'overflow-x' : 'auto'});
        });

        $("#exportExcel").click(function (e) {
            exportTableToExcel('fullReport','Whereabout report', 'Whereabout report');
        });

        var exportTableToExcel = function (table, name, filename) {
            let uri = 'data:application/vnd.ms-excel;base64,',
                template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><title></title><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--><meta http-equiv="content-type" content="text/plain; charset=UTF-8"/></head><body><table>{table}</table></body></html>',
                base64 = function (s) {
                    return window.btoa(decodeURIComponent(encodeURIComponent(s)))
                }, format = function (s, c) {
                    return s.replace(/{(\w+)}/g, function (m, p) {
                        return c[p];
                    })
                }

            if (!table.nodeType)
                table = document.getElementById(table)
            var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}

            var link = document.createElement('a');
            link.download = filename;
            link.href = uri + base64(format(template, ctx));
            link.click();

        }


    });
})(window.jQuery, window.app);