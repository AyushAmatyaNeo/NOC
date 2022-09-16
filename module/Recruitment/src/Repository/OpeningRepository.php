<?php
namespace Recruitment\Repository;
use Application\Repository\HrisRepository;
use Zend\Db\Adapter\AdapterInterface;
use Application\Model\Model;
use Recruitment\Model\OpeningVacancy;
use Zend\Db\Sql\Sql;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Zend\Db\Sql\Expression;

class OpeningRepository extends HrisRepository
{
    public function __construct(AdapterInterface $adapter, $tableName = null) 
    {
        parent::__construct($adapter, OpeningVacancy::TABLE_NAME);
    }

    public function add(Model $opening_data) 
    {
        $addData=$opening_data->getArrayCopyForDB();
        // echo '<pre>'; print_r($addData); die();
        $this->tableGateway->insert($addData);
        $this->linkVacancyWithFiles();
    }
    public function getFilteredRecords($search)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([            
            new Expression("REC.OPENING_ID          AS OPENING_ID"),
            new Expression("REC.OPENING_NO          AS OPENING_NO"),
            new Expression("REC.VACANCY_TOTAL_NO    AS VACANCY_TOTAL_NO"),
            new Expression("REC.RESERVATION_NO      AS RESERVATION_NO"),
            new Expression("REC.START_DATE          AS START_DATE"),
            new Expression("REC.END_DATE            AS END_DATE"),
            new Expression("REC.EXTENDED_DATE       AS EXTENDED_DATE"),
            new Expression("TO_CHAR(REC.INSTRUCTION_EDESC) AS INSTRUCTION_EDESC"),
            new Expression("TO_CHAR(REC.INSTRUCTION_NDESC) AS INSTRUCTION_NDESC"),           
            ], true);

        $select->from(['REC' => OpeningVacancy::TABLE_NAME]);
                // ->where(["REC.STATUS='E'"]);
        if (($search['OpeningId'] != null)) {
            $select->where([
                "REC.OPENING_ID" => $search['OpeningId']
            ]);
        }
        $select->where(['REC.STATUS=\'E\'']);
        $select->order('REC.OPENING_NO ASC');
        $boundedParameter = [];        
        $statement = $sql->prepareStatementForSqlObject($select);
        // print_r($statement->getSql()); die();
        $result = $statement->execute($boundedParameter);
        return $result;
    }
    public function fetchById($id)
    {
        $sql = new sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("REC.OPENING_ID AS OPENING_ID"),
            new Expression("REC.OPENING_NO AS OPENING_NO"),
            new Expression("REC.VACANCY_TOTAL_NO AS VACANCY_TOTAL_NO"),
            new Expression("REC.RESERVATION_NO AS RESERVATION_NO"),
            new Expression("INITCAP(TO_CHAR(REC.START_DATE, 'DD-MON-YYYY')) AS START_DATE"),
            // new Expression("TO_CHAR(REC.START_DATE,'DD-MON-YYYY') AS START_DATE"),
            new Expression("INITCAP(TO_CHAR(REC.END_DATE,'DD-MON-YYYY')) AS END_DATE"),
            new Expression("INITCAP(TO_CHAR(REC.EXTENDED_DATE,'DD-MON-YYYY')) AS EXTENDED_DATE"),
            new Expression("TO_CHAR(REC.INSTRUCTION_EDESC) AS INSTRUCTION_EDESC"),
            new Expression("TO_CHAR(REC.INSTRUCTION_NDESC) AS INSTRUCTION_NDESC"),
            new Expression("(CASE WHEN REC.STATUS= 'E' THEN 'ENABLE' ELSE 'DISABLE' END) AS STATUS"),
        ], true);

        $select->from(['REC' => OpeningVacancy::TABLE_NAME]);
        $select->where(["REC.OPENING_ID='{$id}'"]);
        $boundedParameter = [];
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute($boundedParameter);

        return $result->current();


    }
    public function edit(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        // print_r($array); die;
        $this->tableGateway->update($array, [OpeningVacancy::OPENING_ID => $id]);
        
    }
    public function delete(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        $deleted_dt = date('Y-m-d');
        $rewardSql = "update HRIS_REC_OPENINGS SET status = 'D', Deleted_By = {$array["DELETED_BY"]} , Deleted_Dt = '{$deleted_dt}' WHERE OPENING_ID = {$id}";
        return EntityHelper::rawQueryResult($this->adapter, $rewardSql);
    }

    // File Upload
    public function pushFileLink($data){ 
        $fileName = $data['fileName'];
        $fileInDir = $data['filePath'];
        $uploded_date = date('Y-m-d');
        // $opening_id = Helper::extractDbData($this->adapter->query("SELECT ifnull(MAX(OPENING_ID)+1,'1') AS ID FROM HRIS_REC_OPENINGS")->execute())[0]['ID'];
        $sql = "INSERT INTO HRIS_REC_OPENINGS_DOCUMENTS(FILE_ID, FILE_NAME, FILE_IN_DIR_NAME, UPLOADED_DATE,OPENING_ID,STATUS) VALUES ((SELECT ifnull(MAX(FILE_ID)+1,'1') FROM HRIS_REC_OPENINGS_DOCUMENTS), '$fileName', '$fileInDir','{$uploded_date}', null, 'E')";
        // print_r($sql); die();
        $statement = $this->adapter->query($sql);
        $statement->execute(); 
        $sql = "SELECT * FROM HRIS_REC_OPENINGS_DOCUMENTS WHERE FILE_ID IN (SELECT MAX(FILE_ID) AS FILE_ID FROM HRIS_REC_OPENINGS_DOCUMENTS)";
        $statement = $this->adapter->query($sql);
        return Helper::extractDbData($statement->execute());
    }
    public function pullFileLink($Vid){ 

        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
        new Expression("RVF.FILE_ID AS FILE_ID"),
        new Expression("RVF.FILE_NAME AS FILE_NAME"),
        new Expression("RVF.FILE_IN_DIR_NAME AS FILE_IN_DIR_NAME"),
        new Expression("RVF.UPLOADED_DATE AS UPLOADED_DATE"),
        new Expression("RVF.STATUS AS STATUS"),
        new Expression("RVF.OPENING_ID AS OPENING_ID"),], true);
        $select->from(['RVF' => 'HRIS_REC_OPENINGS_DOCUMENTS']);
        $select->where(["RVF.OPENING_ID='{$Vid}'"]);
        $select->where(["RVF.STATUS='E'"]);
        $boundedParameter = [];
        $statement = $sql->prepareStatementForSqlObject($select);        
        $result = $statement->execute($boundedParameter);
        // print_r ($statement->getSql()); die();
        return $result->current();
        // $sql = "SELECT * FROM HRIS_REC_VACANCY_FILES WHERE VACANCY_ID = $fileId";
        // // print_r($sql); die();
        // $statement = $this->adapter->query($sql);
        // $statement->execute(); 
        // return Helper::extractDbData($statement->execute());
    }
    public function updateFileLink($data)
    {   
        $fileName = $data['fileName'];
        $fileInDir = $data['filePath'];
        $fileId    = $data['linkId'];
        $uploded_date = date('Y-m-d');
        // $sql = "UPDATE HRIS_REC_VACANCY_FILES SET FILE_NAME = '$fileName' , FILE_IN_DIR_NAME = '$fileInDir' WHERE VACANCY_ID = $fileId";
        $sql = "INSERT INTO HRIS_REC_OPENINGS_DOCUMENTS (FILE_ID,FILE_NAME, FILE_IN_DIR_NAME,UPLOADED_DATE,OPENING_ID,STATUS)
                    VALUES ((SELECT ifnull(MAX(FILE_ID)+1,'1') FROM HRIS_REC_OPENINGS_DOCUMENTS), '{$fileName}', '{$fileInDir}','{$uploded_date}', '{$fileId}','E')";
        // echo '<pre>'; print_r($sql); die;
        $statement = $this->adapter->query($sql);
        // $statement->execute(); 
        return Helper::extractDbData($statement->execute());
    }
    public function linkVacancyWithFiles()
    {
        if(!empty($_POST['fileUploadList'])){
            $filesList = $_POST['fileUploadList'];
            $filesList = implode(',', $filesList);

            $sql = "UPDATE HRIS_REC_OPENINGS_DOCUMENTS SET OPENING_ID = (SELECT MAX(OPENING_ID) FROM HRIS_REC_OPENINGS) 
                    WHERE FILE_ID IN ($filesList)";
            $statement = $this->adapter->query($sql);
            $statement->execute();
        }
    }
    public function deleteFileByName($name)
    {
        $sql = "UPDATE HRIS_REC_OPENINGS_DOCUMENTS SET STATUS = 'D' WHERE FILE_IN_DIR_NAME = '{$name}'";
        return $this->rawQuery($sql);
    }

    public function fetchDocuments($id)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
        new Expression("RVF.FILE_ID AS FILE_ID"),
        new Expression("RVF.FILE_NAME AS FILE_NAME"),
        new Expression("RVF.FILE_IN_DIR_NAME AS FILE_IN_DIR_NAME"),
        new Expression("RVF.UPLOADED_DATE AS UPLOADED_DATE"),
        new Expression("RVF.STATUS AS STATUS"),
        new Expression("RVF.OPENING_ID AS OPENING_ID"),], true);
        $select->from(['RVF' => 'HRIS_REC_OPENINGS_DOCUMENTS']);
        $select->where(["RVF.OPENING_ID='{$id}'"]);
        $select->where(["RVF.STATUS='E'"]);
        $statement = $sql->prepareStatementForSqlObject($select);        
        $result = $statement->execute();
        // print_r ($statement->getSql()); die();
        return $result->current();
    }
}