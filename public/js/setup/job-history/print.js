(function ($, app) {
    'use strict';
    $(document).ready(function () {
        // $('select').select2();
        // app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate');

        var $close = $("#close");
        var $pdfExport = $("#pdfExport");
        var $body = $("#body");
        // var $ignore = $("#ignore");
        
        var pdf = new jsPDF('p', 'pt','a4');
        var specialElementHandlers = {
            '#ignore': function (element, renderer) {
                return true;
            }
        };
        
        $pdfExport.on('click', function () {
            pdf.fromHTML($body.html(),20,100,{
                'width': 540,
                'elementHandlers': specialElementHandlers
            });

            // pdf.text(, 10,10, {maxWidth: 185, align: "justify"})
            pdf.save("Letter.pdf");
            
        });
        $close.on('click', function () {
            window.close();
        });
        
    });
    //var $pdfExport = $("#pdfExport");
    // $("body").on("click", "#pdfExport", function () {
    //     console.log("fkdlsfklas");
    //     html2canvas($('#print-data')[0], {
    //         onrendered: function (canvas) {
    //             var data = canvas.toDataURL();
    //             var docDefinition = {
    //                 content: [{
    //                     image: data,
    //                     width: 500
    //                 }]
    //             };
    //             pdfMake.createPdf(docDefinition).download("customer-details.pdf");
    //         }
    //     });
    // });
})(window.jQuery, window.app);