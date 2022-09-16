(function ($, appraisalCustom) {
    'use strict';
    $(document).ready(function () {
        appraisalCustom.tabFormValidation("competenciesForm", "KPIForm", "portlet_tab2_COM", "portlet_tab2_KPI");
        var comSettingId = $("#comSettingId").val();
        if (comSettingId == 'N') {
            appraisalCustom.tabFormValidation("appraisalEvaluation1", "appraisalEvaluation", "portlet_tab2_2", "portlet_tab2_1");
        } else if (comSettingId == 'Y') {
            appraisalCustom.tabFormValidation("appraisalEvaluation1", "competenciesForm", "portlet_tab2_2", "portlet_tab2_COM");
        }
        $('input[type=radio][name=defaultRating]').change(function () {
            console.log($(this).val());
            if ($(this).val() == 'Y') {
                var defaultRating = $("#defaultRating").val();
                $("#appraiserOverallRating").val(defaultRating);
                $('#tabContent').find('input,select,textarea').each(function () {
                    if ($(this).attr('type') !== 'hidden' && $(this).attr('disabled') !== 'disabled') {
                        $(this).removeAttr("required");
                    }
                });
                $('#tabContent').find('input.appraiserKPIRating,select.appraiserComRating').each(function () {
                    $(this).removeAttr("required");
                    $(this).val("");
                    $(this).attr('disabled', true);
                })
                $("#annualRatingCompetency").attr('disabled', true);
                $("#annualRatingCompetency").val("");
                $("#annualRating").val("");
            } else {
                var annualRatingCompetency = $('#annualRatingCompetency').val();
                var annualRating = $('#annualRating').val();
                $("#appraiserOverallRating").val(((!isNaN(annualRating)) ? annualRating : "") + annualRatingCompetency);
                $('#tabContent').find('input,select,textarea').each(function () {
                    if ($(this).attr('type') !== 'hidden' && $(this).attr('disabled') !== 'disabled' && $(this).hasClass('competencyComment') !== true && $(this).attr('readonly') !== 'readonly') {
                        $(this).attr("required", true);
                    }
                });
                $('#tabContent').find('input.appraiserKPIRating,select.appraiserComRating').each(function () {
                    $(this).attr("required", true);
                    $(this).attr('disabled', false);
                })
                $("#annualRatingCompetency").attr('disabled', false);
            }
        });
    });
})(window.jQuery, window.appraisalCustom);