(function ($, app) {
    'use strict';
    $(document).ready(function () {
       $("#BackBtn").click(function(event) {
        event.preventDefault();
        history.back();
    });
  });
})(window.jQuery, window.app);