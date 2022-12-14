<?php

namespace ManagerService\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use SelfService\Model\LoanRequest;
use SelfService\Model\LoanEmiDetail;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;
use Application\Repository\HrisRepository;


class LoanApproveRepository extends HrisRepository implements RepositoryInterface {

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(LoanRequest::TABLE_NAME, $adapter);
        $this->emiTableGateway = new TableGateway(LoanEmiDetail::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        
    }

    public function delete($id) {
        
    }

    public function getAllWidStatus($id, $status) {
        
    }

    public function edit(Model $model, $id) {
        $temp = $model->getArrayCopyForDB();
        $this->tableGateway->update($temp, [LoanRequest::LOAN_REQUEST_ID => $id]);
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("INITCAP(TO_CHAR(LR.LOAN_DATE, 'DD-MON-YYYY')) AS LOAN_DATE"),
            new Expression("INITCAP(TO_CHAR(LR.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE"),
            new Expression("LR.STATUS AS STATUS"),
            new Expression("LR.REPAYMENT_INSTALLMENTS AS REPAYMENT_INSTALLMENTS"),
            new Expression("LR.LOAN_REQUEST_ID AS LOAN_REQUEST_ID"),
            new Expression("INITCAP(TO_CHAR(LR.RECOMMENDED_DATE, 'DD-MON-YYYY')) AS RECOMMENDED_DATE"),
            new Expression("INITCAP(TO_CHAR(LR.APPROVED_DATE, 'DD-MON-YYYY')) AS APPROVED_DATE"),
            new Expression("LR.APPLIED_LOAN AS APPLIED_LOAN"),
            new Expression("LR.REASON AS REASON"),
            new Expression("LR.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("LR.LOAN_ID AS LOAN_ID"),
            new Expression("LR.RECOMMENDED_BY AS RECOMMENDED_BY"),
            new Expression("LR.APPROVED_BY AS APPROVED_BY"),
            new Expression("LR.RECOMMENDED_REMARKS AS RECOMMENDED_REMARKS"),
            new Expression("LR.APPROVED_REMARKS AS APPROVED_REMARKS"),
            new Expression("LR.INTEREST_RATE AS INTEREST_RATE")
                ], true);

        $select->from(['LR' => LoanRequest::TABLE_NAME])
                ->join(['E' => "HRIS_EMPLOYEES"], "E.EMPLOYEE_ID=LR.EMPLOYEE_ID", ["FULL_NAME" => new Expression("INITCAP(E.FULL_NAME)")], "left")
                ->join(['E1' => "HRIS_EMPLOYEES"], "E1.EMPLOYEE_ID=LR.RECOMMENDED_BY", ['RECOMMENDED_BY_NAME' => new Expression("INITCAP(E1.FULL_NAME)")], "left")
                ->join(['E2' => "HRIS_EMPLOYEES"], "E2.EMPLOYEE_ID=LR.APPROVED_BY", ['APPROVED_BY_NAME' => new Expression("INITCAP(E2.FULL_NAME)")], "left")
                ->join(['RA' => "HRIS_RECOMMENDER_APPROVER"], "RA.EMPLOYEE_ID=LR.EMPLOYEE_ID", ['RECOMMENDER_ID' => 'RECOMMEND_BY', 'APPROVER_ID' => 'APPROVED_BY'], "left")
                ->join(['RECM' => "HRIS_EMPLOYEES"], "RECM.EMPLOYEE_ID=RA.RECOMMEND_BY", ['RECOMMENDER_NAME' => new Expression("INITCAP(RECM.FULL_NAME)")], "left")
                ->join(['APRV' => "HRIS_EMPLOYEES"], "APRV.EMPLOYEE_ID=RA.APPROVED_BY", ['APPROVER_NAME' => new Expression("INITCAP(APRV.FULL_NAME)")], "left")
        ;

        $select->where([
            "LR.LOAN_REQUEST_ID=" . $id
        ]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    public function getAllRequest($id) {
        $sql = "SELECT 
                    LR.LOAN_REQUEST_ID,
                    LR.REQUESTED_AMOUNT,
                    INITCAP(TO_CHAR(LR.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE,
                    BS_DATE(LR.REQUESTED_DATE) AS REQUESTED_DATE_N,
                    LR.APPROVED_BY,
                    LR.RECOMMENDED_BY,
                    LR.REASON,
                    LR.LOAN_ID,
                    INITCAP(TO_CHAR(LR.LOAN_DATE, 'DD-MON-YYYY')) AS LOAN_DATE,
                    BS_DATE(LR.LOAN_DATE) AS LOAN_DATE_N,
                    INITCAP(TO_CHAR(LR.RECOMMENDED_DATE, 'DD-MON-YYYY')) AS RECOMMENDED_DATE,
                    INITCAP(TO_CHAR(LR.APPROVED_DATE, 'DD-MON-YYYY')) AS APPROVED_DATE,
                    LR.EMPLOYEE_ID,
                    LR.RECOMMENDED_REMARKS,
                    LR.APPROVED_REMARKS,
                    E.EMPLOYEE_CODE AS EMPLOYEE_CODE,
                    INITCAP(E.FIRST_NAME) AS FIRST_NAME,
                    INITCAP(E.MIDDLE_NAME) AS MIDDLE_NAME,
                    INITCAP(E.LAST_NAME) AS LAST_NAME,
                    INITCAP(E.FULL_NAME) AS FULL_NAME,
                    INITCAP(L.LOAN_NAME) AS LOAN_NAME,
                    L.LOAN_CODE,
                    RA.RECOMMEND_BY as RECOMMENDER,
                    RA.APPROVED_BY AS APPROVER,
                    LEAVE_STATUS_DESC(LR.STATUS)                     AS STATUS,
                    REC_APP_ROLE({$id},RA.RECOMMEND_BY,RA.APPROVED_BY)      AS ROLE,
                    REC_APP_ROLE_NAME({$id},RA.RECOMMEND_BY,RA.APPROVED_BY) AS YOUR_ROLE
                    FROM HRIS_EMPLOYEE_LOAN_REQUEST LR
                    LEFT JOIN HRIS_EMPLOYEES E ON 
                    E.EMPLOYEE_ID=LR.EMPLOYEE_ID
                    LEFT JOIN HRIS_LOAN_MASTER_SETUP L
                    ON LR.LOAN_ID=L.LOAN_ID
                    LEFT JOIN HRIS_RECOMMENDER_APPROVER RA
                    ON E.EMPLOYEE_ID=RA.EMPLOYEE_ID
                    WHERE L.STATUS = 'E' AND E.STATUS='E'
                    AND E.RETIRED_FLAG='N' 
                    AND ((RA.RECOMMEND_BY= {$id} AND LR.STATUS='RQ') OR (RA.APPROVED_BY= {$id} AND LR.STATUS='RC') )
                    ORDER BY LR.REQUESTED_DATE DESC";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }

    public function addToDetails($id){
        $sql = "CAll HRIS_LOAN_PAYMENT_DETAILS_NOC({$id});";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }
    public function fetchLoanDetailView($loanId){
        $sql = "select * from hris_employee_emi_detail WHERE loan_request_id = {$loanId}";
        
        return $this->rawQuery($sql);
        
    }
}
