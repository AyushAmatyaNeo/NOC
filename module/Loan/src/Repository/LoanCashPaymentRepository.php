<?php

namespace Loan\Repository;

use Application\Model\Model;
use Loan\Model\LoanCashPaymentModel;
use Setup\Model\HrEmployees;
use Setup\Model\Loan;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;
use Application\Repository\HrisRepository;

class LoanCashPaymentRepository extends HrisRepository{

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(LoanCashPaymentModel::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }
 
    public function delete($id) {
        $this->tableGateway->update([LoanCashPaymentModel::STATUS => 'C'], [LoanCashPaymentModel::LOAN_REQ_ID => $id]);
    }

    public function edit(Model $model, $id) {
        $this->tableGateway->update($model->getArrayCopyForDB(), [LoanCashPaymentModel::LOAN_REQ_ID => $id]);
    }

    public function fetchAll() {
        
    }
    public function fetchById($id) {
        $sql = "SELECT * FROM HRIS_LOAN_CASH_PAYMENT WHERE ID = $id";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result->current();
    }

    public function getEmployeeByLoanRequestId($id){
        $sql = "SELECT EMPLOYEE_ID FROM HRIS_EMPLOYEE_LOAN_REQUEST WHERE LOAN_REQUEST_ID = $id";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }

    public function updateLoanStatus($loanReqId){
        $sql = "UPDATE HRIS_EMPLOYEE_LOAN_REQUEST SET LOAN_STATUS = 'CLOSED' WHERE LOAN_REQUEST_ID  = $loanReqId";
        $statement = $this->adapter->query($sql);
        $statement->execute();
    }

    public function getRemainingAmount($old_loan_req_id, $paymentAmount){
        $sql = "SELECT 
        ROUND(SUM(AMOUNT)-$paymentAmount) AS REMAINING_AMOUNT 
        FROM HRIS_LOAN_PAYMENT_DETAIL 
        WHERE PAID_FLAG = 'N' AND LOAN_REQUEST_ID = $old_loan_req_id";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }

    public function getUnpaidAmount($old_loan_req_id){
        $sql = "SELECT 
        IFNULL(SUM(AMOUNT), 0) AS UNPAID_AMOUNT 
        FROM HRIS_LOAN_PAYMENT_DETAIL 
        WHERE PAID_FLAG = 'N' AND LOAN_REQUEST_ID = $old_loan_req_id";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }

    public function getRateByLoanReqId($loanReqId){
        $sql = "SELECT INTEREST_RATE FROM HRIS_EMPLOYEE_LOAN_REQUEST WHERE LOAN_REQUEST_ID = $loanReqId";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }

    public function getOldLoanId($id){
        $sql = "SELECT LOAN_ID FROM HRIS_EMPLOYEE_LOAN_REQUEST WHERE LOAN_REQUEST_ID = $id";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }

    public function getPaymentId($id){
        $sql = "SELECT ID FROM HRIS_LOAN_CASH_PAYMENT WHERE LOAN_REQ_ID = $id";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }

    public function editDetails($id){
        $sql = "CALL HRIS_LOAN_CASH_PAYMENT_PROC($id)";
        $statement = $this->adapter->query($sql);
        $statement->execute($sql);
    }
}
