(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        
         app.addDatePicker($('#citizenshipIssueDate'));

        var addrPermZoneId = $('#permanentZoneId');
        var addrPermDistrictId = $('#permanentDistrictId');
        var addrTempZoneId = $('#temporaryZoneId');
        var addrTempDistrictId = $('#temporaryDistrictId');
        
        
        var onChangePermZone = function (zoneId) {
            if (zoneId == null) {
                app.populateSelectElement(addrPermDistrictId, []);
                return;
            }
            app.pullDataById(document.urlDistrict, {id: zoneId}).then(function (data) {
                app.populateSelectElement(addrPermDistrictId, data, document.addressValues.perDisValue);
            }, function (error) {
                console.log("url=>" + document.urlDistrict, error);
            });
        };

        var onChangeTempZone = function (zoneId) {
            if (zoneId == null) {
                app.populateSelectElement(addrTempDistrictId, []);
                return;
            }
            app.pullDataById(document.urlDistrict, {id: zoneId}).then(function (data) {
                app.populateSelectElement(addrTempDistrictId, data, document.addressValues.tempDisValue);
            }, function (error) {
                console.log("url=>" + document.urlDistrict, error);
            });
        };


        addrPermZoneId.on('change', function () {
            var $this = $(this);
            onChangePermZone($this.val());
        });

        addrTempZoneId.on('change', function () {
            var $this = $(this);
            onChangeTempZone($this.val());
        });


        if (document.editPage) {
            onChangePermZone(document.addressValues.perZoneValue);
            onChangeTempZone(document.addressValues.tempZoneValue);
        }


    });
})(window.jQuery, window.app);