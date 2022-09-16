<?php
namespace Setup\Model;

use Application\Model\Model;

class ServiceEventType extends Model{

    const TABLE_NAME="HRIS_SERVICE_EVENT_TYPES";

    const SERVICE_EVENT_TYPE_ID="SERVICE_EVENT_TYPE_ID";
    const SERVICE_EVENT_TYPE_CODE="SERVICE_EVENT_TYPE_CODE";
    const SERVICE_EVENT_TYPE_NAME="SERVICE_EVENT_TYPE_NAME";
    const REMARKS="REMARKS";
    const STATUS="STATUS";
    const CREATED_DT="CREATED_DT";
    const MODIFIED_DT="MODIFIED_DT";

    public $serviceEventTypeId;
    public $serviceEventTypeCode;
    public $serviceEventTypeName;
    public $remarks;
    public $status;
    public $createdDt;
    public $modifiedDt;

    public $mappings = [
        'serviceEventTypeId'=>self::SERVICE_EVENT_TYPE_ID,
        'serviceEventTypeCode'=>self::SERVICE_EVENT_TYPE_CODE,
        'serviceEventTypeName'=>self::SERVICE_EVENT_TYPE_NAME,
        'remarks'=>self::REMARKS,
        'status'=>self::STATUS,
        'createdDt'=>self::CREATED_DT,
        'modifiedDt'=>self::MODIFIED_DT
    ];

}