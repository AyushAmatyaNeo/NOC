<?php

namespace Setup\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class EmployeeFile implements RepositoryInterface {

    private $tableGateway;
    private $fileTypeTableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway('HRIS_EMPLOYEE_FILE', $adapter);
        $this->fileTypeTableGateway = new TableGateway('HRIS_FILE_TYPE', $adapter);
        $this->adapter=$adapter;
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        unset($array['CREATED_DT']);
        unset($array['FILE_CODE']);
        $this->tableGateway->update($array, ["FILE_CODE" => $id]);
    }

    public function fetchAll() {
        return $this->tableGateway->select();
    }
    public function fetchById($id) {
        $rowset = $this->tableGateway->select(function (Select $select) use ($id) {
            $select->where([\Setup\Model\EmployeeFile::FILE_CODE => $id]);
            $select->order('CREATED_DT DESC')->limit(1);
        });
        return $rowset->current();
    }
    public function fetchByEmpId($id) {
//        $rowsetRaw = $this->tableGateway->select(['EMPLOYEE_ID' => $id]);
         $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(['FILE_CODE',
            'EMPLOYEE_ID',
            'FILETYPE_CODE',
            'FILE_PATH']);
        $select->from(['EF' => "HRIS_EMPLOYEE_FILE"])
                ->join(['FT' => "HRIS_FILE_TYPE"], 'EF.FILETYPE_CODE=FT.FILETYPE_CODE', ["FILE_TYPE" => new Expression('INITCAP(FT.NAME)')], "left")
                ->join(['FS' => "HRIS_EMPLOYEE_FILE_SETUP"], 'EF.FILE_ID=FS.FILE_ID', ["FILE_NAME" => new Expression('INITCAP(FS.FILE_NAME)')], "left");;
        $select->where(["EF.STATUS='E'"]);
        $select->where(['EMPLOYEE_ID' => $id]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return \Application\Helper\Helper::extractDbData($result);
    }
    public function delete($id) {
        $this->tableGateway->delete(['FILE_CODE' => $id]);
    }
    public function fetchAllFileType() {
        return $this->fileTypeTableGateway->select(['STATUS' => 'E']);
    }

}
