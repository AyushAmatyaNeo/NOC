<?php

namespace Payroll\Controller;

use Application\Controller\HrisController;
use Application\Custom\CustomViewModel;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\FiscalYear;
use Application\Model\Months;
use Exception;
use Payroll\Repository\PayrollReportRepo;
use Payroll\Repository\RulesRepository;
use Payroll\Repository\SalarySheetRepo;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;

class PayrollReportController extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(PayrollReportRepo::class);

    }

    public function indexAction() {
        die();
        echo 'NO Action';
    }

    public function varianceAction() {
        /**
         * FISCAL YEARS
         * 
         * HRIS_FISCAL_YEARS
         * */
        $fiscalYears = EntityHelper::getTableList($this->adapter, FiscalYear::TABLE_NAME, [FiscalYear::FISCAL_YEAR_ID, FiscalYear::FISCAL_YEAR_NAME]);
        
        /**
         * MONTH CODE
         * 
         * HRIS_MONTH_CODE
         * */
        $months = EntityHelper::getTableList($this->adapter, Months::TABLE_NAME, [Months::MONTH_ID, Months::MONTH_EDESC, Months::FISCAL_YEAR_ID]);

        $columnsList = $this->repository->getVarianceColumns();

        return Helper::addFlashMessagesToArray($this, [
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
                    'fiscalYears' => $fiscalYears,
                    'months' => $months,
                    'columnsList' => $columnsList,
                    'preference' => $this->preference
        ]);
    }

    public function pullVarianceListAction() {
        try {
            $request = $this->getRequest();
            $data    = $request->getPost();


            $results = $this->repository->getVarianceReprot($data);


            $result = [];
            $result['success'] = true;
            $result['data'] = Helper::extractDbData($results);
            $result['error'] = "";
            return new CustomViewModel($result);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function gradeBasicAction() {
        $datas['otVariables'] = $this->repository->getGbVariables();
        $datas['monthList'] = $this->repository->getMonthList();

        return Helper::addFlashMessagesToArray($this, [
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
                    'datas' => $datas,
                    'preference' => $this->preference
        ]);
    }

    public function pullGradeBasicAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            $defaultColumnsList = $this->repository->getOtDefaultColumns();
            $reportType = $data['reportType'];
            if ($reportType == 'S') {
                $results = $this->repository->getGradeBasicSummary($data);
            } elseif ($reportType == 'D') {
                $results = $this->repository->getGradeBasicReport($data);
            } else {
                $results = $this->repository->getGradeBasicReport($data);
            }
            $result = [];
            $result['success'] = true;
            $result['data'] = Helper::extractDbData($results);
            $result['columns'] = $defaultColumnsList;
            $result['error'] = "";
            return new CustomViewModel($result);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function basicMonthlyReportAction() {
        $otVariables = $this->repository->getGbVariables();

        return Helper::addFlashMessagesToArray($this, [
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
                    'otVariables' => $otVariables,
                    'preference' => $this->preference
        ]);
    }

    public function basicMonthlyAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            $defaultColumnsList = $this->repository->getOtMonthlyDefaultColumns($data['fiscalId']);
            $results = $this->repository->getBasicMonthly($data, $defaultColumnsList);
            $result = [];
            $result['success'] = true;
            $result['data'] = Helper::extractDbData($results);
            $result['columns'] = $defaultColumnsList;
            $result['error'] = "";
            return new CustomViewModel($result);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function specialMonthlyReportAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $result = Helper::extractDbData($this->repository->getSpecialMonthly($data));
            return new JsonModel(['success' => true, 'data' => $result, 'message' => null]);
        }

        $otVariables = $this->repository->getGbVariables();

        return Helper::addFlashMessagesToArray($this, [
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
                    'otVariables' => $otVariables,
                    'preference' => $this->preference
        ]);
    }

    // menu for this action not inserted
    public function groupSheetAction() {

        /**
         * VARIANCE NAME
         * 
         * AS [Annual Social Security Tax, Income Tax 10%, Income Tax 20%]
         * */
        $nonDefaultList = $this->repository->getSalaryGroupColumns('S', 'N');

        /**
         * VARIANCE NAME AS
         * 
         * Total Deduction, Monthly Payable, Technical Grade
         * */
        $groupVariables = $this->repository->getSalaryGroupColumns('S');

        

        $salarySheetRepo = new SalarySheetRepo($this->adapter);

        /**
         * SALARY TYPE
         * --NORMAL--BONUS--LEAVE ENCASHMENT--TRANSPORTATION ALLOWANCE -- OVERTIME
         * DB - HRIS_SALARY_TYPE
         * */
        $salaryType = iterator_to_array($salarySheetRepo->fetchAllSalaryType(), false);

        /**
         * FETCHING ALL DATA FROM
         * DB -- HRIS_SALARY_SHEET
         * */
        $data['salarySheetList'] = iterator_to_array($salarySheetRepo->fetchAll(), false);
        $links['getGroupListLink'] = $this->url()->fromRoute('payrollReport', ['action' => 'getGroupList']);
       
        $data['links'] = $links;

        return Helper::addFlashMessagesToArray($this, [
            'searchValues' => EntityHelper::getSearchData($this->adapter),
            'salaryType' => $salaryType,
            'nonDefaultList' => $nonDefaultList,
            'groupVariables' => $groupVariables,
            'preference' => $this->preference,
            'data' => json_encode($data)
        ]);
    }

    public function pullGroupSheetAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            $resultData = [];
            $reportType = $data['reportType'];
            $groupVariable = $data['groupVariable'];


            if ($reportType == "GS") {
                $defaultColumnsList = $this->repository->getDefaultColumns('S');
                $resultData = $this->repository->getGroupReport('S', $data);
            } elseif ($reportType == "GD") {
                $defaultColumnsList = $this->repository->getVarianceDetailColumns($groupVariable);
                $resultData = $this->repository->getGroupDetailReport($data);
            }
            return new CustomViewModel($resultData);
            
            $monthDetails=EntityHelper::rawQueryResult($this->adapter, "SELECT MONTH_EDESC,YEAR FROM HRIS_MONTH_CODE WHERE MONTH_ID=?",['monthId'=>$data['monthId']])->current();
           
            $result = [];
            $result['success'] = true;
            $result['data'] = Helper::extractDbData($resultData);
            $result['columns'] = $defaultColumnsList;
            $result['monthDetails'] = $monthDetails;
            $result['error'] = "";
            return new CustomViewModel($result);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function employeeWiseGroupSheetAction() {
        $nonDefaultList = $this->repository->getSalaryGroupColumns('S', 'N');
        $groupVariables = $this->repository->getSalaryGroupColumns('S');

        $salarySheetRepo = new SalarySheetRepo($this->adapter);
        $salaryType = iterator_to_array($salarySheetRepo->fetchAllSalaryType(), false);

        $data['salarySheetList'] = iterator_to_array($salarySheetRepo->fetchAll(), false);
        $links['getGroupListLink'] = $this->url()->fromRoute('payrollReport', ['action' => 'getGroupList']);
        $data['links'] = $links;

        $fiscalYears = EntityHelper::getTableKVListWithSortOption($this->adapter, FiscalYear::TABLE_NAME,FiscalYear::FISCAL_YEAR_ID, [FiscalYear::FISCAL_YEAR_NAME], [FiscalYear::STATUS => 'E'], FiscalYear::FISCAL_YEAR_ID,  "DESC");
        return Helper::addFlashMessagesToArray($this, [
            'searchValues' => EntityHelper::getSearchData($this->adapter),
            'salaryType' => $salaryType,
            'fiscalYears' => $fiscalYears,
            'nonDefaultList' => $nonDefaultList,
            'groupVariables' => $groupVariables,
            'preference' => $this->preference,
            'data' => json_encode($data)
        ]);
    }

    public function pullemployeeWiseGroupSheetAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            $resultData = [];
            $groupVariable = $data['groupVariable'];

            $defaultColumnsList = $this->repository->getDefaultColumns('S');
            $resultData = $this->repository->getEmployeeWiseGroupReport('S', $data);
            

            // return new CustomViewModel($resultData);


            $result = [];
            $result['success'] = true;
            $result['data'] = Helper::extractDbData($resultData);
            $result['columns'] = $defaultColumnsList;
            $result['error'] = "";
            return new CustomViewModel($result);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function groupTaxReportAction() {
        $nonDefaultList = $this->repository->getSalaryGroupColumns('T', 'N');
        $groupVariables = $this->repository->getSalaryGroupColumns('T');

        $salarySheetRepo = new SalarySheetRepo($this->adapter);
        $salaryType = iterator_to_array($salarySheetRepo->fetchAllSalaryType(), false);

        return Helper::addFlashMessagesToArray($this, [
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
                    'salaryType' => $salaryType,
//                    'fiscalYears' => $fiscalYears,
//                    'months' => $months,
                    'nonDefaultList' => $nonDefaultList,
                    'groupVariables' => $groupVariables,
                    'preference' => $this->preference,
                    'acl' => $this->acl,
        ]);
    }

    public function pullGroupTaxReportAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            $resultData = [];
            $reportType = $data['reportType'];
            $groupVariable = $data['groupVariable'];

            if ($reportType == "GS") {
                $defaultColumnsList = $this->repository->getDefaultColumns('T');
                $resultData = $this->repository->getGroupReport('T', $data);
            } elseif ($reportType == "GD") {
                $defaultColumnsList = $this->repository->getVarianceDetailColumns($groupVariable);
                $resultData = $this->repository->getGroupDetailReport($data);
            }
            $result = [];
            $result['success'] = true;
            $result['data'] = Helper::extractDbData($resultData);
            $result['columns'] = $defaultColumnsList;
            $result['error'] = "";
            return new CustomViewModel($result);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function monthlySummaryAction() {
        $salarySheetRepo = new SalarySheetRepo($this->adapter);
        $salaryType = iterator_to_array($salarySheetRepo->fetchAllSalaryType(), false);
        return Helper::addFlashMessagesToArray($this, [
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
                    'salaryType' => $salaryType,
                    'preference' => $this->preference
        ]);
    }

    public function pullMonthlySummaryAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            $resultData = [];
            $resultData['additionDetail'] = $this->repository->fetchMonthlySummary('A', $data);
            $resultData['deductionDetail'] = $this->repository->fetchMonthlySummary('D', $data);

            $result = [];
            $result['success'] = true;
            $result['data'] = $resultData;
//            $result['columns'] = $defaultColumnsList;
            $result['error'] = "";
            return new CustomViewModel($result);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function departmentWiseAction() {
        $ruleRepo = new RulesRepository($this->adapter);
        $ruleList = iterator_to_array($ruleRepo->fetchAll(), false);

        $salarySheetRepo = new SalarySheetRepo($this->adapter);
        $salaryType = iterator_to_array($salarySheetRepo->fetchAllSalaryType(), false);

        return Helper::addFlashMessagesToArray($this, [
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
                    'preference' => $this->preference,
                    'ruleList' => $ruleList,
                    'salaryType' => $salaryType
        ]);
    }

    public function pulldepartmentWiseAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
//            $resultData = [];
            $resultData = $this->repository->pulldepartmentWise($data);
//            $resultData['deductionDetail'] = $this->repository->fetchMonthlySummary('D', $data);

            $result = [];
            $result['success'] = true;
            $result['data'] = $resultData;
//           $result['columns'] = $defaultColumnsList;
            $result['error'] = "";
            return new CustomViewModel($result);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function jvReportAction() {
        $ruleRepo = new RulesRepository($this->adapter);
        $ruleList = iterator_to_array($ruleRepo->fetchAll(), false);

        $salarySheetRepo = new SalarySheetRepo($this->adapter);
        $salaryType = iterator_to_array($salarySheetRepo->fetchAllSalaryType(), false);
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $data = $request->getPost();
                $resultData = $this->repository->getJvReport($data);
                return new JSONModel(['success' => true, 'data' => $resultData, 'error' => '']);
            } catch (Exception $e) {
                return new JSONModel(['success' => false, 'data' => [], 'error' => '']);
            }
        }
        return Helper::addFlashMessagesToArray($this, [
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
                    'preference' => $this->preference,
                    'ruleList' => $ruleList,
                    'salaryType' => $salaryType
        ]);
    }

    public function taxYearlyAction() {

        $incomes = $this->repository->gettaxYearlyByHeads('IN');
        $taxExcemptions = $this->repository->gettaxYearlyByHeads('TE');
        $otherTax = $this->repository->gettaxYearlyByHeads('OT');
        $miscellaneous = $this->repository->gettaxYearlyByHeads('MI');
        $bMiscellaneou = $this->repository->gettaxYearlyByHeads('BM');
        $cMiscellaneou = $this->repository->gettaxYearlyByHeads('CM');
        $sumOfExemption = $this->repository->gettaxYearlyByHeads('SE', 'sin');
        $sumOfOtherTax = $this->repository->gettaxYearlyByHeads('ST', 'sin');

        $salarySheetRepo = new SalarySheetRepo($this->adapter);
        $salaryType = iterator_to_array($salarySheetRepo->fetchAllSalaryType(), false);

        return Helper::addFlashMessagesToArray($this, [
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
                    'salaryType' => $salaryType,
                    'preference' => $this->preference,
                    'incomes' => $incomes,
                    'taxExcemptions' => $taxExcemptions,
                    'otherTax' => $otherTax,
                    'miscellaneous' => $miscellaneous,
                    'bMiscellaneou' => $bMiscellaneou,
                    'cMiscellaneou' => $cMiscellaneou,
                    'sumOfExemption' => $sumOfExemption,
                    'sumOfOtherTax' => $sumOfOtherTax
        ]);
    }

    public function pulltaxYearlyAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();

            $resultData = $this->repository->getTaxYearly($data);
            $result = [];
            $result['success'] = true;
            $result['data']['employees'] = Helper::extractDbData($resultData);
            $result['error'] = "";
            return new CustomViewModel($result);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function getGroupListAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $data = EntityHelper::getTableList($this->adapter, "HRIS_SALARY_SHEET_GROUP", ["GROUP_ID", "GROUP_NAME"]);
                return new JsonModel(['success' => true, 'data' => $data, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
    }

    public function pullGroupAction() {
        $salarySheetRepo = new SalarySheetRepo($this->adapter);
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            $group=$data['group'];
            $monthId=$data['monthId'];
            $salaryTypeId=$data['salaryTypeId'];

            $sheetList= $salarySheetRepo->fetchGeneratedSheetByGroup($monthId,$group,$salaryTypeId);

            return new JsonModel(['success' => true, 'sheetData' => $sheetList, 'message' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

    public function annualSalarySheetAction() {
        $nonDefaultList = $this->repository->getSalaryGroupColumns('N', 'N');
        $groupVariables = $this->repository->getSalaryGroupColumns('N');

        $salarySheetRepo = new SalarySheetRepo($this->adapter);
        $salaryType = iterator_to_array($salarySheetRepo->fetchAllSalaryType(), false);

        $data['salarySheetList'] = iterator_to_array($salarySheetRepo->fetchAll(), false);
        $links['getGroupListLink'] = $this->url()->fromRoute('payrollReport', ['action' => 'getGroupList']);
        $data['links'] = $links;

        return Helper::addFlashMessagesToArray($this, [
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
                    'salaryType' => $salaryType,
//                    'fiscalYears' => $fiscalYears,
//                    'months' => $months,
                    'nonDefaultList' => $nonDefaultList,
                    'groupVariables' => $groupVariables,
                    'preference' => $this->preference,
                    'data' => json_encode($data),
					'acl' => $this->acl,
                    'employeeDetail' => $this->storageData['employee_detail'],
        ]);
    }

    public function pullAnnualSalarySheetAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            // print_r($data);die;
            $resultData = [];
            $reportType = $data['reportType'];
            $groupVariable = $data['groupVariable'];
            $returnList = [];
            if ($reportType == "GS") {
                $defaultColumnsList = [];
                $resultData = $this->repository->getAnnualSheetReport('N', $data);
                $final = [];
                $employeeIdList = [];
                foreach ($resultData as $data){
                    if (!in_array($data['EMPLOYEE_ID'], $employeeIdList)){
                        array_push($employeeIdList,$data['EMPLOYEE_ID']);
                    }
                }
                
                foreach($employeeIdList as $eId){
                    $tempList = [];
                    $tempList['EMPLOYEE_ID'] = $eId;
                    foreach ($resultData as $data){
                        if($data['EMPLOYEE_ID'] == $eId){
                            $tempList['ID_ACCOUNT_NO'] = $data['ID_ACCOUNT_NO'];
                            $tempList['EMPLOYEE_CODE'] = $data['EMPLOYEE_CODE'];
                            $tempList['COMPANY_NAME'] = $data['COMPANY_NAME'];
                            $tempList['BANK_NAME'] = $data['BANK_NAME'];
                            $tempList['FULL_NAME'] = $data['FULL_NAME'];
                            $tempList[$data['PAY_EDESC']] = $data['AMOUNT'];
                        }
                    }
                    array_push($final,$tempList);
                }

                foreach ($final as $data){
                    $keys = array_keys($data);
                    foreach($keys as $k){
                        if ($k != 'EMPLOYEE_ID' && $k != 'ID_ACCOUNT_NO' && $k != 'EMPLOYEE_CODE' && $k != 'COMPANY_NAME' && $k != 'BANK_NAME' && $k != 'FULL_NAME')
                        if (!in_array(str_replace('/','_',str_replace(' ', '', $k)), $defaultColumnsList)){
                            $defaultColumnsList[str_replace('/','_',str_replace(' ', '', $k))]=$k;
                        }
                    }
                }
                foreach ($final as $data){
                    $keys = array_keys($data);
                    foreach($defaultColumnsList as $defaultColumn){
                        if (!in_array($defaultColumn, $keys)){
                            $data[$defaultColumn] = 0;
                        }
                    }
                    array_push($returnList,$data);
                }
                $tempFinal = [];
                foreach($returnList as $f){
                    $tempFinalRow = [];
                    $tempFinalRow['EMPLOYEE_ID'] = $f['EMPLOYEE_ID'];
                    $tempFinalRow['ID_ACCOUNT_NO'] = $f['ID_ACCOUNT_NO'];
                    $tempFinalRow['EMPLOYEE_CODE'] = $f['EMPLOYEE_CODE'];
                    $tempFinalRow['COMPANY_NAME'] = $f['COMPANY_NAME'];
                    $tempFinalRow['BANK_NAME'] = $f['BANK_NAME'];
                    $tempFinalRow['FULL_NAME'] = $f['FULL_NAME'];
                    foreach($defaultColumnsList as $k => $v){
                        $tempFinalRow[$k] = $f[$v];
                    }
                    array_push($tempFinal,$tempFinalRow);
                }
                

                $returnList = $tempFinal;
                unset($tempFinal);
            } elseif ($reportType == "GD") {
                $defaultColumnsList = $this->repository->getVarianceDetailColumns($groupVariable);
                $resultData = $this->repository->getGroupDetailReport($data);
            }
            
            // $monthDetails=EntityHelper::rawQueryResult($this->adapter, "SELECT MONTH_EDESC,YEAR FROM HRIS_MONTH_CODE WHERE MONTH_ID=:monthId",['monthId'=>$data['monthId']])->current();
            
            $result = [];
            $result['success'] = true;
            $result['data'] = Helper::extractDbData($returnList);
            $result['columns'] = $defaultColumnsList;
            // $result['monthDetails'] = $monthDetails;
            $result['error'] = "";
            return new CustomViewModel($result);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function tdsReportAction(){
        $nonDefaultList = $this->repository->getSalaryGroupColumns('N', 'N');
        $groupVariables = $this->repository->getSalaryGroupColumns('N');
        $salarySheetRepo = new SalarySheetRepo($this->adapter);
        $salaryType = iterator_to_array($salarySheetRepo->fetchAllSalaryType(), false);

        $payHeads = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_PAY_SETUP", "PAY_ID", ["PAY_EDESC"], ["PAY_CODE in ('11211', '11112')"], "PAY_ID", "ASC", "-");
        return Helper::addFlashMessagesToArray($this, [
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
                    'preference' => $this->preference,
					'acl' => $this->acl,
                    'employeeDetail' => $this->storageData['employee_detail'],
                    'payHeads' => $payHeads,
        ]);
    }

    public function pullTdsReportAction(){
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            
            $data = $this->repository->getTdsReport($data);
           
            return new CustomViewModel(['success' => true, 'data' => $data, 'error' => '']);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function pfReportAction(){
        return Helper::addFlashMessagesToArray($this, [
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
                    'preference' => $this->preference,
					'acl' => $this->acl,
                    'employeeDetail' => $this->storageData['employee_detail'],
        ]);

    }

    public function pullPfReportAction(){
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            // print_r($data);
            $data = $this->repository->getPfReport($data);
            // print_r($data);die;
            return new CustomViewModel(['success' => true, 'data' => $data, 'error' => '']);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function citReportAction()
    {
        return Helper::addFlashMessagesToArray($this, [
            'searchValues' => EntityHelper::getSearchData($this->adapter),
            'preference' => $this->preference,
            'acl' => $this->acl,
            'employeeDetail' => $this->storageData['employee_detail'],
        ]);
    }

    public function pullCitReportAction(){
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            // print_r($data);
            $data = $this->repository->getCitReport($data);
            // print_r($data);die;
            return new CustomViewModel(['success' => true, 'data' => $data, 'error' => '']);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function gradeSankhyaReportAction(){
        $nonDefaultList = $this->repository->getSalaryGroupColumns('N', 'N');
        $groupVariables = $this->repository->getSalaryGroupColumns('N');

        $salarySheetRepo = new SalarySheetRepo($this->adapter);
        $salaryType = iterator_to_array($salarySheetRepo->fetchAllSalaryType(), false);

        $payHeads = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_PAY_SETUP", "PAY_ID", ["PAY_EDESC"], ["PAY_CODE in ('11211', '11112')"], "PAY_ID", "ASC", "-");
        return Helper::addFlashMessagesToArray($this, [
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
                    'preference' => $this->preference,
					'acl' => $this->acl,
                    'employeeDetail' => $this->storageData['employee_detail'],
                    'payHeads' => $payHeads,
        ]);
    }

    public function pullGradeSankhyaAction(){
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            $data = $this->repository->getGradeSankhyaReport($data);
           
            return new CustomViewModel(['success' => true, 'data' => $data, 'error' => '']);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function finalReconcilationSheetAction() {
        $nonDefaultList = $this->repository->getSalaryGroupColumns('S', 'N');
        $groupVariables = $this->repository->getSalaryGroupColumns('S');

        $salarySheetRepo = new SalarySheetRepo($this->adapter);
        $salaryType = iterator_to_array($salarySheetRepo->fetchAllSalaryType(), false);

        $data['salarySheetList'] = iterator_to_array($salarySheetRepo->fetchAll(), false);
        $links['getGroupListLink'] = $this->url()->fromRoute('payrollReport', ['action' => 'getGroupList']);
        $data['links'] = $links;

        $fiscalYears = EntityHelper::getTableKVListWithSortOption($this->adapter, FiscalYear::TABLE_NAME,FiscalYear::FISCAL_YEAR_ID, [FiscalYear::FISCAL_YEAR_NAME], [FiscalYear::STATUS => 'E'], FiscalYear::FISCAL_YEAR_ID,  "DESC");
        return Helper::addFlashMessagesToArray($this, [
            'searchValues' => EntityHelper::getSearchData($this->adapter),
            'salaryType' => $salaryType,
            'fiscalYears' => $fiscalYears,
            'nonDefaultList' => $nonDefaultList,
            'groupVariables' => $groupVariables,
            'preference' => $this->preference,
            'data' => json_encode($data)
        ]);
    }

    public function pullFinalReconcilationSheetAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            $resultData = [];
            $groupVariable = $data['groupVariable'];
            $defaultColumnsList = $this->repository->getDefaultColumns('S');
            $resultData = $this->repository->getFinalReconcilationSheetReport($data);

            $result = [];
            $result['success'] = true;
            $result['data'] = Helper::extractDbData($resultData);
            $result['columns'] = $defaultColumnsList;
            $result['error'] = "";
            return new CustomViewModel($result);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function getEmployeeAdditionBreakDownAction(){
        try {
           $request = $this->getRequest();
           if (!$request->isPost()) {
               throw new Exception("The request should be of type post");
           }
           $postData = $request->getPost();
        //    print_r($postData);die;
           $data = $this->repository->getEmployeeAdditionBreakDown($postData['fiscalYear'],$postData['employeeId']);
           return new JsonModel(['success' => true, 'data' => $data, 'error' => '']);
       } catch (Exception $e) {
           return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
       }
   }

    public function getEmployeeSubDetailAction(){
        try {
            $request = $this->getRequest();
            if (!$request->isPost()) {
                throw new Exception("The request should be of type post");
            }
            $postData = $request->getPost();
            $data = $this->repository->getEmployeeSubDetail($postData['payId'],$postData['employeeId'], $postData['fiscalYear']);
            return new JsonModel(['success' => true, 'data' => $data, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

}
