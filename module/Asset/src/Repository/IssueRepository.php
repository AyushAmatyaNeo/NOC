<?php

namespace Asset\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Asset\Model\Group;
use Asset\Model\Issue;
use Asset\Model\Setup;
use Setup\Model\HrEmployees;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class IssueRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(Issue::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        return $this->tableGateway->insert($model->getArrayCopyForDB());
//        return true;
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $id) {
        $data = $model->getArrayCopyForDB();

        unset($data[Issue::ISSUE_ID]);
        unset($data[Issue::CREATED_DATE]);
        unset($data[Issue::STATUS]);
        return $this->tableGateway->update($data, [Issue::ISSUE_ID => $id]);
    }

    public function fetchAll() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Issue::class, null, [Issue::REQUEST_DATE, Issue::RETURN_DATE, Issue::RETURNED_DATE], null, null, null, "AI"), false);
        $select->from(['AI' => Issue::TABLE_NAME])
                ->join(['S' => Setup::TABLE_NAME], 'S.' . Setup::ASSET_ID . '=AI.' . Issue::ASSET_ID, ["ASSET_EDESC" => new Expression("INITCAP(S.ASSET_EDESC)")], "left")
                ->join(['E' => HrEmployees::TABLE_NAME], 'E.' . HrEmployees::EMPLOYEE_ID . '=AI.' . Issue::EMPLOYEE_ID, ["FIRST_NAME" => new Expression("INITCAP(E.FIRST_NAME)"), "MIDDLE_NAME" => new Expression("INITCAP(E.MIDDLE_NAME)"), "LAST_NAME" => new Expression("INITCAP(E.LAST_NAME)")], "left");

        $select->where(["AI." . Issue::STATUS . "='E'"]);
        $select->order("AI." . Issue::ISSUE_DATE . " DESC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function fetchAllById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Issue::class, null, [Issue::REQUEST_DATE,Issue::ISSUE_DATE, Issue::RETURN_DATE, Issue::RETURNED_DATE], null, null, null, "AI"), false);
        $select->from(['AI' => Issue::TABLE_NAME])
                ->join(['S' => Setup::TABLE_NAME], 'S.' . Setup::ASSET_ID . '=AI.' . Issue::ASSET_ID, ["ASSET_EDESC" => new Expression("INITCAP(S.ASSET_EDESC)")], "left")
                ->join(['E' => HrEmployees::TABLE_NAME], 'E.' . HrEmployees::EMPLOYEE_ID . '=AI.' . Issue::EMPLOYEE_ID, ["FIRST_NAME" => new Expression("INITCAP(E.FIRST_NAME)"), "MIDDLE_NAME" => new Expression("INITCAP(E.MIDDLE_NAME)"), "LAST_NAME" => new Expression("INITCAP(E.LAST_NAME)"), "FULL_NAME" => new Expression("INITCAP(E.FULL_NAME)")], "left");

        $select->where(["AI." . Issue::STATUS . "='E'"]);
        $select->where("(AI.RETURNED!='Y' OR AI.RETURNED IS NULL)");
        $select->where(["AI." . Issue::ASSET_ID . "=$id"]);
//        $select->order("S." . Setup::ASSET_EDESC);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function fetchById($id) {

        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Issue::class, null, [Issue::ISSUE_DATE, Issue::REQUEST_DATE, Issue::RETURN_DATE], null, null, null, "AI"), false);
        $select->from(['AI' => Issue::TABLE_NAME]);
        $select->where(["AI." . Issue::ISSUE_ID . "='" . $id . "'"]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    public function fetchallIssuableAsset() {

        $sql = "SELECT * FROM HRIS_ASSET_SETUP WHERE QUANTITY_BALANCE>0 AND STATUS='E' ";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        $list['A'] = [];
        $list['B'] = [];
        foreach ($result as $row) {
            $list['A'][$row['ASSET_ID']] = $row;
            $list['B'][$row['ASSET_ID']] = $row['ASSET_EDESC'];
        }
        return $list;
    }

    public function fetchAssetRemBalance($id) {
        $sql = "SELECT * FROM HRIS_ASSET_SETUP ";
        $sql .= "WHERE ASSET_ID='$id'";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result->current();
    }

    public function fetchAssetByEmployee($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Issue::class, null, null, null, null, null, "AI"), false);
        $select->from(['AI' => Issue::TABLE_NAME])
                ->join(['S' => Setup::TABLE_NAME], 'S.' . Setup::ASSET_ID . '=AI.' . Issue::ASSET_ID, ["ASSET_EDESC" => new Expression("INITCAP(S.ASSET_EDESC)")], "left")
                ->join(['AG' => Group::TABLE_NAME], 'AG.' . Group::ASSET_GROUP_ID . '=S.' . Setup::ASSET_GROUP_ID, ["ASSET_GROUP_EDESC" => new Expression("INITCAP(AG.ASSET_GROUP_EDESC)")], "left")
                ->join(['E' => HrEmployees::TABLE_NAME], 'E.' . HrEmployees::EMPLOYEE_ID . '=AI.' . Issue::EMPLOYEE_ID, ["FIRST_NAME" => new Expression("INITCAP(E.FIRST_NAME)"), "MIDDLE_NAME" => new Expression("INITCAP(E.MIDDLE_NAME)"), "LAST_NAME" => new Expression("INITCAP(E.LAST_NAME)")], "left");

        $select->where(["AI." . Issue::STATUS . "='E'"]);
        $select->where("(AI.RETURNED!='Y' OR AI.RETURNED IS NULL)");
        $select->where(["AI." . Issue::EMPLOYEE_ID . "=$id"]);
//        $select->order("S." . Setup::ASSET_EDESC);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $list = [];

        foreach ($result as $data) {
            array_push($list, $data);
        }
        return $list;
    }
    

    public function getFilteredRecord($data) {
        
        $employeeId = $data['employeeId'];
        $companyId = $data['companyId'];
        $branchId = $data['branchId'];
        $departmentId = $data['departmentId'];
        $designationId = $data['designationId'];
        $positionId = $data['positionId'];
        $serviceTypeId = $data['serviceTypeId'];
        $serviceEventTypeId = $data['serviceEventTypeId'];
        $assetTypeId=$data['assetTypeId'];
        $assetId=$data['assetId'];
        $assetStatusId=$data['assetStatusId'];
        $employeeTypeId = $data['employeeTypeId'];
         
        
        $sql="SELECT AI.ISSUE_ID       AS ISSUE_ID,
                  INITCAP(TO_CHAR(AI.ISSUE_DATE, 'DD-MON-YYYY'))          AS ISSUE_DATE,
                  AI.ASSET_ID            AS ASSET_ID,
                  AI.SNO                 AS SNO,
                  AI.EMPLOYEE_ID         AS EMPLOYEE_ID,
                  E.EMPLOYEE_CODE         AS EMPLOYEE_CODE,
                  AI.QUANTITY            AS QUANTITY,
                  INITCAP(TO_CHAR(AI.REQUEST_DATE, 'DD-MON-YYYY'))        AS REQUEST_DATE,
                  INITCAP(TO_CHAR(AI.RETURN_DATE, 'DD-MON-YYYY'))         AS RETURN_DATE,
                  AI.PURPOSE             AS PURPOSE,
                  AI.RETURNABLE          AS RETURNABLE,
                  AI.AUTHORIZED_BY       AS AUTHORIZED_BY,
                  AI.RETURNED            AS RETURNED,
                  INITCAP(TO_CHAR(AI.RETURNED_DATE, 'DD-MON-YYYY'))       AS RETURNED_DATE,
                  AI.REMARKS             AS REMARKS,
                  AI.COMPANY_ID          AS COMPANY_ID,
                  AI.BRANCH_ID           AS BRANCH_ID,
                  AI.CREATED_BY          AS CREATED_BY,
                  AI.CREATED_DATE        AS CREATED_DATE,
                  AI.MODIFIED_BY         AS MODIFIED_BY,
                  AI.MODIFIED_DATE       AS MODIFIED_DATE,
                  AI.APPROVED            AS APPROVED,
                  AI.APPROVED_BY         AS APPROVED_BY,
                  AI.APPROVED_DATE       AS APPROVED_DATE,
                  AI.STATUS              AS STATUS,
                  INITCAP(S.ASSET_EDESC) AS ASSET_EDESC,
                  INITCAP(E.FIRST_NAME)  AS FIRST_NAME,
                  INITCAP(E.MIDDLE_NAME) AS MIDDLE_NAME,
                  INITCAP(E.LAST_NAME)   AS LAST_NAME,
                  INITCAP(E.FULL_NAME)   AS FULL_NAME
                FROM HRIS_ASSET_ISSUE AI
                LEFT JOIN HRIS_ASSET_SETUP S
                ON S.ASSET_ID=AI.ASSET_ID
                LEFT JOIN HRIS_ASSET_GROUP AG
                ON AG.ASSET_GROUP_ID=S.ASSET_GROUP_ID
                LEFT JOIN HRIS_EMPLOYEES E
                ON E.EMPLOYEE_ID=AI.EMPLOYEE_ID
                WHERE AI.STATUS ='E' ";
        
        if ($assetTypeId != -1) {
            $sql .= "AND AG." . Group::ASSET_GROUP_ID . " =$assetTypeId ";
        }
        if ($assetId != -1) {
            $sql .= " AND S." . Setup::ASSET_ID . " =$assetId ";
        }
        if ($assetStatusId != -1) {
            if($assetStatusId=='NR'){
            $sql .= "AND AI.RETURNABLE='N' ";
            }else if($assetStatusId=='CUR'){
            $sql .= "AND AI.RETURNED IS NULL ";
            }
            else if($assetStatusId=='R'){
            $sql .= "AND AI.RETURNABLE='Y' AND AI.RETURNED IS NULL ";
            }else if($assetStatusId=='RED'){
            $sql .= "AND AI.RETURNED='Y' ";
            }
        }
        
        
        if ($employeeTypeId != null && $employeeTypeId != -1) {
            $sql .= "AND E.EMPLOYEE_TYPE='".$employeeTypeId."' ";
        }
        if ($employeeId != -1) {
            $sql .= "AND E." . HrEmployees::EMPLOYEE_ID . " = $employeeId";
        }
        
        if ($companyId != -1) {
            $sql .= " AND E." . HrEmployees::EMPLOYEE_ID . " IN (SELECT " . HrEmployees::EMPLOYEE_ID . " FROM " . HrEmployees::TABLE_NAME . " WHERE " . HrEmployees::COMPANY_ID . "= $companyId)";
        }

        if ($branchId != -1) {
            $sql .= " AND E." . HrEmployees::EMPLOYEE_ID . " IN (SELECT " . HrEmployees::EMPLOYEE_ID . " FROM " . HrEmployees::TABLE_NAME . " WHERE " . HrEmployees::BRANCH_ID . "= $branchId)";
        }
        if ($departmentId != -1) {
            $sql .= " AND E." . HrEmployees::EMPLOYEE_ID . " IN (SELECT " . HrEmployees::EMPLOYEE_ID . " FROM " . HrEmployees::TABLE_NAME . " WHERE " . HrEmployees::DEPARTMENT_ID . "= $departmentId)";
        }
        if ($designationId != -1) {
            $sql .= " AND E." . HrEmployees::EMPLOYEE_ID . " IN (SELECT " . HrEmployees::EMPLOYEE_ID . " FROM " . HrEmployees::TABLE_NAME . " WHERE " . HrEmployees::DESIGNATION_ID . "= $designationId)";
        }
        if ($positionId != -1) {
            $sql .= " AND E." . HrEmployees::EMPLOYEE_ID . " IN (SELECT " . HrEmployees::EMPLOYEE_ID . " FROM " . HrEmployees::TABLE_NAME . " WHERE " . HrEmployees::POSITION_ID . "= $positionId)";
        }
        if ($serviceTypeId != -1) {
            $sql .= " AND E." . HrEmployees::EMPLOYEE_ID . " IN (SELECT " . HrEmployees::EMPLOYEE_ID . " FROM " . HrEmployees::TABLE_NAME . " WHERE " . HrEmployees::SERVICE_TYPE_ID . "= $serviceTypeId)";
        }
        if ($serviceEventTypeId != -1) {
            $sql .= " AND E." . HrEmployees::EMPLOYEE_ID . " IN (SELECT " . HrEmployees::EMPLOYEE_ID . " FROM " . HrEmployees::TABLE_NAME . " WHERE " . HrEmployees::SERVICE_EVENT_TYPE_ID . "= $serviceEventTypeId)";
        }
        $sql .=" ORDER BY AI.ISSUE_DATE DESC";

        $statement = $this->adapter->query($sql);
        
        $result = $statement->execute();
        return $result;
    }

}
