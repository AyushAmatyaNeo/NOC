(function ($, app) {
    //    'use strict';
        $(document).ready(function () {
            $('select').select2();    
            app.datePickerWithNepali('EffectiveDate', 'nepaliStartDate');
        });

        var inputFieldId = "FunctionalLevelId";
        var formId = "vacancylevel";
        var tableName =  "HRIS_REC_VACANCY_LEVELS";
        var columnName = "FUNCTIONAL_LEVEL_ID";
        var checkColumnName = "FUNCTIONAL_LEVEL_ID";
        var selfId = $("#FunctionalLevelId").val();
        if (typeof(selfId) == "undefined"){
            selfId=0;
        }
        window.app.checkUniqueConstraints(inputFieldId,formId,tableName,columnName,checkColumnName,selfId);

        // $('#PositionId').on('change',LevelValue);
        // LevelValue();
        // $('#PositionId').on('change',LevelValue)
        

        function LevelValue(){
            var pos = $('#PositionId').val();
            app.pullDataById(document.pullLevelId, {
                'designation_id'  : pos            
            }).then(function (response) {
                if (response.success) {
                    console.log(Object.values(response.data));
                    // console.log((response.data).length);
                    $('#FunctionalLevelId option').remove();                                
                    for(var i=0; i <= ((response.data).length); i++ ) {
                        if((parseInt(response.data[i].FUNCTIONAL_LEVEL_ID) <= 8) || (parseInt(response.data[i].FUNCTIONAL_LEVEL_ID) == 16)){
                            $('#FunctionalLevelId').append('<option value="'+response.data[i].FUNCTIONAL_LEVEL_ID+'">'+ response.data[i].FUNCTIONAL_LEVEL_NO +'</option>');
                            //break;
                        }
                        else{
                            $('#FunctionalLevelId').append('<option value="'+response.data[i].FUNCTIONAL_LEVEL_ID+'">'+ response.data[i].FUNCTIONAL_LEVEL_NO +'</option>')
                        }                        
                    }
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });
        }
    })(window.jQuery, window.app);
    
    
    