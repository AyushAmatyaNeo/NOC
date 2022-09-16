(function ($,appraisalCustom) {
    'use strict';
    $(document).ready(function () {
        appraisalCustom.tabFormValidation("competenciesForm","KPIForm","portlet_tab2_COM","portlet_tab2_KPI");
        appraisalCustom.tabFormValidation("appraisalReview2","appraisalReview1","portlet_tab2_3","portlet_tab2_2");
        appraisalCustom.tabFormValidation("appraisalReview1","appraisalReview","portlet_tab2_2","portlet_tab2_1");
    });
})(window.jQuery,window.appraisalCustom);