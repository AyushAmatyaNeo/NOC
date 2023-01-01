(function ($, app) {
    "use strict";
    $(document).ready(function () {
    	var columns = [];
    	// columns.push({field: "A", title: "ID", width: 80});
    	// columns.push({field: "B", title: "NAME", width: 120});
    	// columns.push({field: "C", title: "AMOUNT", width: 120});
    	// app.initializeKendoGrid($table, columns);

    	$("#submit").on('click', function(e){
	        // if(prompt("Make sure all options are correctly selected. Type CONFIRM to proceed.") !== "CONFIRM"){ return; }


            // var selectedFile = e.target.files[0];
            // console.log(selectedFile);

            var selectedFile = document.getElementById('excelImport').files[0];
            var fileUploadedFlag = document.getElementById("excelImport").files.length;

            var reader = new FileReader();

            // reader.onload = function(event) {
            //     var data = event.target.result;
            //     var workbook = XLSX.read(data, {
            //         type: 'binary'
            // });
            
            // console.log(document.uploadUrl);

            app.serverRequest(document.uploadUrl, {data : excelData}).then(function(){
                    app.showMessage('Operation successfull', 'success');
            }, function (error) {
                    console.log(error);
            });


            // console.log(selectedFile);
            // return false;


            // var fiscalYearId = $fiscalYearId.val();
            // if(valueType == null || fileUploadedFlag == 0 || fiscalYearId == -1){
            //     app.showMessage('One or more input missing', 'warning');
            //     return;
            // }
            if (fileUploadedFlag == 1) {

                var reader = new FileReader();

                // reader.onload = function(event) {
                //     var data = selectedFile;
                //     var workbook = XLSX.read(data, {
                //         type: 'binary'
                // });

                // workbook.SheetNames.forEach(function(sheetName) {
                //       //var XL_row_object = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[sheetName]);
                //       var XL_row_object = XLSX.utils.sheet_to_json(workbook.Sheets.Sheet1, {header: "A"}); 
                //       var json_object = JSON.stringify(XL_row_object);
                //       excelData = JSON.parse(json_object);
                //       // app.renderKendoGrid($table, excelData);
                //     });
                // }
                // reader.onerror = function(event) {
                //   console.error("File could not be read! Code " + event.target.error.code);
                // };
                // reader.readAsBinaryString(selectedFile);

                // console.log(workbook)


            }


            // if(typeFlag == 1){
            // 	app.serverRequest(document.updateFlatValuesLink, {data : excelData, fiscalYearId: $fiscalYearId.val(), flatValueId: valueType, basedOn: basedOnFlag}).then(function(){
	        //         app.showMessage('Operation successfull', 'success');
	        //     }, function (error) {
	        //         console.log(error);
	        //     });
            // }
            // if(typeFlag == 2){
            // 	if($monthId.val() == -1){
	        //         app.showMessage('Month not selected', 'warning');
	        //         return;
	        //     }
            // 	app.serverRequest(document.updateMonthlyValuesLink, {data : excelData, fiscalYearId: $fiscalYearId.val(), monthId: $monthId.val(), monthlyValueId: valueType, basedOn: basedOnFlag}).then(function(){
	        //         app.showMessage('Operation successfull', 'success');
	        //     }, function (error) {
	        //         console.log(error);
	        //     });
            // }
            // if(typeFlag == 3){
            //     if($monthId.val() == -1){
            //         app.showMessage('Month not selected', 'warning');
            //         return;
            //     }
            //     if($salaryTypeId.val() == -1){
            //         app.showMessage('Salary type not selected', 'warning');
            //         return;
            //     }
            // 	app.serverRequest(document.updatePayValuesLink, {data : excelData, fiscalYearId: $fiscalYearId.val(), monthId: $monthId.val(), payValueId: valueType, salaryTypeId: $salaryTypeId.val(), basedOn: basedOnFlag}).then(function(){
	        //         app.showMessage('Operation successfull', 'success');
	        //     }, function (error) {
	        //         console.log(error);
	        //     });
            // }
    	});

      	$("#excelImport").change(function(evt){

            /**
             * GETTING EXCEL FILE DETAILS
             * */
            var selectedFile = evt.target.files[0];

            var fileName = selectedFile['name'];
            var extension = fileName.substr(fileName.length - 4);
            // console.log(extension);
            // return false;
            /**
             * CHECK EXTENSION ONLY ACCEPT EXCEL FILE
             * */
            if (extension !== 'xlsx') {

                alert('Please select Excel File');
                // console.log(selectedFile);
                
                // selectedFile.value = '';

            } else {

                var reader = new FileReader();

                reader.onload = function(event) {
                    var data = event.target.result;
                    var workbook = XLSX.read(data, {
                        type: 'binary'
                    
                });
                // console.log(workbook.SheetNames);


                workbook.SheetNames.forEach(function(sheetName) {
                      //var XL_row_object = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[sheetName]);
                      var XL_row_object = XLSX.utils.sheet_to_json(workbook.Sheets.Dates, {header: "A"}); 
                      var json_object = JSON.stringify(XL_row_object);
                      excelData = JSON.parse(json_object);
                    
                      // app.renderKendoGrid($table, excelData);
                    });
                }
                reader.onerror = function(event) {
                  console.error("File could not be read! Code " + event.target.error.code);
                };
                reader.readAsBinaryString(selectedFile);

                // console.log(excelData);


            }

            
      	});
    });
})(window.jQuery, window.app);