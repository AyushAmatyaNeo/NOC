(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.datePickerWithNepali('fromDate', 'nepaliFromDate');
        var $table = $('#table');
        var $search = $('#search');
    

        function initializeKendo(data, days){
            $table.kendoGrid({
                toolbar: ["excel"],
                excel: {
                    fileName: "Weekly Working Hours Report.xlsx",
                    filterable: true,
                    allPages: true
                },
                dataSource: { 
                    data: data,
                    schema:{
                        model: {
                            fields: {
                                TOTAL_HOURS_WORKED: { type: "number" },
                                DEPARTMENT_NAME: { type: "string" }
                            }
                        }
                    },
                    group: { field: "DEPARTMENT_NAME" , aggregates:[
                        { field: "ASSIGNED_HOURS", aggregate: "sum" },
                        { field: "TOTAL_HOURS_WORKED", aggregate: "sum" },
                        { field: "EXTRA_HOURS_WORKED", aggregate: "sum" },
                        { field: "AVG_EXTRA_HOURS_WORKED", aggregate: "sum" }
                    ]},
                    pageSize: 20,
                    aggregate: [ 
                        { field: "ASSIGNED_HOURS", aggregate: "sum" },
                        { field: "TOTAL_HOURS_WORKED", aggregate: "sum" },
                        { field: "EXTRA_HOURS_WORKED", aggregate: "sum" },
                        { field: "AVG_EXTRA_HOURS_WORKED", aggregate: "sum" }
                    ]
                },
                height: 550,
                scrollable: true,
                sortable: true,
                filterable: true,
                pageable: {
                    input: true,
                    numeric: false
                },
                columns: [
                    { field: "EMPLOYEE_CODE", title: "Code", width: "100px" },
                    { field: "FULL_NAME", title: "Full Name", width: "100px" },
                    {title: "No. Of Hours Worked In A Week", columns: days},
                    { field: "ASSIGNED_HOURS", title: "Assigned Hours", width: "100px", aggregates: ["sum"], groupFooterTemplate: "#=sum#" },
                    { field: "EXTRA_HOURS_WORKED", title: "Extra Hours Worked", width: "100px", aggregates: ["sum"], groupFooterTemplate: "#=sum#" },
                    { field: "AVG_EXTRA_HOURS_WORKED", title: "Average Extra Hours", width: "100px", format: "{0:0.##}", aggregates: ["sum"], groupFooterTemplate: "#= kendo.toString(sum, '0.00') #"}
                ] 
            });  
        }
 
        function calculateAverageExtraHours(data){
            var avg_hours;
            if(data.DAY_OFF === 0){
                avg_hours = data.EXTRA_HOURS_WORKED/7;
            }
            else if(data.DAY_OFF === 1){
                avg_hours = data.EXTRA_HOURS_WORKED/6;
            }
            else if(data.DAY_OFF === 2){
                avg_hours = data.EXTRA_HOURS_WORKED/5;
            }
            else{
                avg_hours = 0;
            }
            return avg_hours;
        }

        var searchTable = function (kendoId, searchFields, isHidden) {
            var $kendoId = null;
            if (kendoId instanceof jQuery) {
                $kendoId = kendoId;
            } else {
                $kendoId = $("#" + kendoId);
            }
            var $searchHtml = $(`
                <div class='row search margin-bottom-5 margin-top-10' id='searchFieldDiv'>
                    <div class='col-xs-12 col-sm-6 col-md-4 col-lg-3'>
                        <input class='form-control' placeholder='search here' type='text' id='kendoSearchField' />
                    </div>
                </div>`);
    
    
            $searchHtml.insertBefore($kendoId);
    
            if (typeof isHidden !== "undefined" && isHidden) {
                $("#searchFieldDiv").hide();
            }
            $("#kendoSearchField").keyup(function () {
                var val = $(this).val();
                var filters = [];
                for (var i = 0; i < searchFields.length; i++) {
                    filters.push({
                        field: searchFields[i],
                        operator: "contains",
                        value: val
                    });
                }
    
                $kendoId.data("kendoGrid").dataSource.filter({
                    logic: "or",
                    filters: filters
                });
            });
        }
        searchTable($table, ['FULL_NAME','EMPLOYEE_CODE']);
         

        $search.on('click', function () {
            var responseData;
            var days;
            var weekDays;
            var data = document.searchManager.getSearchValues();
            var fromDate = $('#fromDate').val();
            data.toDate = fromDate; 
            app.serverRequest(document.pullEmployeeListForEmployeeTableLink, data).then(function (response) { 
                if (response.success) {
                    var dayOffCounter;
                    responseData = response.data;
                    $table.empty(); 

                    weekDays = ['SUN_WH', 'MON_WH', 'TUE_WH', 'WED_WH', 'THU_WH', 'FRI_WH', 'SAT_WH'];
                    days = [{ field: "TOTAL_HOURS_WORKED", title: "Total Hours", width: "100px", aggregates: ["sum"], groupFooterTemplate: "#=sum#"},
                        { field: "DAY_OFF", title: "Day Off", width: "100px" }];

                    for(var i = response.days.length - 1; i >= 0; i--){
                        days.unshift({ field: weekDays[response.days[i].WEEKDAY], title: response.days[i].WEEKNAME, width: "100px" });
                    }

                    for(var i = 0; i < responseData.length; i++){
                        dayOffCounter = 0;
                        responseData[i].TOTAL_HOURS_WORKED = (parseInt(responseData[i].SUN_WH) || 0)+(parseInt(responseData[i].MON_WH) || 0)+(parseInt(responseData[i].TUE_WH) || 0)+(parseInt(responseData[i].WED_WH) || 0)+(parseInt(responseData[i].THU_WH) || 0)+(parseInt(responseData[i].FRI_WH) || 0)+(parseInt(responseData[i].SAT_WH) || 0);
                        responseData[i].ASSIGNED_HOURS = (parseInt(responseData[i].SUN_AH) || 0)+(parseInt(responseData[i].MON_AH) || 0)+(parseInt(responseData[i].TUE_AH) || 0)+(parseInt(responseData[i].WED_AH) || 0)+(parseInt(responseData[i].THU_AH) || 0)+(parseInt(responseData[i].FRI_AH) || 0)+(parseInt(responseData[i].SAT_AH) || 0);
                        responseData[i].EXTRA_HOURS_WORKED = responseData[i].TOTAL_HOURS_WORKED - responseData[i].ASSIGNED_HOURS;
                        dayOffCounter+= responseData[i].SUN_OS == 'DO' ? 1 : 0;
                        dayOffCounter+= responseData[i].MON_OS == 'DO' ? 1 : 0;
                        dayOffCounter+= responseData[i].TUE_OS == 'DO' ? 1 : 0;
                        dayOffCounter+= responseData[i].WED_OS == 'DO' ? 1 : 0;
                        dayOffCounter+= responseData[i].THU_OS == 'DO' ? 1 : 0;
                        dayOffCounter+= responseData[i].FRI_OS == 'DO' ? 1 : 0;
                        dayOffCounter+= responseData[i].SAT_OS == 'DO' ? 1 : 0;
                        responseData[i].DAY_OFF = dayOffCounter;
                        responseData[i].AVG_EXTRA_HOURS_WORKED = calculateAverageExtraHours(responseData[i]);
                    }
                    initializeKendo(responseData, days);
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });  
        });
        
//        $("#reset").on("click", function () {
//            $(".form-control").val("");
//        });
    });  
})(window.jQuery, window.app);