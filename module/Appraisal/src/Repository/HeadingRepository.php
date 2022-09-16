<?php
namespace Appraisal\Repository;

use Application\Repository\RepositoryInterface;
use Appraisal\Model\Heading;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;
use Application\Helper\EntityHelper;

class HeadingRepository implements RepositoryInterface{
    
    private $tableGateway;
    private $adapter;
    
    public function __construct(AdapterInterface $adapter) {
       $this->adapter = $adapter; 
       $this->tableGateway = new TableGateway(Heading::TABLE_NAME,$adapter);
    }

    public function add(\Application\Model\Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        $this->tableGateway->update([Heading::STATUS=>'D'],[Heading::HEADING_ID=>$id]);
    }

    public function edit(\Application\Model\Model $model, $id) {
        $data = $model->getArrayCopyForDB();
        unset($data[Heading::HEADING_ID]);
        unset($data[Heading::CREATED_DATE]);
        unset($data[Heading::STATUS]);
        $this->tableGateway->update($data,[Heading::HEADING_ID=>$id]);
    }

    public function fetchAll() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Heading::class,
                [Heading::HEADING_EDESC,Heading::HEADING_NDESC],null,null,null,null,"AH"), true);
        $select->from(['AH' => "HRIS_APPRAISAL_HEADING"])
                ->join(['AT' => 'HRIS_APPRAISAL_TYPE'], 'AT.APPRAISAL_TYPE_ID=AH.APPRAISAL_TYPE_ID', ["APPRAISAL_TYPE_EDESC"=>new Expression("INITCAP(AT.APPRAISAL_TYPE_EDESC)")], "left");
        
        $select->where(["AH.STATUS='E' AND AT.STATUS='E'"]);
        $select->order("AH.HEADING_EDESC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function fetchById($id) {
        $rowset = $this->tableGateway->select(function(Select $select) use($id){
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Heading::class,
                [Heading::HEADING_EDESC,Heading::HEADING_NDESC]),false);
            $select->where([Heading::HEADING_ID => $id, Heading::STATUS => 'E']);
        });
        return $result = $rowset->current();
    }
    
    public function fetchByAppraisalTypeId($appraisalTypeId) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Heading::class,
                [Heading::HEADING_EDESC,Heading::HEADING_NDESC],null,null,null,null,"AH"),false);
        $select->from(['AH' => "HRIS_APPRAISAL_HEADING"])
                ->join(['AT' => 'HRIS_APPRAISAL_TYPE'], 'AT.APPRAISAL_TYPE_ID=AH.APPRAISAL_TYPE_ID', ["APPRAISAL_TYPE_EDESC"=>new Expression("INITCAP(AT.APPRAISAL_TYPE_EDESC)")], "left");
        
        $select->where(["AH.STATUS='E' AND AH.APPRAISAL_TYPE_ID=".$appraisalTypeId." AND AT.STATUS='E'"]);
        $select->order("AH.HEADING_ID");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }
    
    public function getActiveRecord(){
        $sql = "SELECT QUESTION_ID, INITCAP(QUESTION_EDESC) AS QUESTION_EDESC, 
   TO_CHAR(NULL) INITCAP(HEADING_EDESC) AS HEADING_EDESC, HEADING_ID  FROM HRIS_APPRAISAL_QUESTION
   UNION
   SELECT (NULL) QUESTION_ID, TO_CHAR(NULL) INITCAP(QUESTION_EDESC) AS QUESTION_EDESC , INITCAP(HEADING_EDESC) AS HEADING_EDES ,HEADING_ID
   FROM HRIS_APPRAISAL_HEADING WHERE APPRAISAL_TYPE_ID=6";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
        
    }

}