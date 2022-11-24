
(function ($, app) {
    $(document).ready(function () {
        $("select").select2();
        console.log('here');
        $('#exportPdfData').on('click', function () {
            kendo.drawing.drawDOM($("#rootwizard")).then(function (group) {
                kendo.drawing.pdf.saveAs(group, "UserApplication_"+ document.userData['FIRST_NAME'] +".pdf");
            });
        });

        // app.populateSelect($('#stageId'), document.Stages , 'REC_STAGE_ID', 'STAGE_EDESC', null,null);
        $('#dxtcfyvgubionm').click(function() {
            var stage_id = $('#StageId').val();
            var remarksNp = $("#remarksNp").val();
            // var remarksEn = $("#remarksEn").val();
            var url = window.location.href;
            var id = parseInt(url.substring(url.lastIndexOf('/') + 1));
            var selectedValues = [];
            // var val = $('#InclusionId').val();
            // selectedValues.push({
            //     StageId: stage_id,
            //     remarks : remarks,
            //     id: id,
            //     inclusion: val,
            // });
            // app.bulkServerRequest(document.bulkStageIdWS, selectedValues, function () {
            //     window.location.reload(true);
            // }, function (data, error) {
                
            // }); 
            selectedValues.push({
                StageId: stage_id,
                remarksNp : remarksNp,
                id: id,
            });
            app.bulkServerRequest(document.bulkStageIdWS, selectedValues, function () {
                window.location.reload(true);
            }, function (data, error) {
                
            }); 
        });
       

    });
})(window.jQuery, window.app);