<?php

namespace Notification\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Notification\Model\NewsTypeModel;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class NewsTypeRepository implements RepositoryInterface {

    private $adapter;
    private $tableGateway;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(NewsTypeModel::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        $this->tableGateway->update([NewsTypeModel::STATUS => 'D'], [NewsTypeModel::NEWS_TYPE_ID => $id]);
    }

    public function edit(Model $model, $id) {
        $data = $model->getArrayCopyForDB();
        unset($data[NewsTypeModel::CREATED_BY]);
        unset($data[NewsTypeModel::CREATED_DT]);
        unset($data[NewsTypeModel::STATUS]);
        $this->tableGateway->update($data, [NewsTypeModel::NEWS_TYPE_ID => $id]);
    }

    public function fetchAll() {
        $sql = "SELECT NEWS_TYPE_ID,NEWS_TYPE_DESC,STATUS,
            CASE UPLOAD_FLAG WHEN 'Y' THEN 'YES' ELSE 'NO' END AS UPLOAD_FLAG
                  FROM HRIS_NEWS_TYPE WHERE STATUS='E' ORDER BY NEWS_TYPE_DESC";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;

//        return $this->tableGateway->select(function(Select $select){
//            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(NewsTypeModel::class, [NewsTypeModel::NEWS_TYPE_DESC]), false);
//            $select->where([NewsTypeModel::STATUS=>'E']);
//            $select->order(NewsTypeModel::NEWS_TYPE_DESC);
//        });
    }

    public function fetchById($id) {
        $rawResult = $this->tableGateway->select(function(Select $select)use($id) {
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(NewsTypeModel::class, [
                        NewsTypeModel::NEWS_TYPE_DESC,
                    ]), false);
            $select->where([NewsTypeModel::STATUS => EntityHelper::STATUS_ENABLED]);
            $select->where([NewsTypeModel::NEWS_TYPE_ID => $id]);
        });
        return $rawResult->current();
    }

}
