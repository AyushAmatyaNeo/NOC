<?php

namespace SelfService\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\Helper;
use Exception;
use Training\Repository\TrainingAssignRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;

class TrainingList extends AbstractActionController {

    private $form;
    private $adapter;
    private $trainingAssignRepo;
    private $employeeId;

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        $this->adapter = $adapter;
        $this->trainingAssignRepo = new TrainingAssignRepository($this->adapter);
        $this->storageData = $storage->read();
        $this->employeeId = $this->storageData['employee_id'];
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $result = $this->trainingAssignRepo->getAllTrainingList($this->employeeId);
                $list = [];
                $getValue = function($trainingTypeId) {
                    if ($trainingTypeId == 'CC') {
                        return 'Company Contribution';
                    } else if ($trainingTypeId == 'CP') {
                        return 'Company Personal';
                    }
                };
                foreach ($result as $row) {
                    $row['TRAINING_TYPE'] = $getValue($row['TRAINING_TYPE']);
                    array_push($list, $row);
                }
                return new CustomViewModel(['success' => true, 'data' => $list, 'error' => '']);
            } catch (Exception $e) {
                return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        return Helper::addFlashMessagesToArray($this, []);
    }

    public function viewAction() {
        $employeeId = (int) $this->params()->fromRoute("employeeId");
        $trainingId = (int) $this->params()->fromRoute("trainingId");

        if (!$employeeId && !$trainingId) {
            return $this->redirect()->toRoute('trainingList');
        }

        $detail = $this->trainingAssignRepo->getDetailByEmployeeID($employeeId, $trainingId)[0];
        return Helper::addFlashMessagesToArray($this, ['detail' => $detail]);
    }

    public function acceptAction() {
        $employeeId = (int) $this->params()->fromRoute("employeeId");
        $trainingId = (int) $this->params()->fromRoute("trainingId");
        $this->trainingAssignRepo->updateAcceptFlag($employeeId, $trainingId, 'Y');
        $this->flashmessenger()->addMessage("Training Accepted");
        return $this->redirect()->toRoute('trainingList');
    }

    public function declineAction() {
        $employeeId = (int) $this->params()->fromRoute("employeeId");
        $trainingId = (int) $this->params()->fromRoute("trainingId");
        $this->trainingAssignRepo->updateAcceptFlag($employeeId, $trainingId, 'N');
        $this->flashmessenger()->addMessage("Training Declined");
        return $this->redirect()->toRoute('trainingList');
    }

}
