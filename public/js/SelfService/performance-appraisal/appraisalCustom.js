window.appraisalCustom = (function ($, toastr) {
    "use strict";
    var tabFormValidation = function (submitForm, checkingForm, submitTab, checkingTab) {
        $("#" + submitForm).on("submit", function () {
            App.blockUI({target: "#hris-page-content"});
            var appraiseeTabFail = false;
            var formOwnTabFail = false;
            $(this).find('input,select,textarea').each(function () {
                if ($(this).attr('type') !== 'hidden' && $(this).attr('disabled') !== 'disabled') {
                    if ($(this).attr("required") === "required") {
                        if ($(this).val() === "") {
                            formOwnTabFail = true;
                            var parentId = $(this).parent("div");
                            var errorMsgSpan = parentId.find('span#inputRequired');
                            if (errorMsgSpan.length > 0) {
                                parentId.find('span#inputRequired').html("This field is required");
                            } else {
                                var errorMsgSpan = $('<span />', {
                                    "class": 'errorMsg',
                                    "id": 'inputRequired',
                                    text: "This field is required!!!"
                                });
                                errorMsgSpan.appendTo(parentId);
                                var tabErrorMsgSpanNum = $("#tabContent").find('span#appraisalError');
                                if (tabErrorMsgSpanNum.length > 0) {
                                    $("#tabContent").find('span#appraisalError').html("Appraisal Submission Failed!!!!");
                                } else {
                                    var errorMsgSpan1 = $('<span />', {
                                        "class": 'errorMsg',
                                        "id": 'appraisalError',
                                        "style": 'margin-bottom:10px',
                                        text: "Appraisal Submission Failed!!!!"
                                    });
                                    $("#tabContent").prepend(errorMsgSpan1);
                                }
                            }
                        }
                    }
                }
                $(this).on("blur", function () { 
                    var parentId = $(this).parent("div");
                    parentId.find('span.errorMsg').remove();
                    $("#tabContent").find('span#appraisalError').remove();
                });
            });
            if (!formOwnTabFail) {
                $('#' + checkingForm).find('input,select,textarea').each(function () {
                    if ($(this).attr('type') !== 'hidden' && $(this).attr('disabled') !== 'disabled') {
                        if ($(this).attr("required") === "required") {
                            if ($(this).val() === "") {
                                appraiseeTabFail = true;
                                var parentId = $(this).parent("div");
                                var errorMsgSpan = parentId.find('span#inputRequired');
                                if (errorMsgSpan.length > 0) {
                                    parentId.find('span#inputRequired').html("This field is required");
                                } else {
                                    var errorMsgSpan = $('<span />', {
                                        "class": 'errorMsg',
                                        "id": 'inputRequired',
                                        text: "This field is required!!!"
                                    });
                                    errorMsgSpan.appendTo(parentId);
                                    var tabErrorMsgSpanNum = $("#tabContent").find('span#appraisalError');
                                    if (tabErrorMsgSpanNum.length > 0) {
                                        $("#tabContent").find('span#appraisalError').html("Appraisal Submission Failed!!!!");
                                    } else {
                                        var errorMsgSpan1 = $('<span />', {
                                            "class": 'errorMsg',
                                            "id": 'appraisalError',
                                            "style": 'margin-bottom:10px',
                                            text: "Appraisal Submission Failed!!!!"
                                        });
                                        $("#tabContent").prepend(errorMsgSpan1);
                                    }
                                }
                            }
                        }
                    }
                    $(this).on("blur", function () {
                        var parentId = $(this).parent("div");
                        parentId.find('span.errorMsg').remove();
                        $("#tabContent").find('span#appraisalError').remove();
                    });
                });
                if (appraiseeTabFail) {
                    App.unblockUI("#hris-page-content");
                    $("#" + checkingTab).addClass("active");
                    $("ul#tabList").find('a[href="#' + checkingTab + '"]').parent("li").addClass("active");
                    $("#" + submitTab).removeClass("active");
                    $("ul#tabList").find('a[href="#' + submitTab + '"]').parent("li").removeClass("active");
                    App.unblockUI("#hris-page-content");
                    return false;
                } else {
                    return true;
                }
            }else{
                App.unblockUI("#hris-page-content");
                return false;
            }
        });
    }
    return {
        tabFormValidation: tabFormValidation,
    };
})(window.jQuery, window.toastr);



