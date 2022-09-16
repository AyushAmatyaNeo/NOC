<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/11/16
 * Time: 10:38 AM
 */

namespace Setup\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Setup\Model\AcademicCourse;
use Setup\Model\AcademicProgram;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class AcademicCourseRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(AcademicCourse::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [AcademicCourse::ACADEMIC_COURSE_ID => $id]);
    }

    public function fetchAll() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(
                AcademicCourse::class,
                [
                    AcademicCourse::ACADEMIC_COURSE_NAME
                ],NULL, NULL, NULL, NULL,'AC')
                , false);
        $select->from(['AC' => AcademicCourse::TABLE_NAME])
                ->join(['AP' => AcademicProgram::TABLE_NAME], 'AC.ACADEMIC_PROGRAM_ID=AP.ACADEMIC_PROGRAM_ID', ["ACADEMIC_PROGRAM_NAME" => new Expression('INITCAP(AP.ACADEMIC_PROGRAM_NAME)')], "left");
        $select->where(["AC.STATUS='E'"]);
        $select->order("AC.ACADEMIC_COURSE_NAME ASC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function fetchById($id) {
        $rowset= $this->tableGateway->select(function(Select $select)use($id){
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(
                AcademicCourse::class,
                [
                    AcademicCourse::ACADEMIC_COURSE_NAME
                ])
                , false);
            $select->where([AcademicCourse::ACADEMIC_COURSE_ID => $id]);
        });
        return $rowset->current();
    }

    public function delete($id) {
        $this->tableGateway->update([AcademicCourse::STATUS => 'D'], [AcademicCourse::ACADEMIC_COURSE_ID => $id]);
    }

}
