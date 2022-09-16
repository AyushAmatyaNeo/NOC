(function($,ap){
    $(document).ready(function(){
       $('select').select2(); 
       $('#form-constraintValue').combodate({
            minuteStep: 1
        });
//       app.setLoadingOnSubmit("preferenceSetup-form")
       
       app.setLoadingOnSubmit("preferenceSetup-form",function(){
          var consValue=$('#form-constraintValue').val();
           console.log(consValue);
           if(consValue==''){
               $('#errMsgPC').text('this field is required');
                return false;
           }else{
                return true;
           }
           
       });

       
       
       
    });
})(window.jQuery,window.app);

