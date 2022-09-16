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
use Setup\Model\EmployeeExperience;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;
use Zend\Authentication\AuthenticationService;
use Application\Helper\Helper;

class EmployeeExperienceRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;
    private $loggedIdEmployeeId;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(EmployeeExperience::TABLE_NAME,$adapter);
        $auth = new AuthenticationService();
        $this->loggedIdEmployeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function add(Model $model)
    {
        // print_r($model);die;
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }


    public function edit(Model $model, $id)
    {
        $this->tableGateway->update($model->getArrayCopyForDB(),[EmployeeExperience::ID=>$id]);

    }

    public function fetchAll()
    {
        return $this->tableGateway->select([EmployeeExperience::STATUS=>'E']);
    }

    public function fetchById($id)
    {
        $result = $this->tableGateway->select([EmployeeExperience::ID=>$id]);
        return $result->current();
    }

    public function fetchByEmployeeId($employeeId){
        return $this->tableGateway->select(function(Select $select)use($employeeId){
                $select->where([EmployeeExperience::EMPLOYEE_ID=>$employeeId,EmployeeExperience::STATUS=>'E']);
                $select->order(EmployeeExperience::ID);
            });
    }

    public function delete($id)
    {
        $modifiedDt = Helper::getcurrentExpressionDate();
        $employeeID = $this->loggedIdEmployeeId;
        $this->tableGateway->update([EmployeeExperience::STATUS=>'D', EmployeeExperience::MODIFIED_BY=>$employeeID, EmployeeExperience::MODIFIED_DATE=>$modifiedDt],[EmployeeExperience::ID=>$id]);
    }
    public function getByEmpId($employeeId){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("INITCAP(TO_CHAR(EE.FROM_DATE, 'DD-MON-YYYY')) AS FROM_DATE"),
            new Expression("INITCAP(TO_CHAR(EE.TO_DATE, 'DD-MON-YYYY')) AS TO_DATE"), 
            new Expression("EE.ID AS ID"), 
            new Expression("EE.ORGANIZATION_TYPE AS ORGANIZATION_TYPE"),
            new Expression("EE.ORGANIZATION_NAME AS ORGANIZATION_NAME"),
            new Expression("EE.ADDRESS AS ADDRESS"),
            new Expression("EE.POSITION AS POSITION")],TRUE);
        $select->from(['EE'=>EmployeeExperience::TABLE_NAME]); 
        $select->where(["EE." . EmployeeExperience::EMPLOYEE_ID => $employeeId]);
        $select->where(["EE." . EmployeeExperience::STATUS . "='E'"]);
        $select->order("EE.".EmployeeExperience::ID);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        // print_r($result);die;
        return $result;
    }
}