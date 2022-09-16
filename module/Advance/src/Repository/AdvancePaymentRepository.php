<?php

namespace Advance\Repository;

use Advance\Model\AdvancePayment;
use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class AdvancePaymentRepository implements RepositoryInterface {

    private $adapter;
    private $tableGateway;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(AdvancePayment::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        
    }

    public function getMonthCode($date) {
        $sql = "SELECT * FROM HRIS_MONTH_CODE WHERE '" . $date . "' between FROM_DATE AND TO_DATE";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result->current();
    }

    public function getPaymentStatus($id) {
        $sql = "SELECT 
            MC.MONTH_EDESC,
            AP.ADVANCE_REQUEST_ID,
            AP.AMOUNT,
            AP.PAYMENT_MODE,
            AP.PAYAMENT_DATE,
            AP.NEP_YEAR,
            AP.NEP_MONTH,
            AP.REF_NEP_YEAR,
            AP.REF_NEP_MONTH,
            AP.STATUS,
            (CASE AP.STATUS WHEN 'PE' THEN 'Pending' WHEN 'PA' THEN 'Paid' WHEN 'SK' THEN 'Skip' END)  STATUS_DESC,
            (case AP.PAYMENT_MODE WHEN 'S' THEN 'Salary' WHEN 'H' THEN 'Hand Cash' END) PAYMENT_MODE_DESC
            FROM HRIS_EMPLOYEE_ADVANCE_PAYMENT AP
            LEFT JOIN HRIS_MONTH_CODE MC ON(MC.YEAR=AP.NEP_YEAR AND MC.MONTH_NO=AP.NEP_MONTH)
            WHERE AP.ADVANCE_REQUEST_ID=$id";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }

    public function skipAdvance($year, $month, $id, $employee_id) {
        $maxValue = $this->getMaxYearMonth($id);
        $maxYear = $maxValue['NEP_YEAR'];
        $maxMonth = $maxValue['NEP_MONTH'];
        $amount = $maxValue['AMOUNT'];
        if ($maxMonth == 12) {
            $newMaxYear = $maxYear + 1;
            $newMaxMonth = 1;
        } else {
            $newMaxYear = $maxYear;
            $newMaxMonth = $maxMonth + 1;
        }

        $advancePayment = new AdvancePayment();
        $advancePayment->modifiedBy = $employee_id;
        $advancePayment->modifiedDate = Helper::getcurrentExpressionDate();
        $advancePayment->status = 'SK';

        $this->updatePayment($advancePayment, $id, $year, $month);

        $advancePayment->advanceRequestId = $id;
        $advancePayment->createdBy = $employee_id;
        $advancePayment->status = 'PE';
        $advancePayment->amount = $amount;
        $advancePayment->nepYear = $newMaxYear;
        $advancePayment->nepMonth = $newMaxMonth;
        $advancePayment->modifiedBy = null;
        $advancePayment->modifiedDate = null;

        $this->add($advancePayment);
    }

    public function getMaxYearMonth($id) {
        $sql = "SELECT * FROM HRIS_EMPLOYEE_ADVANCE_PAYMENT
                WHERE NEP_YEAR=(SELECT MAX(NEP_YEAR) FROM HRIS_EMPLOYEE_ADVANCE_PAYMENT WHERE ADVANCE_REQUEST_ID=$id) 
            AND NEP_MONTH=(SELECT MAX(NEP_MONTH) FROM HRIS_EMPLOYEE_ADVANCE_PAYMENT WHERE ADVANCE_REQUEST_ID=$id AND NEP_YEAR=(SELECT MAX(NEP_YEAR) FROM HRIS_EMPLOYEE_ADVANCE_PAYMENT WHERE ADVANCE_REQUEST_ID=$id))
            AND ADVANCE_REQUEST_ID=$id";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result->current();
    }

    public function updatePayment(Model $model, $id, $year, $month) {
        $this->tableGateway->update($model->getArrayCopyForDB(), [AdvancePayment::ADVANCE_REQUEST_ID => $id, AdvancePayment::NEP_YEAR => $year, AdvancePayment::NEP_MONTH => $month]);
    }

}
