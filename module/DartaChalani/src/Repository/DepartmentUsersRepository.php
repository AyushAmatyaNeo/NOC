<?php
namespace DartaChalani\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Application\Repository\HrisRepository;
use DartaChalani\Model\DepartmentUsers;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Application\Helper\EntityHelper;


class DepartmentUsersRepository extends HrisRepository implements RepositoryInterface {

    protected $tableGateway;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(DepartmentUsers::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [DepartmentUsers::DU_ID => $id]);
    }

    public function editWith(Model $model, $where) {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, $where);
    }

    public function fetchAll() {
        return $this->tableGateway->select(function(Select $select) {
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(DepartmentUsers::class, [DepartmentUsers::DEPARTMENT_ID]), false);
            $select->where([DepartmentUsers::STATUS => EntityHelper::STATUS_ENABLED]);
            $select->order([DepartmentUsers::DEPARTMENT_ID => Select::ORDER_ASCENDING]);
        });
    }

    public function fetchById($id) {
        $rowset = $this->tableGateway->select([DepartmentUsers::DEPARTMENT_ID => $id]);
        return $rowset->current();
    }

    public function delete($id) {
        $this->tableGateway->update([DepartmentUsers::STATUS => 'D'], [DepartmentUsers::DEPARTMENT_ID => $id]);
        
    }

    public function deleteByDepartmentId($id){
        
        $sql = "DELETE FROM DC_DEPARTMENTS_USERS WHERE DEPARTMENT_ID = {$id}";
        return $this->rawQuery($sql);
        
    }
    public function deleteByLocationId($id){
        
        $sql = "DELETE FROM DC_DEPARTMENTS_USERS WHERE LOCATION_ID = {$id}";
        return $this->rawQuery($sql);
        
    }
    public function assignedDepartmentList(){
        $sql = "select department_id from dc_departments_users";
        return $this->rawQuery($sql);
    }

    // public function getSearchResults($data) {
    //     $sql = "select OFFICE_ID, OFFICE_CODE, OFFICE_EDESC, OFFICE_NDESC from DC_OFFICES where DC_OFFICES.status = 'E' ";


    //     if ($data['officeCode'] != null || $data['officeCode'] != ''){
    //         $sql.=" and DC_OFFICES.OFFICE_CODE = {$data['officeCode']}";
    //     }

    //     if ($data['officeEDESC'] != null || $data['officeEDESC'] != ''){
    //         $sql.=" and DC_OFFICES.OFFICE_EDESC = '{$data['officeEDESC']}'";   
    //     }

    //     if ($data['officeNDESC'] != null || $data['officeNDESC'] != ''){
    //         $sql.=" and DC_OFFICES.OFFICE_NDESC = '{$data['officeNDESC']}'";   
    //     }


    //     return $this->rawQuery($sql);
    // }

    
}