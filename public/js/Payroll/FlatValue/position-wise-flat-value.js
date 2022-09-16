(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();

        var $searchEmployeesBtn = $('#searchEmployeesBtn');
        var $fiscalYearId = $("#fiscalYearId");
        var $assignFlatValueBtn = $('#assignFlatValueBtn');
        var $positionId = $("#positionId");
        var $flatValueId = $("#flatValueId");

        var $table = $('#flatValueDetailTable');

        var changedValues = [];

        app.populateSelect($positionId, document.positions, "POSITION_ID", "POSITION_NAME", "Select position");
        app.populateSelect($flatValueId, document.flatValues, "FLAT_ID", "FLAT_EDESC", "Select Flat Value");
        app.populateSelect($fiscalYearId, document.fiscalYears, "FISCAL_YEAR_ID", "FISCAL_YEAR_NAME", "Select Fiscal Year");
        $('#fiscalYearId').val($('#fiscalYearId option:last').val());
        app.searchTable($table, ['FULL_NAME', 'EMPLOYEE_CODE']);
        $("#searchFieldDiv").hide();
        $("#assignFlatValueBtn").hide();

        $searchEmployeesBtn.on('click', function () {
            changedValues = [];
            var flatIdOptions = $("#flatValueId option");
            var positionIdOptions = $("#positionId option");
            var flatId = []; var positionId = [];

            flatId = $flatValueId.val();
            if(flatId == null || flatId.length == 0){
                flatId = $.map(flatIdOptions ,function(option) {
                    return option.value;
                });
            }
            positionId = $positionId.val();
            if(positionId == null || positionId.length == 0){
                positionId = $.map(positionIdOptions ,function(option) {
                    return option.value;
                });
            }
            if ($fiscalYearId.val() == -1) {
                app.showMessage("No fiscal year Selected.", 'error');
                $fiscalYearId.focus();
                return;
            }
            
            $table.empty();
            app.serverRequest(document.getPositionFlatLink, {
                flatId: flatId,
                positionId: positionId,
                fiscalYearId: $fiscalYearId.val()}).then(function (response) {
                var columns = []; 
                columns.push({field: "LEVEL_NO", title: "Level", width: 80, locked: true});
                columns.push({field: "POSITION_NAME", title: "Position", width: 80, locked: true});
                let totalRow = {};
                totalRow = {...totalRow, ...response.data[0]};
                for(let i in response.data[0]){
                    totalRow[i] = '';
                    if(i.startsWith("F_")){
                        let title = response.columns.filter((item) => item.TITLE == i);
                        columns.push({field: i, title: title[0].FLAT_EDESC, width: 160,
                template: '<input type="number" class="'+i+'" value="#: '+i+'||""#" style="height:17px;">'});
                    }
                }
                response.data.push(totalRow);
                app.initializeKendoGrid($table, columns);
                app.renderKendoGrid($table, response.data);
                $("#searchFieldDiv").show();
                $("#assignFlatValueBtn").show();
            }, function (error) {
                console.log(error);
            });
        });

        $table.on('input', 'input', function(e){
            var grid = $table.data("kendoGrid");
            var row = $(e.target).closest("tr");
            var dataItem = grid.dataItem(row);
            var updatedValue = this.value;
            //var data = $table.data("kendoGrid").dataItem($(e.target).closest("tr"));
            var key = this.className;
            dataItem[key] = updatedValue;

            if(row.is(":last-child") && (grid.dataSource.view().length == grid.dataSource.total())){
                $("."+key).val(updatedValue);
                var dataSource = grid.dataSource;
                $.each(grid.items(), function(index, item) {
                    var uid = $(item).data("uid");
                    var dataItem = dataSource.getByUid(uid);
                    dataItem[key] = updatedValue;
                    var index = changedValues.findIndex(function(x){
                        return x.positionId == dataItem.POSITION_ID && x.flatValue == key;
                    });
                    if(index == -1){ 
                        changedValues.push({positionId: dataItem.POSITION_ID, flatValue: key, flatId: key.substring(2)}); 
                    }
                });
                return;
            }
            //var index = changedValues.findIndex(x => x.EMPLOYEE_ID==dataItem.EMPLOYEE_ID);
            var index = changedValues.findIndex(function(x){
                return x.positionId == dataItem.POSITION_ID && x.flatValue == key;
            });
            if(index == -1){ 
                changedValues.push({positionId: dataItem.POSITION_ID, flatValue: key, flatId: key.substring(2)}); 
            }
        });

        $assignFlatValueBtn.on('click', function () {
            var grid = $table.data("kendoGrid");
            var currentData = grid.dataSource._data;
            for(let x = 0; x < changedValues.length; x++){
                var data = currentData.filter(function(item, i) { 
                    return item.POSITION_ID == changedValues[x].positionId;
                });
                changedValues[x].value = data[0][changedValues[x].flatValue];
            }
            var fiscalYearId = $fiscalYearId.val();
            app.serverRequest(document.setPositionFlatValueLink, {data : changedValues, fiscalYearId: fiscalYearId}).then(function(){
                changedValues = [];
                app.showMessage('Operation successfull', 'success');
            }, function (error) {
                console.log(error);
            });
        });

    });
})(window.jQuery, window.app);
