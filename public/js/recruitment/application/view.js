
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
            // console.log(inclusionCheckbox.length);
            // for(var i = 0; i < inclusionCheckbox.length; i++){
            //     console.log($(inclusionCheckbox[i]).val());
            //     console.log($(inclusionCheckbox[i]).checked);
            // }
            var selectedInclusions    = [];
            $.each($("input:checkbox[name=approvedInclusion]:checked"), function(){
                selectedInclusions.push($(this).val());
            });
           
            var unSelectedInclusions    = [];
            console.log(selectedInclusions);
            $.each($("input:checkbox[name=approvedInclusion]:not(:checked)"), function(){
                unSelectedInclusions.push($(this).val());
            });
            var stage_id = $('#StageId').val();
            if(stage_id==0){
                alert("Please select stage!!");
            }else{
                // if(selectedInclusions.length < 1){
                //     alert("Atleast 1 inclusion should be selected!!");
                // }else{
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
                        unSelectedInclusions: unSelectedInclusions,
                        selectedInclusions: selectedInclusions,
                    });
                    app.bulkServerRequest(document.bulkStageIdWS, selectedValues, function () {
                        // window.location.reload(true);
                        window.location.href = "../../userapplication";
                    }, function (data, error) {
                        
                    }); 
                // }
                
            }
            
        });
       

    });
})(window.jQuery, window.app);