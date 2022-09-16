<?php
namespace SelfService\Repository;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Application\Repository\RepositoryInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;
use SelfService\Model\TravelSubstitute;
use SelfService\Model\TravelRequest;

class TravelSubstituteRepository implements RepositoryInterface{
    private $adapter;
    private $tableGateway;
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(TravelSubstitute::TABLE_NAME,$adapter);
    }

    public function add(\Application\Model\Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        
    }

    public function edit(\Application\Model\Model $model, $id) {
        $tempData = $model->getArrayCopyForDB();
        $this->tableGateway->update($tempData,[TravelSubstitute::TRAVEL_ID=>$id]);
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        $result = $this->tableGateway->select([TravelSubstitute::TRAVEL_ID=>$id]);
        return $result->current();
    }
    
    public function fetchByEmployeeId($employeeId){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("TO_CHAR(TR.FROM_DATE) AS FROM_DATE"),
            new Expression("BS_DATE(TR.FROM_DATE) AS FROM_DATE_N"),
            new Expression("TO_CHAR(TR.TO_DATE) AS TO_DATE"),
            new Expression("BS_DATE(TR.TO_DATE) AS TO_DATE_N"),
            new Expression("TR.STATUS AS STATUS"),
            new Expression("TR.DESTINATION AS DESTINATION"),
            new Expression("TO_CHAR(TR.REQUESTED_DATE) AS REQUESTED_DATE"),
            new Expression("BS_DATE(TR.REQUESTED_DATE) AS REQUESTED_DATE_N"),
            new Expression("TO_CHAR(TR.APPROVED_DATE) AS APPROVED_DATE"),
            new Expression("TO_CHAR(TR.RECOMMENDED_DATE) AS RECOMMENDED_DATE"),
            new Expression("TR.REQUESTED_AMOUNT AS REQUESTED_AMOUNT"),
            new Expression("TR.TRAVEL_ID AS TRAVEL_ID"),
            new Expression("TR.TRAVEL_CODE AS TRAVEL_CODE"),
            new Expression("TR.REFERENCE_TRAVEL_ID AS REFERENCE_TRAVEL_ID"),
            new Expression("TR.PURPOSE AS PURPOSE"),
            new Expression("TR.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("TR.RECOMMENDED_BY AS RECOMMENDED_BY"),
            new Expression("TR.APPROVED_BY AS APPROVED_BY"),
            new Expression("TR.APPROVED_REMARKS AS APPROVED_REMARKS"),
            new Expression("TR.RECOMMENDED_REMARKS AS RECOMMENDED_REMARKS"),
            new Expression("TR.REMARKS AS REMARKS"),
            new Expression("TR.TRANSPORT_TYPE AS TRANSPORT_TYPE"),
            new Expression("TO_CHAR(TR.DEPARTURE_DATE) AS DEPARTURE_DATE"),
            new Expression("TO_CHAR(TR.RETURNED_DATE) AS RETURNED_DATE"),
            new Expression("TR.REQUESTED_TYPE AS REQUESTED_TYPE") 
        ], true);

        $select->from(['TR' => TravelRequest::TABLE_NAME])
            ->join(['E'=>"HRIS_EMPLOYEES"],"E.EMPLOYEE_ID=TR.EMPLOYEE_ID",["FIRST_NAME" => new Expression("INITCAP(E.FIRST_NAME)"),"MIDDLE_NAME" => new Expression("INITCAP(E.MIDDLE_NAME)"),"LAST_NAME" => new Expression("INITCAP(E.LAST_NAME)"),"FULL_NAME" => new Expression("INITCAP(E.FULL_NAME)")],"left")
            ->join(['E1'=>"HRIS_EMPLOYEES"],"E1.EMPLOYEE_ID=TR.RECOMMENDED_BY",['FN1' =>  new Expression("INITCAP(E1.FIRST_NAME)"), 'MN1' => new Expression("INITCAP(E1.MIDDLE_NAME)"), 'LN1' => new Expression("INITCAP(E1.LAST_NAME)")],"left")
            ->join(['E2'=>"HRIS_EMPLOYEES"],"E2.EMPLOYEE_ID=TR.APPROVED_BY",['FN2' =>  new Expression("INITCAP(E2.FIRST_NAME)"), 'MN2' => new Expression("INITCAP(E2.MIDDLE_NAME)"), 'LN2' => new Expression("INITCAP(E2.LAST_NAME)")],"left")
            ->join(['RA'=>"HRIS_RECOMMENDER_APPROVER"],"RA.EMPLOYEE_ID=TR.EMPLOYEE_ID",['RECOMMENDER'=>'RECOMMEND_BY','APPROVER'=>'APPROVED_BY'],"left")
            ->join(['RECM'=>"HRIS_EMPLOYEES"],"RECM.EMPLOYEE_ID=RA.RECOMMEND_BY",['RECM_FN' =>  new Expression("INITCAP(RECM.FIRST_NAME)"), 'RECM_MN' => new Expression("INITCAP(RECM.MIDDLE_NAME)"), 'RECM_LN' => new Expression("INITCAP(RECM.LAST_NAME)")],"left")
            ->join(['APRV'=>"HRIS_EMPLOYEES"],"APRV.EMPLOYEE_ID=RA.APPROVED_BY",['APRV_FN' =>  new Expression("INITCAP(APRV.FIRST_NAME)"), 'APRV_MN' => new Expression("INITCAP(APRV.MIDDLE_NAME)"), 'APRV_LN' => new Expression("INITCAP(APRV.LAST_NAME)")],"left")
            ->join(['TS'=>"HRIS_TRAVEL_SUBSTITUTE"],"TS.TRAVEL_ID=TR.TRAVEL_ID",['SUB_EMPLOYEE_ID'=>'EMPLOYEE_ID','SUB_APPROVED_DATE'=>new Expression("INITCAP(TO_CHAR(TS.APPROVED_DATE, 'DD-MON-YYYY'))"),'SUB_REMARKS'=>"REMARKS",'SUB_APPROVED_FLAG'=>"APPROVED_FLAG"],"left");

        $select->where([
            "TS.EMPLOYEE_ID" => $employeeId
        ]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }
}
