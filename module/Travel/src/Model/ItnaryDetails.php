<?php
namespace Travel\Model;

use Application\Model\Model;

class ItnaryDetails extends Model{
    const TABLE_NAME = "HRIS_ITNARY_MEMBERS";
    const ITNARY_ID = "ITNARY_ID";
    const DEPARTURE_DT = "DEPARTURE_DT";
    const DEPARTURE_TIME = "DEPARTURE_TIME";
    const LOCATION_FROM = "LOCATION_FROM";
    const LOCATION_TO = "LOCATION_TO";
    const TRANSPORT_TYPE = "TRANSPORT_TYPE";
    const ARRIVE_DT = "ARRIVE_DT";
    const ARRIVE_TIME = "ARRIVE_TIME";
    const REMARKS = "REMARKS";
    const SNO = "SNO";
    
    
    public $itnaryId;
    public $departureDt;
    public $departureTime;
    public $locationFrom;
    public $locationTo;
    public $transportType;
    public $arriveDt;
    public $arriveTime;
    public $remarks;
    public $sno;

    public $mappings= [
        'itnaryId'=>self::ITNARY_ID,
        'departureDt'=>self::DEPARTURE_DT,
        'departureTime'=>self::DEPARTURE_TIME,
        'locationFrom'=>self::LOCATION_FROM,
        'locationTo'=>self::LOCATION_TO,
        'transportType'=>self::TRANSPORT_TYPE,
        'arriveDt'=>self::ARRIVE_DT,
        'arriveTime'=>self::ARRIVE_TIME,
        'remarks'=>self::REMARKS,
        'sno'=>self::SNO,
    ];   
}
