<?php
namespace Appraisal\Repository;

use Application\Repository\RepositoryInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Appraisal\Model\Setup;
use Application\Model\Model;
use Appraisal\Model\Type;
use Appraisal\Model\Stage;
use Application\Helper\EntityHelper;

class SetupRepository implements RepositoryInterface{
    private $tableGateway;
    private $adapter;
    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(Setup::TABLE_NAME,$adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        $this->tableGateway->update([Setup::STATUS=>'D'],[Setup::APPRAISAL_ID=>$id]);
    }

    public function edit(Model $model, $id) {
        $data = $model->getArrayCopyForDB();
        unset($data[Setup::APPRAISAL_ID]);
        unset($data[Setup::CREATED_DATE]);
        unset($data[Setup::STATUS]);
        $this->tableGateway->update($data,[Setup::APPRAISAL_ID=>$id]);
    }

    public function fetchAll() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Setup::class,
                [Setup::APPRAISAL_EDESC, Setup::APPRAISAL_NDESC],
                [Setup::START_DATE,Setup::END_DATE],null,null,null,"S"),false);
        $select->from(['S' => Setup::TABLE_NAME])
                ->join(['AT' => Type::TABLE_NAME], 'AT.'.Type::APPRAISAL_TYPE_ID.'=S.'.Setup::APPRAISAL_TYPE_ID, ["APPRAISAL_TYPE_EDESC"=>new Expression("INITCAP(AT.APPRAISAL_TYPE_EDESC)")], "left")
                ->join(['AST' => Stage::TABLE_NAME], 'AST.'.Stage::STAGE_ID.'=S.'.Setup::CURRENT_STAGE_ID, ["STAGE_EDESC"=>new Expression("INITCAP(AST.STAGE_EDESC)")], "left");
        
        $select->where(["S.".Setup::STATUS."='E'"]);
        $select->order("S.".Setup::APPRAISAL_EDESC);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Setup::class,
                [Setup::APPRAISAL_EDESC, Setup::APPRAISAL_NDESC],
                [Setup::START_DATE,Setup::END_DATE],null,null,null,"S"),false);
        $select->from(['S' => Setup::TABLE_NAME])
                ->join(['AT' => Type::TABLE_NAME], 'AT.'.Type::APPRAISAL_TYPE_ID.'=S.'.Setup::APPRAISAL_TYPE_ID, ["APPRAISAL_TYPE_EDESC"=>new Expression("INITCAP(AT.APPRAISAL_TYPE_EDESC)")], "left")
                ->join(['AST' => Stage::TABLE_NAME], 'AST.'.Stage::STAGE_ID.'=S.'.Setup::CURRENT_STAGE_ID, ["STAGE_EDESC"=>new Expression("INITCAP(AST.STAGE_EDESC)")], "left");
        
        $select->where(["S.".Setup::STATUS."='E' AND ".Setup::APPRAISAL_ID."=". $id]);
        $select->order("S.".Setup::APPRAISAL_EDESC);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

}
