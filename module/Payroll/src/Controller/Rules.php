<?php

namespace Payroll\Controller;

use Application\Controller\HrisController;
use Application\Helper\Helper;
use Exception;
use Payroll\Form\Rules as RuleForm;
use Payroll\Model\Rules as RulesModel;
use Payroll\Model\SpecialRules as SpecialRulesModel;
use Payroll\Repository\FlatValueRepository;
use Payroll\Repository\MonthlyValueRepository;
use Payroll\Repository\RulesRepository;
use Payroll\Repository\SpecialRulesRepo;
use Payroll\Service\PayrollGenerator;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;

class Rules extends HrisController {
protected $adapter;
    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->adapter = $adapter;
        $this->initializeRepository(RulesRepository::class);
        $this->initializeForm(RuleForm::class);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
//                $data = $request->getPost();
                $rawList = $this->repository->fetchAll();
               
                $list = Helper::extractDbData($rawList);
                return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }

        return $this->stickFlashMessagesTo(['acl' => $this->acl]);
    }

    public function addAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $ruleModel = new RulesModel();
                $ruleModel->exchangeArrayFromForm($this->form->getData());
                $ruleModel->payId = ((int) Helper::getMaxId($this->adapter, RulesModel::TABLE_NAME, RulesModel::PAY_ID)) + 1;
                $ruleModel->createdDt = Helper::getcurrentExpressionDate();
                $ruleModel->createdBy = $this->employeeId;
                $ruleModel->status = 'E';

                $this->repository->add($ruleModel);

                $this->flashmessenger()->addMessage("Rule successfully added.");
                return $this->redirect()->toRoute("rules");
            }
        }
        $formulaData['monthlyValueList'] = $this->getMonthlyValues();
        $formulaData['flatValueList'] = $this->getFlatValues();
        $formulaData['variableList'] = PayrollGenerator::VARIABLES;
        $formulaData['systemRuleList'] = PayrollGenerator::SYSTEM_RULE;
        $formulaData['referencingRuleList'] = $this->getReferencingRules();
        $formulaData['referencingRuleListOthers'] = $this->getReferencingRules();

        return [
            'form' => $this->form,
            'customRenderer' => Helper::renderCustomView(),
            'formulaData' => json_encode($formulaData),
        ];
    }

    private function getMonthlyValues() {
        $monthlyValueRepo = new MonthlyValueRepository($this->adapter);
        $monthlyValueList = $monthlyValueRepo->fetchAll();
        return Helper::extractDbData($monthlyValueList);
    }

    private function getFlatValues() {
        $flatValueRepo = new FlatValueRepository($this->adapter);
        $flatValueList = $flatValueRepo->fetchAll();
        return Helper::extractDbData($flatValueList);
    }

    private function getReferencingRules($payId = null) {
        $referencingruleList = $this->repository->fetchReferencingRules($payId);
        return Helper::extractDbData($referencingruleList);
    }

    public function editAction() {
        $request = $this->getRequest();
        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute('rules');
        }
        $ruleModel = new RulesModel();
        $specialRuleRepo = new SpecialRulesRepo($this->adapter);
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $ruleModel->exchangeArrayFromForm($this->form->getData());
                if($_POST['salaryType'] != 1){
                    $specialRuleModel = new SpecialRulesModel();
                    $specialRuleModel->formula = $ruleModel->formula;
                    $specialRuleModel->flag = $_POST['flag'];
                    $specialRuleExists = Helper::extractDbData($specialRuleRepo->checkSpecialRuleExists($_POST['salaryType'], $id))[0]['RECORD_EXISTS'];
                    if($specialRuleExists == 'N'){
                        $specialRuleModel->payId = $id;
                        $specialRuleModel->status = 'E';
                        $specialRuleModel->salaryTypeId = $_POST['salaryType'];
                        $specialRuleModel->createdBy = $this->employeeId;
                        $specialRuleRepo->add($specialRuleModel);
                    }
                    else{
                        $specialRuleModel->modifiedDt = Helper::getcurrentExpressionDate();
                        $specialRuleModel->modifiedBy = $this->employeeId;
                        $specialRuleRepo->update($specialRuleModel, $id, $_POST['salaryType']);
                    }
                    $this->flashmessenger()->addMessage("Rule successfully edited.");
                    return $this->redirect()->toRoute("rules");
                }
                
                $ruleModel->modifiedDt = Helper::getcurrentExpressionDate();
                $ruleModel->modifiedBy = $this->employeeId;
                $this->repository->edit($ruleModel, $id);
                $this->flashmessenger()->addMessage("Rule successfully edited.");
                return $this->redirect()->toRoute("rules");
            }
        }
        $ruleModel->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
        $this->form->bind($ruleModel);
        $formulaData['monthlyValueList'] = $this->getMonthlyValues();
        $formulaData['flatValueList'] = $this->getFlatValues();
        $formulaData['variableList'] = PayrollGenerator::VARIABLES;
        $formulaData['systemRuleList'] = PayrollGenerator::SYSTEM_RULE;
        $formulaData['referencingRuleList'] = $this->getReferencingRules($id);
        $formulaData['referencingRuleListOthers'] = $this->getReferencingRules();
        
        $salaryTypes = Helper::extractDbData($specialRuleRepo->fetchSalaryTypes());
        $specialRules = Helper::extractDbData($specialRuleRepo->fetchSpecialRules($id));
        
        return [
            'id' => $id,
            'salaryTypes' => $salaryTypes,
            'specialRules' => $specialRules,
            'form' => $this->form,
            'customRenderer' => Helper::renderCustomView(),
            'formulaData' => json_encode($formulaData),
        ];
    }

    public function deleteAction() {
        $request = $this->getRequest();
        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute('rules');
        }
        $this->repository->delete($id);

        $this->flashmessenger()->addMessage("Rule successfully deleted.");
        return $this->redirect()->toRoute("rules");
    }

}
