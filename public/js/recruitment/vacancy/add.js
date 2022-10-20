(function ($, app) {
    //    'use strict';
    $(document).ready(function () {
        $('select').select2();
        var $employee = $('#employeeId');
        app.floatingProfile.setDataFromRemote($employee.val());
        var $form = $('#RecruitmentVacancy');
    });
    var ids = 1;
    var optids = 1;
    var incId = 1;
    var skillsId = 1;
    var AdnoCount = 1;
    var vacNo = $(".VacancyNo").length;
    app.populateSelect($('.ServiceEventsId'), document.ServiceEvents, 'SERVICE_EVENT_ID', 'SERVICE_EVENT_NAME', null, null);
    app.populateSelect($('.DepartmentId'), document.DepartmentList, 'DEPARTMENT_ID', 'DEPARTMENT_NAME', null, null);
    app.populateSelect($('.QualificationId'), document.QualificationList, 'ACADEMIC_DEGREE_ID', 'ACADEMIC_DEGREE_NAME', null, null);
    app.populateSelect($('.PositionId'), document.Positions, 'DESIGNATION_ID', 'DESIGNATION_TITLE', null, null);
    app.populateSelect($('.ServiceTypesId'), document.ServiceTypes, 'SERVICE_TYPE_ID', 'SERVICE_TYPE_NAME', null, null);
    app.populateSelect($('.InclusionId'), document.InclusionList, 'OPTION_ID', 'OPTION_EDESC', null, null);
    app.populateSelect($('.LevelId'), document.LevelList, 'FUNCTIONAL_LEVEL_ID', 'FUNCTIONAL_LEVEL_EDESC', null, null);
    app.populateSelect($('.SkillId'), document.Skills, 'SKILL_ID', 'SKILL_NAME', null, null);

    var $vacancyaddBtn = $('.vacancyAddBtn');
    var $vacancyDelBtn = $('#vacancyDetails');
    var $vacancyOptions = $('#addOptvacancyrow');

    // Section to append Vacancy row
    $vacancyaddBtn.on('click', AddVacancyTable);
    // Total vacancy number as per opening value choosen;
    // console.log(document.OpeningVacancyNo);
    $("#OpeningId").change(CheckVno);
    
    // $('.VacancyReservationNo').on('change',CheckReservationNo);
    $(document).on('change','.VacancyReservationNo',CheckReservationNo);

    $(document).on('change','.VacancyNo',function () {
        window.app.pullDataById(document.uniqueCheck, {
             'vacancyNo': '567',
        }).then(function (success) {
            console.log(success);
        })
    });
  

    

    $(document).on("click", ".vacancyAddBtn", function () {
        checkQuota();
    });
   
   
    $(document).on("click", ".vacancyOptAddBtn", addOptions);
    var adno_list = 2;

    
    // Add option in vacancy type
    // Delete vacancy or options:
    $vacancyDelBtn.on('click', '.vacancyVacancyDebtn', function () {
        var selectedtr = $(this).parent().parent().parent().parent();
        var cnf = confirm('Are you sure?');
        if (cnf) {
            selectedtr.detach();
            checkQuota();
        }
    });
    $vacancyDelBtn.on('click', '.vacancyOptionDebtn', function () {
        var selectedtr1 = $(this).parent().parent();
        var cnf = confirm('Are you sure?');
        if (cnf) {
            selectedtr1.remove();
        }
    });
    $('#addvacancyrow').attr('disabled', 'disabled');

    $('#OpeningId').on('change', checkQuota);
    
    //Check Unique Ad No.

    $('.AdNo').on('input', function () {
        var adno = $(this).val();
        var baseurl = window.location.origin;
        // console.log(adno);
        $.ajax({
            type: "POST",
            url: "http://localhost/hana-noc/neo-hris/public/recruitment/vacancy/getadno/" + adno,
            data: { adno: adno },
            success: function (response) {
                var responses = jQuery.parseJSON(response)
                // console.log(responses);
            }
        });
    });

    // functions for all ---------------------------------------------------------------------------------------------------------------------------
    // check quota
    
    function checkQuota() {
        var CurrentVacancy = parseInt($("#rem_vacancy").text());
        // console.log((CurrentVacancy));
        var appendVacancy = $('table').length;
        // console.log((appendVacancy));
        if (CurrentVacancy == 0) {
            $('#vacancyDetails').find('table').remove();
            $('#addvacancyrow').attr('disabled', 'disabled').attr('value', 'Quota Full');
            $(":submit").attr("disabled", true);
        } else if (CurrentVacancy <= appendVacancy) {
            // $('#vacancyDetails').find('table:gt(0)').remove();
            // AddVacancyTable();
            $(":submit").attr("disabled", false);
            $('#addvacancyrow').attr('disabled', 'disabled').attr('value', 'Quota Full');
        } else {
            $(":submit").attr("disabled", false);
            $('#addvacancyrow').removeAttr('disabled').attr('value', 'ADD VACANCY +');
        }
    }
    // Add Vacancy table
    function AddVacancyTable() {
        AdnoCount++;
        vacNo++;
        var appendDataVacancy = `
                <table class="vacancyDetails table table-bordered" id ="vacancyDetails${ids}"> 
                    <thead>
                        <tr>
                            <th colspan="4">Vacancy details</th>      
                        </tr>                                   
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                            <label>Vacancy No.</label>
                            <div style="overflow:hidden">
                                <input type="number" class='VacancyNo' name='VacancyNo[]' required="required"  />
                            </div>
                        </td>
                            <td class="departmentInput">
                                <label>Department</label>
                                <div style="overflow:hidden">
                                    <select class='DepartmentId' name='DepartmentId[]' required="required" >
                                    </select>
                                </div>
                            </td>
                            <td>
                                <label>Qualification</label>
                                <div style="overflow:hidden">
                                    <select class='QualificationId' name='QualificationId[]' required="required" >
                                    </select>
                                </div>
                            </td>
                            <td>
                                <label>Position</label>
                                <div style="overflow:hidden">
                                    <select class='PositionId' name='PositionId[]' required="required" >
                                    </select>
                                </div>
                            </td>
                            <td>
                                <label>Experience</label>
                                <div style="overflow:hidden">
                                    <input type="number" class='Experience' name='Experience[]'  required="required" />
                                </div>
                            </td>
                            <td>
                            <label>Service Type</label>
                            <div style="overflow:hidden">
                                <select class='ServiceTypesId' name='ServiceTypesId[]' required="required" >
                                </select>
                            </div>
                        </td>
                        <td>
                            <label>Service Event</label>
                            <div style="overflow:hidden">
                                <select class='ServiceEventsId' name='ServiceEventsId[]' required="required" >
                                </select>
                            </div>
                        </td>
                            <td>
                            <label>Level</label>
                                <div style="overflow:hidden">
                                    <select class='LevelId' name='LevelId[]' required="required" >
                                    </select>
                                </div>
                            </td>
                        </tr>
                        <tr class="vacancyOptions" id= "vacancyOptions`+ ids + `">
                                <td>
                                    <label>Ad number</label>
                                    <div style="overflow:hidden">
                                        <input class='AdNo' name='AdNo[]'  required="required" />
                                    </div>
                                </td>
                                <td>
                                    <label>Reservation No.</label>
                                    <div style="overflow:hidden">
                                        <input type="number" class='VacancyReservationNo' name='VacancyReservationNo[]' required="required"  />
                                    </div>
                                </td>
                                <td>
                                    <label>Inclusion</label>
                                    <div style="overflow:hidden">
                                        <select class='InclusionId' name='InclusionId[`+ incId + `][]' required="required" required="required" multiple >
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <label>Skills</label>
                                    <div style="overflow:hidden">
                                        <select class='SkillId' name='SkillId[`+ skillsId + `][]'  multiple  required="required">
                                        </select>                                                
                                    </div>
                                </td>
                                <td>
                                    <label>Remark</label>
                                    <div>
                                        <textarea style="width:100%" cols="20" name="Remark[]"  class="Remark"></textarea>
                                    </div>
                                </td>
                                <td>
                                <div>
                                    <input class="vacancyOptAddBtn btn btn-primary" id="addOptvacancyrow`+ optids + `" type="button" value="Add Option +">
                                </div>
                                </td>  
                                <td><input class="vacancyVacancyDebtn btn btn-danger" type="button" value="Delete Vacancy -" style="padding:3px;"></td>                                                  
                        </tr>
                    </tbody>
                </table>
                `;
        // var tableNo =   $('table:eq(1)');           
        $('#vacancyDetails').append(appendDataVacancy);
        $('.DepartmentId:last').select2();
        $('.ServiceEventsId').select2();
        $('.DepartmentId').select2();
        $('.QualificationId').select2();
        $('.PositionId').select2();
        $('.ServiceTypesId').select2();
        $('.InclusionId').select2();
        $('.LevelId').select2();
        $('.SkillId').select2();
        app.populateSelect($('#vacancyDetails tbody').find('.DepartmentId').last(), document.DepartmentList, 'DEPARTMENT_ID', 'DEPARTMENT_NAME', null, null);
        app.populateSelect($('#vacancyDetails tbody').find('.ServiceEventsId').last(), document.ServiceEvents, 'SERVICE_EVENT_ID', 'SERVICE_EVENT_NAME', null, null);
        app.populateSelect($('#vacancyDetails tbody').find('.DepartmentId').last(), document.DepartmentList, 'DEPARTMENT_ID', 'DEPARTMENT_NAME', null, null);
        app.populateSelect($('#vacancyDetails tbody').find('.QualificationId').last(), document.QualificationList, 'ACADEMIC_DEGREE_ID', 'ACADEMIC_DEGREE_NAME', null, null);
        app.populateSelect($('#vacancyDetails tbody').find('.PositionId').last(), document.Positions, 'DESIGNATION_ID', 'DESIGNATION_TITLE', null, null);
        app.populateSelect($('#vacancyDetails tbody').find('.ServiceTypesId').last(), document.ServiceTypes, 'SERVICE_TYPE_ID', 'SERVICE_TYPE_NAME', null, null);
        app.populateSelect($('#vacancyDetails tbody').find('.InclusionId').last(), document.InclusionList, 'OPTION_ID', 'OPTION_EDESC', null, null);
        app.populateSelect($('#vacancyDetails tbody').find('.LevelId').last(), document.LevelList, 'FUNCTIONAL_LEVEL_ID', 'FUNCTIONAL_LEVEL_EDESC', null, null);
        app.populateSelect($('#vacancyDetails tbody').find('.SkillId').last(), document.Skills, 'SKILL_ID', 'SKILL_NAME', null, null);
        ids++;
        optids++;
        incId++;
        vacNo++;
        skillsId++;
        left_quota--;
    }
    function addOptions() {
        AdnoCount++;
        var thisId = $(this).closest('tbody');
        var appendDataOptions = `
            <tr>
                <input type="hidden"  name='VacancyNo[]'  />
                <input type="hidden"  name='DepartmentId[]' />
                <input type="hidden"  name='QualificationId[]' />
                <input type="hidden"  name='PositionId[]'   />
                <input type="hidden"  name='Experience[]'   />
                <input type="hidden"  name='ServiceTypesId[]'   />
                <input type="hidden"  name='ServiceEventsId[]'   />
                <input type="hidden"  name='LevelId[]' />
            </tr>
            <tr>
                <td>
                    <label>Ad number</label>
                    <div style="overflow:hidden">
                        <input class='AdNo' name='AdNo[]' required="required"  />
                    </div>
                </td>
                <td>
                    <label>Reservation No.</label>
                    <div style="overflow:hidden">
                        <input type="number" class='VacancyReservationNo' name='VacancyReservationNo[]'  required="required" />
                    </div>
                </td>x  
                <td>
                    <label>Inclusion</label>
                    <div style="overflow:hidden">
                        <select class='InclusionId' name='InclusionId[`+ incId + `][]' required="required"  multiple >
                        </select>
                        
                    </div>
                </td>
                <td>
                    <label>Skills</label>
                    <div style="overflow:hidden">
                        <select class='SkillId' name='SkillId[`+ skillsId + `][]'  multiple  required="required">
                        </select>                                                
                    </div>
                </td>
                <td>
                    <label>Remark</label>
                    <div>
                        <textarea style="width:100%" cols="20" name="Remark[]"  class="Remark"></textarea>
                    </div>
                </td>
                <td>
                    <input class="vacancyOptionDebtn btn btn-danger" type="button" value="Delete Options -" style="padding:3px;">
                </td>                        
            </tr>
            `;

        thisId.append(appendDataOptions);
        $('.InclusionId').select2();
        $('.LevelId').select2();
        $('.SkillId').select2();
        app.populateSelect($('#vacancyDetails tbody').find('.InclusionId:last'), document.InclusionList, 'OPTION_ID', 'OPTION_EDESC', null, null);
        app.populateSelect($('#vacancyDetails tbody').find('.SkillId:last'), document.Skills, 'SKILL_ID', 'SKILL_NAME', null, null);
        adno_list++;
        incId++;
        skillsId++;
    };
    var totalQuota = 0;
    var left_quota = 0;
    
    function CheckVno() {
        var oid = $("#OpeningId").val();
        for (let i in document.OpeningVacancyNo) {
            if (document.OpeningVacancyNo[i].OPENING_ID == oid) {
                totalQuota = document.OpeningVacancyNo[i].VACANCY_TOTAL_NO;
                totalrservation = document.OpeningVacancyNo[i].RESERVATION_NO;
                totalQuota = (totalQuota == null) ? 0 : totalQuota;
                totalrservation = (totalrservation == null) ? 0 : totalrservation;

                // Add Vacancy Data
                $("#Total_vacancy").html(totalQuota);
                $("#Total_vacancy").attr({ 'value': totalQuota });
                // $("#Total_vacancy").addClass('form-control');
                $("#Total_vacancy").css({ "border-style": "ridge", "font-size": "12px", "padding-left": "12px" });
                // Add Reservation Data
                $("#Reservation_no").html(totalrservation);
                $("#Reservation_no").attr({ 'value': totalrservation });
                $("#Reservation_no").css({ "border-style": "ridge", "font-size": "12px", "padding-left": "12px" });
                $('#vacancyDetails').find('table:gt(0)').remove();
            }
        }
        var result = null;
        jQuery.extend({
            getValues: function (url) {
                $.ajax({
                    url: url,
                    data: { 'oid': oid },
                    type: 'POST',
                    async: false,
                    success: function (data) {
                        result = data;
                    }
                });
                return result;
            }
        });
        var results = $.getValues(document.CheckVacancyNo);
        var Reservation_data = $.getValues(document.CheckReserNo);
        results.data = (results.data == null) ? 0 : results.data;        
        remaining_reservation = (remaining_reservation == null) ? 0 : remaining_reservation;
        Reservation_data.data = (Reservation_data.data == null) ? 0 : Reservation_data.data;
        var remaning_vacancy = (totalQuota - results.data);
        var remaining_reservation = (totalrservation - Reservation_data.data);
        $('#posted_vacancy').html(results.data);
        $("#posted_vacancy").css({ "border-style": "ridge", "font-size": "12px", "padding-left": "12px" });
        $('#rem_vacancy').html(remaning_vacancy);
        $("#rem_vacancy").css({ "border-style": "ridge", "font-size": "12px", "padding-left": "12px" });
        // Remaininng Reservation No.
        $('#posted_Reservation_no').html(Reservation_data.data);
        $("#posted_Reservation_no").css({ "border-style": "ridge", "font-size": "12px", "padding-left": "12px" });
        $('#rem_Reservation_no').html(remaining_reservation);
        $("#rem_Reservation_no").css({ "border-style": "ridge", "font-size": "12px", "padding-left": "12px" });

        left_quota = parseInt(remaning_vacancy);

        var tab = $(document).find('table').length;
        if (tab == 0 && left_quota != 0) {
            AddVacancyTable();
        }
    };
    function CheckReservationNo(){
        var total = 0;
        var oid = $("#OpeningId").val();
        for (let i in document.OpeningVacancyNo) {
            if (document.OpeningVacancyNo[i].OPENING_ID == oid) {
                totalrservation = document.OpeningVacancyNo[i].RESERVATION_NO;
                totalrservation = (totalrservation == null) ? 0 : totalrservation;
            }
        }
        jQuery.extend({
            getValues: function (url) {
                $.ajax({
                    url: url,
                    data: { 'oid': oid },
                    type: 'POST',
                    async: false,
                    success: function (data) {
                        result = data;
                    }
                });
                return result;
            }
        });
        $(".VacancyReservationNo").each(function() {
            var quantity = parseInt($(this).val());
            
            total += quantity;
            // console.log(total);
        });
        var Reservation_data = $.getValues(document.CheckReserNo);
        Reservation_data.data = (Reservation_data.data == null) ? 0 : Reservation_data.data;
        var remaining_reservation = (totalrservation - Reservation_data.data - total);

        if(remaining_reservation < 0){
            toastr.error('Reservation Number exceeded..!! ');
            $('input[name="submit"]').attr('disabled', true);
            $('#rem_Reservation_no').css('border','2px solid red')
        }else{
            $('input[name="submit"]').attr('disabled', false);
            $('#rem_Reservation_no').css({ "border-style": "ridge", "font-size": "12px", "padding-left": "12px","border": '2px solid green' })
        }
        // console.log('Total - ' + total);
        console.log('Remainng quota - ' + remaining_reservation);
    }
})(window.jQuery, window.app);


