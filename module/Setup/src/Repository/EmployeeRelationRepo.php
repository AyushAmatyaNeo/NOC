<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/13/16
 * Time: 3:14 PM
 */
namespace Setup\Repository;

use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Setup\Model\EmployeeRelation;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class EmployeeRelationRepo implements RepositoryInterface {

    private $tableGateway;
    private $adapter;
    private $loggedIdEmployeeId;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(EmployeeRelation::TABLE_NAME,$adapter);
        $auth = new AuthenticationService();
        $this->loggedIdEmployeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function add(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }


    public function edit(Model $model, $id)
    {
        $this->tableGateway->update($model->getArrayCopyForDB(),[EmployeeRelation::E_R_ID=>$id]);

    }

    public function fetchAll()
    {
        return $this->tableGateway->select([EmployeeRelation::STATUS=>'E']);
    }

    public function fetchById($id)
    {
        $result = $this->tableGateway->select([EmployeeRelation::E_R_ID=>$id]);
        return $result->current();
    }

    public function fetchByEmployeeId($employeeId){
        return $this->tableGateway->select(function(Select $select)use($employeeId){
                $select->where([EmployeeRelation::EMPLOYEE_ID=>$employeeId,EmployeeRelation::STATUS=>'E']);
                $select->order(EmployeeRelation::E_R_ID);
            });
    }

    public function delete($id)
    {
        $deletedDt = Helper::getcurrentExpressionDate();
        $employeeID = $this->loggedIdEmployeeId;
        $this->tableGateway->update([EmployeeRelation::STATUS=>'D', EmployeeRelation::DELETED_BY=>$employeeID, EmployeeRelation::DELETED_DT=>$deletedDt],[EmployeeRelation::E_R_ID=>$id]);
    }
    public function getByEmpId($employeeId){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("INITCAP(TO_CHAR(ER.DOB, 'DD-MON-YYYY')) AS DOB"),
            new Expression("ER.E_R_ID AS E_R_ID"), 
            new Expression("ER.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("ER.RELATION_ID AS RELATION_ID"),
            new Expression("ER.PERSON_NAME AS PERSON_NAME"),
            new Expression("ER.IS_NOMINEE AS IS_NOMINEE"),
            new Expression("ER.IS_DEPENDENT AS IS_DEPENDENT"),
            new Expression("ER.CONTACT_NO AS CONTACT_NO"),
            new Expression("CASE ER.IS_NOMINEE WHEN 'Y' THEN 1   WHEN 'N' THEN 0 END AS IS_NOMINEE_DET"),
            new Expression("CASE ER.IS_DEPENDENT WHEN 'Y' THEN 1   WHEN 'N' THEN 0 END AS IS_DEPENDENT_DET"),
            new Expression("R.RELATION_NAME AS RELATION_NAME"),
            new Expression("ER.STATUS AS STATUS")],TRUE);
        $select->from(['ER'=> EmployeeRelation::TABLE_NAME]);
        $select->join(['R'=> "HRIS_RELATIONS"], "ER.RELATION_ID=R.RELATION_ID");
        $select->where(["ER." . EmployeeRelation::EMPLOYEE_ID => $employeeId]);
        $select->where(["ER." . EmployeeRelation::STATUS =>'E']);
        $select->order("ER.".EmployeeRelation::E_R_ID);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }
}