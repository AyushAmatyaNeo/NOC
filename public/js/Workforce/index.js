(function ($, app) {
    
    $(document).ready(function () {
        $("#departmentDiv").hide();

        app.populateSelect($('.branch'), document.branchList, 'LOCATION_ID', 'LOCATION_EDESC', '-select-',null, 1, true, false);
        app.populateSelect($('.position'), document.positionList, 'FUNCTIONAL_LEVEL_ID', 'FUNCTIONAL_LEVEL_EDESC', '-select-',null, 1, true, false); 
        app.populateSelect($('.group'), document.groupList, 'SERVICE_GROUP_ID', 'SERVICE_GROUP_NAME', '-----',null, 1, false, false); 
        app.populateSelect($('.service'), document.serviceList, 'SERVICE_TYPE_ID', 'SERVICE_TYPE_NAME', '-----',null, 1, false, false); 
        app.populateSelect($('.designation'), document.designationList, 'DESIGNATION_ID', 'DESIGNATION_TITLE', '-----',null, 1, false, false);     
        var position_id = $(".position:first").val();
        var group_id = $(".group:first").val();
        var service_id = $(".service:first").val();
        var designation_id = $(".designation:first").val();
        var location_id = $(".branch").val();
        var department_id = $(".department").val();
        data = {
            'position' : position_id,
            'group' : group_id,
            'service' : service_id,
            'designation' : designation_id,
            'location' : location_id,
            'department' : department_id
        };
        app.pullDataById(document.getPastValues,data).then(function (success){
            if(success.success){
                if(success.data.length != 0){
                    $('#workforceTable').find("tr:gt(1)").remove();
                    success.data.forEach(addPastRows);
                }else{
                    var position_id = $(".position:first").val();
                    var group_id = $(".group:first").val();
                    var service_id = $(".service:first").val();
                    var designation_id = $(".designation:first").val();
                    var location_id = $(".branch").val();
                    var department_id = $(".department").val();
                    data = {
                        'position' : position_id,
                        'group' : group_id,
                        'service' : service_id,
                        'designation' : designation_id,
                        'location' : location_id,
                        'department' : department_id
                    };
                    app.pullDataById(document.getCurrentStaff, data).then(function (success) {
                        if (success.success) {
                            $(".current:first").val(success.data);
                        }
                    });
                }
                
            }
        });  

        $(document).on('change', ".branch", function (){
            
            $('#workforceTable').find("tr:gt(2)").remove();
            $('.quota:first').val(null);
            
            if($(".branch").val()==26){
                $("#departmentDiv").show();
                app.populateSelect($('.department'), document.departmentList, 'DEPARTMENT_ID', 'DEPARTMENT_NAME', '-select-',null, 1, true, false);
            }else{
                $("#departmentDiv").hide();
                app.populateSelect($('.department'), null, null, null, null, null, null, null, false); 
            }

            var position_id = $(".position:first").val();
            var group_id = $(".group:first").val();
            var service_id = $(".service:first").val();
            var designation_id = $(".designation:first").val();
            var location_id = $(".branch").val();
            var department_id = $(".department").val();
            data = {
                'position' : position_id,
                'group' : group_id,
                'service' : service_id,
                'designation' : designation_id,
                'location' : location_id,
                'department' : department_id
            };
            app.pullDataById(document.getCurrentStaff, data).then(function (success) {
                if (success.success) {
                    $(".current:first").val(success.data);
                    $('.vacant:first').val($('.quota:first').val()-$('.current:first').val());
                }
            });
            var position_id = $(".position:first").val();
            var group_id = $(".group:first").val();
            var service_id = $(".service:first").val();
            var designation_id = $(".designation:first").val();
            var location_id = $(".branch").val();
            var department_id = $(".department").val();
            data = {
                'position' : position_id,
                'group' : group_id,
                'service' : service_id,
                'designation' : designation_id,
                'location' : location_id,
                'department' : department_id
            };
            app.pullDataById(document.getPastValues,data).then(function (success){
                if(success.success){
                    if(success.data.length != 0){
                        $('#workforceTable').find("tr:gt(1)").remove();
                        success.data.forEach(addPastRows);
                    }else{
                        var position_id = $(".position:first").val();
                        var group_id = $(".group:first").val();
                        var service_id = $(".service:first").val();
                        var designation_id = $(".designation:first").val();
                        var location_id = $(".branch").val();
                        var department_id = $(".department").val();
                        data = {
                            'position' : position_id,
                            'group' : group_id,
                            'service' : service_id,
                            'designation' : designation_id,
                            'location' : location_id,
                            'department' : department_id
                        };
                        app.pullDataById(document.getCurrentStaff, data).then(function (success) {
                            if (success.success) {
                                $(".current:first").val(success.data);
                                $('.vacant:first').val($('.quota:first').val()-$('.current:first').val());
                            }
                        });
                    }
                    
                }
            });
            
        });
        function addPastRows(item) {
            var appendData = `
            <tr>
                <td><input class="dtlDelBtn btn btn-danger" type="button" value="Del -" style="padding:3px;"></td>
                <td>
                    <div style="width:90px">
                        <select class='position' name='position[]' style="width:100%">
                        </select>
                    </div>
                </td>
                <td>
                    <div style="overflow:hidden">
                        <select class='group' name='group[]' >
                        </select>       
                    </div>
                </td>
                <td>
                    <div style="overflow:hidden">
                        <select class='service' name='service[]' >
                        </select>      
                    </div>
                </td>
                <td>
                    <div style="overflow:hidden">
                        <select class='designation' name='designation[]' >
                        </select>
                    </div>
                </td>       
                <td>
                    <div style="width:80px">
                        <input style="width:100%" type="number" name="quota[]"   class="quota">       
                    </div>
                </td>
                <td>
                    <div style="width:80px">
                        <input style="width:100%" type="number" readonly name="current[]" required="required"  class="current">       
                    </div>
                </td>
                <td>
                    <div style="width:80px">
                        <input style="width:100%" type="number" name="vacant[]"  class="vacant">       
                    </div>
                </td> 

            </tr>
            `;
            
            $('#workforceTable tbody').append(appendData);
            

            app.populateSelect($('#workforceTable tbody').find('.position:last'), document.positionList, 'FUNCTIONAL_LEVEL_ID', 'FUNCTIONAL_LEVEL_EDESC', '-select-',null, 1, true, false); 
            app.populateSelect($('#workforceTable tbody').find('.group:last'), document.groupList, 'SERVICE_GROUP_ID', 'SERVICE_GROUP_NAME', '-----',null, 1, false, false); 
            app.populateSelect($('#workforceTable tbody').find('.service:last'), document.serviceList, 'SERVICE_TYPE_ID', 'SERVICE_TYPE_NAME', '-----',null, 1, false, false); 
            app.populateSelect($('#workforceTable tbody').find('.designation:last'), document.designationList, 'DESIGNATION_ID', 'DESIGNATION_TITLE', '-----',null, 1, false, false);
            
            $('#workforceTable tbody').find('.quota:last').val(item['QUOTA']);
            $('#workforceTable tbody').find('.position:last').val(item['POSITION_ID']);
            $('#workforceTable tbody').find('.group:last').val(item['SERVICE_GROUP_ID']);
            $('#workforceTable tbody').find('.designation:last').val(item['DESIGNATION_ID']);
            $('#workforceTable tbody').find('.service:last').val(item['SERVICE_TYPE_ID']);
            // $('#workforceTable tbody').find('.current:last').val(10);
            var position_id = item['POSITION_ID'];
            var group_id = item['SERVICE_GROUP_ID'];
            var service_id = item['SERVICE_TYPE_ID'];
            var designation_id = item['DESIGNATION_ID'];
            var location_id = item['LOCATION_ID'];
            var department_id = item['DEPARTMENT_ID'];
            data = {
                'position' : position_id,
                'group' : group_id,
                'service' : service_id,
                'designation' : designation_id,
                'location' : location_id,
                'department' : department_id
            };
            var rowThisCurrent = $('#workforceTable tbody').find('.current:last') ;
            var rowThisVacant = $('#workforceTable tbody').find('.vacant:last') ;
            var rowThisQuota = $('#workforceTable tbody').find('.quota:last') ;
            app.pullDataById(document.getCurrentStaff, data).then(function (success) {
                if (success.success) {
                    rowThisCurrent.val(success.data);
                    rowThisVacant.val(rowThisQuota.val()-rowThisCurrent.val());
                }
            });
            // $('#workforceTable tbody').find('.vacant:last').val($('#workforceTable tbody').find('.quota:last').val()-$('#workforceTable tbody').find('.current:last').val());
        }
        $(document).on('change', ".department", function (){
            if($('.department').val() != -1){
                $('#workforceTable').find("tr:gt(2)").remove();
                $('.quota:first').val(null);
                
                var position_id = $(".position:first").val();
                var group_id = $(".group:first").val();
                var service_id = $(".service:first").val();
                var designation_id = $(".designation:first").val();
                var location_id = $(".branch").val();
                var department_id = $(".department").val();
                data = {
                    'position' : position_id,
                    'group' : group_id,
                    'service' : service_id,
                    'designation' : designation_id,
                    'location' : location_id,
                    'department' : department_id
                };
                app.pullDataById(document.getPastValues,data).then(function (success){
                    if(success.success){
                        if(success.data.length != 0){
                            $('#workforceTable').find("tr:gt(1)").remove();
                            success.data.forEach(addPastRows);
                        }else{
                            var position_id = $(".position:first").val();
                            var group_id = $(".group:first").val();
                            var service_id = $(".service:first").val();
                            var designation_id = $(".designation:first").val();
                            var location_id = $(".branch").val();
                            var department_id = $(".department").val();
                            data = {
                                'position' : position_id,
                                'group' : group_id,
                                'service' : service_id,
                                'designation' : designation_id,
                                'location' : location_id,
                                'department' : department_id
                            };
                            app.pullDataById(document.getCurrentStaff, data).then(function (success) {
                                if (success.success) {
                                    $(".current:first").val(success.data);
                                    $('.vacant:first').val($('.quota:first').val()-$('.current:first').val());
                                }
                            }); 
                        }
                        
                    }
                });                                     
            }
            
        });
        $(document).on('change', ".position, .group, .service, .designation, .quota", function () {
            
            var position_id = $(this).closest("tr").find("td div select.position").val();
            var group_id = $(this).closest("tr").find("td div select.group").val();
            var service_id = $(this).closest("tr").find("td div select.service").val();
            var designation_id = $(this).closest("tr").find("td div select.designation").val();
            var location_id = $(".branch").val();
            var department_id = $(".department").val();
            data = {
                'position' : position_id,
                'group' : group_id,
                'service' : service_id,
                'designation' : designation_id,
                'location' : location_id,
                'department' : department_id
            };
            var thisRow = $(this);
            app.pullDataById(document.getCurrentStaff, data).then(function (success) {
                if (success.success) {
                    console.log(success.data);
                    thisRow.closest("tr").find("td div input.current").val(success.data);
                    thisRow.closest("tr").find("td div input.vacant").val(thisRow.closest("tr").find("td div input.quota").val()-thisRow.closest("tr").find("td div input.current").val());
                }
            });
        });


        $('.deatilAddBtn').on('click', function () {
            var appendData = `
            <tr>
                <td><input class="dtlDelBtn btn btn-danger" type="button" value="Del -" style="padding:3px;"></td>
                <td>
                    <div style="width:90px">
                        <select class='position' name='position[]' style="width:100%">
                        </select>
                    </div>
                </td>
                <td>
                    <div style="overflow:hidden">
                        <select class='group' name='group[]' >
                        </select>       
                    </div>
                </td>
                <td>
                    <div style="overflow:hidden">
                        <select class='service' name='service[]' >
                        </select>      
                    </div>
                </td>
                <td>
                    <div style="overflow:hidden">
                        <select class='designation' name='designation[]' >
                        </select>
                    </div>
                </td>       
                <td>
                    <div style="width:80px">
                        <input style="width:100%" type="number" name="quota[]"   class="quota">       
                    </div>
                </td>
                <td>
                    <div style="width:80px">
                        <input style="width:100%" type="number" readonly name="current[]" required="required"  class="current">       
                    </div>
                </td>
                <td>
                    <div style="width:80px">
                        <input style="width:100%" type="number" name="vacant[]"  class="vacant">       
                    </div>
                </td> 

            </tr>
            `;
            
            $('#workforceTable tbody').append(appendData);
            
            // app.addComboTimePicker(
            //         $('#domesticConfigTable tbody').find('.depTime:last'),
            //         $('#domesticConfigTable tbody').find('.arrTime:last')
            //         );
            app.populateSelect($('#workforceTable tbody').find('.position:last'), document.positionList, 'FUNCTIONAL_LEVEL_ID', 'FUNCTIONAL_LEVEL_EDESC', '-select-',null, 1, true); 
            app.populateSelect($('#workforceTable tbody').find('.group:last'), document.groupList, 'SERVICE_GROUP_ID', 'SERVICE_GROUP_NAME', '-----',null, 1, false); 
            app.populateSelect($('#workforceTable tbody').find('.service:last'), document.serviceList, 'SERVICE_TYPE_ID', 'SERVICE_TYPE_NAME', '-----',null, 1, false); 
            app.populateSelect($('#workforceTable tbody').find('.designation:last'), document.designationList, 'DESIGNATION_ID', 'DESIGNATION_TITLE', '-----',null, 1, false);     
        });

        $('#workforceTable').on('click', '.dtlDelBtn', function () {
            console.log($(this));
            var selectedtr = $(this).parent().parent();
            selectedtr.remove();
        });
    });
})(window.jQuery, window.app);