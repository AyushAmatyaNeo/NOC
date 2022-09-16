<?php

namespace Setup\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Setup\Model\Company;
use Setup\Model\Loan;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;
use Application\Repository\HrisRepository;

class LoanRepository extends HrisRepository implements RepositoryInterface {

    protected $tableGateway;
    protected $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter=$adapter;
        $this->tableGateway = new TableGateway(Loan::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
        $this->linkWithFiles();
    }

    public function edit(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        unset($array[Loan::LOAN_ID]);
        unset($array[Loan::CREATED_DATE]);
        $this->tableGateway->update($array, [Loan::LOAN_ID => $id]);
    }

    public function delete($id) {
        $this->tableGateway->update([Loan::STATUS => 'D'], [Loan::LOAN_ID => $id]);
    }

    public function fetchAll() {
        return $this->tableGateway->select();
    }

    public function linkWithFiles(){
        if(!empty($_POST['fileUploadList'])){
            $filesList = $_POST['fileUploadList'];
            $filesList = "'".implode("','" , $filesList)."'";
            $sql = "UPDATE HRIS_LOAN_MASTER_FILES SET LOAN_ID = (SELECT MAX(LOAN_ID) FROM HRIS_LOAN_MASTER_SETUP) 
                    WHERE FILE_NAME IN($filesList)";
            $statement = $this->adapter->query($sql);
            $statement->execute();
        }
    }

    public function fetchActiveRecord() {        
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Loan::class, [Loan::LOAN_NAME],NULL,NULL,NULL,NULL,'L',FALSE,FALSE), false);
        $select->from(['L' => Loan::TABLE_NAME]);
        // $select->join(['C' => Company::TABLE_NAME], "C.".Company::COMPANY_ID."=L.". Loan::COMPANY_ID, [Company::COMPANY_NAME => new Expression('(C.COMPANY_NAME)')], 'left');
        $select->where(["L.".Loan::STATUS."='E'"]);
        $select->order("L.".Loan::LOAN_NAME . " ASC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $rowset = $statement->execute();
        $result = [];
        $i = 1;
        foreach ($rowset as $row) {
            array_push($result, [
                'SN' => $i,
                'LOAN_ID' => $row['LOAN_ID'],
                'LOAN_CODE' => $row['LOAN_CODE'],
                'LOAN_NAME' => $row['LOAN_NAME'],
                'MIN_AMOUNT' => $row['MIN_AMOUNT'],
                'MAX_AMOUNT' => $row['MAX_AMOUNT'],
                'INTEREST_RATE' => $row['INTEREST_RATE'],
                'REPAYMENT_AMOUNT' => $row['REPAYMENT_AMOUNT'],
                'REPAYMENT_PERIOD' => $row['REPAYMENT_PERIOD'],
                'REMARKS' => $row['REMARKS'],
                'ISSUED_BY' => $row['ISSUED_BY'],
                'ELIGIBLE_PERIOD' => $row['ELIGIBLE_SERVICE_PERIOD']
            ]);
            $i += 1;
        }
        return $result;
    }

    public function fetchById($id) {
        $row = $this->tableGateway->select(function(Select $select)use($id) {
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Loan::class, [Loan::LOAN_NAME], [Loan::VALID_FROM, Loan::VALID_UPTO]), false);
            $select->where([Loan::LOAN_ID => $id]);
        });
        return $row->current();
    }

    public function getPayCodesList(){
        $sql = "SELECT PAY_ID, PAY_EDESC FROM HRIS_PAY_SETUP";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    public function getSelectedPayCodes($id){
        $sql = "SELECT PAY_ID_AMT, PAY_ID_INT FROM hris_loan_master_setup WHERE LOAN_ID = $id";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    public function getRateFlexibleFlag($id){
        $sql = "SELECT IS_RATE_FLEXIBLE FROM hris_loan_master_setup WHERE LOAN_ID = $id";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result)[0]['IS_RATE_FLEXIBLE'];
    }

    public function pushFileLink($data){
        $fileName = $data['file'];
        $id = $data['id'] == null ? 0 : $data['id'] ;
        $oldfile = $data['fileName'];
        $sql = "INSERT INTO HRIS_LOAN_MASTER_FILES(FILE_ID, FILE_NAME, FILE_IN_DIR_NAME, UPLOADED_DATE, LOAN_ID)
        VALUES( (select ifnull(max(FILE_ID)+1, 1) from HRIS_LOAN_MASTER_FILES), '{$fileName}', '{$oldfile}', current_date, {$id})";
        return $this->rawQuery($sql);
    }

    public function pullFilesbyLoanId($id){
        $sql = "select * from HRIS_LOAN_MASTER_FILES where LOAN_ID = $id and status = 'E'";
        return $this->rawQuery($sql);
    }

    public function deleteFileByName($name){
        $sql = "update HRIS_LOAN_MASTER_FILES set status = 'D' where FILE_NAME = '{$name}'";
        return $this->rawQuery($sql);
    }
}
