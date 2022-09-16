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
use Setup\Model\EmployeeQualification;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Setup\Model\AcademicDegree;
use Setup\Model\AcademicUniversity;
use Setup\Model\AcademicCourse;
use Setup\Model\AcademicProgram;

class EmployeeQualificationRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(EmployeeQualification::TABLE_NAME,$adapter);
    }

    public function add(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }


    public function edit(Model $model, $id)
    {
        $this->tableGateway->update($model->getArrayCopyForDB(),[EmployeeQualification::ID=>$id]);

    }

    public function fetchAll()
    {
        return $this->tableGateway->select([EmployeeQualification::STATUS=>'E']);
    }

    public function fetchById($id)
    {
        $result = $this->tableGateway->select([EmployeeQualification::ID=>$id]);
        return $result->current();
    }

    public function fetchByEmployeeId($employeeId){
        return $this->tableGateway->select([EmployeeQualification::EMPLOYEE_ID=>$employeeId,EmployeeQualification::STATUS=>'E']);
    }

    public function delete($id)
    {
        $this->tableGateway->update([EmployeeQualification::STATUS=>'D'],[EmployeeQualification::ID=>$id]);
    }
    public function getByEmpId($employeeId){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(['EQ'=>EmployeeQualification::TABLE_NAME]); 
        $select->join(['AD' => AcademicDegree::TABLE_NAME], "EQ." . EmployeeQualification::ACADEMIC_DEGREE_ID. "=AD." . AcademicDegree::ACADEMIC_DEGREE_ID, ['ACADEMIC_DEGREE_NAME'], 'left')
                ->join(['AC' => AcademicCourse::TABLE_NAME], "EQ." . EmployeeQualification::ACADEMIC_COURSE_ID . "=AC." . AcademicCourse::ACADEMIC_COURSE_ID, ['ACADEMIC_COURSE_NAME'], 'left')
                ->join(['AP' => AcademicProgram::TABLE_NAME], "EQ." . EmployeeQualification::ACADEMIC_PROGRAM_ID . "=AP." . AcademicProgram::ACADEMIC_PROGRAM_ID, ['ACADEMIC_PROGRAM_NAME'], 'left')
                ->join(['AU' => AcademicUniversity::TABLE_NAME], "EQ." . EmployeeQualification::ACADEMIC_UNIVERSITY_ID . "=AU.". AcademicUniversity::ACADEMIC_UNIVERSITY_ID, ['ACADEMIC_UNIVERSITY_NAME'], 'left');
        $select->where(["EQ." . EmployeeQualification::EMPLOYEE_ID . "=$employeeId"]);
        $select->where(["EQ." . EmployeeQualification::STATUS . "='E'"]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }
}