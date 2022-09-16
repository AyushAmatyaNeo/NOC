var UITree = function () {
    var n = function (data) {
        $("#tree_3").jstree({
            core: {
                themes: {responsive: !1},
                check_callback: !0,
                data: data
            },
            types: {
                "default": {icon: "fa fa-folder icon-state-success icon-lg"},
                file: {icon: "fa fa-file icon-state-warning icon-lg"}
            },
            state: {key: "demo2"},
            plugins: ["search", "types"]
        });
        var to = false;
        $('#search').keyup(function () {
            if(to) { clearTimeout(to); }
            to = setTimeout(function () {
                var v = $('#search').val();
                $('#tree_3').jstree(true).search(v);
            }, 250);
        });
    };

    return {
        init: function () {
            $("#appraisalTypeId").on("change",function(){
                $('#stageAssign').css('display', 'none');
                var appraisalTypeId = $(this).val();
                console.log(appraisalTypeId);
                window.app.pullDataById(document.url,
                {
                    action:"getHeadingList",
                    data:{appraisalTypeId:appraisalTypeId}
                }
                ).then(function (success) {
                    console.log("success", success.data);
                    n(success.data);
                    $("#tree_3").jstree(true).settings.core.data = success.data;
                    $("#tree_3").jstree(true).refresh();
                }, function (failure) {
                    console.log("failure", failure);
                }); 
            });
        },
        populateTree: n
    }
}();
App.isAngularJsApp() === !1 && jQuery(document).ready(function () {
    UITree.init();
    $('select').select2();
});

