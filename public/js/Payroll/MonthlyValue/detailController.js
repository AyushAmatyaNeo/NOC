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

        var $grid = $('#monthlyValueDetailGrid');
        var $header = $('#monthlyValuesDetailHeader');
        var $table = $('#monthlyValueDetailTable');
        var $footer = $('#monthlyValueDetailFooter');

        var exportMonthList;
        var selectedMonthlyValueName;

        app.populateSelect($monthlyValueId, document.monthlyValues, "MTH_ID", "MTH_EDESC", "Select Monthly Value");
        app.populateSelect($fiscalYearId, document.fiscalYears, "FISCAL_YEAR_ID", "FISCAL_YEAR_NAME", "Select Fiscal Year");
        
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
            if ($fiscalYearId.val() == -1) {
                app.showMessage("No fiscal year Selected.", 'error');
                $fiscalYearId.focus();
                return;
            }
            if ($monthlyValueId.val() == -1) {
                app.showMessage("No monthly value Selected.", 'error');
                $monthlyValueId.focus();
                return;
            }
            app.pullDataById(document.getMonthlyValueDetailWS, {
                mthId: $monthlyValueId.val(),
                fiscalYearId: $fiscalYearId.val(),
                employeeFilter: document.searchManager.getSearchValues()}).then(function (response) {
//                console.log(response.employeeList);
                initTable($fiscalYearId.val(), response.employeeList, response.data);
            }, function (error) {
                console.log(error);
            });
        });

        var findMonthValue = function (serverData, employeeId, monthId) {
            var result = serverData.filter(function (item) {
                return item['EMPLOYEE_ID'] == employeeId && item['MONTH_ID'] == monthId;
            });

            if (result.length > 0) {
                return result[0]['MTH_VALUE'];
            } else {
                return null;
            }
        };

        var initTable = function (fiscalYearId, employeeList, serverData) {
            var selectedfiscalYearId = $fiscalYearId.val();
            var selecetedMonthlyValueId = $monthlyValueId.val();
            selectedMonthlyValueName = $("#monthlyValueId option:selected").text();
            let selectedMonthly = $monthId.val();
            
             if (selectedMonthly != null && selectedMonthly != -1 && selectedMonthly != '') {
                var filteredMonths = months.filter(function (item) {
                    if (item['FISCAL_YEAR_ID'] == fiscalYearId && item['MONTH_ID'] == selectedMonthly) {
                        return item;
                    }
                });
            } else {
                var filteredMonths = months.filter(function (item) {
                    if (item['FISCAL_YEAR_ID'] == fiscalYearId) {
                        return item;
                    }
                });
            }

            exportMonthList = filteredMonths;

            $header.html('');
//            $header.html('<tr>');
            $header.append($('<th>', {text: 'Id'}));
            $header.append($('<th>', {text: 'Code'}));
            $header.append($('<th>', {text: 'Name'}));
            $.each(filteredMonths, function (index, item) {
                $header.append($('<th>', {text: item['MONTH_EDESC']}));
            });
            $header.append($('<th>', {text: ''}));
//            $header.append('</tr>');

            $grid.html('');
            $.each(employeeList, function (index, item) {
                var $tr = $('<tr>');

                $tr.append($('<td>', {text: item['EMPLOYEE_ID']}));
                $tr.append($('<td>', {text: item['EMPLOYEE_CODE']}));
//                $tr.append($('<td>', {text: item['FULL_NAME'],
//                    FISAL_YEAR_ID:selectedfiscalYearId,
//                    class: 'allRowDetails',
//                    ID:'SDFSDF',
////                    MONTHLY_VALUE_NAME:selectedMonthlyValueName,
////                    MONTHLY_VALUE_ID:selecetedMonthlyValueId,
////                    EMPLOYEE_ID: item['EMPLOYEE_ID'],
////                    EMPLOYEE_CODE: item['EMPLOYEE_CODE'],
////                    FULL_NAME: item['FULL_NAME']
//                }));

                var monthValueDtl = '';
                $.each(filteredMonths, function (k, v) {
                    var tempValue = findMonthValue(serverData, item['EMPLOYEE_ID'], v['MONTH_ID']);
                    tempValue = (tempValue != null) ? tempValue : '';
                    monthValueDtl += v.MONTH_EDESC + `='` + tempValue + `' `;
                });

                var appendData = `<td><label class='allRowDetails' `
                        + `FISAL_YEAR_ID='` + selectedfiscalYearId
                        + `'MONTHLY_VALUE_NAME='` + selectedMonthlyValueName
                        + `'MONTHLY_VALUE_ID='` + selecetedMonthlyValueId
                        + `'EMPLOYEE_ID='` + item['EMPLOYEE_ID']
                        + `'EMPLOYEE_CODE='` + item['EMPLOYEE_CODE']
                        + `'FULL_NAME='` + item['FULL_NAME']
                        + `' ` + monthValueDtl + `>`
                        + item['FULL_NAME']
                        + `</label></td>`;
                $tr.append(appendData);

                $.each(filteredMonths, function (k, v) {
                    var $td = $('<td>');
                    $td.append($('<input>', {type: 'number', row: item['EMPLOYEE_ID'], col: v['MONTH_ID'], value: findMonthValue(serverData, item['EMPLOYEE_ID'], v['MONTH_ID']), class: 'form-control',style:'width: 100px;'}));
                    $tr.append($td);
                });
                var $td = $('<td>');
                $td.append($('<input>', {type: 'number', row: item['EMPLOYEE_ID'], class: 'group form-control',style:'width: 100px;'}));
                $tr.append($td);

                $grid.append($tr);
            });

            $footer.html('');
            var $tr = $('<tr>');

            $tr.append($('<td>', {text: ''}));
            $tr.append($('<td>', {text: ''}))
            $tr.append($('<td>', {text: ''}))

            $.each(filteredMonths, function (k, v) {
                var $td = $('<td>');
                $td.append($('<input>', {type: 'number', col: v['MONTH_ID'], class: 'group form-control'}));
                $tr.append($td);
            });
            var $td = $('<td>');
            $td.append($('<input>', {type: 'number', class: 'group form-control'}));
            $tr.append($td);

            $footer.append($tr);
//            $table.bootstrapTable({height: 400});
        };

        $table.on('change', '.group', function () {
            var $this = $(this);
            var value = $this.val();
            var row = $this.attr('row');
            if (typeof row !== typeof undefined && row !== false) {
                $('input[row=' + row + ']').val(value);
            }
            var col = $this.attr('col');
            if (typeof col !== typeof undefined && col !== false) {
                $('input[col=' + col + ']').val(value);
            }

            if ((typeof row === "undefined" || row === false) && (typeof col === "undefined" || col === false)) {
                $.each($this.parent().parent().children(), function (key, item) {
                    var $td = $(item);
                    var $input = $td.find('input');
                    if ($input.attr('col')) {
                        $input.val(value);
                        $input.trigger('change');
                    }
                });
            }


        });

        $assignMonthlyValueBtn.on('click', function () {
            var fiscalYearId = $fiscalYearId.val();
            var mthId = $monthlyValueId.val();

            var promiseList = [];
            App.blockUI({target: "#hris-page-content"});
            $.each($grid.find('input'), function (key, item) {
                var $item = $(item);
                var rowValue = $item.attr('row');
                var colValue = $item.attr('col');
                var value = $item.val();
                if (typeof rowValue !== "undefined" && rowValue != null && rowValue != "" && typeof colValue !== "undefined" && colValue != null && colValue != "" && typeof value !== "undefined" && value != null) {
                    promiseList.push(app.pullDataById(document.postMonthlyValueDetailWS, {
                        data: {
                            mthId: mthId,
                            fiscalYearId: fiscalYearId,
                            employeeId: rowValue,
                            monthId: colValue,
                            mthValue: value
                        }
                    }));
                }

            });

            Promise.all(promiseList).then(function (response) {
                App.unblockUI("#hris-page-content");
                app.showMessage("Monthly Value assigned successfully!!!");
            }, function (error) {
                App.unblockUI("#hris-page-content");
            });



        });


        //  IMPORT EXPORT CHANGES
        var map = {
            'EMPLOYEE_ID': 'EMPLOYEE_ID',
            'EMPLOYEE_CODE': 'EMPLOYEE_CODE',
            'FULL_NAME': 'Name',
            'FISAL_YEAR_ID': 'FISAL_YEAR_ID',
            'MONTHLY_VALUE_ID': 'MONTHLY_VALUE_ID',
            'MONTHLY_VALUE_NAME': 'MONTHLY_VALUE'
        };

        $('#excelExport').on('click', function () {
            console.log(exportMonthList);
            $.each(exportMonthList, function (k, v) {
                map[v.MONTH_EDESC] = v.MONTH_EDESC;
            });
//            console.log(map);
            var exportData = createcodes($table, map);
            app.excelExport(exportData, map, selectedMonthlyValueName+'.xlsx');
        });

        function createcodes($tableId, map) {
//            console.log('createCode');


            var retrunValues = [];
            $tableId.each(function (i, row) {
                var $row = $(row);
                var $inputValues = $row.find('label[class*="allRowDetails"]');
                $inputValues.each(function (key, value) {
//                    console.log(key, value);
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
