<?php

namespace SelfService\Controller;

use Application\Controller\HrisController;
use Application\Custom\CustomViewModel;
use Application\Helper\Helper;
use Exception;
use SelfService\Model\TrainingFeedback as TrainingFeedbackModel;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use  SelfService\Form\TrainingFeedbackForm;
use  SelfService\Repository\TrainingFeedbackRepository; 
use  Training\Repository\TrainingAssignRepository; 
class TrainingFeedback extends HrisController {

    protected $adapter;
    protected $trainingAssignRepo;
    protected $employeeId;

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        $this->adapter = $adapter;
        $this->trainingAssignRepo = new TrainingAssignRepository($this->adapter);
        $this->storageData = $storage->read();
        $this->employeeId = $this->storageData['employee_id'];
        $this->initializeForm(TrainingFeedbackForm::class);
        $this->initializeRepository(TrainingFeedbackRepository::class);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $result = $this->trainingAssignRepo->getAllTrainingListAccepted($this->employeeId);
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

        $detail = $this->trainingAssignRepo->getDetailByEmployeeID($employeeId, $trainingId);
        return Helper::addFlashMessagesToArray($this, ['detail' => $detail]);
    }

    public function feedbackAction() {
        $employeeId = (int) $this->params()->fromRoute("employeeId");
        $trainingId = (int) $this->params()->fromRoute("trainingId");

        if (!$employeeId && !$trainingId) {
            return $this->redirect()->toRoute('trainingFeedback');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $data = $request->getPost();
                $trainingFeedbackModel = new TrainingFeedbackModel();
                
                $trainingFeedbackModel->feedbackId = (int) Helper::getMaxId($this->adapter, TrainingFeedbackModel::TABLE_NAME, TrainingFeedbackModel::FEEDBACK_ID) + 1;
                $trainingFeedbackModel->employeeId = $employeeId;
                $trainingFeedbackModel->trainingId = $trainingId;
                $trainingFeedbackModel->trainingFeedback = $data['trainingFeedback'];
                $trainingFeedbackModel->status = 'E';

                $this->repository->add($trainingFeedbackModel);
                
                $this->flashmessenger()->addMessage("Training Feedback Submitted!!!");
                return $this->redirect()->toRoute('trainingFeedback');
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }

        $detail = $this->trainingAssignRepo->getDetailByEmployeeID($employeeId, $trainingId);
        return Helper::addFlashMessagesToArray($this, ['detail' => $detail, 'form' => $this->form, 'employeeId' => $employeeId, 'trainingId' => $trainingId]);
    }


}
