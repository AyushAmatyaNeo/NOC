<?php

namespace Application\Repository;

use Application\Helper\EntityHelper;
use Exception;
use Zend\Db\Adapter\AdapterInterface;

class VoucherRepository {

    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function generateAdvanceVoucher($companyId, $formCode, $transactionDate, $tableName, $branchId, $createdBy, $createdDate, $accCode, $particulars, $amount, $subCode): mixed {
        /* Default values
          $companyId='01';
          $formCode='436';
          $transactionDate='TRUNC(SYSDATE)';
          $tableName='FA_DOUBLE_VOUCHER';
         */
        $sql = "SELECT FN_NEW_VOUCHER_NO('{$companyId}','{$formCode}',{$transactionDate},{$tableName}) AS VOUCHER_NO FROM DUAL";
        $rawResult = EntityHelper::rawQueryResult($this->adapter, $sql);
        $voucherCurrent = $rawResult->current();
        if ($voucherCurrent == null) {
            throw new Exception('Error while generating voucher.');
        }
        $voucherNo = $voucherCurrent['VOUCHER_NO'];

        EntityHelper::rawQueryResult($this->adapter, "EXECUTE GENERATE_ADVANCE_VOUCHER('{$companyId}','{$formCode}',{$transactionDate},'{$branchId}','{$createdBy}',{$createdDate},{$accCode},'{$particulars}',{$amount},'{$subCode}','{$voucherNo}')");
        return $voucherNo;
    }

}
