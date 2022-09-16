<?php

namespace PayrollApi\Controller;

use Application\Controller\HrisController;
use Application\Custom\CustomViewModel;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\FiscalYear;
use Application\Model\Months;
use Payroll\Form\MonthlyValue as MonthlyValueForm;
use Payroll\Repository\MonthlyValueRepository;
use PayrollApi\Service\PayrollApiConfig;
use Exception;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;

class PayrollApi extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(MonthlyValueRepository::class);
        $this->initializeForm(MonthlyValueForm::class);
    }

    public function getToken() {
        $sql = "SELECT TOKEN  FROM HRIS_PAYROLL_API_TOKEN";
        $tokenData = EntityHelper::rawQueryResult($this->adapter, $sql)->current();
        return $tokenData['TOKEN'];
    }

    public function indexAction() {
        $tokenData = $this->getToken();
        if (!isset($tokenData)) {
            $this->refreshNewToken();
        };
        $this->redirect()->toRoute('payroll-api', ['action' => 'PostSalary']);
    }

    public function loginAction() {
        
    }

    public function PostSalaryAction() {
        $refreshPage = 'N';
        
        $tokenData = $this->getToken();
        if (!isset($tokenData)) {
        $refreshPage = 'Y';
        };

        $request = $this->getRequest();
        if ($request->isPost()) {

            $postData = $request->getPost();
            $fiscalYear = $request->getPost('fiscalYear');
            $monthId = $request->getPost('monthId');

            $monthWiseDetail = $this->getMonthWiseSalaryShett($monthId);

            $postDataFormat = array();
			
			$postDataFormat['year']='';
            $postDataFormat['month']='';
            $postDataFormat['edate']='';
            $postDataFormat['payroll'] = array();
            $loopCounter = 0;
            foreach ($monthWiseDetail as $salaryData) {

                if ($loopCounter == 0) {
                    $postDataFormat['year'] = $salaryData['YEAR'];
                    $postDataFormat['month'] = $salaryData['MONTH_NO'];
					$postDataFormat['edate'] = $salaryData['FROM_DATE'];
                }

                $tempData = array();
                $tempData['empid'] = $salaryData['EMPLOYEE_ID'];
                $tempData['salhead'] = array();

                foreach (PayrollApiConfig::PAY_HEADS as $value) {
                    array_push($tempData['salhead'], array('headCode' => $value, 'amount' => $salaryData[$value]));
                }

                array_push($postDataFormat['payroll'], $tempData);
                $loopCounter++;
            };

            $curl = curl_init();
//
            $bodyData = json_encode($postDataFormat);
			
			//echo '<pre>';
			//print_r($bodyData);
			//die();
			
			

            $token = $this->getToken();

            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => PayrollApiConfig::POST_SALARY_URL,
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => $bodyData,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $token,
                    'Content-Length: ' . strlen($bodyData)
                )
            ));
            $response = json_decode(curl_exec($curl));
            curl_close($curl);
			
			
			
			//echo '<pre>';
			//print_r($response);
			//die();
			
			



            if ($response->status == 'true') {
                $this->flashmessenger()->addMessage("Procedure Sucessfully Completed");
            } else {
				
                $refreshPage = 'Y';
                $this->flashmessenger()->addMessage($response->errdesc);
            }
			
			
        }



        $fiscalYears = EntityHelper::getTableList($this->adapter, FiscalYear::TABLE_NAME, [FiscalYear::FISCAL_YEAR_ID, FiscalYear::FISCAL_YEAR_NAME]);
        $months = EntityHelper::getTableList($this->adapter, Months::TABLE_NAME, [Months::MONTH_ID, Months::MONTH_EDESC, Months::FISCAL_YEAR_ID]);
        return Helper::addFlashMessagesToArray($this, [
                    'fiscalYears' => $fiscalYears,
                    'months' => $months,
                    'refreshPage' => $refreshPage
        ]);
    }

    private function getMonthWiseSalaryShett($month_id) {
        $sql ="SELECT * FROM (select hs.YEAR,lpad(hsc.MONTH_NO, 2, '0') as MONTH_NO ,to_number(he.EMPLOYEE_CODE) as EMPLOYEE_ID,
		hsp.PAY_ID,to_char(hsc.from_date,'YYYY-MM-DD') as from_date,
             case when hsd.VAL  is null then 0 else round(hsd.VAL,2) end as VAL 
            from HRIS_SALARY_SHEET hs join hris_salary_sheet_detail hsd on hs.SHEET_NO=hsd.SHEET_NO 
             join hris_pay_setup hsp on hsp.PAY_ID=hsd.PAY_ID
             join HRIS_MONTH_CODE hsc on hsc.MONTH_ID=hs.MONTH_ID
             join hris_employees he on 
             he.EMPLOYEE_ID=hsd.EMPLOYEE_ID where he.status='E' and he.retired_flag='N' and 
             hs.MONTH_ID= {$month_id} 
             and hsd.PAY_ID in (1,4,5,6,8,9,13,14,50,51)
             )
             PIVOT  (MAX(VAL)  FOR (pay_id) IN (1,4,5,6,8,9,13,14,50,51))";
			 
			 //echo $sql;
			// die();
			
			 
	

//        $sql = " SELECT * FROM (select hs.YEAR,hsc.MONTH_NO,he.EMPLOYEE_ID,he.EMPLOYEE_CODE,hsp.PAY_ID,
//             --hsp.PAY_EDESC,
//             hsd.VAL
//            from LLBS_PAYROLL.HRIS_SALARY_SHEET hs join 
//            LLBS_PAYROLL.hris_salary_sheet_detail hsd on hs.SHEET_NO=hsd.SHEET_NO 
//             join LLBS_PAYROLL.hris_pay_setup hsp on hsp.PAY_ID=hsd.PAY_ID
//             join LLBS_PAYROLL.HRIS_MONTH_CODE hsc on hsc.MONTH_ID=hs.MONTH_ID
//             join LLBS_PAYROLL.hris_employees he on 
//             he.EMPLOYEE_ID=hsd.EMPLOYEE_ID where
//             --he.EMPLOYEE_ID=83 AND
//             hs.MONTH_ID= 37 and hsd.PAY_ID in (1,4,5,6,8,10,9,7,11,35,36,13,14,34)
//             )
//             PIVOT  (MAX(VAL)  FOR (pay_id) IN (1,4,5,6,8,10,9,7,11,35,36,13,14,34))";

        $result = EntityHelper::rawQueryResult($this->adapter, $sql);
        return Helper::extractDbData($result);
    }

    public function refreshNewToken() {
        $loginUrl = PayrollApiConfig::LOGIN_BASE_URL;
        $bodyData = json_encode(array("username" => PayrollApiConfig::USER_NAME,
            "password" => PayrollApiConfig::PASSWORD));
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $loginUrl,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $bodyData,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($bodyData))
        ));
        $response = json_decode(curl_exec($curl));
        curl_close($curl);


        if ($response->status == true && $response->token != '' && $response->token != null) {
            $this->saveNewToken($response->token);
            return true;
        } else {
            return false;
        }
    }

    public function refreshTokenAction() {
        try {
            $tokenStatus = $this->refreshNewToken();
            
            
            if($tokenStatus!=true){
                throw new Exception('error generating new Token');
            }
            
            return new CustomViewModel(['success' => true, 'error' =>'']);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function saveNewToken($token) {
        $sql = "BEGIN
                DELETE  FROM HRIS_PAYROLL_API_TOKEN;
                INSERT INTO HRIS_PAYROLL_API_TOKEN VALUES('{$token}');
                END;";
        EntityHelper::rawQueryResult($this->adapter, $sql);
    }

}
