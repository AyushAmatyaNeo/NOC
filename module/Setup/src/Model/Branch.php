<?php

namespace Setup\Model;

use Application\Model\Model;

class Branch extends Model {

    const TABLE_NAME = "HRIS_BRANCHES";
    const BRANCH_ID = "BRANCH_ID";
    const BRANCH_CODE = "BRANCH_CODE";
    const BRANCH_NAME = "BRANCH_NAME";
    const STREET_ADDRESS = "STREET_ADDRESS";
    const COUNTRY_ID = "COUNTRY_ID";
    const COMPANY_ID = "COMPANY_ID";
    const TELEPHONE = "TELEPHONE";
    const FAX = "FAX";
    const EMAIL = "EMAIL";
    const REMARKS = "REMARKS";
    const STATUS = "STATUS";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_DT = "MODIFIED_DT";
    const CREATED_BY = "CREATED_BY";
    const MODIFIED_BY = "MODIFIED_BY";
    const BRANCH_MANAGER_ID = "BRANCH_MANAGER_ID";
    const PROVINCE_ID = "PROVINCE_ID";
    const IS_REMOTE = "IS_REMOTE";
    const ALLOWANCE_REBATE = "ALLOWANCE_REBATE";
    const BRANCH_NDESC = "BRANCH_NDESC";

    public $branchId;
    public $branchCode;
    public $branchName;
    public $streetAddress;
    public $countryId;
    public $companyId;
    public $telephone;
    public $fax;
    public $email;
    public $remarks;
    public $status;
    public $createdDt;
    public $modifiedDt;
    public $createdBy;
    public $modifiedBy;
    public $branchManager;
    public $province;
    public $isRemote;
    public $allowanceRebate;
    public $branchNdesc;
    public $mappings = [
        'branchId' => self::BRANCH_ID,
        'branchCode' => self::BRANCH_CODE,
        'branchName' => self::BRANCH_NAME,
        'streetAddress' => self::STREET_ADDRESS,
        'countryId' => self::COUNTRY_ID,
        'companyId' => self::COMPANY_ID,
        'telephone' => self::TELEPHONE,
        'fax' => self::FAX,
        'email' => self::EMAIL,
        'remarks' => self::REMARKS,
        'status' => self::STATUS,
        'createdDt' => self::CREATED_DT,
        'modifiedDt' => self::MODIFIED_DT,
        'createdBy' => self::CREATED_BY,
        'modifiedBy' => self::MODIFIED_BY,
        'branchManager' => self::BRANCH_MANAGER_ID,
        'province' => self::PROVINCE_ID,
        'isRemote' => self::IS_REMOTE,
        'allowanceRebate' => self::ALLOWANCE_REBATE,
        'branchNdesc' => self::BRANCH_NDESC
    ];

}
