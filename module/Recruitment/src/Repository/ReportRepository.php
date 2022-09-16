<?php
namespace Recruitment\Repository;

use Application\Repository\HrisRepository;
use Zend\Db\Adapter\AdapterInterface;
use Recruitment\Model\OpeningVacancy;
use Application\Model\Model;
use Zend\Db\Sql\Sql;
use Application\Helper\EntityHelper;
use Recruitment\Model\OptionsModel;
use Application\Helper\Helper;
use Zend\Db\Sql\Expression;

class ReportRepository extends HrisRepository{
    public function __construct(AdapterInterface $adapter, $tablename = null)
    {
        parent::__construct($adapter, OptionsModel::TABLE_NAME);
    }

    public function getFilteredRecords($search)
    {
        // echo '<pre>'; print_r($search['OpeningNo']); die;
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([            
            new Expression("REC.OPENING_ID AS OPENING_ID"),
            new Expression("REC.OPENING_NO AS OPENING_NO"),
            new Expression("REC.VACANCY_TOTAL_NO AS VACANCY_TOTAL_NO"),
            new Expression("REC.START_DATE AS START_DATE"),
            new Expression("REC.END_DATE AS END_DATE"),
            new Expression("REC.EXTENDED_DATE AS EXTENDED_DATE"),
            new Expression("TO_CHAR(REC.INSTRUCTION_EDESC) AS INSTRUCTION_EDESC"),
            new Expression("TO_CHAR(REC.INSTRUCTION_NDESC) AS INSTRUCTION_NDESC"),
            new Expression("HV.VACANCY_ID AS VACANCY_ID"),
            new Expression("HV.VACANCY_TYPE AS VACANCY_TYPE"),
            new Expression("HV.AD_NO AS AD_NO"),
            new Expression("HV.VACANCY_RESERVATION_NO AS VACANCY_RESERVATION_NO"),
            new Expression("HDS.DESIGNATION_TITLE AS DESIGNATION_TITLE"),
            new Expression("HS.STAGE_EDESC AS STAGE_EDESC"),
            ], true);

        $select->from(['REC' => OpeningVacancy::TABLE_NAME])
                ->join(['HV'  => 'HRIS_REC_VACANCY'],'HV.OPENING_ID=REC.OPENING_ID', 'VACANCY_NO', 'left') 
                ->join(['HDS' => 'HRIS_DESIGNATIONS'],'HDS.DESIGNATION_ID=HV.POSITION_ID', 'DESIGNATION_CODE', 'left')
                ->join(['HDE' => 'HRIS_DEPARTMENTS'],'HDE.DEPARTMENT_ID=HV.DEPARTMENT_ID', 'DEPARTMENT_CODE', 'left')    
                ->join(['HFL' => 'HRIS_FUNCTIONAL_LEVELS'],'HFL.FUNCTIONAL_LEVEL_ID=HV.LEVEL_ID', 'FUNCTIONAL_LEVEL_NO', 'left')
                ->join(['HVI' => 'HRIS_REC_VACANCY_INCLUSION'],'HVI.VACANCY_ID=HV.VACANCY_ID', 'VACANCY_ID', 'left')
                ->join(['HVD' => 'HRIS_ACADEMIC_DEGREES'],'HVD.ACADEMIC_DEGREE_ID=HV.QUALIFICATION_ID', 'REMARKS', 'left')
                ->join(['HVS' => 'HRIS_REC_VACANCY_STAGES'],'HVS.VACANCY_ID=HV.VACANCY_ID', 'VACANCY_ID', 'left')
                ->join(['HS'  => 'HRIS_REC_STAGES'],'HS.REC_STAGE_ID=HVS.REC_STAGE_ID', 'ORDER_NO', 'left')
                ->where(["REC.STATUS='E' AND HV.STATUS='E'"]);
        if (($search['OpeningNo'] != null)) {
            $select->where([
                "REC.OPENING_ID" => $search['OpeningNo']
            ]);
        }
        if (($search['stageId'] != null)) {
            $select->where([
                "HVS.REC_STAGE_ID" => $search['stageId']
            ]);
        }
        // if ($search['End_dt'] != null) {
        //     $select->where([
        //         "REC.END_DATE" => $search['Ed_ndt']
        //     ]);
        // }
        // $select->order("REC.OPENING_ID ASC");
        $boundedParameter = [];        
        $statement = $sql->prepareStatementForSqlObject($select);
        // print_r($statement->getSql()); die();
        $result = $statement->execute($boundedParameter);
        return $result;
    }
}