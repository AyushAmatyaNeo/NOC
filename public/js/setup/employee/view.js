(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("#printAsPDF").click(function (e) {
            // console.log("hellow");
            var divToPrint = document.getElementById('quaConId');

            var newWin = window.open('', 'Print-Window');

            newWin.document.open();

            newWin.document.write('<html><body onload="window.print()">' + divToPrint.innerHTML + '</body></html>');

            newWin.document.close();

            setTimeout(function () {
                newWin.close();
            }, 10);
        });

        var doc = new jsPDF();
        var specialElementHandlers = {
            '#editor': function (element, renderer) {
                return true;
            }
        };

        $('#cmd').click(function () {
            doc.fromHTML($('#rootwizard').html(), 15, 15, {
                'width': 170,
                'elementHandlers': specialElementHandlers
            });
            doc.save('sample-file.pdf');
        });
        
        
   
        $('#exportPdf').on('click', function () {
            kendo.drawing.drawDOM($("#rootwizard")).then(function (group) {
                kendo.drawing.pdf.saveAs(group, "EmployeeDetails.pdf");
            });
        });
        
        
        
        
        
        
        
    });
})(window.jQuery, window.app);
