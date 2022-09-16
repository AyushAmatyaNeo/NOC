<?php

namespace Setup\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Setup\Model\Branch;
use Setup\Model\Company;
use Setup\Model\FileType;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression as Expression2;
use Zend\Db\Sql\Join;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class FileTypeRepo implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(FileType::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [Branch::BRANCH_ID => $id]);
    }

    public function fetchAll() {
        return $this->tableGateway->select(function(Select $select) {
                    $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(FileType::class, [FileType::NAME]), false);
                    $select->where([FileType::STATUS => EntityHelper::STATUS_ENABLED]);
                    $select->order([FileType::NAME => Select::ORDER_ASCENDING]);
                });
    }


    public function fetchById($id) {
        $id=new Expression2("lpad({$id}, 3, '0')");
        $rowset = $this->tableGateway->select([FileType::FILETYPE_CODE => $id]);
        return $rowset->current();
    }

    public function delete($id) {
         $id=new Expression2("lpad({$id}, 3, '0')");
        $this->tableGateway->update([FileType::STATUS => 'D'], [FileType::FILETYPE_CODE => $id]);
    }

}
