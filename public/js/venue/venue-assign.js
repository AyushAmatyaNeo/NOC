(function ($, app) {
    'use strict';
    $(document).ready(function () {

        /**
         * Venue assign functions
         */
        var $table = $('#venueAssignTable');

        var action = `
            <div class="clearfix">
                <a class="btn btn-icon-only green" href="${document.editLink}/#:VENUE_ASSIGN_ID#" style="height:17px;" title="View Detail">
                    <i class="fa fa-search"></i>
                </a>
            </div>
                `;

        app.initializeKendoGrid($table, [

            {field: "VENUE_NAME", title: "Venue Name",width: 160, locked: false},
            {field: "ASSIGN_TYPE", title: "Assign Type",  width: 160, locked: false},
            {field: "EXAM_TYPE", title: "Exam Type",  width: 160, locked: false},
            {field: "START_INDEX", title: "Start Index",width: 160, locked: false},
            {field: "END_INDEX", title: "End Index",  width: 160, locked: false},
            {field: "START_TIME", title: "Start Time",  width: 160, locked: false},
            {field: "END_TIME", title: "End Time",  width: 160, locked: false},
            {field: "EXAM_DATE", title: "Exam Date",  width: 160, locked: false},
            {field: "STATUS", title:"Status", width:160, locked:false},
            {field: "VENUE_SETUP_ID", title: "Action", width:160, template: action},
        ], null, null, null, 'Assigned List');

        app.searchTable($table, ['VENUE_NAME', 'START_INDEX', 'END_INDEX', 'ASSIGN_TYPE'], false);

        $('#search').on('click', function () {
            document.body.style.cursor='wait';

            app.pullDataById('', {

            }).then(function (response) {
                if (response.success) {
                    document.body.style.cursor='default';
                    console.log(response);
                    app.renderKendoGrid($table, response.data);
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });

        });

        /**
         * Excel upload functions
         */
        var columns = [];

        var excelData = null;

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
                      var XL_row_object = XLSX.utils.sheet_to_json(workbook.Sheets.Dates, {header: "A"}); 
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
  
          $("#submit").on('click', function(e){
  
              var selectedFile = document.getElementById('excelImport').files[0];
              var fileUploadedFlag = document.getElementById("excelImport").files.length;
  
              var reader = new FileReader();
  
              app.serverRequest(document.uploadUrl, {data : excelData}).then(function(){
                      app.showMessage('Operation successfull', 'success');
              }, function (error) {
                      console.log(error);
              });
  
              if (fileUploadedFlag == 1) {
  
                  var reader = new FileReader();
              }
          });

    });
    
})(window.jQuery, window.app);
