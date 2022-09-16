<?php

namespace Appraisal\Model;

use Application\Model\Model;

class AppraisalStatus extends Model {

    const TABLE_NAME = "HRIS_APPRAISAL_STATUS";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const APPRAISAL_ID = "APPRAISAL_ID";
    const ANNUAL_RATING_KPI = "ANNUAL_RATING_KPI";
    const ANNUAL_RATING_COMPETENCY = "ANNUAL_RATING_COMPETENCY";
    const APPRAISER_OVERALL_RATING = "APPRAISER_OVERALL_RATING";
    const REVIEWER_AGREE = "REVIEWER_AGREE";
    const APPRAISEE_AGREE = "APPRAISEE_AGREE";
    const APPRAISED_BY = "APPRAISED_BY";
    const REVIEWED_BY = "REVIEWED_BY";
    const CREATED_BY = "CREATED_BY";
    const CREATED_DATE = "CREATED_DATE";
    const MODIFIED_BY = "MODIFIED_BY";
    const MODIFIED_DATE = "MODIFIED_DATE";
    const DEFAULT_RATING = "DEFAULT_RATING";
    const REVIEW_PERIOD = "REVIEW_PERIOD";
    const PREVIOUS_REVIEW_PERIOD = "PREVIOUS_REVIEW_PERIOD";
    const PREVIOUS_RATING = "PREVIOUS_RATING";
    const HR_FEEDBACK = "HR_FEEDBACK";
    const SUPER_REVIEWER_AGREE = "SUPER_REVIEWER_AGREE";
    const SUPER_REVIEWER_FEEDBACK = "SUPER_REVIEWER_FEEDBACK";
    const HR_STRENGTH = "HR_STRENGTH";
    const HR_WEAKNESS = "HR_WEAKNESS";
    const HR_AREAS_OF_IMPROVEMENT = "HR_AREAS_OF_IMPROVEMENT";
    const HR_STEPS_FOR_IMPROVEMENT = "HR_STEPS_FOR_IMPROVEMENT";

    public $employeeId;
    public $appraisalId;
    public $annualRatingKPI;
    public $annualRatingCompetency;
    public $appraiserOverallRating;
    public $reviewerAgree;
    public $appraiseeAgree;
    public $appraisedBy;
    public $reviewedBy;
    public $createdBy;
    public $createdDate;
    public $modifiedBy;
    public $modifiedDate;
    public $defaultRating;
    public $reviewPeriod;
    public $previousReviewPeriod;
    public $previousRating;
    public $hrFeedback;
    public $superReviewerAgree;
    public $superReviewerFeedback;
    public $hrStrength;
    public $hrWeakness;
    public $hrAreasOfImprovement;
    public $hrStepsForImprovement;
    public $mappings = [
        'employeeId' => self::EMPLOYEE_ID,
        'appraisalId' => self::APPRAISAL_ID,
        'annualRatingKPI' => self::ANNUAL_RATING_KPI,
        'annualRatingCompetency' => self::ANNUAL_RATING_COMPETENCY,
        'appraiserOverallRating' => self::APPRAISER_OVERALL_RATING,
        'reviewerAgree' => self::REVIEWER_AGREE,
        'appraiseeAgree' => self::APPRAISEE_AGREE,
        'appraisedBy' => self::APPRAISED_BY,
        'reviewedBy' => self::REVIEWED_BY,
        'createdBy' => self::CREATED_BY,
        'createdDate' => self::CREATED_DATE,
        'modifiedBy' => self::MODIFIED_BY,
        'modifiedDate' => self::MODIFIED_DATE,
        'defaultRating' => self::DEFAULT_RATING,
        'reviewPeriod' => self::REVIEW_PERIOD,
        'previousReviewPeriod' => self::PREVIOUS_REVIEW_PERIOD,
        'previousRating' => self::PREVIOUS_RATING,
        'hrFeedback' => self::HR_FEEDBACK,
        'superReviewerAgree' => self::SUPER_REVIEWER_AGREE,
        'superReviewerFeedback' => self::SUPER_REVIEWER_FEEDBACK,
        'hrStrength' => self::HR_STRENGTH,
        'hrWeakness' => self::HR_WEAKNESS,
        'hrAreasOfImprovement' => self::HR_AREAS_OF_IMPROVEMENT,
        'hrStepsForImprovement' => self::HR_STEPS_FOR_IMPROVEMENT
    ];

}
