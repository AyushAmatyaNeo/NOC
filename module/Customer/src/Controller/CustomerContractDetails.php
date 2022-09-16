<?php

namespace Customer\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use AttendanceManagement\Model\ShiftSetup;
use Customer\Model\CustomerContractDetailModel;
use Customer\Model\DutyTypeModel;
use Customer\Repository\CustomerContractDetailRepo;
use Customer\Repository\CustomerContractRepo;
use Setup\Model\Designation;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;

class CustomerContractDetails extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(CustomerContractDetailRepo::class);
    }

    public function indexAction() {
        
    }

    public function viewAction() {
        $id = (int) $this->params()->fromRoute("id", -1);
        if ($id === -1) {
            return $this->redirect()->toRoute('customer-contract');
        }

        $contractDetail = $this->repository->fetchAllContractDetailByContractId($id);
//        echo '<Pre>';
//        print_r($contractDetail);
//        die();

        return Helper::addFlashMessagesToArray($this, [
                    'acl' => $this->acl,
                    "id" => $id,
                    'contractDetail' => $contractDetail,
                    'designationList' => EntityHelper::getTableList($this->adapter, Designation::TABLE_NAME, [Designation::DESIGNATION_ID, Designation::DESIGNATION_TITLE], [Designation::STATUS => "E"]),
                    'dutyTypeList' => EntityHelper::getTableList($this->adapter, DutyTypeModel::TABLE_NAME, [DutyTypeModel::DUTY_TYPE_ID, DutyTypeModel::DUTY_TYPE_NAME], [DutyTypeModel::STATUS => "E"]),
        ]);
    }

    public function addAction() {
        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute("customer-contract");
        }
        $request = $this->getRequest();

        if ($request->isPost()) {
            $postData = $request->getPost();

            $designation = $request->getPost('designation');
            $dutyType = $request->getPost('dutyType');
            $rate = $request->getPost('rate');

            if ($designation && $dutyType && $rate) {
                $contractDetailModel = new CustomerContractDetailModel();
                $i = 0;
                foreach ($designation as $designationDetails) {
                    if ($designationDetails > 0) {
                        $contractDetailModel->rate = $rate[$i];
                        $this->repository->editWithCondition($contractDetailModel, [
                            $contractDetailModel::DESIGNATION_ID => $designationDetails,
                            $contractDetailModel::DUTY_TYPE_ID => $dutyType[$i],
                            $contractDetailModel::CONTRACT_ID => $id,
                        ]);
                    }
                    $i++;
                }
            }
            $this->flashmessenger()->addMessage("Contract Details Sucessfully Updated");
            $this->redirect()->toRoute("customer-contract");
        }
    }

}
