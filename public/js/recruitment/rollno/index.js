(function ($, app) {
    "use strict";
    $(document).ready(function () {
    	var columns = [];
        var excelData;

    	$("#submit").on('click', function(e){

            var selectedFile = document.getElementById('excelImport').files[0];
            var fileUploadedFlag = document.getElementById("excelImport").files.length;

            var reader = new FileReader();

            var sendMail = $("#sendMail").is(':checked');
            var generateAdmit = $("#generateAdmit").is(':checked');


            app.serverRequest(document.uploadUrl, {data : excelData, sendMail: sendMail, generateAdmit:generateAdmit}).then(function(){
                    app.showMessage('Operation successfull', 'success');

            }, function (error) {
                    app.showMessage('Excel File Unable to Upload', 'error');
            });

            if (fileUploadedFlag == 1) {
                var reader = new FileReader();
            }
    	});

      	$("#excelImport").change(function(evt){

            /**
             * GETTING EXCEL FILE DETAILS
             * */
            var selectedFile = evt.target.files[0];

            var fileName = selectedFile['name'];
            var extension = fileName.substr(fileName.length - 4);
        
            /**
             * CHECK EXTENSION ONLY ACCEPT EXCEL FILE
             * */
            if (extension !== 'xlsx') {

                alert('Please select Excel File');

            } else {

                var reader = new FileReader();

                reader.onload = function(event) {
                    var data = event.target.result;
                    var workbook = XLSX.read(data, {
                        type: 'binary'
                    
                });

                workbook.SheetNames.forEach(function(sheetName) {
                      var XL_row_object = XLSX.utils.sheet_to_json(workbook.Sheets[sheetName], {header: "A"}); 
                      var json_object = JSON.stringify(XL_row_object);
                      excelData = JSON.parse(json_object);
                    });
                }
                
                reader.onerror = function(event) {
                  console.error("File could not be read! Code " + event.target.error.code);
                };

                reader.readAsBinaryString(selectedFile);
            }

            
      	});
    });
})(window.jQuery, window.app);