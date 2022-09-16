(function ($, app) {
    'use strict';
    app.datePickerWithNepali("logDate", "nepaliLogDate");
    var monthShortNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
        "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    var d = new Date();
    $("#logDate").val(("0" + d.getDate()).slice(-2) + "-" + monthShortNames[(d.getMonth())] + "-" +
    d.getFullYear());
    $("#nepaliLogDate").val(window.nepaliDatePickerExt.fromEnglishToNepali($("#logDate").val()));
    //$("#logDate").datepicker('setStartDate', new Date());
    var tableData = '<table class="table table-wrapper"><tr><td>S.NO</td><td>Menu Description</td><td>Qty</td><td>Rate</td><td>Amount</td></tr></table>';
    let empId = 0;
    let presentStatus;

    $.post(
        document.fetchPresentStatusLink,
        {
            date : $("#logDate").val()
        },
        function(response){
            presentStatus = response.data;
        }
    );

    $("#scp").prop("checked", true);

    function saveRecord(){
        if(confirm("Are you sure to save this record?")){
            alert("Record saved successfully.");
            $("#empCode").focus();
        }
        else{
            return false;
        }
        var qty = [], total = [], menu_id = [], rate = [];
        $("input[name='qty[]']").each(function() {
            qty.push($(this).val());
        });
        $("input[name='total[]']").each(function() {
            total.push($(this).val());
        });
        $("input[name='menu_id[]']").each(function() {
            menu_id.push($(this).val());
        });
        $("input[name='rate[]']").each(function() {
            rate.push($(this).val());
        });
        var logData = new FormData(document.querySelector('#cafeteria-activity'));
        var data = {
            logDate : logData.get('logDate'),
            menuTime : logData.get('menuTime'),
            payType : logData.get('payType'),
            employee : empId,
            qty : qty,
            rate : rate,
            total : total,
            menu_id : menu_id
        }
        $.post(
            '',
            {'data':data},
            function(response){
                
            }
        );
        return false;
    }

    function validateData(){
        var qtyValues = $("input[name='qty[]']").map(function(){return $(this).val();}).get();
        var qtyValidate = false;
        if(empId == 0){
            alert("Invalid Employee");
            $("#empCode").val('');
            $("#empCode").focus();
            return false;
        }
        if($("#menuTime option:selected").index() == 0){
            alert("Time is not selected."); return false;
        }
        for(let i = 0; i < qtyValues.length; i++){
            if(qtyValues[i] > 0 || qtyValues[i] < 0){
                qtyValidate = true;
            }
        }
        if(!qtyValidate){
            alert("No items in the list!");
            clearForm();
            return false;
        }
        return true;
    }

    function clearForm(){
        $(".image").empty();
        $("#empName").val('');
        $("#dept").val('');
        $("#empCode").val('');
        $("#desg").val('');
        $('.totalAmount input').val('');
        $('.qty input').val('');
        $("#empCode").focus();
    }

    $("#submit").click(function(){
        if(validateData()){
            saveRecord(); clearForm();
        } 
        return false;
    });

    $(document).on('keydown', ':tabbable', function (e) {
        if (e.which == 38  || e.keyCode == 38  ) { 
            e.preventDefault();
            var $canfocus = $(':tabbable:visible')
            var index = $canfocus.index(document.activeElement) - 1;
            if (index >= $canfocus.length) index = 0;
            $canfocus.eq(index).focus();
        }
        if (e.which == 40  || e.keyCode == 40  ) { 
            e.preventDefault();
            var $canfocus = $(':tabbable:visible')
            var index = $canfocus.index(document.activeElement) + 1;
            if (index >= $canfocus.length) index = 0;
            $canfocus.eq(index).focus();
        }
        if (e.which == 116  || e.code == 116  ) { 
            e.preventDefault();
            $("#submit").focus();
            $("#submit").click();
        }
        if (e.which == 13  || e.keyCode == 13  ) {      
            e.preventDefault();
            var $canfocus = $(':tabbable:visible')
            var index = $canfocus.index(document.activeElement) + 1;
            if (index >= $canfocus.length) index = 0;
            $canfocus.eq(index).focus();
            if(this.id == 'empCode'){ 
                empId = 0;
                var index = -1;
                var empPresentStatus = '';
                var empCode = this.value;
                for(let i = 0; i <  document.employeeProfiles.length; i++){
                    if(document.employeeProfiles[i].EMPLOYEE_CODE == empCode){
                        empId = document.employeeProfiles[i].EMPLOYEE_ID;
                        index = i;
                        break;
                    }
                }
                if(index >= 0){
                    $(".image").empty();
                    $("#empName").val(document.employeeProfiles[index].FULL_NAME);
                    $("#dept").val(document.employeeProfiles[index].DEPARTMENT_NAME);
                    $("#desg").val(document.employeeProfiles[index].DESIGNATION_TITLE);
                    $(".image").append('<img src="'+document.basePath+'/uploads/'+document.employeeProfiles[index].FILE_PATH+'" width="125" height="125">');
                }
                else{
                    alert("Invalid Employee Code");
                    clearForm();
                    return;
                }
                index = 0;
                for(let i = 0; i < presentStatus.length; i++){
                    if(presentStatus[i].EMPLOYEE_ID == empId){
                        index = i;
                        break;
                    }
                }
                if(index == 0){
                    //alert("Employee is Absent.");
                    app.showMessage( $("#empName").val()+" is Absent.", 'warning');
                }
                else{
                    empPresentStatus = presentStatus[index].STATUS;
                    if(empPresentStatus == 'AB'){
                        //alert(presentStatus[index].FULL_NAME + " is Absent.");
                        app.showMessage(presentStatus[index].FULL_NAME + " is Absent.", 'warning');
                    }
                }
            }

            if(this.id == 'submit'){ 
                if(validateData()){
                    saveRecord(); clearForm();
                } 
                return false;
            }
        }   
    });

    $(document).ready(function () {
        $(document).on('input change', '#logDate', function () {
            $.post(
                document.fetchPresentStatusLink,
                {
                    date : $("#logDate").val()
                },
                function(response){
                    presentStatus = response.data;
                }
            );
        });

        $(document).on('input', '.qty', function () {
            var tableRow = $('#menuTable tr').length;
            let grandTotal = 0;
            var thisTd = $(this).closest('td').parent()[0].sectionRowIndex;
            parseFloat(thisTd);
            var qty = $("tr:nth-of-type(" + (thisTd + 1) + ") .qty input").val();
            qty = qty == '' ? 0 : qty;
            qty = qty || 0;
            var rate = $("tr:nth-of-type(" + (thisTd + 1) + ") .rate input").val();
            rate = rate || 0;
            var total = parseFloat(qty) * parseFloat(rate);
            $("tr:nth-of-type(" + (thisTd + 1) + ") .totalAmount input").val(total);
            for (let i = 2; i < tableRow; i++) {
                let a = parseFloat($("tr:nth-of-type(" + i + ") .totalAmount input").val());
                a = a || 0
                parseFloat(a);
                grandTotal += a;
            }
            $("tr:nth-of-type(" + (tableRow) + ") .totalAmount input").val(grandTotal);
        });

        $('#menuTime, input[name="type"]').change(function () {
            var type = $('input[name="type"]:checked').val();
            var menuItems = [];
            $('#menuTable').empty();
            $('#menuTable').append(tableData);
            let time = $("#menuTime option:selected").text();
            if(time == '--'){return;}
            for (let i = 0; i < document.menuList.length; i++) {
                if (document.menuList[i] != null) {
                    for (let j = 0; j < document.mapList[time].length; j++) {
                        if (document.menuList[i].MENU_ID == document.mapList[time][j].MENU_ID && document.mapList[time][j].TYPE == type ) {
                            menuItems.push(document.menuList[i]);
                        }
                    }
                }
            }
            var counter = 1;
            
            for (let i = 0; i < menuItems.length; i++) {
                $('#menuTable tr:last').after('<tr><td>' + counter + '</td><td>' + menuItems[i].MENU_NAME + '</td><td class="qty"><input type="number" value="" name="qty[]" id="qty"' + counter + '></td><td class="rate"><input type="number" readonly name="rate[]" tabindex="-1" value="'+menuItems[i].RATE+'"></td><td class="totalAmount"><input type="number" readonly name="total[]" tabindex="-1" value=""></td><td><input type="hidden" name="menu_id[]" value="' + menuItems[i].MENU_ID + '"></td></tr>');
                counter++;
            }
            $('#menuTable tr:last').after('<tr><td>-</td><td>Total</td><td>-</td><td>-</td><td class="totalAmount"><input type="number" readonly name="total[]" tabindex="-1"></td></tr>');
        });
    });
})(window.jQuery, window.app);
