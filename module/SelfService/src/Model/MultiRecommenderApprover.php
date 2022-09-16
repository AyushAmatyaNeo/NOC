<?php
namespace SelfService\Model;

use Application\Model\Model;

class MultiRecommenderApprover extends Model{
    const TABLE_NAME = "HRIS_MULTIPLE_RECOMMENDER_APPROVER";
    const ID = "ID";
    const REQUEST_ID = "REQUEST_ID";
    const REQUEST_FOR = "REQUEST_FOR";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const RECOMMEND_BY = "RECOMMEND_BY";
    const APPROVED_BY = "APPROVED_BY";
    const STATUS = "STATUS";
    const CREATED_DT = "CREATED_DT";
    const CREATED_BY = "CREATED_BY";
    const MODIFIED_DT = "MODIFIED_DT";
    const MODIFIED_BY = "MODIFIED_BY";
    
    public $travelId;
    public $employeeId;
    public $requestedDate;
    public $fromDate;
    public $toDate;
    public $destination;
    Public $departure;
    public $purpose;
    public $requestedType;
    public $requestedAmount;
    public $remarks;
    public $status;
    public $recommendedDate;
    public $recommendedBy;
    public $recommendedRemarks;
    public $approvedDate;
    public $approvedBy;
    public $approvedRemarks;
    public $travelCode;
    public $referenceTravelId;
    public $departureDate;
    public $returnedDate;
    public $transportType;
    public $hardcopySignedFlag;
    public $itnaryId;
    public $transportTypeList;

    public $mappings= [
        'travelId'=>self::TRAVEL_ID,
        'employeeId'=>self::EMPLOYEE_ID,
        'requestedDate'=>self::REQUESTED_DATE,
        'fromDate'=>self::FROM_DATE,
        'toDate'=>self::TO_DATE,
        'destination'=>self::DESTINATION,
        'departure'=>self::DEPARTURE,
        'purpose'=>self::PURPOSE,
        'requestedAmount'=>self::REQUESTED_AMOUNT,
        'requestedType'=>self::REQUESTED_TYPE,
        'remarks'=>self::REMARKS,
        'status'=>self::STATUS,
        'recommendedBy'=>self::RECOMMENDED_BY,
        'recommendedDate'=>self::RECOMMENDED_DATE,       
        'recommendedRemarks'=>self::RECOMMENDED_REMARKS,       
        'approvedBy'=>self::APPROVED_BY,
        'approvedDate'=>self::APPROVED_DATE,
        'approvedRemarks'=>self::APPROVED_REMARKS,
        'travelCode'=>self::TRAVEL_CODE,
        'referenceTravelId'=>self::REFERENCE_TRAVEL_ID,
        'departureDate'=>self::DEPARTURE_DATE,
        'returnedDate'=>self::RETURNED_DATE,
        'transportType'=>self::TRANSPORT_TYPE,
        'hardcopySignedFlag' => self::HARDCOPY_SIGNED_FLAG,
        'itnaryId' => self::ITNARY_ID,
        'transportTypeList'=>self::TRANSPORT_TYPE_LIST,
    ];   
}
