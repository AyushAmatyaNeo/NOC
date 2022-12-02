<?php
namespace JobResponsibilityManagement\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\HrisRepository;
use Application\Repository\RepositoryInterface;
use Setup\Model\Company;
use Setup\Model\Designation;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Setup\Model\JobResponsibility;

class JobResponsibilityRepository extends HrisRepository implements RepositoryInterface {

    public function __construct(AdapterInterface $adapter) {
        parent::__construct($adapter, JobResponsibility::TABLE_NAME);
    }

    public function fetchAll() {
        return $this->tableGateway->select(function(Select $select)use($id) {
            $select->where(['STATUS' => 'E']);
        });
    }

    public function fetchById($id) {
        $rowset = $this->tableGateway->select(function(Select $select)use($id) {
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(JobResponsibility::class, [JobResponsibility::JOB_RES_ENG_NAME]), false);
            $select->where([JobResponsibility::ID => $id, JobResponsibility::STATUS => 'E']);
        });
        return $rowset->current();
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [JobResponsibility::ID => $id]);
    }

    public function delete($id) {
        $this->tableGateway->update([JobResponsibility::STATUS => 'D'], ["ID" => $id]);
    }
}
