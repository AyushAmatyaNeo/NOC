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
use Setup\Model\AcademicDegree;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class AcademicDegreeRepository implements RepositoryInterface {

    private $tableGateway;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(AcademicDegree::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        try {
            $this->tableGateway->insert($model->getArrayCopyForDB());
        } catch (Zend_Db_Adapter_Exception $ex) {
            print_r($ex);
            die();
        }
    }

    public function edit(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [AcademicDegree::ACADEMIC_DEGREE_ID => $id]);
    }

    public function fetchAll() {
        return $this->tableGateway->select(function(Select $select) {
                    $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(AcademicDegree::class, [AcademicDegree::ACADEMIC_DEGREE_NAME]), false);
                    $select->where([AcademicDegree::STATUS => 'E']);
                    $select->order(AcademicDegree::ACADEMIC_DEGREE_NAME . " ASC");
                });
    }

    public function fetchById($id) {
        $rowset = $this->tableGateway->select(function(Select $select)use($id) {
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(AcademicDegree::class, [AcademicDegree::ACADEMIC_DEGREE_NAME]), false);
            $select->where([AcademicDegree::ACADEMIC_DEGREE_ID => $id]);
        });
        return $rowset->current();
    }

    public function delete($id) {
        $this->tableGateway->update([AcademicDegree::STATUS => 'D'], [AcademicDegree::ACADEMIC_DEGREE_ID => $id]);
    }

}
