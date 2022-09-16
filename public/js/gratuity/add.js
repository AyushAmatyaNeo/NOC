(function ($, app) {
    $(document).ready(function () {
        $('select').select2();


        app.datePickerWithNepali('form-retirementDate','nepaliRetirementDate');

        var $print = $('#print');
        $print.on('click', function () {
            app.exportDomToPdf('printableArea', document.urlCss);
        });
    });

})(window.jQuery, window.app);
