<?php
namespace SelfService\Model;

use Application\Model\Model;

class TransferSettlement extends Model{
    const TABLE_NAME = "HRIS_TRANSFER_SETTLEMENT";
    const TRANSFER_SETTLEMENT_ID = "TRANSFER_SETTLEMENT_ID";
    const JOB_HISTORY_ID = "JOB_HISTORY_ID";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const REQUESTED_DATE = "REQUESTED_DATE";
    const FROM_DATE = "FROM_DATE";
    const TO_DATE = "TO_DATE";
    const DEPARTURE = "DEPARTURE";
    const DESTINATION = "DESTINATION";
    const ADDRESS = "ADDRESS";
    const TRANSFER_REASON = "TRANSFER_REASON";
    const TRAVELLED_DAYS = "TRAVELLED_DAYS";
    const TOTAL_TADA_AMT = "TOTAL_TADA_AMT";
    const FAMILY_NO_TRAVLLED_WITH = "FAMILY_NO_TRAVLLED_WITH";
    const FAMILY_TADA_AMT = "FAMILY_TADA_AMT";
    const WEIGHT = "WEIGHT";
    const EXTRA_WEIGHT_AMT = "EXTRA_WEIGHT_AMT";
    const YEARLY_SETTTLEMENT_REQ_AMT = "YEARLY_SETTTLEMENT_REQ_AMT";
    const YEARLY_SETTTLEMENT_AP_AMT = "YEARLY_SETTTLEMENT_AP_AMT";
    const PLANE_EXPENSE_REQ_AMT = "PLANE_EXPENSE_REQ_AMT";
    const PLANE_EXPENSE_AP_AMT = "PLANE_EXPENSE_AP_AMT";
    const PLANE_EXPENSE_FILE = "PLANE_EXPENSE_FILE";
    const VEHICLE_EXPENSE_REQ_AMT = "VEHICLE_EXPENSE_REQ_AMT";
    const VEHICLE_EXPENSE_AP_AMT = "VEHICLE_EXPENSE_AP_AMT";
    const VEHICLE_EXPENSE_FILE = "VEHICLE_EXPENSE_FILE";
    const MISC_EXPENSE_REQ_AMT = "MISC_EXPENSE_REQ_AMT";
    const MISC_EXPENSE_AP_AMT = "MISC_EXPENSE_AP_AMT";
    const MISC_EXPENSE_FILE = "MISC_EXPENSE_FILE";
    const HOURS = "HOURS";
    const CHECKED_DT = "CHECKED_DT";
    const CHECKED_BY = "CHECKED_BY";
    const APPROVED_DT = "APPROVED_DT";
    const APPROVED_BY = "APPROVED_BY";
    const STATUS = "STATUS";
    const REMARKS = "REMARKS";
    const CREATED_DT = "CREATED_DT";
    const CREATED_BY = "CREATED_BY";
    const MODIFIED_DT = "MODIFIED_DT";
    const MODIFIED_BY = "MODIFIED_BY";
    const DELETED_DT = "DELETED_DT";
    const DELETED_BY = "DELETED_BY";
    const FOR_FAMILY = "FOR_FAMILY";
    const MILES = "MILES";
    const MISC_EXPENSE_DETAIL = "MISC_EXPENSE_DETAIL";
    const PURPOSE = "PURPOSE";
    const TRANSPORT_CLASS = "TRANSPORT_CLASS";
    const EXPENSE_CATEGORY = "EXPENSE_CATEGORY";
    const TRANSPORTATION = "TRANSPORTATION";
    const WEIGHT_REQ_AMT = "WEIGHT_REQ_AMT";
    const WEIGHT_AP_AMT = "WEIGHT_AP_AMT";
    const FAMILY_NAME ="FAMILY_NAME";
    const SERIAL_NUMBER = "SERIAL_NUMBER";
    const APPROVER_REMARKS= "APPROVER_REMARKS";
    const JV_NUMBER="JV_NUMBER";
    const CHEQUE_NUMBER="CHEQUE_NUMBER";
    const BANK_ID ="BANK_ID";
    
    public $transferSettlementId;
    public $jobHistoryId;
    public $employeeId;
    public $requestedDate;
    public $fromDate;
    public $toDate;
    public $departure;
    public $destination;
    public $address;
    public $transferReason;
    public $travelledDays;
    public $totalTadaAmt;
    public $familyNoTravlledWith;
    public $familyTadaAmt;
    public $weight;
    public $extraWeightAmt;
    public $yearlySettlementReqAmt;
    public $yearlySettlementApAmt;
    public $planeExpenseReqAmt;
    public $planeExpenseApAmt;
    public $planeExpenseFile;
    public $vehicleExpenseReqAmt;
    public $vehicleExpenseApAmt;
    public $vehicleExpenseFile;
    public $miscExpenseReqAmt;
    public $miscExpenseApAmt;
    public $miscExpenseFile;
    public $hours;
    public $checkedDt;
    public $checkedBy;
    public $approvedDt;
    public $approvedBy;
    public $status;
    public $remarks;
    public $createdDt;
    public $createdBy;
    public $modifiedDt;
    public $modifiedBy;
    public $deletedDt;
    public $deletedBy;
    public $forFamily;
    public $miles;
    public $miscExpenseDetail;
    public $purpose;
    public $transportClass;
    public $expenseCategory;
    public $transportation;
    public $weightReqAmt;
    public $weightApAmt;
    public $familyName;
    public $serialNumber;
    public $approverRemarks;
    public $jvNumber;
    public $chequeNumber;
    public $bankId;

    public $mappings= [
        'transferSettlementId'=>self::TRANSFER_SETTLEMENT_ID,
        'jobHistoryId'=>self::JOB_HISTORY_ID,
        'employeeId'=>self::EMPLOYEE_ID,
        'requestedDate'=>self::REQUESTED_DATE,
        'fromDate'=>self::FROM_DATE,
        'toDate'=>self::TO_DATE,
        'departure'=>self::DEPARTURE,
        'destination'=>self::DESTINATION,
        'address'=>self::ADDRESS,
        'transferReason'=>self::TRANSFER_REASON,
        'travelledDays'=>self::TRAVELLED_DAYS,
        'totalTadaAmt'=>self::TOTAL_TADA_AMT,
        'familyNoTravlledWith'=>self::FAMILY_NO_TRAVLLED_WITH,
        'familyTadaAmt'=>self::FAMILY_TADA_AMT,
        'weight'=>self::WEIGHT,
        'extraWeightAmt'=>self::EXTRA_WEIGHT_AMT,
        'yearlySettlementReqAmt'=>self::YEARLY_SETTTLEMENT_REQ_AMT,
        'yearlySettlementApAmt'=>self::YEARLY_SETTTLEMENT_AP_AMT,
        'planeExpenseReqAmt'=>self::PLANE_EXPENSE_REQ_AMT,
        'planeExpenseApAmt'=>self::PLANE_EXPENSE_AP_AMT,
        'planeExpenseFile'=>self::PLANE_EXPENSE_FILE,
        'vehicleExpenseReqAmt'=>self::VEHICLE_EXPENSE_REQ_AMT,
        'vehicleExpenseApAmt'=>self::VEHICLE_EXPENSE_AP_AMT,
        'vehicleExpenseFile'=>self::VEHICLE_EXPENSE_FILE,
        'miscExpenseReqAmt'=>self::MISC_EXPENSE_REQ_AMT,
        'miscExpenseApAmt'=>self::MISC_EXPENSE_AP_AMT,
        'miscExpenseFile'=>self::MISC_EXPENSE_FILE,
        'hours'=>self::HOURS,
        'checkedDt'=>self::CHECKED_DT,
        'checkedBy'=>self::CHECKED_BY,
        'approvedDt'=>self::APPROVED_DT,
        'approvedBy'=>self::APPROVED_BY,
        'status'=>self::STATUS,
        'remarks'=>self::REMARKS,
        'createdDt'=>self::CREATED_DT,
        'createdBy'=>self::CREATED_BY,
        'modifiedDt'=>self::MODIFIED_DT,
        'modifiedBy'=>self::MODIFIED_BY,
        'deletedDt'=>self::DELETED_DT,
        'deletedBy'=>self::DELETED_BY,
        'forFamily'=>self::FOR_FAMILY,
        'miles'=>self::MILES,
        'miscExpenseDetail'=>self::MISC_EXPENSE_DETAIL,
        'purpose'=>self::PURPOSE,
        'transportClass'=>self::TRANSPORT_CLASS,
        'expenseCategory'=>self::EXPENSE_CATEGORY,
        'transportation'=>self::TRANSPORTATION,
        'weightReqAmt'=>self::WEIGHT_REQ_AMT,
        'weightApAmt'=>self::WEIGHT_AP_AMT,
        'familyName'=>self::FAMILY_NAME,
        'serialNumber'=>self::SERIAL_NUMBER,
        'approverRemarks' =>self::APPROVER_REMARKS,
        'jvNumber'=>self::JV_NUMBER,
        'chequeNumber' =>self::CHEQUE_NUMBER,
        'bankId' =>self::BANK_ID

    ];   
}
