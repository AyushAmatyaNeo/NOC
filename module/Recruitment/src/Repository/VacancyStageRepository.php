<?php
namespace Recruitment\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Repository\HrisRepository;
use Zend\Db\Adapter\AdapterInterface;
use Application\Model\Model;
use Recruitment\Model\VacancyStageModel;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;

class VacancyStageRepository extends HrisRepository{
    public function __construct(AdapterInterface $adapter, $tableName = null) {
        parent::__construct($adapter, VacancyStageModel::TABLE_NAME);
    }
    public function getFilteredRecords($search)
    {
        $sql =  new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("RS.REC_VACANCY_STAGE_ID AS REC_VACANCY_STAGE_ID"),
            new Expression("HV.AD_NO AS AD_NO"),
            new Expression("HS.ORDER_NO AS ORDER_NO"),
            new Expression("HS.STAGE_EDESC AS REC_STAGE_ID"),
            new Expression("OPN.OPENING_NO AS OPENING_NO"),
            new Expression("RS.REMARKS AS REMARKS"),
            new Expression("(CASE WHEN RS.STATUS = 'E' THEN 'ENABLE' ELSE 'DISABLE' END) AS STATUS")
        ],true);
        $select->from(['RS' => VacancyStageModel::TABLE_NAME])
                ->join(['HV' => 'HRIS_REC_VACANCY'],'RS.VACANCY_ID=HV.VACANCY_ID', 'POSITION_ID', 'left')
                ->join(['HS' => 'HRIS_REC_STAGES'], 'HS.REC_STAGE_ID=RS.REC_STAGE_ID', 'ORDER_NO','left')
                ->join(['OPN' => 'HRIS_REC_OPENINGS'], 'OPN.OPENING_ID=HV.OPENING_ID', 'STATUS','left')
                ->where("HV.STATUS='E'");
        if (($search['adnumberId'] != null)) {
            $select->where([
                "HV.VACANCY_ID" => $search['adnumberId']
            ]);
        }
        if (($search['openingId'] != null)) {
            $select->where([
                "HV.OPENING_ID" => $search['openingId']
            ]);
        }
        $select->order('HV.opening_id ASC');
        $boundedParameter = [];
        $statement = $sql->prepareStatementForSqlObject($select);
        // print_r ($statement->getSql()); die();
        $result = $statement->execute($boundedParameter);

        return $result;
    }
    public function add($data){
        // echo'<pre>'; print_r($data); die;
        $array = $data->getArrayCopyForDb();
        $this->tableGateway->insert($array);
    }
    public function fetchById($id)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("RS.REC_VACANCY_STAGE_ID AS REC_VACANCY_STAGE_ID"),
            new Expression("RS.REC_STAGE_ID AS REC_STAGE_ID"),
            new Expression("RS.VACANCY_ID AS VACANCY_ID"),
            new Expression("RS.REC_STAGE_ID AS REC_STAGE_ID"),
            new Expression("RS.REMARKS AS REMARKS"),
            new Expression("(CASE WHEN RS.STATUS = 'E' THEN 'ENABLE' ELSE 'DISABLE' END) AS STATUS")
        ], true);
        $select->from(['RS' => VacancyStageModel::TABLE_NAME]);
                // ->join(['HV' => 'HRIS_REC_VACANCY'],'RS.VACANCY_ID=HV.VACANCY_ID', 'POSITION_ID', 'left')
                // ->join(['HS' => 'HRIS_REC_STAGES'], 'HS.REC_STAGE_ID=RS.REC_STAGE_ID', 'ORDER_NO','left');
        $select->where(["RS.REC_VACANCY_STAGE_ID='{$id}'"]);
        $boundedParameter = [];
        $statement = $sql->prepareStatementForSqlObject($select);
        // print_r ($statement->getSql()); die();
        $result = $statement->execute($boundedParameter);
        return $result->current();
    }
    public function edit(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [VacancyStageModel::REC_VACANCY_STAGE_ID => $id]);
        
    }
    public function delete(Model $model, $id){
        $array = $model->getArrayCopyForDB();
        $deleted_dt = date('Y-m-d');
        $rawsql = "UPDATE HRIS_REC_VACANCY_STAGES SET STATUS = 'D', DELETED_BY = {$array['DELETED_BY']}, DELETED_DT = '{$deleted_dt}' WHERE REC_VACANCY_STAGE_ID = '{$id}'";
        return EntityHelper::rawQueryResult($this->adapter, $rawsql);
    }
    public function manualStage($stageId, $vid){
        $sql = "UPDATE HRIS_REC_VACANCY_STAGES SET REC_STAGE_ID = $stageId where REC_VACANCY_STAGE_ID = $vid";
        $statement = $this->adapter->query($sql);
        $result = Helper::extractDbData($statement->execute());
        return $result;
    }
    public function getFilteredStages()
    {
        $sql = "SELECT * FROM hris_rec_stages WHERE ORDER_NO NOT IN (2,3,10,25,26,27)";
        $result =  $this->rawQuery($sql);
        return $result;
    }
}