(function ($, app, formulaWriter) {
    'use strict';
    $(document).ready(function () {
        var $form = $('#rules');
        var $formula = $('#formula');
        var editor = formulaWriter('formula', formulaData);
        $form.on('submit', function () {
            var value = editor.getValue()
            if (value == '') {
                app.showMessage('Formula is Required.', 'error');
                return false;
            }
            return true;
        });
    });

})(window.jQuery, window.app, window.formulaWriter);