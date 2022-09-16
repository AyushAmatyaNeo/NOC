<?php

namespace Overtime\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\HrisQuery;
use Overtime\Repository\OvertimeBulkSetupRepository;
use SelfService\Form\OvertimeRequestForm;
use SelfService\Model\Overtime;
use SelfService\Model\OvertimeDetail;
use SelfService\Repository\OvertimeDetailRepository;
use SelfService\Repository\OvertimeRepository;
use TheSeer\Tokenizer\Exception;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;


class OvertimeBulkSetup extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(OvertimeBulkSetupRepository::class);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $data = $request->getPost();
                $rawList = $this->repository->getEmployeeList($data['data']);
                $list = Helper::extractDbData($rawList);
                return new JsonModel([
                    "success" => true,
                    "data" => $list,
                    "message" => null,
                ]);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
            }
        }

        return $this->stickFlashMessagesTo([
            'searchValues' => EntityHelper::getSearchData($this->adapter),
            'acl' => $this->acl,
            'employeeDetail' => $this->storageData['employee_detail']
        ]);
    }

    public function assignAction() {

        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            $empList=$data['data'];

            foreach($empList as $list){
                $employeeId=$list['employeeId'];
                $updateData = array();
                $updateData['isChecked'] = $list['isChecked'];
                $updateData['wohFlag'] = $list['wohFlag'];
                $updateData['overtimeEligible'] = $list['overtimeEligible'];
                $updateData['updateValue'] = $list['updateValue'];

//                $this->repository->makeNull($employeeId);

                if($updateData['isChecked'] == 'true'){
                    $this->repository->updateOvertime($employeeId, $updateData);
                }
            }

            return new JsonModel([
                "success" => true,
//                    "data" => $list,
                "message" => null,
            ]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

}
