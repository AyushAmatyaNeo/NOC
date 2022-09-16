(function($,app){
  $(document).ready(function () {
    $('select').select2();
    
    app.populateSelect($('.department'), document.departmentList, 'DEPARTMENT_ID', 'DEPARTMENT_NAME', '-select-',null, 1, true, false);
    app.populateSelect($('.functionalLevelList'), document.functionalLevelList, 'FUNCTIONAL_LEVEL_ID', 'FUNCTIONAL_LEVEL_EDESC', '-select-',null, 1, true, false); 
    app.populateSelect($('.service'), document.serviceList, 'SERVICE_TYPE_ID', 'SERVICE_TYPE_NAME', '-select-',null, 1, true, false);
    app.populateSelect($('.serviceGroup'), document.serviceGroupList, 'SERVICE_ID', 'SERVICE_NAME', '-select-',null, 1, true, false);
    var id = document.getId;
    if(id){
        $('#departmentId').val(id);
        $('#departmentId').trigger('change');
        // $('#departmentId').setAttribute('disabled', true);
        document.getElementById("departmentId").disabled = true;
    }
    var $departmentId = $('#departmentId');
    var currentDetails = {};
    
    var departmentChangeAction = function(){
      var departmentId = $('#departmentId').val();
      app.serverRequest(document.getWorkForceData, {departmentId: departmentId}).then(function (response) {
          if (response.success) {
              currentDetails = response.data;
          }else {
            console.log('false');
          }
      }, function (error) {
          app.showMessage(error, 'error');
      });
    } 

    departmentChangeAction();



    $('.deatilAddBtn').on('click', function () {
      var appendData = `
      <tr>
      <td><input class="dtlDelBtn btn-danger" type="button" value="Del -" style="padding:3px;"></td>
      <td>
          <div style="width:90px">
              <select class='functionalLevelList' name='functionalLevelList[]' style="width:100%">
              </select>
          </div>
      </td>
      <td>
          <div style="width:80px">
              <select class='service' name='service[]' >
              </select>      
          </div>
      </td>
      <td>
      <div style="width:80px">
          <select class='serviceGroup' name='serviceGroup[]' id = 'serviceGroup'>
          </select>      
      </div>
      </td>
      <td>
      <div style="width:110px">
          <select class='serviceSubgroup' name='serviceSubgroup[]' style =" width:150px;" id = 'serviceSubgroup' >
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
              <input style="width:100%" type="number" readonly name="current[]" class="current">       
          </div>
      </td>
      <td>
          <div style="width:80px">
              <input style="width:100%" type="number" readonly name="vacant[]"  class="vacant">       
          </div>
      </td> 

      </tr>
      `;
      
      $('#workforceTable tbody').append(appendData);


      app.populateSelect($('#workforceTable tbody').find('.functionalLevelList:last'), document.functionalLevelList, 'FUNCTIONAL_LEVEL_ID', 'FUNCTIONAL_LEVEL_EDESC', '-select-',null, 1, true); 
      app.populateSelect($('#workforceTable tbody').find('.group:last'), document.groupList, 'SERVICE_GROUP_ID', 'SERVICE_GROUP_NAME', '-select-',null, 1, true); 
      app.populateSelect($('#workforceTable tbody').find('.service:last'), document.serviceList, 'SERVICE_TYPE_ID', 'SERVICE_TYPE_NAME', '-select-',null, 1, true); 
      app.populateSelect($('#workforceTable tbody').find('.designation:last'), document.designationList, 'DESIGNATION_ID', 'DESIGNATION_TITLE', '-select-',null, 1, true);
      app.populateSelect($('#workforceTable tbody').find('.serviceGroup:last'),document.serviceGroupList,'SERVICE_ID','SERVICE_NAME', '-select-',1,1,true);
      });

    $('#workforceTable').on('click', '.dtlDelBtn', function () {
        console.log($(this));
        var selectedtr = $(this).parent().parent();
        selectedtr.remove();
    });
    var quotaAction = function(){
        var departmentId = $('#departmentId').val();
        app.serverRequest(document.getQuotaData, {departmentId: departmentId}).then(function (response) {
            if (response.success) {
                response.data.forEach(addPreFilledRows);
            }else {
            console.log('false');
            }
        }, function (error) {
            app.showMessage(error, 'error');
        });
    }
    quotaAction();

    $departmentId.on('change', function(){
        $("#workforceTable tbody tr").remove();
        quotaAction();
        departmentChangeAction();
    });
    function addPreFilledRows(item){
        var appendData = `
      <tr>
      <td><input class="dtlDelBtn btn-danger" type="button" value="Del -" style="padding:3px;"></td>
      <td>
          <div style="width:90px">
              <select class='functionalLevelList' name='functionalLevelList[]' style="width:100%" required>
              </select>
          </div>
      </td>
      <td>
          <div style="width:80px">
              <select class='service' name='service[]' required >
              </select>      
          </div>
      </td>
      <td>
      <div style="width:80px">
          <select class='serviceGroup' name='serviceGroup[]' id='serviceGroup' required>
          </select>      
      </div>
      </td>
      <td>
      <div style="width:110px">
          <select required class='serviceSubgroup' name='serviceSubgroup[]' style =" width:150px;" id='serviceSubgroup'>
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
              <input style="width:100%" type="number" readonly name="current[]" class="current">       
          </div>
      </td>
      <td>
          <div style="width:80px">
              <input style="width:100%" type="number" readonly name="vacant[]"  class="vacant">       
          </div>
      </td> 

      </tr>
      `;
      
      $('#workforceTable tbody').append(appendData);

   



      app.populateSelect($('#workforceTable tbody').find('.functionalLevelList:last'), document.functionalLevelList, 'FUNCTIONAL_LEVEL_ID', 'FUNCTIONAL_LEVEL_EDESC', '-select-',null, 1, true); 
      app.populateSelect($('#workforceTable tbody').find('.group:last'), document.groupList, 'SERVICE_GROUP_ID', 'SERVICE_GROUP_NAME', '-select-',null, 1, true); 
      app.populateSelect($('#workforceTable tbody').find('.service:last'), document.serviceList, 'SERVICE_TYPE_ID', 'SERVICE_TYPE_NAME', '-select-',null, 1, true); 
      app.populateSelect($('#workforceTable tbody').find('.designation:last'), document.designationList, 'DESIGNATION_ID', 'DESIGNATION_TITLE', '-select-',null, 1, true);
      app.populateSelect($('#workforceTable tbody').find('.serviceGroup:last'),document.serviceGroupList,'SERVICE_ID','SERVICE_NAME', '-select-',null,1,true);
      app.populateSelect($('#workforceTable tbody').find('.serviceSubgroup:last'),document.getServiceSubgroupList[item.SERVICE_ID],'SERVICE_SUBGROUP_ID','SERVICE_NAME', '-select-',null,1,true);       
    
      $('#workforceTable tbody').find('.functionalLevelList:last').val(item.FUNCTIONAL_LEVEL_ID);
      $('#workforceTable tbody').find('.service:last').val(item.SERVICE_TYPE_ID);
      $('#workforceTable tbody').find('.quota:last').val(item.QUOTA);
      $('#workforceTable tbody').find('.current:last').val(item.CURRENT);
      $('#workforceTable tbody').find('.vacant:last').val(item.QUOTA-item.CURRENT);
      $('#workforceTable tbody').find('.serviceGroup:last').val(item.SERVICE_ID);
      $('#workforceTable tbody').find('.serviceSubgroup:last').val(item.SERVICE_SUBGROUP_ID);
    }

    $(document).on('change', '.functionalLevelList, .service, .quota, .serviceGroup, .serviceSubgroup', function () {
        var found = false;
        for (let step = 0; step < currentDetails.length; step++) {
            if($(this).closest("tr").find("td div select.functionalLevelList").val() == currentDetails[step].FUNCTIONAL_LEVEL_ID 
            && $(this).closest("tr").find("td div select.service").val() == currentDetails[step].SERVICE_TYPE_ID
            && $(this).closest("tr").find("td div select.serviceGroup").val() == currentDetails[step].SERVICE_ID
            && $(this).closest("tr").find("td div select.serviceSubgroup").val() == currentDetails[step].SERVICE_SUBGROUP_ID){
                $(this).closest("tr").find("td div input.current").val(currentDetails[step].CURRENT);
                found = true;
            }
        }
        if(!found){
            $(this).closest("tr").find("td div input.current").val(0);
        }
        $(this).closest("tr").find("td div input.vacant").val( $(this).closest("tr").find("td div input.quota").val()- $(this).closest("tr").find("td div input.current").val())
        const functionalLevels = document.getElementsByClassName('functionalLevelList');
        const functionalLevelArr = [...functionalLevels].map(input => input.value);
        const serviceTypes = document.getElementsByClassName('service');
        const serviceTypeArr = [...serviceTypes].map(input => input.value);
        // if (document. readyState === 'complete') { 
        //     checkForErrors(functionalLevelArr, serviceTypeArr);
        // }
    });
    var $form = $('#workforceHoForm');
    var isvalid = 'N';
    var checkForErrors = function (functionalLevelArr, serviceTypeArr, serviceGroupArr, serviceSubGroupArr) {
        app.pullDataById(document.validateWorkForce, {functionalLevels: functionalLevelArr, serviceTypes: serviceTypeArr, serviceGroups: serviceGroupArr, serviceSubGroups: serviceSubGroupArr}).then(function (response) {
            if(response.data != ''){
                $form.prop('valid', 'false');
                $form.prop('error-message', response.data);
                app.showMessage(response.data, 'error');
                isvalid = 'N';
                return;
            }else{
                isvalid = 'Y';
                return;
            }
        }, function (error) {
            app.showMessage(error, 'error');
        });
    }

    $('#submitBtn').on('click', function () {
        const functionalLevels = document.getElementsByClassName('functionalLevelList');
        const functionalLevelArr = [...functionalLevels].map(input => input.value);
        const serviceTypes = document.getElementsByClassName('service');
        const serviceTypeArr = [...serviceTypes].map(input => input.value);
        const serviceGroup =  document.getElementsByClassName('serviceGroup');
        const serviceGroupArr = [...serviceGroup].map(input => input.value);
        const serviceSubGroup =  document.getElementsByClassName('serviceSubgroup');
        const serviceSubGroupArr = [...serviceSubGroup].map(input => input.value);

        checkForErrors(functionalLevelArr, serviceTypeArr, serviceGroupArr, serviceSubGroupArr);
        console.log(isvalid);
        if(isvalid=='N'){
            return false;
        }
    });

    $(document).on('change', '.serviceGroup', function () {
        var serviceId = $(this).closest("tr").find("td div select.serviceGroup").val();
        app.populateSelect($(this).closest("tr").find("td div select.serviceSubgroup"),document.getServiceSubgroupList[serviceId],'SERVICE_SUBGROUP_ID','SERVICE_NAME', '-select-',null,1,true);       
    });

    
  });
})(window.jQuery, window.app);