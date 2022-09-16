<?php

namespace Setup\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Setup\Model\OsCompany;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class OsCompanyRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(OsCompany::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        
        //  echo'<pre>';print_r($model);die;
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        // print_r($array);die;
        $this->tableGateway->update($array, [OsCompany::COMPANY_ID => $id]);
    }

    public function fetchAll() {
        return $this->tableGateway->select(function(Select $select) {
                    $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(OsCompany::class, [OsCompany::COMPANY_NAME]), false);
                    $select->where([OsCompany::STATUS => EntityHelper::STATUS_ENABLED]);
                    $select->order([OsCompany::COMPANY_NAME => Select::ORDER_ASCENDING]);
                });
    }

    public function fetchById($id) {
        $rowset = $this->tableGateway->select(function(Select $select) use($id) {
            $select->where([
                OsCompany::COMPANY_ID => $id,
                OsCompany::STATUS => EntityHelper::STATUS_ENABLED
            ]);
        });
        // print_r($rowset); die;
        return $rowset->current();

// $sql="SELECT * FROM HRIS_COMPANY WHERE COMPANY_ID = $id";

        //  $statement = $this->adapter->query($sql);
        // $result = $statement->execute();
        // return $result->current();
    }

    public function delete($id) {
        $this->tableGateway->update([
            OsCompany::STATUS => EntityHelper::STATUS_DISABLED
                ], [
            OsCompany::COMPANY_ID => $id
        ]);
    }

}
