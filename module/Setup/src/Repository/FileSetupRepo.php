<?php

namespace Setup\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Setup\Model\FileSetup;
use Setup\Model\FileType;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Join;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class FileSetupRepo implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(FileSetup::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [FileSetup::FILE_ID => $id]);
    }

    public function fetchAll() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(['FS' => FileSetup::TABLE_NAME]);
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(FileSetup::class, [FileSetup::FILE_NAME],null, null, null, null, 'FS'), false);
        $select->join(['FT' => FileType::TABLE_NAME], "FT.FILETYPE_CODE = FS.FILE_TYPE_CODE", [FileType::NAME => new Expression("FT.NAME")], Join::JOIN_LEFT);
        $select->where(['FS.' . FileSetup::STATUS => EntityHelper::STATUS_ENABLED]);
        $select->order(['FS.' . FileSetup::FILE_NAME => Select::ORDER_ASCENDING]);

        $statement = $sql->prepareStatementForSqlObject($select);
//        print_r($statement->getSql()); die;
        $result = $statement->execute();
        return $result;

    }


    public function fetchById($id) {
        $rowset = $this->tableGateway->select([FileSetup::FILE_ID => $id]);
        return $rowset->current();
    }

    public function delete($id) {
        $this->tableGateway->update([FileSetup::STATUS => 'D'], [FileSetup::FILE_ID => $id]);
    }

}
