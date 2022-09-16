(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        var months = document.months;

        var $monthlyValueId = $("#monthlyValueId");
        var $fiscalYearId = $("#fiscalYearId");
        var $monthId = $("#monthId");

        var $searchEmployeesBtn = $('#searchEmployeesBtn');
        var $assignMonthlyValueBtn = $('#assignMonthlyValueBtn');
        var changedValues = [];
        var $table = $('#monthlyValueTable');

        var exportMonthList;
        var selectedMonthlyValueName;

        app.populateSelect($monthlyValueId, document.monthlyValues, "MTH_ID", "MTH_EDESC", "Select Monthly Value");
        app.populateSelect($fiscalYearId, document.fiscalYears, "FISCAL_YEAR_ID", "FISCAL_YEAR_NAME", "Select Fiscal Year");
        $("#assignMonthlyValueBtn").hide();
        // $('#fiscalYearId').val($('#fiscalYearId option:last').val());
        // $('#fiscalYearId').trigger('input');

        $fiscalYearId.on('change', function () {
            var value = $(this).val();
            var filteredMonths = [];
            if (value != -1) {
                var filteredMonths = months.filter(function (item) {
                    return item['FISCAL_YEAR_ID'] == value;
                });
            }
            app.populateSelect($monthId, filteredMonths, "MONTH_ID", "MONTH_EDESC", "Select Month");
        });

        $searchEmployeesBtn.on('click', function () {
            changedValues = [];
            var monthlyValueIdOptions = $("#monthlyValueId option");
            var monthlyValueId = [];
            monthlyValueId = $monthlyValueId.val();
            if(monthlyValueId == null || monthlyValueId.length == 0){
                monthlyValueId = $.map(monthlyValueIdOptions ,function(option) {
                    return option.value;
                });
            }
            if ($fiscalYearId.val() == -1) {
                app.showMessage("No fiscal year Selected.", 'error');
                $fiscalYearId.focus();
                return;
            }
            if ($monthId.val() == -1) {
                app.showMessage("No month Selected.", 'error');
                $monthId.focus();
                return;
            }
            
            $table.empty();
            app.serverRequest(document.getMonthlyValueDetailWS, {
                monthlyValueId: monthlyValueId,
                fiscalYearId: $fiscalYearId.val(),
                monthId: $monthId.val(),
                employeeFilter: document.searchManager.getSearchValues()}).then(function (response) {
                var columns = []; 
                columns.push({field: "EMPLOYEE_CODE", title: "Code", width: 80, locked: true});
                columns.push({field: "FULL_NAME", title: "Name", width: 90, locked: true});
                let totalRow = {};
                totalRow = {...totalRow, ...response.data[0]};
                for(let i in response.data[0]){
                    totalRow[i] = '';
                    if(i.startsWith("M_")){
                        let title = response.columns.filter((item) => item.TITLE == i);
                        columns.push({field: i, title: title[0].MTH_EDESC, width: 160,
                template: '<input type="number" class="'+i+'" value="#: '+i+'||""#" style="height:17px;">'});
                    }
                }
                response.data.push(totalRow);
                app.initializeKendoGrid($table, columns);
                app.renderKendoGrid($table, response.data);
                $("#assignMonthlyValueBtn").show();
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
            //var dataSource = grid.dataSource.dataFiltered();
            
            if(row.is(":last-child") && (grid.dataSource.view().length == grid.dataSource.total())){
                //var elms = document.getElementsByClassName(key);
                //for (var i = 0; i < elms.length; i++) {
                   //elms[i].setAttribute("value", updatedValue);
                //}
                $("."+key).val(updatedValue);
                var dataSource = grid.dataSource;
                $.each(grid.items(), function(index, item) {
                    var uid = $(item).data("uid");
                    var dataItem = dataSource.getByUid(uid);
                    dataItem[key] = updatedValue;
                    var index = changedValues.findIndex(function(x){
                        return x.employeeId == dataItem.EMPLOYEE_ID && x.monthlyValue == key;
                    });
                    if(index == -1){ 
                        changedValues.push({employeeId: dataItem.EMPLOYEE_ID, monthlyValue: key, monthlyValueId: key.substring(2)}); 
                    }
                });
                return;
            }
            //var index = changedValues.findIndex(x => x.EMPLOYEE_ID==dataItem.EMPLOYEE_ID);
            var index = changedValues.findIndex(function(x){
                return x.employeeId == dataItem.EMPLOYEE_ID && x.monthlyValue == key;
            });
            if(index == -1){ 
                changedValues.push({employeeId: dataItem.EMPLOYEE_ID, monthlyValue: key, monthlyValueId: key.substring(2)}); 
            }
        });

        $assignMonthlyValueBtn.on('click', function () {
            var grid = $table.data("kendoGrid");
            var currentData = grid.dataSource._data;
            for(let x = 0; x < changedValues.length; x++){
                var data = currentData.filter(function(item, i) { 
                    return item.EMPLOYEE_ID == changedValues[x].employeeId;
                });
                changedValues[x].value = data[0][changedValues[x].monthlyValue];
            }
            var fiscalYearId = $fiscalYearId.val();
            var monthId = $monthId.val();
            app.serverRequest(document.postMonthlyValueDetailWS, {data : changedValues, fiscalYearId: fiscalYearId, monthId: monthId}).then(function(){
                changedValues = [];
                app.showMessage('Operation successfull', 'success');
            }, function (error) {
                console.log(error);
            });
        });

    });
})(window.jQuery, window.app);
