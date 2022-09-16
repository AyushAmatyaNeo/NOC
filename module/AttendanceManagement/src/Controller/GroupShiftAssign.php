<?php
namespace AttendanceManagement\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\HrisQuery;
use Exception;
use Setup\Model\ShiftGroup;
use AttendanceManagement\Repository\GroupShiftAssignRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\View\Model\JsonModel;

class GroupShiftAssign extends HrisController {
    protected $adapter;
    
    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        $this->adapter = $adapter;
        parent::__construct($adapter, $storage);
    }

    public function indexAction() {
        $shiftGroupList = HrisQuery::singleton()
            ->setAdapter($this->adapter)
            ->setTableName(ShiftGroup::TABLE_NAME)
            ->setColumnList([ShiftGroup::CASE_ID, ShiftGroup::CASE_NAME])
            ->setWhere([ShiftGroup::STATUS => 'E'])
            ->setOrder([ShiftGroup::CASE_NAME => Select::ORDER_ASCENDING])
            ->setKeyValue(ShiftGroup::CASE_ID, ShiftGroup::CASE_NAME)
            ->result();
        $config = [
            'name' => 'caseId',
            'id' => 'caseId',
            'class' => 'form-control reset-field',
            'label' => 'Shift Group'
        ];
        $shiftGroupList = $this->getSelectElement($config, $shiftGroupList);

        return [
            'ShiftGroupFormElement' => $shiftGroupList,
            'acl' => $this->acl,
            'employeeDetail' => $this->storageData['employee_detail']
        ];
    }

    public function pullEmployeeGroupShiftAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            $caseRepo = new GroupShiftAssignRepository($this->adapter);
            $temp = $caseRepo->filter($data['branchId'], $data['departmentId'], $data['genderId'], $data['designationId'], $data['serviceTypeId'], $data['employeeId'], $data['companyId'], $data['positionId'], $data['employeeTypeId'], $data['caseId']);
            $list = [];
            foreach ($temp as $item) {
                $item["CASE_ID"] = $item["CASE_ID"];
                $item["CASE_NAME"] = $item["CASE_NAME"];
                array_push($list, $item);
            }
            return new JsonModel([
                "success" => "true",
                "data" => $list
            ]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

    public function pushEmployeeGroupShiftAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();

            $caseRepo = new GroupShiftAssignRepository($this->adapter);
            $caseRepo->insertOrUpdate($data['employeeId'], $data['caseId'], $data['action']);

            return new JsonModel(["success" => "true", "data" => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }
}
