<?php

namespace Asset\Model;

use Application\Model\Model;

class Setup extends Model {
    const TABLE_NAME = 'HRIS_ASSET_SETUP';
    
    const ASSET_ID = 'ASSET_ID';
    const ASSET_CODE = 'ASSET_CODE';
    const ASSET_EDESC = 'ASSET_EDESC';
    const ASSET_NDESC = 'ASSET_NDESC';
    const ASSET_GROUP_ID = 'ASSET_GROUP_ID';
    const BRAND_NAME = 'BRAND_NAME';
    const MODEL_NO = 'MODEL_NO';
    const SERIES = 'SERIES';
    const VENDOR_NAME = 'VENDOR_NAME';
    const QUANTITY = 'QUANTITY';
    const WARRANTY = 'WARRANTY';
    const PURCHASE_DATE = 'PURCHASE_DATE';
    const EXPIARY_DATE = 'EXPIARY_DATE';
    const PURCHASE_THROUGH = 'PURCHASE_THROUGH';
    const ASSET_IMAGE = 'ASSET_IMAGE';
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
    const QUANTITY_BALANCE = 'QUANTITY_BALANCE';

    public $assetId;
    public $assetCode;
    public $assetEdesc;
    public $assetNdesc;
    public $assetGroupId;
    public $brandName;
    public $modelNo;
    public $series;
    public $vendorName;
    public $quantity;
    public $warranty;
    public $purchaseDate;
    public $expiaryDate;
    public $purchaseThrough;
    public $assetImage;
    public $remarks;
    public $companyId;
    public $branchId;
    public $createdBy;
    public $createdDate;
    public $modifiedBy;
    public $modifiedDate;
    public $approved;
    public $approvedBy;
    public $approvedDate;
    public $status;
    public $quantityBalance;

    public $mappings = [
    'assetId'=>self::ASSET_ID,
    'assetCode'=>self::ASSET_CODE,
    'assetEdesc'=>self::ASSET_EDESC,
    'assetNdesc'=>self::ASSET_NDESC,
    'assetGroupId'=>self::ASSET_GROUP_ID,
    'brandName'=>self::BRAND_NAME,
    'modelNo'=>self::MODEL_NO,
    'series'=>self::SERIES,
    'vendorName'=>self::VENDOR_NAME,
    'quantity'=>self::QUANTITY,
    'warranty'=>self::WARRANTY,
    'purchaseDate'=>self::PURCHASE_DATE,
    'expiaryDate'=>self::EXPIARY_DATE,
    'purchaseThrough'=>self::PURCHASE_THROUGH,
    'assetImage'=>self::ASSET_IMAGE,
    'remarks'=>self::REMARKS,
    'companyId'=>self::COMPANY_ID,
    'branchId'=>self::BRANCH_ID,
    'createdBy'=>self::CREATED_BY,
    'createdDate'=>self::CREATED_DATE,
    'modifiedBy'=>self::MODIFIED_BY,
    'modifiedDate'=>self::MODIFIED_DATE,
    'approved'=>self::APPROVED,
    'approvedBy'=>self::APPROVED_BY,
    'approvedDate'=>self::APPROVED_DATE,
    'status'=>self::STATUS,
    'quantityBalance'=>self::QUANTITY_BALANCE
    ];
}
