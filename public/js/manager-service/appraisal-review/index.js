(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, true);
        var $tableContainer = $("#table");
        var $search = $('#search');


        var objectiveSet = `
        <span id="#if(KPI_ANS_NUM>0){ #green#}else{#red#}#">
            #if(KPI_ANS_NUM>0 && KPI_SETTING=='Y'){   #
            #= "&\\#10004;" #
            # }else if(KPI_SETTING=='N'){ #
            #='-'#
            #}else{ #
            #= "&\\#10006;" #
            # } #
        </span>
            `;
        var objectiveApproved = `
        <span id="#if(KPI_APPROVED_DATE==null){#red#}else{##}#">
            #if(KPI_APPROVED_DATE!=null){   #
            #= KPI_APPROVED_DATE #
            # }else if(KPI_SETTING=='N'){ #
            #='-'#
            # }else{ #
            #= "&\\#10006;" #
            # } #
        </span>`;
        var appraiseeSelfRating = `
        <span id="#if(KPI_SELF_RATING_NUM>0){ #green#}else{#red#}#">
            #if(KPI_SELF_RATING_NUM>0){   #
            #= "&\\#10004;" #
            # }else if(KPI_SETTING=='N'){ #
            #='-'#
            # }else{ #
            #= "&\\#10006;" #
            # } #
        </span>`;
        var appraisalEvaluation = `
        <span id="#if(APPRAISED_BY!=null){ #green#}else{#red#}#">
            #if(APPRAISED_BY!=null){   #
            #= "&\\#10004;" #
            # }else{ #
            #= "&\\#10006;" #
            # } #
        </span>`;
        var reviewerView = `
        <span id="#if(REVIEWED_BY!=null){ #green#}else{#red#}#">
            #if(REVIEWED_BY!=null){   #
            #= "&\\#10004;" #
            #}else if(REVIEWED_BY==null && DEFAULT_RATING=='N'){ #
            #= "&\\#10006;" #
            # }else{#-#}#
        </span>`;
        var finalRating = `
        <span id="#if(APPRAISER_OVERALL_RATING!=null){ #green#}else{#red#}#">
            #if(APPRAISER_OVERALL_RATING!=null){   #
            #= "&\\#10004;" #
            # }else if(KPI_SETTING=='N'){ #
            #='-'#
            # }else{ #
            #= "&\\#10006;" #
            # } #
        </span>`;
        var rating = `
        <span>    
        #: (APPRAISER_OVERALL_RATING == null) ? '-' : APPRAISER_OVERALL_RATING #
        </span>`;

        var superReviewerAgree = `        
        <span id="#if(SUPER_REVIEWER_AGREE!='Y'){ #green#}else{#red#}#">
            #if(SUPER_REVIEWER_AGREE=='Y'){   #
            #= "&\\#10004;" #
            #}else if(SUPER_REVIEWER_AGREE=='N'){ #
            #= "&\\#10006;" #
            # }else{#-#}#
        </span>`;

        var appraiseeAgree = `
        <span id="#if(APPRAISEE_AGREE=='Y'){ #green#}else{#red#}#">  
            #if(APPRAISEE_AGREE=='Y'){   #
            #= "&\\#10004;" #
            # }else if(APPRAISEE_AGREE=='N'){ #
            #= "&\\#10006;" #
            # }else{#-#}#
        </span>`;
        var action = `<a class="btn-edit"
        href="` + document.viewLink + `/#:APPRAISAL_ID#/#:EMPLOYEE_ID#/1" title="view" style="height:17px;">
        <i class="fa fa-search-plus"></i>
        </a>`;
        var columns = [
            {field: "EMPLOYEE_CODE", title: "Code", width: 50, locked: true},
            {field: "FULL_NAME", title: "Employee", width: 150, locked: true},
            {field: "APPRAISAL_EDESC", title: "Appraisal", width: 120, locked: true},
            {field: "APPRAISAL_TYPE_EDESC", title: "Appraisal Type", width: 150},
            {field: "STAGE_EDESC", title: "Current Stage", width: 140},
            {field: "START_DATE", title: "Start Date", width: 120},
            {field: "END_DATE", title: "End Date", width: 100},
            {field: ["KPI_ANS_NUM", "KPI_SETTING"], title: "Objective Set?", width: 130, template: objectiveSet},
            {field: ["KPI_APPROVED_DATE", "KPI_SETTING"], title: "Objective Approved?", width: 170, template: objectiveApproved},
            {field: ["KPI_SELF_RATING_NUM", "KPI_SETTING"], title: "Appraisee Self Rating?", width: 170, template: appraiseeSelfRating},
            {field: ["APPRAISED_BY"], title: "Appraiser Evaluation?", width: 170, template: appraisalEvaluation},
            {field: ["REVIEWED_BY", "DEFAULT_RATING"], title: "Reviewer View?", width: 140, template: reviewerView},
            {field: ["APPRAISER_OVERALL_RATING", "KPI_SETTING"], title: "Final Rating?", width: 120, template: finalRating},
            {field: "APPRAISER_OVERALL_RATING", title: "Rating", width: 100, template: rating},
            {field: "SUPER_REVIEWER_AGREE", title: "Super Reviewer Agree", width: 170, template: superReviewerAgree},
            {field: "APPRAISEE_AGREE", title: "Appraisee Agree", width: 140, template: appraiseeAgree},
            {field: "APPRAISER_NAME", title: "Appraiser Name", width: 150},
            {field: "REVIEWER_NAME", title: "Reviewer Name", width: 150},
            {field: ["APPRAISAL_ID", "EMPLOYEE_ID"], title: "Action", width: 90, template: action}
        ];
        app.initializeKendoGrid($tableContainer, columns, null, null, null, 'Appraisal Review');
        app.searchTable($tableContainer, ['FULL_NAME', 'APPRAISAL_EDESC', 'APPRAISAL_TYPE_EDESC', 'STAGE_EDESC', 'START_DATE', 'END_DATE', 'APPRAISER_NAME', 'REVIEWER_NAME']);

        var map = {
            'FULL_NAME': 'Name',
            'APPRAISAL_EDESC': 'Appraisal',
            'APPRAISAL_TYPE_EDESC': 'Appraisal Type',
            'STAGE_EDESC': 'Current Stage',
            'START_DATE': 'Start Date',
            'END_DATE': 'EndDate',
            'APPRAISER_OVERALL_RATING': 'Rating',
            'APPRAISER_NAME': 'Appraiser Name',
            'ALT_APPRAISER_NAME': 'Alt. Appraiser Name',
            'REVIEWER_NAME': 'Reviewer Name',
            'ALT_REVIEWER_NAME': 'Alt. Reviewer Name'

        };

        $search.on('click', function () {
            var q = document.searchManager.getSearchValues();
            q['appraisalId'] = $('#appraisalId').val();
            q['appraisalStageId'] = $('#appraisalStageId').val();
            q['fromDate'] = $('#fromDate').val();
            q['toDate'] = $('#toDate').val();
            q['userId'] = $('#userId').val();
            q['reportType'] = $('#reportType').val();
            q['durationType'] = $('#durationType').val();
            app.serverRequest(document.pullLeaveRequestStatusListLink, q).then(function (success) {
                app.renderKendoGrid($tableContainer, success.data);
            }, function (failure) {
            });
        });

        $('#excelExport').on('click', function () {
            app.excelExport($tableContainer, map, "Appraisal Review.xlsx");
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($tableContainer, map, "Appraisal Review.pdf");
        });

        document.searchManager.registerResetEvent(function () {
            console.log('test');
        });
        
//        $('#reset').on('click', function (){
//            $('.form-control').val("");
//        });

    });
})(window.jQuery, window.app);
