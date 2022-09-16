(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, false);
//        
        var $table = $('#table');
        app.searchTable($table, ['FULL_NAME', 'EMPLOYEE_CODE']);

        var $search = $('#search');

        $search.on('click', function () {
//            var q = document.searchManager.getSearchValues();
//            app.pullDataById(document.getWeeklyRosterListLink, {q}).then(function (response) {
//                app.renderKendoGrid($table, response.data)
//
//            }, function () {
//
//            });

            if (typeof $table.data('kendoGrid') === 'undefined') {
                $table.kendoGrid(kendoConfig);
            } else {
                $table.data('kendoGrid').dataSource.read();
                $table.data('kendoGrid').refresh();
            }



        });




        var crudServiceBaseUrl = "https://demos.telerik.com/kendo-ui/service",
                dataSource = new kendo.data.DataSource({
                    transport: {
                        read: {
                            url: data.pvmReadLink,
                            type: "POST",
//                                    url: crudServiceBaseUrl + "/Products",
//                                    dataType: "jsonp"
                        },
                        update: {
                            url: data.pvmUpdateLink,
                            type: "POST",
                            success: function (result) {
                                console.log('sdfsdf');
                            }
//                                    url: crudServiceBaseUrl + "/Products/Update",
//                                    dataType: "jsonp"
                        },
                        parameterMap: function (options, operation) {

                            var q = document.searchManager.getSearchValues();

                            if (operation === "read") {
                                return {
                                    q: q
                                };
                            }
//                            if (operation === "update") {
//                                $table.data('kendoGrid').dataSource.read();
//                                $table.data('kendoGrid').refresh();
//                            }

                            if (operation !== "read" && options.models) {
                                return {models: kendo.stringify(options.models)};
                            }
                        }
                    },
                    batch: true,
                    pageSize: 20,
                    schema: {
                        model: {
                            id: "EMPLOYEE_ID",
                            fields: {
                                EMPLOYEE_ID: {editable: false, nullable: true},
                                EMPLOYEE_CODE: {editable: false},
                                FULL_NAME: {editable: false},
                                SUNARR: {},
                                MONARR: {},
                                TUEARR: {},
                                WEDARR: {},
                                THUARR: {},
                                FRIARR: {},
                                SATARR: {},
//                                UnitPrice: { type: "number", validation: { required: true, min: 1} }
                            }

//                                    fields: {
//                                        ProductID: { editable: false, nullable: true },
//                                        ProductName: { validation: { required: true } },
//                                        UnitPrice: { type: "number", validation: { required: true, min: 1} },
//                                        Discontinued: { type: "boolean" },
//                                        UnitsInStock: { type: "number", validation: { min: 0, required: true } }
//                                    }
                        }
                    }
                });

        $table.kendoGrid({
            toolbar: ["excel", "pdf"],
             excel: {
                fileName: 'WeeklyRoaster.xlsx',
                filterable: false,
                allPages: true
            },
            dataSource: dataSource,
            pageable: true,
            columns: [
                {field: "EMPLOYEE_CODE", title: "Code", width: "90px"},
                {field: "FULL_NAME", title: "Employee", width: "110px"},
                {field: "SUNARR", title: "Sunday", width: "100px", editor: categoryDropDownEditor, template: "#=SUNARR.SHIFT_ENAME#"},
                {field: "MONARR", title: "Monday", width: "100px", editor: categoryDropDownEditor, template: "#=MONARR.SHIFT_ENAME#"},
                {field: "TUEARR", title: "Tuesday", width: "100px", editor: categoryDropDownEditor, template: "#=TUEARR.SHIFT_ENAME#"},
                {field: "WEDARR", title: "Wednesday", width: "100px", editor: categoryDropDownEditor, template: "#=WEDARR.SHIFT_ENAME#"},
                {field: "THUARR", title: "Thursday", width: "100px", editor: categoryDropDownEditor, template: "#=THUARR.SHIFT_ENAME#"},
                {field: "FRIARR", title: "Friday", width: "100px", editor: categoryDropDownEditor, template: "#=FRIARR.SHIFT_ENAME#"},
                {field: "SATARR", title: "Saturday", width: "100px", editor: categoryDropDownEditor, template: "#=SATARR.SHIFT_ENAME#"},
                {command: ["edit"], title: "&nbsp;", width: "250px"}
            ],

            editable: "inline"
        });





    });






})(window.jQuery, window.app);

function customBoolEditor(container, options) {
    var guid = kendo.guid();
    $('<input class="k-checkbox" id="' + guid + '" type="checkbox" name="Discontinued" data-type="boolean" data-bind="checked:Discontinued">').appendTo(container);
    $('<label class="k-checkbox-label" for="' + guid + '">&#8203;</label>').appendTo(container);
}


function categoryDropDownEditor(container, options) {
    $('<input required name="' + options.field + '"/>')
            .appendTo(container)
            .kendoDropDownList({
                autoBind: false,
                dataTextField: "SHIFT_ENAME",
                dataValueField: "SHIFT_ID",
                dataSource: {
                    type: "odata",
                    data: document.shifts
                }
            });
}