<?php
namespace DartaChalani\Repository;

use Application\Model\Model;
use Application\Helper\Helper;
use Application\Repository\RepositoryInterface;
use Application\Repository\HrisRepository;
use DartaChalani\Model\ChalaniFinal;
use DartaChalani\Model\UserAssign;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Application\Helper\EntityHelper;


class FinalDispatchRepo extends HrisRepository implements RepositoryInterface {

    protected $tableGateway;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(ChalaniFinal::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [Chalani::DISPATCH_DRAFT_ID => $id]);
    }


    public function fetchAll() {
        return $this->tableGateway->select(function(Select $select) {
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Chalani::class, [Chalani::DISPATCH_TEMP_CODE]), false);
            $select->where([Chalani::STATUS => EntityHelper::STATUS_ENABLED]);
            $select->order([Chalani::DISPATCH_TEMP_CODE => Select::ORDER_ASCENDING]);
        });
    }

    public function fetchById($id) {
        return $this->tableGateway->select(function(Select $select) use($id) {
            $select->columns(Helper::convertColumnDateFormat($this->adapter, new Chalani(), [
                        'documentDt', 'draftDt'
                    ]), false);
            $select->where([Chalani::DISPATCH_DRAFT_ID => $id]);})->current();
    }

    public function delete($id) {
        $this->tableGateway->update([Chalani::STATUS => 'D'], [Chalani::DISPATCH_DRAFT_ID => $id]);
        
    }

    public function forward(Model $model) {
        $tableGateway = new TableGateway(UserAssign::TABLE_NAME, $this->adapter);
        $tableGateway->insert($model->getArrayCopyForDB());
    }

    public function ack(Model $model) {
        $tableGateway = new TableGateway(UserAssign::TABLE_NAME, $this->adapter);
        $tableGateway->insert($model->getArrayCopyForDB());
    }

    public function getSearchResults($data) {
        $sql = "select DC.RESPONSE_FLAG, DC.DESCRIPTION, DC.DISPATCH_DATE, DC.DOCUMENT_DATE, DC.DRAFT_ID, DC.DISPATCH_CODE, DC.LETTER_NUMBER, DC.REMARKS from DC_DISPATCH DC 
                left join HRIS_DEPARTMENTS D on (DC.FROM_DEPARTMENT_CODE = D.DEPARTMENT_ID)
                 where DC.status = 'E' ";

        if($data['departmentId'] != -1){
            $sql.=" and D.DEPARTMENT_ID = {$data['departmentId']}";
        }

        if ($data['letterNumber'] != null || $data['letterNumber'] != ''){
            $sql.=" and DC.LETTER_NUMBER = {$data['letterNumber']}";
        }

        if ($data['description'] != null || $data['description'] != ''){
            $sql.=" and DC.DESCRIPTION = '{$data['description']}'";   
        }

        if ($data['toOfficeCode'] != null || $data['toOfficeCode'] != ''){
            $sql.=" and DC.TO_OFFICE_ID = '{$data['toOfficeCode']}'";
        }

        if ($data['responseFlag'] != null || $data['responseFlag'] != ''){
            $sql.=" and DC.RESPONSE_FLAG = '{$data['responseFlag']}'";
        }

        return $this->rawQuery($sql);
    }

    public function pushFileLink($data){ 
        $fileName = $data['fileName'];
        $fileInDir = $data['filePath'];
        $sql = "INSERT INTO HRIS_LEAVE_FILES(FILE_ID, FILE_NAME, FILE_IN_DIR_NAME, LEAVE_ID) VALUES((SELECT MAX(FILE_ID)+1 FROM HRIS_LEAVE_FILES), '$fileName', '$fileInDir', null)";
        $statement = $this->adapter->query($sql);
        $statement->execute(); 
        $sql = "SELECT * FROM HRIS_LEAVE_FILES WHERE FILE_ID IN (SELECT MAX(FILE_ID) AS FILE_ID FROM HRIS_LEAVE_FILES)";
        $statement = $this->adapter->query($sql);
        return Helper::extractDbData($statement->execute());
    }

    
    
    public function getEmployeeList($departmentId){
        $sql = "select distinct employee_id from dc_departments_users where department_id = $departmentId";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }


    // public function getEmployeeList(){
    //     $sql = "SELECT EMPLOYEE_ID, full_name, department_id FROM HRIS_EMPLOYEES where status = 'E'";
    //     $statement = $this->adapter->query($sql);
    //     $result = Helper::extractDbData($statement->execute());

    //     $employeeList = [];

    //     foreach ($result as $allEmployee) {
            
    //         $tempId = $allEmployee['DEPARTMENT_ID'];
    //         (!array_key_exists($tempId, $employeeList)) ?
    //         $employeeList[$tempId][0] = $allEmployee :
    //         array_push($employeeList[$tempId], $allEmployee);
    //     }

    //     return $employeeList;
    // }
    
}
