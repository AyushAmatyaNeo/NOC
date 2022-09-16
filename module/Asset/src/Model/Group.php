<?php

namespace Asset\Model;

use Application\Model\Model;

class Group extends Model {
    const TABLE_NAME = 'HRIS_ASSET_GROUP';

    const ASSET_GROUP_ID = 'ASSET_GROUP_ID';
    const ASSET_GROUP_CODE = 'ASSET_GROUP_CODE';
    const ASSET_GROUP_EDESC = 'ASSET_GROUP_EDESC';
    const ASSET_GROUP_NDESC = 'ASSET_GROUP_NDESC';
    const REMARKS = 'REMARKS';
    const COMPANY_ID = 'COMPANY_ID';
    const BRANCH_ID = 'BRANCH_ID';
    const CREATED_BY = 'CREATED_BY';
    const CREATED_DATE = 'CREATED_DATE';
    const MODIFIED_BY = 'MODIFIED_BY';
    const MODIFIED_DATE = 'MODIFIED_DATE';
    const APPROVED = 'APPROVED';
    const APPROVED_BY = 'APPROVED_BY';
    const APPROVED_DATE = 'APPROVED_DATE';
    const STATUS = 'STATUS';

    public $assetGroupId;
    public $assetGroupCode;
    public $assestGroupEdesc;
    public $assetGroupNdesc;
    public $remarks;
    public $companyId;
    public $branchId;
    public $createdBy;
    public $createdDate;
    public $modifiedBy;
    public $modifiedDate;
    public $approved;
    public $approveBy;
    public $approveDate;
    public $status;

    public $mappings = [
    'assetGroupId'=>self::ASSET_GROUP_ID,
    'assetGroupCode'=>self::ASSET_GROUP_CODE,
    'assestGroupEdesc'=>self::ASSET_GROUP_EDESC,
    'assetGroupNdesc'=>self::ASSET_GROUP_NDESC,
    'remarks'=>self::REMARKS,
    'companyId'=>self::COMPANY_ID,
    'branchId'=>self::BRANCH_ID,
    'createdBy'=>self::CREATED_BY,
    'createdDate'=>self::CREATED_DATE,
    'modifiedBy'=>self::MODIFIED_BY,
    'modifiedDate'=>self::MODIFIED_DATE,
    'approved'=>self::APPROVED,
    'approveBy'=>self::APPROVED_BY,
    'approveDate'=>self::APPROVED_DATE,
    'status'=>self::STATUS  
    ];
}
