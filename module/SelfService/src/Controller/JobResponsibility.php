<?php

namespace SelfService\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use SelfService\Repository\LeaveRepository;
use SelfService\Repository\JobResponsibilityRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use JobResponsibilityManagement\Model\JobResponsibility as JRModel;
use JobResponsibilityManagement\Form\JobResponsibilityForm;

class JobResponsibility extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(JobResponsibilityRepository::class);
        $this->initializeForm(JobResponsibilityForm::class);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $result = $this->repository->fetchAllEmp($this->employeeId);
                $jobResponsibilityAssignList = Helper::extractDbData($result);

                foreach($jobResponsibilityAssignList as $key=>$jrl){
                    $jobResponsibilityAssignList[$key]['JOB_RES_NEP_NAME'] = base64_decode($jobResponsibilityAssignList[$key]['JOB_RES_NEP_NAME']);
                }
                return new JsonModel(['success' => true, 'data' => $jobResponsibilityAssignList, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        return $this->stickFlashMessagesTo(['acl' => $this->acl]);
    }

    public function viewAction() {
        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute('jobResponsibility');
        }
        $model = new JRModel();
        // print_r($id);die;
        $fetchData = $this->repository->fetchById($id);
        $model->exchangeArrayFromDB($fetchData[0]);
        $model->jobResNepName = base64_decode($model->jobResNepName);
        $model->jobResNepDescription = base64_decode($model->jobResNepDescription);
        $this->form->bind($model);
        // echo('<pre>');print_r($model);die;
        return [
            'form' => $this->form,
            'id' => $id,
            'customRender' => Helper::renderCustomView(),
        ];
    }

}
