(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("img.lazy").lazyload({
//            effect: "fadeIn",
            threshold: 5000
        });
        
        
        
    });
})(window.jQuery, window.app);