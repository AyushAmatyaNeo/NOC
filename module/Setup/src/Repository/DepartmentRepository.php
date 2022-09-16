<?php
namespace Setup\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\HrisRepository;
use Application\Repository\RepositoryInterface;
use Setup\Model\Branch;
use Setup\Model\Company;
use Setup\Model\Department;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

class DepartmentRepository extends HrisRepository implements RepositoryInterface {

    protected $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        parent::__construct($adapter, Department::TABLE_NAME);
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        $temp = $model->getArrayCopyForDB();
        if (!isset($temp[Department::COMPANY_ID])) {
            $temp[Department::COMPANY_ID] = null;
        }
        $this->tableGateway->update($temp, [Department::DEPARTMENT_ID => $id]);
    }

    public function fetchAll() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Department::class, [Department::DEPARTMENT_NAME], NULL, NULL, NULL, NULL, 'D'), false);


        $select->from(['D' => Department::TABLE_NAME]);
        $select->join(['C' => "HRIS_COUNTRIES"], "D." . Department::COUNTRY_ID . "=C.COUNTRY_ID", ['COUNTRY_NAME' => new Expression('INITCAP(C.COUNTRY_NAME)')], 'left')
            ->join(['PD' => Department::TABLE_NAME], "D." . Department::PARENT_DEPARTMENT . "=PD.DEPARTMENT_ID", ['PARENT_DEPARTMENT' => new Expression('(PD.DEPARTMENT_NAME)')], 'left')
            ->join(['B' => Branch::TABLE_NAME], "D." . Department::BRANCH_ID . "=B." . Branch::BRANCH_ID, [Branch::BRANCH_NAME => new Expression('(B.' . Branch::BRANCH_NAME . ')')], 'left')
            ->join(['CP' => Company::TABLE_NAME], "CP." . Company::COMPANY_ID . "=D." . Department::COMPANY_ID, [Company::COMPANY_NAME => new Expression('(CP.COMPANY_NAME)')], 'left');
        $select->where(["D.STATUS='E'"]);
        $select->order([
            "D." . Department::DEPARTMENT_NAME => Select::ORDER_ASCENDING,
            'CP.' . Company::COMPANY_NAME => Select::ORDER_ASCENDING,
            'B.' . Branch::BRANCH_NAME => Select::ORDER_ASCENDING
        ]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function fetchById($id) {
        $result = $this->tableGateway->select(function(Select $select)use($id) {
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Department::class, [Department::DEPARTMENT_NAME]), false);
            $select->where([Department::DEPARTMENT_ID => $id]);
        });

        return $result->current();
    }

    public function delete($id) {
        $this->tableGateway->update([Department::STATUS => 'D'], [Department::DEPARTMENT_ID => $id]);
    }

    public function fetchAllBranchAndCompany() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(['BRANCH_ID', 'BRANCH_NAME']);
        $select->from(['B' => Branch::TABLE_NAME]);
        $select->join(['C' => Company::TABLE_NAME], "C." . Company::COMPANY_ID . "=B." . Branch::COMPANY_ID, array('COMPANY_ID', 'COMPANY_NAME' => new Expression('(C.COMPANY_NAME)')), 'inner');
        $select->where(["C.STATUS='E'"]);
        $select->where(["B.STATUS='E'"]);
        $select->order("B." . Branch::BRANCH_NAME . " ASC");
        $statement = $sql->prepareStatementForSqlObject($select);

        $result = $statement->execute();

        $list = [];
        foreach ($result as $row) {
            array_push($list, $row);
        }

        $companyList = [];
        foreach ($list as $val) {
            $newKey = $val['COMPANY_ID'];
            $companyList[$newKey][] = $val;
        }
        return $companyList;
    }

    public function fetchAllBranchAndDepartment() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(['DEPARTMENT_ID', 'DEPARTMENT_NAME']);
        $select->from(['D' => Department::TABLE_NAME]);
        $select->join(['B' => Branch::TABLE_NAME], "B." . Branch::BRANCH_ID . "=D." . Department::BRANCH_ID, array('BRANCH_ID', 'BRANCH_NAME' => new Expression('B.BRANCH_NAME')), 'inner');
        $select->where(["B.STATUS='E'"]);
        $select->where(["D.STATUS='E'"]);
        $select->order("D." . Department::DEPARTMENT_NAME . " ASC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $list = [];
        foreach ($result as $row) {
            array_push($list, $row);
        }
        $departmentList = [];
        foreach ($list as $val) {
            $newKey = $val['BRANCH_ID'];
            $departmentList[$newKey][] = $val;
        }
        return $departmentList;
    }

    public function jvTableFlag(){
        $sql = "SELECT (CASE WHEN (SELECT COUNT(*) FROM TABS WHERE UPPER(TABLE_NAME) = 'HRIS_PAYROLL_JV') < 1 
                THEN 'N' ELSE 'Y' END) JV_TABLE_FLAG FROM DUAL";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    public function fetchJvDetails($deptId){
        $sql = "SELECT HPS.PAY_ID, HPS.PAY_EDESC, HPJ.JV_NAME, HPJ.FLAG, HPJ.PAY_TYPE_FLAG
        FROM 
        HRIS_PAY_SETUP HPS
        LEFT JOIN HRIS_PAYROLL_JV HPJ ON HPS.PAY_ID = HPJ.PAY_ID AND HPJ.DEPARTMENT_ID = $deptId
        ORDER BY PAY_ID";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    public function updateJv($data, $employeeId){
        $deptId = $data['deptId'];
        for($i = 0; $i < count($data['payId']); $i++){
            if($data['jvName'][$i] == '' || $data['jvName'][$i] == null){
                continue;
            }
            $payId = $data['payId'][$i];
            $jvName = $data['jvName'][$i];
            $flag = $data['flag'][$i];
            $payTypeFlag = $data['payTypeFlag'][$i];
            
            $sql = "
            declare
            v_exists varchar2(1) := 'F';
            begin
            begin
                select 'T'
                into v_exists
                from HRIS_PAYROLL_JV
                where DEPARTMENT_ID = $deptId
                and PAY_ID = $payId;
            exception
                when no_data_found then
                null;
            end;
            if v_exists = 'T' then
                update HRIS_PAYROLL_JV
                set JV_NAME = '$jvName', FLAG = '$flag', MODIFIED_BY = $employeeId, PAY_TYPE_FLAG = '$payTypeFlag'
                where DEPARTMENT_ID = $deptId
                and PAY_ID = $payId;
            else
            INSERT INTO HRIS_PAYROLL_JV(DEPARTMENT_ID, PAY_ID, JV_NAME, STATUS, PAY_TYPE_FLAG, FLAG, CREATED_BY)
            VALUES($deptId, $payId, '$jvName', 'E', '$payTypeFlag', '$flag', $employeeId);
            end if;
            end;
            ";
            $statement = $this->adapter->query($sql);
            $statement->execute();
        }
    }
}
