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
use Setup\Model\AcademicUniversity;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class AcademicUniversityRepository implements RepositoryInterface {

    private $tableGateway;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(AcademicUniversity::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [AcademicUniversity::ACADEMIC_UNIVERSITY_ID => $id]);
    }

    public function fetchAll() {
        return $this->tableGateway->select(function(Select $select) {
                    $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(AcademicUniversity::class, [AcademicUniversity::ACADEMIC_UNIVERSITY_NAME]), false);
                    $select->where([AcademicUniversity::STATUS => 'E']);
                    $select->order(AcademicUniversity::ACADEMIC_UNIVERSITY_NAME . " ASC");
                });
    }

    public function fetchById($id) {
        $rowset = $this->tableGateway->select(function(Select $select)use($id) {
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(AcademicUniversity::class, [AcademicUniversity::ACADEMIC_UNIVERSITY_NAME]), false);
            $select->where([AcademicUniversity::ACADEMIC_UNIVERSITY_ID => $id]);
        });
        return $rowset->current();
    }

    public function delete($id) {
        $this->tableGateway->update([AcademicUniversity::STATUS => 'D'], [AcademicUniversity::ACADEMIC_UNIVERSITY_ID => $id]);
    }

}
