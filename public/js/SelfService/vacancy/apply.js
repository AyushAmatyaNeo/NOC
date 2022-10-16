(function ($, app) {
    //    'use strict';
    $(document).ready(function () {  
        console.log(document.eduFaculty);
        var max_fields_edu = 5; // Maximum input fields
        var t_education = $('#educationalbody');
        var btn_edu_remove = $('.btn-edu-remove');
        var btn_add_edu = $('.btn-add-edu');
        var degrees = [];
        // app.serverRequest( document.eduDetail,'').then (function (response) {
        //     if (response.success) {
        //         degrees = response.degrees;
        //     }
        //     console.log(degrees);
        var appendDataedu =
            `<tr>
            <td>
            <div for="edu_institute" class="form-group">
                <input type="text" name="edu_institute[]" class="form-control form-control-sm" required>
            </div>
        </td>
                                                <td>
                                                    <div for="level_id" class="form-group">
                                                    <select name="level_id[]" class="form-control form-control-sm level_id" required>
                                                    <option value="">-- Select -- </option>
                                                        
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
                                                <div class="form-group">
                                                    <select class="form-control form-control-sm" id="" name="rank_type[]" required>
                                                        <option value="">Select Level</option>
                                                        <option value="GPA">GPA</option>
                                                        <option value="Percentage">Percentage</option>
                                                    </select>
                                                </div>
                                            </td>
                                            <td>
                                                <div for="rank_value" class="form-group">
                                                    <input type="text" name="rank_value[]" class="form-control form-control-sm" required>
                                                </div>
                                            </td>
                                            <td>
                                                <div for="univerity_board" class="form-group" id="rank_value_error">
                                                <select name="univerity_board[]" class="form-control form-control-sm university" required>
                                                    <option value=""> Select University</option>
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
                                                <div for="passed_year" class="form-group">
                                                    <input type="number" name="passed_year[]" class="form-control form-control-sm" required>
                                                </div>
                                            </td>
                                            <td>
                                                <i class="fa fa-minus-circle btn-edu-remove" aria-hidden="true" style="color: red; cursor: pointer"></i>
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
        
        $('.inclusion').on('change', function () {
            var val = [];
            $(':checkbox:checked').each(function (i) {
              val[i] = $(this).val();
            });
            var level_id = $('#rec_level_id').val();
            var position_id = $('#position_id').val();
            console.log(level_id);
            console.log(position_id);

            if (val.length > 1) {
                app.serverRequest(document.ajaxurl, {
                    'level_id': level_id,
                    'position_id': position_id
                }).then(function (success) {
                    console.log(success);
                    if (success.success) {
                        var now = new Date();
                        var year = now.getFullYear();
                        var month = now.getMonth() + 1;
                        var day = now.getDate();
                        // console.log(year+'-'+month+'-'+day);
                        // console.log(success.data.END_DATE);
                        // console.log(new Date(year+'-'+month+'-'+day).getTime() / 1000);
                        // console.log(new Date(success.data.END_DATE).getTime());

                        // month = (month < 10) ? (month = "-0" + month) : month;
                        // day = (day < 10) ? (day = "-0" + day) : day;
                        var Unix_today = new Date(year+'-'+month+'-'+day).getTime() / 1000;
                        Unix_end = new Date(success.data.END_DATE).getTime() / 1000;
                        if (val.length == 1) {
                            if (Unix_end >= Unix_today) {
                            $('#inclusion_amount').val(success.data.NORMAL_AMOUNT);
                            } else {
                            $('#inclusion_amount').val(success.data.LATE_AMOUNT);
                            }
                        }else {
                            if (Unix_end >= Unix_today) {
                            let total = parseInt(success.data.NORMAL_AMOUNT) + parseInt(success.data.INCLUSION_AMOUNT) * (val.length - 2);
                            $('#inclusion_amount').val(total);
                            } else {
                            let total = parseInt(success.data.LATE_AMOUNT) + parseInt(success.data.INCLUSION_AMOUNT) * (val.length - 2);
                            $('#inclusion_amount').val(total);
                            }
                        }
                        if (val == '') {
                            
                        }
                    }
                })
            } else {
               $('#inclusion_amount').val('');
            }
        //   });
        });
        
    });   
    
})(window.jQuery, window.app);