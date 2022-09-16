<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/13/16
 * Time: 3:14 PM
 */
namespace Setup\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use Setup\Model\EmployeeTraining;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;
use Zend\Authentication\AuthenticationService;
use Application\Helper\Helper;

class EmployeeTrainingRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;
    private $loggedIdEmployeeId;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(EmployeeTraining::TABLE_NAME,$adapter);
        $auth = new AuthenticationService();
        $this->loggedIdEmployeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function add(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }


    public function edit(Model $model, $id)
    {
        $this->tableGateway->update($model->getArrayCopyForDB(),[EmployeeTraining::ID=>$id]);

    }

    public function fetchAll()
    {
        return $this->tableGateway->select([EmployeeTraining::STATUS=>'E']);
    }

    public function fetchById($id)
    {
        $result = $this->tableGateway->select([EmployeeTraining::ID=>$id]);
        return $result->current();
    }

    public function fetchByEmployeeId($employeeId){
        return $this->tableGateway->select(function(Select $select)use($employeeId){
                $select->where([EmployeeTraining::EMPLOYEE_ID=>$employeeId,EmployeeTraining::STATUS=>'E']);
                $select->order(EmployeeTraining::ID);
            });
    }

    public function getByEmpId($employeeId){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("INITCAP(TO_CHAR(ET.FROM_DATE, 'DD-MON-YYYY')) AS FROM_DATE"),
            new Expression("INITCAP(TO_CHAR(ET.TO_DATE, 'DD-MON-YYYY')) AS TO_DATE"), 
            new Expression("ET.ID AS ID"), 
            new Expression("INITCAP(ET.TRAINING_NAME) AS TRAINING_NAME"),
            new Expression("ET.TOTAL_DAYS AS TOTAL_DAYS"),
            new Expression("ET.LOCATION AS LOCATION"),
            new Expression("ET.DELIVERED_BY AS DELIVERED_BY"),
            new Expression("ET.FUNDING AS FUNDING"),
            new Expression("ET.CERTIFICATION AS CERTIFICATION"),
            new Expression("ET.DESCRIPTION AS DESCRIPTION")],TRUE);
        $select->from(['ET'=>EmployeeTraining::TABLE_NAME]); 
        $select->where(["ET." . EmployeeTraining::EMPLOYEE_ID => $employeeId]);
        $select->where(["ET." . EmployeeTraining::STATUS . "='E'"]);
        $select->order("ET.".EmployeeTraining::ID);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }
    
    public function delete($id)
    {
        $modifiedDt = Helper::getcurrentExpressionDate();
        $employeeID = $this->loggedIdEmployeeId;
        $this->tableGateway->update([EmployeeTraining::STATUS=>'D', EmployeeTraining::MODIFIED_BY=>$employeeID, EmployeeTraining::MODIFIED_DATE=>$modifiedDt],[EmployeeTraining::ID=>$id]);
    }
}