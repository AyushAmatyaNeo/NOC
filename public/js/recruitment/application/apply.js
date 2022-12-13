(function ($, app) {
    //    'use strict';
    $(document).ready(function () {  
        var max_fields_edu = 5; // Maximum input fields
        var t_education = $('#educationalbody');
        var btn_edu_remove = $('.btn-edu-remove');
        var btn_add_edu = $('.btn-add-edu');

        var ti_education = $('#educationalbodyinternal');
        var btn_add_eduinternal = $('.btn-add-eduinternal');
        var btn_edu_removeinternal = $('.btn-edu-removeinternal');
        var btn_add_eduinternal = $('.btn-add-eduinternal');
        var degrees = [];
       
        var appendDataedu =
            `<tr>
                <td>
                    <input type="text" name="institute[]">
                </td>
                <td>
                    <div for="level_id" class="form-group">
                    <select name="level_id[]" class="form-control form-control-sm level_id" required>
                    <option value="">-- Select -- </option>
                        
                    </select>
                    </div>
                </td>
                <td>
                    <div for="university_board" class="form-group" id="rank_value_error">
                    <select name="university_board[]" class="form-control form-control-sm university" required>
                        <option value=""> Select University</option>
                    </select>
                    
                    </div>
                </td>
                <td>
                    <div for="faculty" class="form-group">
                    <select name="faculty[]" id="faculty" class="form-control form-control-sm faculty" required>
                        
                    </select>
                    
                    </div>
                </td>
                <td>
                    <div for="major_subject" class="form-group">
                    <select name="major_subject[]" class="form-control form-control-sm course" required>
                    <option value=""> Select Course</option>
                    </select>
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <select class="form-control form-control-sm" id="" name="rank_type[]" required>
                            <option value="">Select Level</option>
                            <option value="GPA">GPA</option>
                            <option value="Percentage">Percentage</option>
                            <option value="Division/grade">Division/grade</option>
                        </select>
                    </div>
                </td>
                <td>
                    <div for="rank_value" class="form-group">
                        <input type="text" name="rank_value[]" class="form-control form-control-sm" required>
                    </div>
                </td>
                <td>
                    <div for="passed_year" class="form-group">
                        <input type="number" name="passed_year[]" class="form-control form-control-sm" required>
                    </div>
                </td>
                <td>
                    <i class="fa fa-minus-circle btn-edu-remove" aria-hidden="true" style="color: red; cursor: pointer"></i>
                </td>
            </tr>`;

        var appendDataeduinternal =
            `<tr>
                <td>
                    <div for="level_id" class="form-group">
                    <select name="level_id[]" class="form-control form-control-sm level_id" required>
                    <option value="">-- Select -- </option>
                        
                    </select>
                    </div>
                </td>
                <td>
                    <div for="university_board" class="form-group" id="rank_value_error">
                    <select name="university_board[]" class="form-control form-control-sm university" required>
                        <option value=""> Select University</option>
                    </select>
                    
                    </div>
                </td>
                <td>
                    <div for="faculty" class="form-group">
                    <select name="faculty[]" id="faculty" class="form-control form-control-sm faculty" required>
                        
                    </select>
                    
                    </div>
                </td>
                <td>
                    <div for="major_subject" class="form-group">
                    <select name="major_subject[]" class="form-control form-control-sm course" required>
                    <option value=""> Select Course</option>
                    </select>
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <select class="form-control form-control-sm" id="" name="rank_type[]" required>
                            <option value="">Select Level</option>
                            <option value="GPA">GPA</option>
                            <option value="Percentage">Percentage</option>
                            <option value="Division/grade">Division/grade</option>
                        </select>
                    </div>
                </td>
                <td>
                    <div for="rank_value" class="form-group">
                        <input type="text" name="rank_value[]" class="form-control form-control-sm" required>
                    </div>
                </td>
                <td>
                    <div for="passed_year" class="form-group">
                        <input type="number" name="passed_year[]" class="form-control form-control-sm" required>
                    </div>
                </td>
                <td>
                    <i class="fa fa-minus-circle btn-edu-removeinternal" aria-hidden="true" style="color: red; cursor: pointer"></i>
                </td>
            </tr>`;


        $(btn_add_edu).click(function (e) {
            e.preventDefault();
            var edu_len = $(document).find('#educationalbody > tr').length;
            console.log(edu_len);
            if (edu_len < 5) {
                $(t_education).append(appendDataedu);
                app.populateSelect($('#education tbody').find('.level_id:last'),document.eduDetail,'ACADEMIC_DEGREE_ID','ACADEMIC_DEGREE_NAME', '-----',null,1,false);
                app.populateSelect($('#education tbody').find('.faculty:last'),document.eduFaculty,'ACADEMIC_PROGRAM_ID','ACADEMIC_PROGRAM_NAME', '-----',null,1,false);
                app.populateSelect($('#education tbody').find('.university:last'),document.eduUniversity,'ACADEMIC_UNIVERSITY_ID','ACADEMIC_UNIVERSITY_NAME', '-----',null,1,false);
                app.populateSelect($('#education tbody').find('.course:last'),document.eduCourses,'ACADEMIC_COURSE_ID','ACADEMIC_COURSE_NAME', '-----',null,1,false);
            }
            
        });
        $(t_education).on("click", ".btn-edu-remove", function (e) {
          e.preventDefault();
          var tr = this;
          var conf = confirm("Are you sure?");
          // console.log('btn-edu-remove');  
          var edid = $('.btn-remove-edu').val();
          if (conf == true) {
            $(tr).closest('tr').remove();
          }
        });

        $(btn_add_eduinternal).click(function (e) {
            e.preventDefault();
            var edu_len = $(document).find('#educationalbodyinternal > tr').length;
            console.log(edu_len);
            if (edu_len < 5) {
                $(educationalbodyinternal).append(appendDataeduinternal);
                app.populateSelect($('#education tbody').find('.level_id:last'),document.eduDetail,'ACADEMIC_DEGREE_ID','ACADEMIC_DEGREE_NAME', '-----',null,1,false);
                app.populateSelect($('#education tbody').find('.faculty:last'),document.eduFaculty,'ACADEMIC_PROGRAM_ID','ACADEMIC_PROGRAM_NAME', '-----',null,1,false);
                app.populateSelect($('#education tbody').find('.university:last'),document.eduUniversity,'ACADEMIC_UNIVERSITY_ID','ACADEMIC_UNIVERSITY_NAME', '-----',null,1,false);
                app.populateSelect($('#education tbody').find('.course:last'),document.eduCourses,'ACADEMIC_COURSE_ID','ACADEMIC_COURSE_NAME', '-----',null,1,false);
            }
            
        });
        $(ti_education).on("click", ".btn-edu-removeinternal", function (e) {
          e.preventDefault();
          var tr = this;
          var conf = confirm("Are you sure?");
          // console.log('btn-edu-remove');  
          var edid = $('.btn-remove-edu').val();
          if (conf == true) {
            $(tr).closest('tr').remove();
          }
        });
        
    });


    // H. Experience Detail START ------------------------------------------xxxxxxxxxxxxx-----------------------

    $(document).ready(function () {  
      var max_fields_exp = 5; // Maximum input fields
      var t_experiance = $('#experiancebody');
      var btn_exp_remove = $('.btn-exp-remove');
      var btn_add_exp = $('.btn-add-exp');
      var tr_id = 1;
      var y = 1; // initial row value
      
      // $(document).on("click", '.btn-add-exp', addexprow);

      var appendDataexp =
           `<tr>
                  <td>
                      <input type="text" name="org_name[]" class="form-control form-control-sm">
                  </td>
                  <td>
                      <input type="text" name="post_name[]" class="form-control form-control-sm">
                  </td>
                  <td>
                      <input type="text" name="service_name[]" class="form-control form-control-sm">
                  </td>
                  <td>
                      <input type="text" name="org_level[]" class="form-control form-control-sm">
                  </td>
                  <td>
                      <div for="employee_type" class="form-group">
                          <select name="employee_type[]" class="form-control form-control-sm">
                              <option></option>
                              <option value="1">Permanent</option>
                              <option value="2">Temporary</option>
                              <option value="3">Contract</option>
                          </select>
                      </div>
                  </td>
                  <td>
                    <input type="date" name="from_date[]" class="form-control form-control-sm">
                  </td>
                  <td>
                        <input type="date" name="to_date[]" class="form-control form-control-sm">
                  </td>
                  <td>
                      <i class="fa fa-minus-circle btn-exp-remove" aria-hidden="true" style="color: red; cursor: pointer"></i>
                  </td>
          </tr>`;


      $(btn_add_exp).click(function (e) {
        alert('here');
            e.preventDefault();
            var exp_len = $(document).find('#experiancebody > tr').length;
            console.log(exp_len);
            if (edu_len < 5) {
                $(t_experiance).append(appendDataexp);
            }
            
        });
      
      $(t_experiance).on("click", ".btn-exp-remove", function (e) {
        e.preventDefault();
        $(this).closest('tr').remove();
        y--;
      });

     });
      // H. Experience Detail END ------------------------------------------xxxxxxxxxxxxx-----------------------


    // $(document).ready(function () {
    //     app.startEndDatePickerWithNepali('nepaliStartDate1', 'startDate', 'nepaliEndDate1', 'endDate');
    //     app.datePickerWithNepali("form-loanDate","nepaliDate");
    //     window.app.floatingProfile.setDataFromRemote(document.employeeId);
    // });

   
})(window.jQuery, window.app);