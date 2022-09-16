(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();

        var $flatValueId = $("#flatValueId");
        var $fiscalYearId = $("#fiscalYearId");

        var $searchEmployeesBtn = $('#searchEmployeesBtn');
        var $assignFlatValueBtn = $('#assignFlatValueBtn');

        var $grid = $('#flatValueDetailGrid');
        var $header = $('#flatValuesDetailHeader');
        var $table = $('#flatValueDetailTable');
        var $footer = $('#flatValueDetailFooter');
        
        var selectedflatValueName;
        var selectedfisaclYear;

        app.populateSelect($flatValueId, document.flatValues, "FLAT_ID", "FLAT_EDESC", "Select Flat Value");
        app.populateSelect($fiscalYearId, document.fiscalYears, "FISCAL_YEAR_ID", "FISCAL_YEAR_NAME", "Select Fiscal Year");

        $searchEmployeesBtn.on('click', function () {
            if ($fiscalYearId.val() == -1) {
                app.showMessage("No fiscal year Selected.", 'error');
                $fiscalYearId.focus();
                return;
            }
            if ($flatValueId.val() == -1) {
                app.showMessage("No monthly value Selected.", 'error');
                $flatValueId.focus();
                return;
            }
            app.pullDataById(document.getFlatValueDetailWS, {
                flatId: $flatValueId.val(),
                fiscalYearId: $fiscalYearId.val(),
                employeeFilter: document.searchManager.getSearchValues()}).then(function (response) {
                initTable($fiscalYearId.val(), document.searchManager.getSelectedEmployee(), response.data);
            }, function (error) {
                console.log(error);
            });
        });

        var findMonthValue = function (serverData, employeeId) {
            var result = serverData.filter(function (item) {
                return item['EMPLOYEE_ID'] == employeeId;
            });

            if (result.length > 0) {
                return result[0]['FLAT_VALUE'];
            } else {
                return null;
            }
        };

        var initTable = function (fiscalYearId, employeeList, serverData) {
            
            var selecetedflatValueId=$flatValueId.val();
             selectedfisaclYear=$fiscalYearId.val();
             selectedflatValueName=$("#flatValueId option:selected").text();
            $header.html('');
            $header.append($('<th>', {text: 'Id'}));
            $header.append($('<th>', {text: 'Code'}));
            $header.append($('<th>', {text: 'Name'}));
            $header.append($('<th>', {text: 'Value'}));

            $grid.html('');
            $.each(employeeList, function (index, item) {
                var $tr = $('<tr>');

                $tr.append($('<td>', {text: item['EMPLOYEE_ID']}));
                $tr.append($('<td>', {text: item['EMPLOYEE_CODE']}));
                $tr.append($('<td>', {text: item['FULL_NAME']}))

                var $td = $('<td>');
                var updatedValue=findMonthValue(serverData, item['EMPLOYEE_ID']);
                $td.append($('<input>', {FISAL_YEAR_ID:selectedfisaclYear,FLAT_VALUE_NAME:updatedValue,FLAT_VALUE_ID:selecetedflatValueId,EMPLOYEE_ID: item['EMPLOYEE_ID'], EMPLOYEE_CODE: item['EMPLOYEE_CODE'], FULL_NAME: item['FULL_NAME'], type: 'number', col: 'col', row: item['EMPLOYEE_ID'], value: updatedValue, class: 'form-control flatValueInput'}));
                $tr.append($td);

                $grid.append($tr);
            });

            $footer.html('');
            var $tr = $('<tr>');

            $tr.append($('<td>', {text: ''}));
            $tr.append($('<td>', {text: ''}));
            $tr.append($('<td>', {text: ''}));

            var $td = $('<td>');
            $td.append($('<input>', {type: 'number', class: 'group form-control'}));
            $tr.append($td);

            $footer.append($tr);
            $table.bootstrapTable({height: 400});
        };

        $table.on('change', '.group', function () {
            var $this = $(this);
            var value = $this.val();
            $('input[col="col"]').val(value);
        });

        $assignFlatValueBtn.on('click', function () {
            var fiscalYearId = $fiscalYearId.val();
            var flatId = $flatValueId.val();

            var promiseList = [];
            App.blockUI({target: "#hris-page-content"});
            $.each($grid.find('input[col="col"]'), function (key, item) {
                var $item = $(item);
                var rowValue = $item.attr('row');
                var value = $item.val();
                if (value != null && value != "") {
                    promiseList.push(app.pullDataById(document.postFlatValueDetailWS, {
                        data: {
                            flatId: flatId,
                            fiscalYearId: fiscalYearId,
                            employeeId: rowValue,
                            flatValue: value
                        }
                    }));
                }

            });

            Promise.all(promiseList).then(function (response) {
                App.unblockUI("#hris-page-content");
                app.showMessage("Flat Value assigned successfully!!!");
            }, function (error) {
                App.unblockUI("#hris-page-content");
            });



        });

        //IMPORT EXPORT CHANGES
        var map = {
            'EMPLOYEE_ID': 'EMPLOYEE_ID',
            'EMPLOYEE_CODE': 'EMPLOYEE_CODE',
            'FULL_NAME': 'Name',
            'FISAL_YEAR_ID': 'FISAL_YEAR_ID',
            'FLAT_VALUE_ID': 'FLAT_VALUE_ID'
        };

        $('#excelExport').on('click', function () {
            console.log();
            map['FLAT_VALUE_NAME']=selectedflatValueName;
            console.log(map);
            var exportData = createcodes($table, map);
//            console.log(exportData);
            app.excelExport(exportData, map, selectedflatValueName+'.xlsx');
        });


        function createcodes($tableId, map) {
            

            var retrunValues = [];
            $tableId.each(function (i, row) {
                var $row = $(row);
                var $inputValues = $row.find('input[class*="flatValueInput"]');
                $inputValues.each(function (key, value) {
                var mapValues = [];
                    var currentelement = $(this);
                    $.each(map, function (k, v) {
                        mapValues[k] = currentelement.attr(k)
                    });
                    retrunValues[key] = mapValues;
                });
            });

            return retrunValues;
        }



    });
})(window.jQuery, window.app);
