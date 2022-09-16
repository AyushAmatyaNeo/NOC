<?php
namespace SelfService\Model;

use Application\Model\Model;

class TravelExpenses extends Model{
    const TABLE_NAME = "HRIS_TRAVEL_EXPENSE";
    const TRAVEL_EXPENSE_ID = "TRAVEL_EXPENSE_ID";
    const TRAVEL_ID = "TRAVEL_ID";
    const CONFIG_ID = "CONFIG_ID";
    const AMOUNT = "AMOUNT";
    const OTHER_EXPENSE = "OTHER_EXPENSE";
    const TOTAL = "TOTAL";
    const EXCHANGE_RATE = "EXCHANGE_RATE";
    const EXPENSE_DATE = "EXPENSE_DATE";
    const REMARKS ="REMARKS";
    const STATUS = "STATUS"; 
    const CREATED_BY = "CREATED_BY";
    const APPROVED_BY = "APPROVED_BY";
    const APPROVED_DATE = "APPROVED_DATE";
    const MODIFIED_BY = "MODIFIED_BY";
    const CREATED_DT = "CREATED_DT";
    const CHECKED_BY = "CHECKED_BY";
    const CHECKED_DT = "CHECKED_DT";
    const MODIFIED_DT = "MODIFIED_DT";
    const DELETED_BY = "DELETED_BY";
    const DELETED_DT = "DELETED_DT";
    const DEPARTURE_DT = "DEPARTURE_DT";
    const DEPARTURE_PLACE = "DEPARTURE_PLACE";
    const ARRAIVAL_DT = "ARRAIVAL_DT";
    const ARRAIVAL_PLACE = "ARRAIVAL_PLACE";
    const TRANSPORTATION = "TRANSPORTATION";
    const TRANSPORTATION_CLASS = "TRANSPORTATION_CLASS";
    const RATE1 = "RATE1";
    const MILES = "MILES";
    const RATE2 = "RATE2";
    const PURPOSE = "PURPOSE";
    const JV_NUMBER = "JV_NUMBER";
    const CHEQUE_NUMBER = "CHEQUE_NUMBER";
    const OTHER_EXPENSE_DETAIL = "OTHER_EXPENSE_DETAIL";
    const HALF_DAY_FLAG = "HALF_DAY_FLAG";
        
    
    public $travelExpenseId;
    public $travelId;
    public $configId;
    public $amount;
    public $other_expense;
    public $total;
    public $exchangeRate;
    public $expenseDate;
    public $remarks;
    public $status;
    public $createdBy;
    public $approvedBy;
    public $approvedDate;
    public $modifiedBy;
    public $createdDt;
    public $checkedBy;
    public $checkedDt;
    public $modifiedDt;
    public $deletedBy;
    public $deletedDt;   
    public $departure_DT;
    public $departure_Place;
    public $arraival_place;
    public $arraival_DT;
    public $transportation; 
    public $transportationClass;
    public $rate1;
    public $miles;
    public $rate2;
    public $purpose;
    public $jvNumber;
    public $chequeNumber;
    public $otherExpenseDetail;
    public $halfDayFlag;
    
    

    public $mappings= [
        'travelExpenseId'=>self::TRAVEL_EXPENSE_ID,
        'travelId'=>self::TRAVEL_ID,
        'configId'=>self::CONFIG_ID,
        'amount'=>self::AMOUNT,
        'other_expense'=>self::OTHER_EXPENSE,
        'total'=>self::TOTAL,
        'exchangeRate'=>self::EXCHANGE_RATE,
        'expenseDate'=>self::EXPENSE_DATE,
        'status'=>self::STATUS,
        'remarks'=>self::REMARKS,
        'createdBy'=>self::CREATED_BY,
        'approvedBy'=>self::APPROVED_BY,
        'approvedDate'=>self::APPROVED_DATE,
        'modifiedBy'=>self::MODIFIED_BY,
        'createdDt'=>self::CREATED_DT,
        'checkedBy'=>self::CHECKED_BY,
        'checkedDt'=>self::CHECKED_DT,
        'modifiedDt'=>self::MODIFIED_DT,
        'deletedBy'=>self::DELETED_BY,
        'deletedDt'=>self::DELETED_DT,
        'departure_DT'=>self::DEPARTURE_DT,
        'departure_Place'=>self::DEPARTURE_PLACE,
        'arraival_place'=>self::ARRAIVAL_PLACE,
        'arraival_DT'=>self::ARRAIVAL_DT,
        'transportation'=>self::TRANSPORTATION,
        'transportationClass'=>self::TRANSPORTATION_CLASS,
        'rate1'=>self::RATE1,
        'miles'=>self::MILES,
        'rate2'=>self::RATE2,
        'purpose'=>self::PURPOSE,
        'jvNumber'=>self::JV_NUMBER,
        'chequeNumber'=>self::CHEQUE_NUMBER,
        'otherExpenseDetail'=>self::OTHER_EXPENSE_DETAIL,
        'halfDayFlag' => self::HALF_DAY_FLAG
    ];   
}