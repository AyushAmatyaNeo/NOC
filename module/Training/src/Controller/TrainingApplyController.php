<?php
namespace Training\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use SelfService\Form\TrainingRequestForm;
use Setup\Model\HrEmployees;
use Training\Repository\TrainingStatusRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;

class TrainingApplyController extends HrisController {
    
      public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(TrainingStatusRepository::class);
        $this->initializeForm(TrainingRequestForm::class);
    }
    
    public function indexAction() {
        die();
    }

    
    public function addAction(){
         
         return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    // 'advance' => EntityHelper::getTableList($this->adapter, AdvanceSetupModel::TABLE_NAME, ['*'], [AdvanceSetupModel::STATUS => 'E']),
                    'customRenderer' => Helper::renderCustomView(),
                    'employeeList' => EntityHelper::getTableList($this->adapter, HrEmployees::TABLE_NAME, [HrEmployees::EMPLOYEE_ID, HrEmployees::FULL_NAME], [HrEmployees::STATUS => "E", HrEmployees::RETIRED_FLAG => "N"]),
        ]);
    }
    
    public function pullEmployeeTrainingAction(){
        try {
            $request = $this->getRequest();
            $employeeId = $request->getPost('employeeId');
            $advanceList = $this->repository->fetchEmployeeTraining($employeeId);
            return new JsonModel(['success' => true, 'data' => $advanceList, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

}
