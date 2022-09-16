(function ($, app) {
    'use strict';
    $(document).ready(function () {

        $("select").select2();

        var $payHeadId = $("#payHeadId");
        var $fiscalYearId = $("#fiscalYearId");
        var $searchEmployeesBtn = $('#searchEmployeesBtn');
        var $saveChanges = $('#saveChanges');
    	let $monthId = $("#monthId");
        let $table = $("#table");
        let $employeeId = $("#employeeId");
        var $companyId = $('#companyId');
        var $groupId = $('#groupId');
        var $salaryTypeId = $('#salaryTypeId');
        salaryTypeId
        var changedValues = [];
        var companyList = null;
        var groupList = null;

        app.populateSelect($payHeadId, document.payHeads, "PAY_ID", "PAY_EDESC", "Select Pay Head");
        app.populateSelect($fiscalYearId, document.fiscalYears, "FISCAL_YEAR_ID", "FISCAL_YEAR_NAME", "Select Fiscal Year");
        $('#fiscalYearId').val($('#fiscalYearId option:last').val());
        app.populateSelect($employeeId, document.employees, "EMPLOYEE_ID", "FULL_NAME", "Select Employee");
        app.populateSelect($salaryTypeId, document.salaryTypes, "SALARY_TYPE_ID", "SALARY_TYPE_NAME");
        document.getElementById("salaryTypeId").selectedIndex = "1";
        var selectedYearMonthList = document.months.filter(function (item) {
            return item['FISCAL_YEAR_ID'] == $fiscalYearId.val();
        });
        
        app.populateSelect($monthId, selectedYearMonthList, 'MONTH_ID', 'MONTH_EDESC', 'Months');
        app.searchTable($table, ['FULL_NAME', 'EMPLOYEE_CODE']);
        $("#searchFieldDiv").hide();
        $saveChanges.hide();

        $fiscalYearId.change(function(){
    		selectedYearMonthList = document.months.filter(function (item) {
                return item['FISCAL_YEAR_ID'] == $fiscalYearId.val();
            });
            app.populateSelect($monthId, selectedYearMonthList, 'MONTH_ID', 'MONTH_EDESC', 'Months');
    	});

        $searchEmployeesBtn.on('click', function () {
            changedValues = [];
            var payHeadOptions = $("#payHeadId option");
            var payHeadId = [];
            payHeadId = $payHeadId.val();
            if(payHeadId == null || payHeadId.length == 0){
                payHeadId = $.map(payHeadOptions ,function(option) {
                    return option.value;
                });
            }
            if ($fiscalYearId.val() == -1) {
                app.showMessage("No fiscal year Selected.", 'error');
                $fiscalYearId.focus();
                return;
            }
            if ($employeeId.val() == -1) {
                app.showMessage("No employee Selected.", 'error');
                $fiscalYearId.focus();
                return;
            }
            if ($monthId.val() == -1) {
                app.showMessage("No month Selected.", 'error');
                $monthId.focus();
                return;
            }
            if ($salaryTypeId.val() == -1) {
                app.showMessage("No Salary Type Selected.", 'error');
                $salaryTypeId.focus();
                return;
            }
            $table.empty();
            app.serverRequest(document.getPayValueModifiedLink, {
                payHeadId: payHeadId,
                //fiscalYearId: $fiscalYearId.val(),
                monthId: $monthId.val(),
                employeeId: $employeeId.val(),
                salaryTypeId: $salaryTypeId.val()}).then(function (response) {
                var columns = []; 
                columns.push({field: "EMPLOYEE_CODE", title: "Code", width: 20});
                columns.push({field: "FULL_NAME", title: "Employee", width: 60,});
                columns.push({field: "PAY_EDESC", title: "Pay Head", width: 70});
                columns.push({field: "PAY_TYPE", title: "Pay Head", width: 40});
                columns.push({field: "VAL", title: "Value", width: 70, template: '<input type="number" class="V" id="V_#: PAY_ID#" value="#:VAL||""#" style="height:17px;">'});
                columns.push({field: "REMARKS", title: "Remarks", width: 190, template: '<input type="text"  class="R" id="R_#: PAY_ID#" value="#: REMARKS||""#" size="60" style="height:17px;">'});
                // let totalRow = {};
                // totalRow = {...totalRow, ...response.data[0]};
                // for(let i in response.data[0]){
                //     totalRow[i] = '';
                // }
                // response.data.push(totalRow);
                app.initializeKendoGrid($table, columns);
                app.renderKendoGrid($table, response.data);
                $("#searchFieldDiv").show();
                $("#saveChanges").show();
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
            var id = this.id;
            if(key == 'V'){
                dataItem['VAL'] = updatedValue;
            }
            if(key == 'R'){
                dataItem['REMARKS'] = updatedValue;
            }
            // if(row.is(":last-child") && (grid.dataSource.view().length == grid.dataSource.total())){
            //     $("."+key).val(updatedValue);
            //     var dataSource = grid.dataSource;
            //     $.each(grid.items(), function(index, item) {
            //         var uid = $(item).data("uid");
            //         var dataItem = dataSource.getByUid(uid);
            //         console.log(dataItem);
            //         dataItem[key] = updatedValue;
            //         var index = changedValues.findIndex(function(x){
            //             return x.valueType == key;
            //         });
            //         if(index == -1){ 
            //             changedValues.push({valueType: key, payId: key.substring(2)}); 
            //         }
            //     });
            //     return;
            // }
            //var index = changedValues.findIndex(x => x.EMPLOYEE_ID==dataItem.EMPLOYEE_ID);
            var index = changedValues.findIndex(function(x){
                return x.payId == id.substring(2) && x.valueType == key;
            });
            if(index == -1){ 
                changedValues.push({valueType: key, payId: id.substring(2)}); 
            }
        });

        $saveChanges.on('click', function () {
            var grid = $table.data("kendoGrid");
            var currentData = grid.dataSource._data;
            for(let x = 0; x < changedValues.length; x++){
                let data = currentData.filter(function(item, i) { 
                    return item.PAY_ID == changedValues[x].payId;
                });
                if(changedValues[x].valueType == 'V'){
                    changedValues[x].value = data[0]['VAL'];
                }
                if(changedValues[x].valueType == 'R'){
                    changedValues[x].value = data[0]['REMARKS'];
                }
            }
            var monthId = $monthId.val();
            var salaryTypeId = $salaryTypeId.val();
            var employeeId = $employeeId.val();

            // function groupBy(objectArray, property) {
            //     return objectArray.reduce(function (acc, obj) {
            //         let key = obj[property]
            //         if (!acc[key]) {
            //             acc[key] = []
            //         }
            //         acc[key].push(obj)
            //         return acc
            //     }, {})
            // }
            // const group = groupBy(changedValues,'payId');
            // console.log(group); debugger

            app.serverRequest(document.postPayValueModifiedLink, {data : changedValues, salaryTypeId: salaryTypeId, monthId: monthId, employeeId: employeeId}).then(function(){
                changedValues = [];
                app.showMessage('Operation successfull', 'success');
            }, function (error) {
                console.log(error);
            });
        });
    });
})(window.jQuery, window.app);
