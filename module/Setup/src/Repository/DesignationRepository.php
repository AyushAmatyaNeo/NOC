<?php
namespace Setup\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\HrisRepository;
use Application\Repository\RepositoryInterface;
use Setup\Model\Company;
use Setup\Model\Designation;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

class DesignationRepository extends HrisRepository implements RepositoryInterface {

    public function __construct(AdapterInterface $adapter) {
        parent::__construct($adapter, Designation::TABLE_NAME);
    }

    public function fetchAll() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Designation::class, [Designation::DESIGNATION_TITLE], NULL, NULL, NULL, NULL, 'D1'), false);
        $companyIdKey = Designation::COMPANY_ID;
        $companyNameKey = Company::COMPANY_NAME;
        $select->from(["D1" => Designation::TABLE_NAME])
            ->join(["D2" => Designation::TABLE_NAME], 'D1.PARENT_DESIGNATION=D2.DESIGNATION_ID', ["PARENT_DESIGNATION_TITLE" => new Expression('(D2.DESIGNATION_TITLE)')], "left")
            ->join(["C" => Company::TABLE_NAME], "C.{$companyIdKey}=D1.{$companyIdKey}", [Company::COMPANY_NAME => new Expression("(C.{$companyNameKey})")], "left")
        ;
        $select->where(["D1.STATUS= 'E'"]);
        $select->order(["D1." . Designation::DESIGNATION_TITLE => Select::ORDER_ASCENDING, "C.{$companyNameKey}" => Select::ORDER_ASCENDING]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function fetchById($id) {
        $rowset = $this->tableGateway->select(function(Select $select)use($id) {
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Designation::class, [Designation::DESIGNATION_TITLE]), false);
            $select->where([Designation::DESIGNATION_ID => $id, Designation::STATUS => 'E']);
        });
        return $rowset->current();
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [Designation::DESIGNATION_ID => $id]);
    }

    public function delete($id) {
        $this->tableGateway->update([Designation::STATUS => 'D'], ["DESIGNATION_ID" => $id]);
    }

    public function fetchAllDesignationCompanyWise() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([Designation::DESIGNATION_ID, Designation::DESIGNATION_TITLE]);
        $select->from(['D' => Designation::TABLE_NAME]);
        $select->join(['C' => Company::TABLE_NAME], "C." . Company::COMPANY_ID . "=D." . Designation::COMPANY_ID, array(Company::COMPANY_ID, 'COMPANY_NAME' => new Expression('(C.' . Company::COMPANY_NAME . ')')), 'inner');
        $select->where(["C.STATUS='E'"]);
        $select->where(["D.STATUS='E'"]);
        $select->order("D." . Designation::DESIGNATION_TITLE . " ASC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $list = [];
        foreach ($result as $row) {
            array_push($list, $row);
        }
        $designationList = [];
        foreach ($list as $val) {
            $newKey = $val['COMPANY_ID'];
            $designationList[$newKey][] = $val;
        }

        return $designationList;
    }
}
