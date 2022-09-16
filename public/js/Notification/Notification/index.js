(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var $markAsViewed = $('.markAsViewed');
        var $markAllAsSeen = $('#markAllAsSeen');

        $markAsViewed.on('click', function (e) {
            e.preventDefault();
            var $this = $(this);
            app.pullDataById(document.markAsViewed, {
                messageId: $this.attr('message-id')
            }).then(function (success) {
                console.log("markAsViewed res", success);
                if (success.success) {
                    location.reload();
                }

            }, function (failure) {
                console.log("markAsViewed fail", failure);
            });

        });
        $markAllAsSeen.on('click', function (e) {
            e.preventDefault();
            var $this = $(this);
            app.pullDataById(document.markAsViewed, {
            }).then(function (success) {
                if (success.success) {
                    location.reload();
                }
            }, function (failure) {
            });

        });



    });

})(window.jQuery, window.app);