(function ($, app) {
    'use strict';
    $(document).ready(function () {
        
        $("#menu-form").hide();
        
        $("#add").click(function(){
            $(".delete").hide();
           $("#menuId").val('');
           $("#deleteMenuId").val(menuId);
           $(".edit-cancel").html('Cancel');
           $("#menu-form").show();
           $("#submit").show();
           $(".edit-cancel").hide();
           $(".menu-data").removeAttr('disabled');
           $(".menu-data").val('');
        });
        
        $(document).on('click', '.menu', function(){
           var menuId = $(this).val();
           $("#menuId").val(menuId);
           $("#deleteMenuId").val(menuId);
           $("#menu-form").show();
           $("#submit").hide();
           $(".delete").show();
           $(".menu-data").attr('disabled', 'disabled');
           $(".edit-cancel").html('Edit');
           $(".edit-cancel").show();
           $("#menuName").val(document.menuDetails[menuId-1].MENU_NAME);
           $("#quantity").val(document.menuDetails[menuId-1].QUANTITY);
           $("#rate").val(document.menuDetails[menuId-1].RATE);
           $("#remarks").val(document.menuDetails[menuId-1].REMARKS);
        });
        
        $(".edit-cancel").click(function(){
           var btnText = $(".edit-cancel").html();
           if(btnText.toUpperCase() == 'Edit'.toUpperCase()){
               $(".edit-cancel").html('Cancel');
               $("#submit").show();
               $(".menu-data").removeAttr('disabled');
           }
           if(btnText.toUpperCase() == 'Cancel'.toUpperCase()){
               $(".edit-cancel").html('Edit');
               $("#submit").hide();
               $(".menu-data").attr('disabled', 'disabled');
           }
        });
        
        function addToView(response){
            document.menuDetails.push({
                MENU_ID : response[0].MENU_ID,
                MENU_NAME : response[0].MENU_NAME,
                QUANTITY : response[0].QUANTITY,
                RATE : response[0].RATE,
                REMARKS: response[0].REMARKS
            });
            
            $("#menuTable table").append('<tr><td><button type="button" value="'+document.menuDetails[document.menuDetails.length-1].MENU_ID+'" class="btn btn-link menu" style="border: none;">'+document.menuDetails[document.menuDetails.length-1].MENU_NAME+'</button></td></tr>');
        }
        
        function updateView(data, response){
            document.menuDetails[data.id-1].MENU_ID = response[0].MENU_ID;
            document.menuDetails[data.id-1].MENU_NAME = response[0].MENU_NAME;
            document.menuDetails[data.id-1].QUANTITY = response[0].QUANTITY;
            document.menuDetails[data.id-1].RATE = response[0].RATE;
            document.menuDetails[data.id-1].REMARKS = response[0].REMARKS;
            $("#tr-"+document.menuDetails[data.id-1].MENU_ID+" button").html(document.menuDetails[data.id-1].MENU_NAME);
        }
        
        $("#submit").click(function(){
           if($(".edit-cancel").html().toUpperCase() == 'EDIT'){
               return false;
           }
           else{
               var menuData = new FormData(document.querySelector('#menu-form'));
               //var data = menuData.get('menuName');
               var data = {
                   id : menuData.get('menuId'),
                   menuName : menuData.get('menuName'),
                   quantity : menuData.get('quantity'),
                   rate : menuData.get('rate'),
                   remarks : menuData.get('remarks')
               }
               if(data.menuName == '' || data.quantity == '' || data.rate == '' ){
                   return false;
               }
               app.serverRequest(document.editMenuLink, data).then(function (response) {
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

        $(document).on("keypress", function(e){
            if(e.keyCode == 13 && !$("#remarks").is(":focus")){ return false; }
        });
        
        $(".delete").click(function(){
            return confirm("Confirm delete?") ? true : false ;
//               var id = $("#menuId").val();
//               app.serverRequest(document.deleteMenuLink, {id: id}).then(function (response) {
//                if (response.success) {
//                    app.showMessage(response.message);
//                    window.location.reload();
//                } else {
//                    app.showMessage(response.error, 'error');
//                }
//                }, function (error) {
//                    app.showMessage(error, 'error');
//                });
           
        });
    });
})(window.jQuery, window.app);
