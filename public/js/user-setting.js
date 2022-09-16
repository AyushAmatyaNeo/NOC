(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var $enableNotificaion = $('#enableNotification');
        var $enableEmail = $('#enableEmail');
        var setting = {};

        (function ($n, $e) {
            app.serverRequest(document.settingUrl, {
                test: 'test'
            }).then(function (success) {
                setting = success[0];
                $enableNotificaion.bootstrapSwitch({
                    state: setting["ENABLE_NOTIFICATION"] === "Y",
                    onSwitchChange: function () {
                        var $this = $(this);
                        app.serverRequest(document.updateSettingUrl, {
                            "ENABLE_NOTIFICATION": $this.bootstrapSwitch("state") ? "Y" : "N"
                        }).then(function (success) {
                            console.log('success', success);
                        }, function (failure) {
                            console.log('failure', failure);
                        });

                    }});
                $enableEmail.bootstrapSwitch({
                    state: setting["ENABLE_EMAIL"] === "Y",
                    onSwitchChange: function () {
                        var $this = $(this);
                        app.serverRequest(document.updateSettingUrl, {
                            "ENABLE_NOTIFICATION": $this.bootstrapSwitch("state") ? "Y" : "N"
                        }).then(function (success) {
                            console.log('success', success);
                        }, function (failure) {
                            console.log('failure', failure);
                        });
                    }});
            }, function (failure) {
                console.log('setting fail', failure);
            });
        })($enableNotificaion, $enableEmail);
    });
})(window.jQuery, window.app);