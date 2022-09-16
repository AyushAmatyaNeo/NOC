(function ($,app,appraisalCustom) {
    'use strict';
    $(document).ready(function () {
        app.setLoadingOnSubmit("selfEvaluation1");
        appraisalCustom.tabFormValidation("competenciesForm","KPIForm","portlet_tab2_COM","portlet_tab2_KPI");
    });
})(window.jQuery,window.app,window.appraisalCustom);