(function ($, app) {
    "use strict";
    $(document).ready(function () {
    	let $flatValue = $("#flatValue");
    	let $employeeIdBased = $("#employeeIdBased");
    	let $employeeCodeBased = $("#employeeCodeBased");
    	let $table = $("#table");
    	var excelData;
        let basedOnFlag = 1;
    	$("select").select2();
    	$(".months").hide();

        $("#employeeIdBased").click(function(){ basedOnFlag = 1; });
        $("#employeeCodeBased").click(function(){ basedOnFlag = 2; });

    	var columns = [];
    	columns.push({field: "A", title: "ID", width: 80});
    	columns.push({field: "B", title: "NAME", width: 120});
    	columns.push({field: "C", title: "AMOUNT", width: 120});
    	app.initializeKendoGrid($table, columns);

    	$("#submit").on('click', function(){
            if(prompt("Make sure all options are correctly selected. Type CONFIRM to proceed.") !== "CONFIRM"){ return; }
            var fileUploadedFlag = document.getElementById("excelImport").files.length;
            if(fileUploadedFlag == 0){
                app.showMessage('No file selected.', 'warning');
                return;
            }
        	app.serverRequest(document.updateSalaryLink, {data : excelData, basedOn: basedOnFlag}).then(function(){
                app.showMessage('Operation successfull', 'success');
            }, function (error) {
                console.log(error);
            });
    	});

      	$("#excelImport").change(function(evt){
            var selectedFile = evt.target.files[0];
            var reader = new FileReader();
            reader.onload = function(event) {
              var data = event.target.result;
              var workbook = XLSX.read(data, {
                  type: 'binary'
              });
              workbook.SheetNames.forEach(function(sheetName) {
                  //var XL_row_object = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[sheetName]);
                  var XL_row_object = XLSX.utils.sheet_to_json(workbook.Sheets.Sheet1, {header: "A"});
                  var json_object = JSON.stringify(XL_row_object);
                  excelData = JSON.parse(json_object);
                  app.renderKendoGrid($table, excelData);
                });
            }
            reader.onerror = function(event) {
              console.error("File could not be read! Code " + event.target.error.code);
            };
            reader.readAsBinaryString(selectedFile);
      	});
    });
})(window.jQuery, window.app);