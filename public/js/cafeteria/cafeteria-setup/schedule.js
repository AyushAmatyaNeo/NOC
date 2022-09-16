(function ($, app) {
    'use strict';
    $(document).ready(function () {
        
        $("#time-form").hide();
        
        $("#add").click(function(){
            $(".delete").hide();
           $("#timeId").val('');
           $(".edit-cancel").html('Cancel');
           $("#time-form").show();
           $("#submit").show();
           $(".edit-cancel").hide();
           $(".time-data").removeAttr('disabled');
           $(".time-data").val('');
        });
        
        $(document).on('click', '.time', function(){
           var timeId = $(this).val();
           $("#timeId").val(timeId);
           $("#deleteTimeId").val(timeId);
           $("#time-form").show();
           $("#submit").hide();
           $(".delete").show();
           $(".time-data").attr('disabled', 'disabled');
           $(".edit-cancel").html('Edit');
           $(".edit-cancel").show();
           $("#timeName").val(document.scheduleDetails[timeId-1].TIME_NAME);
           $("#remarks").val(document.scheduleDetails[timeId-1].REMARKS);
        });
        
        $(".edit-cancel").click(function(){
           var btnText = $(".edit-cancel").html();
           if(btnText.toUpperCase() == 'Edit'.toUpperCase()){
               $(".edit-cancel").html('Cancel');
               $("#submit").show();
               $(".time-data").removeAttr('disabled');
           }
           if(btnText.toUpperCase() == 'Cancel'.toUpperCase()){
               $(".edit-cancel").html('Edit');
               $("#submit").hide();
               $(".time-data").attr('disabled', 'disabled');
           }
        });
        
        function addToView(response){
            document.scheduleDetails.push({
                TIME_ID : response[0].TIME_ID,
                TIME_NAME : response[0].TIME_NAME,
                REMARKS: response[0].REMARKS
            });
            
            $("#timeTable table").append('<tr><td><button type="button" value="'+document.scheduleDetails[document.scheduleDetails.length-1].TIME_ID+'" class="btn btn-link time" style="border: none;">'+document.scheduleDetails[document.scheduleDetails.length-1].TIME_NAME+'</button></td></tr>');
        }
        
        function updateView(data, response){
            document.scheduleDetails[data.id-1].TIME_ID = response[0].TIME_ID;
            document.scheduleDetails[data.id-1].TIME_NAME = response[0].TIME_NAME;
            document.scheduleDetails[data.id-1].REMARKS = response[0].REMARKS;
            $("#tr-"+document.scheduleDetails[data.id-1].TIME_ID+" button").html(document.scheduleDetails[data.id-1].TIME_NAME);
        }

        $(document).on("keypress", function(e){
            if(e.keyCode == 13 && !$("#remarks").is(":focus")){ return false; }
        });
        
        $("#submit").click(function(){
           if($(".edit-cancel").html().toUpperCase() == 'EDIT'){
               return false;
           }
           else{
               var timeData = new FormData(document.querySelector('#time-form'));
               //var data = timeData.get('timeName');
               var data = {
                   id : timeData.get('timeId'),
                   timeName : timeData.get('timeName'),
                   remarks : timeData.get('remarks')
               }
               if(data.timeName == ''){
                   return false;
               }
               app.serverRequest(document.editScheduleLink, data).then(function (response) {
                if (response.success) {
                    if(data.id == ''){
                        addToView(response.data);
                    }
                    else{
                        updateView(data, response.data);
                    }
                    app.showMessage(response.message);
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });
           }
        });
        
        $(".delete").click(function(){
            return confirm("Confirm delete?") ? true : false ;
        });
    });
})(window.jQuery, window.app);
