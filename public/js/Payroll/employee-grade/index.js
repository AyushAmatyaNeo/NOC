(function ($, app) {
    'use strict';
    $(document).ready(function () {

        $("select").select2();

        var $flatValueId = $("#flatValueId");
        var $fiscalYearId = $("#fiscalYearId");
        var $searchEmployeesBtn = $('#searchEmployeesBtn');
        var $submit = $('#submit');
        var $table = $('#table');
        
        var changedValues = [];

        app.populateSelect($flatValueId, document.flatValues, "FLAT_ID", "FLAT_EDESC", "Select Flat Value");
        app.populateSelect($fiscalYearId, document.fiscalYears, "FISCAL_YEAR_ID", "FISCAL_YEAR_NAME", "Select Fiscal Year");
        $('#fiscalYearId').val($('#fiscalYearId option:last').val());
        app.searchTable($table, ['FULL_NAME', 'EMPLOYEE_CODE']);
        $("#searchFieldDiv").hide();
        $("#submit").hide();

        var columns = [ 
            {field: "EMPLOYEE_CODE", title: "Code"},
            {field: "FULL_NAME", title: "Employee"},
            {field: "OPENING_GRADE", title: "Opening Grade", template: '<input type="number" class="OPENING_GRADE" value="#:OPENING_GRADE#">'},
            {field: "ADDITIONAL_GRADE", title: "Additional Grade", template: '<input type="number" class="ADDITIONAL_GRADE" value="#:ADDITIONAL_GRADE#">'},
            {field: "GRADE_VALUE", title: "Grade Value", template: '<input type="number" class="GRADE_VALUE" value="#:GRADE_VALUE#">'},
            {field: "GRADE_DATE", title: "Grade Date", template: '<input type="text" class="GRADE_DATE" value="#:GRADE_DATE||""#">'},
            {field: "REMARKS", title: "Remarks", template: '<input type="text" class="REMARKS" value="#:REMARKS||""#">'}
        ];

        $searchEmployeesBtn.on('click', function () {
            changedValues = [];
            if ($fiscalYearId.val() == -1) {
                app.showMessage("No fiscal year Selected.", 'error');
                $fiscalYearId.focus();
                return;
            }
            
            $table.empty();
            app.serverRequest(document.getGradeValueDetailWS, {
                fiscalYearId: $fiscalYearId.val(),
                employeeFilter: document.searchManager.getSearchValues()}).then(function (response) {
                app.initializeKendoGrid($table, columns);
                app.renderKendoGrid($table, response.data);
                $(".GRADE_DATE").kendoDatePicker({format: "dd-MMM-yy"});
                $("#searchFieldDiv").show();
                $("#submit").show();
            }, function (error) {
                console.log(error);
            });
        });

        kendo.data.DataSource.prototype.dataFiltered = function () {
            var filters = this.filter();
            var allData = this.data();
            var query = new kendo.data.Query(allData);
            return query.filter(filters).data;
        }

        $table.on('input', 'input', function(e){
            var grid = $table.data("kendoGrid");
            var row = $(e.target).closest("tr");
            var dataItem = grid.dataItem(row);
            var updatedValue = this.value;
            var data = $table.data("kendoGrid").dataItem($(e.target).closest("tr"));
            var key = this.className;
            dataItem[key] = updatedValue;
            var dataSource = grid.dataSource.dataFiltered();
            
            var index = changedValues.findIndex(x => x.EMPLOYEE_ID == dataItem.EMPLOYEE_ID);
            // var index = changedValues.findIndex(function(x){
            //     return x.employeeId == dataItem.EMPLOYEE_ID && x.flatValue == key;
            // });
            if(index == -1){ 
                changedValues.push({employeeId: dataItem.EMPLOYEE_ID}); 
            }
        });

        $submit.on('click', function () {
            var grid = $table.data("kendoGrid");
            var currentData = grid.dataSource._data;
            var data = currentData.filter((el) => { 
                return changedValues.some((f) => {
                    return f.employeeId == el.EMPLOYEE_ID;
                });
            });
            let postData = [];
            for(let x in data){
                postData.push({
                    EMPLOYEE_ID: data[x].EMPLOYEE_ID,
                    OPENING_GRADE: data[x].OPENING_GRADE,
                    ADDITIONAL_GRADE: data[x].ADDITIONAL_GRADE,
                    GRADE_VALUE: data[x].GRADE_VALUE,
                    GRADE_DATE: data[x].GRADE_DATE,
                    REMARKS: data[x].REMARKS
                });
            }
            var fiscalYearId = $fiscalYearId.val();
            app.serverRequest(document.getGradeValueUpdateWS, {data : postData, fiscalYearId: fiscalYearId}).then(function(){
                changedValues = [];
                app.showMessage('Operation successfull', 'success');
            }, function (error) {
                console.log(error);
            });
        });
    });
})(window.jQuery, window.app);
