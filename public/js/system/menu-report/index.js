(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();

        var menuList = [];
        var roleList = document.roleList;

        var $menuContainer = $('#menu-container');
        var $roleList = $('#role-list');
        app.populateSelect($roleList, roleList, 'ROLE_ID', 'ROLE_NAME', 'Select a Role');
        $roleList.on('change', function () {
            var $this = $(this);
            var value = $this.val();
            if (value !== '') {
                app.pullDataById(document.fetchRoleWiseMenuUrl, {roleId: value}).then(function (response) {
                    if (response.success) {
                        menuList = response.data;
                        $menuContainer.html('');
                        var $element = $('<div></div>');
                        $element.jstree({'core': {'data': menuList}});
                        $menuContainer.append($element);

                    }
                }, function (failure) {

                });

            }
        });


    });

})(window.jQuery, window.app);