<?php
namespace SelfService\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use SelfService\Model\TravelRequest;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression; 
use Zend\Db\Sql\Sql;
use Application\Helper\Helper;
use Zend\Db\TableGateway\TableGateway;
use Application\Repository\HrisRepository;

class TravelRequestRepository extends HrisRepository implements RepositoryInterface {

    protected $tableGateway;
    protected $adapter;
 
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(TravelRequest::TABLE_NAME, $adapter);
    } 

    /*
    public function pushFileLink($data){ 
        $fileName = $data['fileName'];
        $fileInDir = $data['filePath'];
        $sql = "INSERT INTO HRIS_TRAVEL_FILES(FILE_ID, FILE_NAME, FILE_IN_DIR_NAME, TRAVEL_ID) VALUES((SELECT MAX(FILE_ID)+1 FROM HRIS_TRAVEL_FILES), '$fileName', '$fileInDir', null)";
        $statement = $this->adapter->query($sql);
        $statement->execute(); 
        $sql = "SELECT * FROM HRIS_TRAVEL_FILES WHERE FILE_ID IN (SELECT MAX(FILE_ID) AS FILE_ID FROM HRIS_TRAVEL_FILES)";
        $statement = $this->adapter->query($sql);
        return Helper::extractDbData($statement->execute());
    }
  
    
    public function linkTravelWithFiles(){
        if(!empty($_POST['fileUploadList'])){
            $filesList = $_POST['fileUploadList'];
            $filesList = implode(',', $filesList);

            $sql = "UPDATE HRIS_TRAVEL_FILES SET TRAVEL_ID = (SELECT MAX(TRAVEL_ID) FROM HRIS_EMPLOYEE_TRAVEL_REQUEST) 
                    WHERE FILE_ID IN($filesList)";
            $statement = $this->adapter->query($sql);
            $statement->execute();
        }
    } 

    public function fetchAttachmentsById($id){
      $sql = "SELECT * FROM HRIS_TRAVEL_FILES WHERE TRAVEL_ID = $id";
      $result = EntityHelper::rawQueryResult($this->adapter, $sql);
      return Helper::extractDbData($result);
    }
     * 
     */
    
    public function add(Model $model) {
        $addData=$model->getArrayCopyForDB();
        $this->tableGateway->insert($addData);

        if ($addData['STATUS']=='AP' && date('Y-m-d', strtotime($model->fromDate)) <= date('Y-m-d')) {
            //THE FOLLOWING CODE WAS DONE IN THE URGENCY FOR MAKING THE DATE COMPATIBLE WITH SAP HANA
            $sql = "CALL 
            HRIS_REATTENDANCE((select to_char(from_date,'yyyy-mm-dd') from HRIS_EMPLOYEE_TRAVEL_REQUEST where travel_id = $model->travelId), $model->employeeId,(select to_char(to_date,'yyyy-mm-dd') from HRIS_EMPLOYEE_TRAVEL_REQUEST where travel_id = $model->travelId));";
//            $boundedParameter = [];
//            $boundedParameter['fromDate'] = $model->fromDate;
//            $boundedParameter['employeeId'] = $model->employeeId;
//            $boundedParameter['toDate'] = $model->toDate;

            $this->rawQuery($sql);
        }
        //$this->linkTravelWithFiles();
        $this->linkTravelWithFiles();
    }

    public function delete($id) {

        $travelStatus = $this->getTravelFrontOrBack($id);
        
        $currentDate = Helper::getcurrentExpressionDate();
        $travelStatusAction=$travelStatus['CANCEL_ACTION'];
        $this->tableGateway->update([TravelRequest::STATUS => $travelStatusAction], [TravelRequest::TRAVEL_ID => $id]);
        $boundedParameter = [];
        $boundedParameter['id']=$id;
        EntityHelper::rawQueryResult($this->adapter, "
        DO 
        BEGIN
         DECLARE V_FROM_DATE DATE DEFAULT NULL;
                 DECLARE V_TO_DATE DATE DEFAULT NULL;
               DECLARE V_EMPLOYEE_ID INT DEFAULT NULL;
               DECLARE V_STATUS VARCHAR(255) DEFAULT NULL;
               DECLARE V_TRAVEL_ID INT DEFAULT {$id};
               
       
          BEGIN
            CALL HRIS_TRAVEL_CANCELLATION(V_TRAVEL_ID);
          END; 
          
                   SELECT FROM_DATE ,
                 TO_DATE,
                 EMPLOYEE_ID,
                 STATUS
                   INTO V_FROM_DATE,
                 V_TO_DATE,
                 V_EMPLOYEE_ID,
                 V_STATUS
                   FROM HRIS_EMPLOYEE_TRAVEL_REQUEST
                   WHERE TRAVEL_ID =V_TRAVEL_ID;
               
                   IF V_STATUS IN ('AP','C') AND V_FROM_DATE < CURRENT_DATE THEN
                     CALL HRIS_REATTENDANCE(V_FROM_DATE,V_EMPLOYEE_ID,V_TO_DATE);
                   END IF;
                               
          END;
          
        
        ");
    }
    public function edit(Model $model, $id) {
        // echo("<pre>");print_r($model);die;
        $array = $model->getArrayCopyForDB();
        unset($array['EMPLOYEE_ID']);
        $this->tableGateway->update($array, [TravelRequest::TRAVEL_ID => $id]);
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("TR.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("TR.IS_TEAM_LEAD AS IS_TEAM_LEAD"),
            new Expression("TR.TRAVEL_ID AS TRAVEL_ID"),
            new Expression("TR.TRAVEL_CODE AS TRAVEL_CODE"),
            new Expression("TR.DESTINATION AS DESTINATION"),
            new Expression("TR.DEPARTURE AS DEPARTURE"),
            new Expression("TR.HARDCOPY_SIGNED_FLAG AS HARDCOPY_SIGNED_FLAG"),
            new Expression("TR.REQUESTED_AMOUNT AS REQUESTED_AMOUNT"),
            new Expression("TR.PURPOSE AS PURPOSE"),
            new Expression("TR.TRANSPORT_TYPE AS TRANSPORT_TYPE"),
            new Expression("TR.TRANSPORT_TYPE_LIST AS TRANSPORT_TYPE_LIST"),
            new Expression("INITCAP(HRIS_GET_FULL_FORM(TR.TRANSPORT_TYPE,'TRANSPORT_TYPE')) AS TRANSPORT_TYPE_DETAIL"),
            new Expression("TR.REQUESTED_TYPE AS REQUESTED_TYPE"),
            new Expression("(CASE WHEN LOWER(TR.REQUESTED_TYPE) = 'ad' THEN 'Advance' ELSE 'Expense' END) AS REQUESTED_TYPE_DETAIL"),
            new Expression("INITCAP(TO_CHAR(TR.DEPARTURE_DATE, 'DD-MON-YYYY')) AS DEPARTURE_DATE"),
            new Expression("INITCAP(TO_CHAR(TR.RETURNED_DATE, 'DD-MON-YYYY')) AS RETURNED_DATE"),
            new Expression("INITCAP(TO_CHAR(TR.FROM_DATE, 'DD-MON-YYYY')) AS FROM_DATE"),
            new Expression("INITCAP(TO_CHAR(TR.FROM_DATE, 'DD-MM-YYYY')) AS FROM_DATE_FORMATED"),
            new Expression("INITCAP(BS_DATE(TR.FROM_DATE)) AS FROM_DATE_BS"),
            new Expression("INITCAP(TO_CHAR(TR.TO_DATE, 'DD-MON-YYYY')) AS TO_DATE"),
            new Expression("INITCAP(TO_CHAR(TR.TO_DATE, 'DD-MM-YYYY')) AS TO_DATE_FORMATED"),
            new Expression("INITCAP(BS_DATE(TR.TO_DATE)) AS TO_DATE_BS"),
            new Expression("days_between(TR.FROM_DATE,TR.TO_DATE)+1 AS DURATION"),
            new Expression("INITCAP(TO_CHAR(TR.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE"),
            new Expression("INITCAP(TO_CHAR(TR.REQUESTED_DATE, 'DD-MM-YYYY')) AS REQUESTED_DATE_FORMATED"),
            new Expression("INITCAP(BS_DATE(TR.REQUESTED_DATE)) AS REQUESTED_DATE_BS"),
            new Expression("TR.REMARKS AS REMARKS"),
            new Expression("TR.STATUS AS STATUS"),
            new Expression("TR.ACCOMPLISHMENT AS ACCOMPLISHMENT"),
            // new Expression("(CASE WHEN TR.STATUS = 'RC' THEN 'Travel Approved'  
            // when TR.STATUS = 'AP' then 'Travel Advance Approved' else LEAVE_STATUS_DESC(TR.STATUS) END) AS STATUS_DETAIL"),
            
            new Expression("LEAVE_STATUS_DESC(TR.STATUS) AS STATUS_DETAIL"),
            new Expression("TR.RECOMMENDED_BY AS RECOMMENDED_BY"),
            new Expression("INITCAP(TO_CHAR(TR.RECOMMENDED_DATE, 'DD-MON-YYYY')) AS RECOMMENDED_DATE"),
            new Expression("TR.RECOMMENDED_REMARKS AS RECOMMENDED_REMARKS"),
            new Expression("TR.APPROVED_BY AS APPROVED_BY"),
            new Expression("INITCAP(TO_CHAR(TR.APPROVED_DATE, 'DD-MON-YYYY')) AS APPROVED_DATE"),
            new Expression("TR.APPROVED_REMARKS AS APPROVED_REMARKS"),
            new Expression("TR.REFERENCE_TRAVEL_ID AS REFERENCE_TRAVEL_ID"),
            new Expression("TR.ITNARY_ID AS ITNARY_ID"),
            ], true);

        $select->from(['TR' => TravelRequest::TABLE_NAME])
            ->join(['TS' => "HRIS_TRAVEL_SUBSTITUTE"], "TR.TRAVEL_ID=TS.TRAVEL_ID", [
                'SUB_EMPLOYEE_ID' => 'EMPLOYEE_ID',
                'SUB_APPROVED_DATE' => new Expression("INITCAP(TO_CHAR(TS.APPROVED_DATE, 'DD-MON-YYYY'))"),
                'SUB_REMARKS' => "REMARKS",
                'SUB_APPROVED_FLAG' => "APPROVED_FLAG",
                'SUB_APPROVED_FLAG_DETAIL' => new Expression("(CASE WHEN APPROVED_FLAG = 'Y' THEN 'Approved' WHEN APPROVED_FLAG = 'N' THEN 'Rejected' ELSE 'Pending' END)")
                ], "left")
            ->join(['TSE' => 'HRIS_EMPLOYEES'], 'TS.EMPLOYEE_ID=TSE.EMPLOYEE_ID', ["SUB_EMPLOYEE_NAME" => new Expression("INITCAP(TSE.FULL_NAME)")], "left")
            ->join(['TSED' => 'HRIS_DESIGNATIONS'], 'TSE.DESIGNATION_ID=TSED.DESIGNATION_ID', ["SUB_DESIGNATION_TITLE" => "DESIGNATION_TITLE"], "left")
            ->join(['E' => 'HRIS_EMPLOYEES'], 'E.EMPLOYEE_ID=TR.EMPLOYEE_ID', ["FULL_NAME" => new Expression("INITCAP(E.FULL_NAME)"), "EMPLOYEE_CODE" => new Expression("INITCAP(E.EMPLOYEE_CODE)")], "left")
            ->join(['ED' => 'HRIS_DESIGNATIONS'], 'E.DESIGNATION_ID=ED.DESIGNATION_ID', ["DESIGNATION_TITLE" => "DESIGNATION_TITLE"], "left")
            ->join(['EC' => 'HRIS_COMPANY'], 'E.COMPANY_ID=EC.COMPANY_ID', ["COMPANY_NAME" => "COMPANY_NAME"], "left")
            ->join(['ECF' => 'HRIS_EMPLOYEE_FILE'], 'EC.LOGO=ECF.FILE_CODE', ["COMPANY_FILE_PATH" => "FILE_PATH"], "left")
            ->join(['E2' => "HRIS_EMPLOYEES"], "E2.EMPLOYEE_ID=TR.RECOMMENDED_BY", ['RECOMMENDED_BY_NAME' => new Expression("INITCAP(E2.FULL_NAME)")], "left")
            ->join(['E3' => "HRIS_EMPLOYEES"], "E3.EMPLOYEE_ID=TR.APPROVED_BY", ['APPROVED_BY_NAME' => new Expression("INITCAP(E3.FULL_NAME)")], "left")
            ->join(['E4' => "HRIS_EMPLOYEES"], "E4.EMPLOYEE_ID=TR.RECOMMENDER_ID", ['NAME_RECOMMENDER' => new Expression("INITCAP(E4.FULL_NAME)")], "left")
            ->join(['E5' => "HRIS_EMPLOYEES"], "E5.EMPLOYEE_ID=TR.APPROVER_ID", ['NAME_APPROVER' => new Expression("INITCAP(E5.FULL_NAME)")], "left")
            ->join(['RA' => "HRIS_RECOMMENDER_APPROVER"], "RA.EMPLOYEE_ID=TR.EMPLOYEE_ID", ['RECOMMENDER_ID' => 'RECOMMEND_BY', 'APPROVER_ID' => 'APPROVED_BY'], "left")
            ->join(['RECM' => "HRIS_EMPLOYEES"], "RECM.EMPLOYEE_ID=RA.RECOMMEND_BY", ['RECOMMENDER_NAME' => new Expression("INITCAP(RECM.FULL_NAME)")], "left")
            ->join(['B' => "HRIS_BRANCHES"], "B.BRANCH_ID=E.BRANCH_ID", ['BRANCH_NAME' => "BRANCH_NAME"], "left")
            ->join(['HL' => 'HRIS_LOCATIONS'], 'HL.LOCATION_ID=E.LOCATION_ID', ["LOCATION_EDESC" => "LOCATION_EDESC"], "left")
            ->join(['APRV' => "HRIS_EMPLOYEES"], "APRV.EMPLOYEE_ID=RA.APPROVED_BY", ['APPROVER_NAME' => new Expression("INITCAP(APRV.FULL_NAME)")], "left");
        $select->where(["TR.TRAVEL_ID" => $id]);
        $select->order("TR.REQUESTED_DATE DESC");
        $statement = $sql->prepareStatementForSqlObject($select);

        $result = $statement->execute();
        return $result->current();
    }

    public function getFilteredRecords(array $search) {
        $sql = new Sql($this->adapter);
        $employeeId = $search['employeeId'];
        $select = $sql->select();
        $select->columns([
            new Expression("INITCAP(TO_CHAR(TR.FROM_DATE, 'DD-MON-YYYY')) AS FROM_DATE_AD"),
            new Expression("BS_DATE(TR.FROM_DATE) AS FROM_DATE_BS"),
            new Expression("INITCAP(TO_CHAR(TR.TO_DATE, 'DD-MON-YYYY')) AS TO_DATE_AD"),
            new Expression("BS_DATE(TR.TO_DATE) AS TO_DATE_BS"),
            new Expression("TR.STATUS AS STATUS"),
            new Expression("TR.HARDCOPY_SIGNED_FLAG AS HARDCOPY_SIGNED_FLAG"),
            // new Expression("(CASE WHEN TR.STATUS = 'RC' THEN 'Travel Approved'  
            // when TR.STATUS = 'AP' then 'Travel Advance Approved' else LEAVE_STATUS_DESC(TR.STATUS) END) AS STATUS_DETAIL"),
            new Expression("LEAVE_STATUS_DESC(TR.STATUS) AS STATUS_DETAIL"),
            new Expression("TR.DESTINATION AS DESTINATION"),
            new Expression("TR.DEPARTURE AS DEPARTURE"),
            new Expression("INITCAP(TO_CHAR(TR.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE_AD"),
            new Expression("BS_DATE(TR.REQUESTED_DATE) AS REQUESTED_DATE_BS"),
            new Expression("INITCAP(TO_CHAR(TR.APPROVED_DATE, 'DD-MON-YYYY')) AS APPROVED_DATE"),
            new Expression("INITCAP(TO_CHAR(TR.RECOMMENDED_DATE, 'DD-MON-YYYY')) AS RECOMMENDED_DATE"),
            new Expression("TR.REQUESTED_AMOUNT AS REQUESTED_AMOUNT"),
            new Expression("TR.TRAVEL_ID AS TRAVEL_ID"),
            new Expression("TR.TRAVEL_CODE AS TRAVEL_CODE"),
            new Expression("TR.PURPOSE AS PURPOSE"),
            new Expression("TR.TRANSPORT_TYPE AS TRANSPORT_TYPE"),
            new Expression("TR.TRANSPORT_TYPE_LIST AS TRANSPORT_TYPE_LIST"),
            new Expression("INITCAP(HRIS_GET_FULL_FORM(TR.TRANSPORT_TYPE,'TRANSPORT_TYPE')) AS TRANSPORT_TYPE_DETAIL"),
            new Expression("TR.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("TR.RECOMMENDED_BY AS RECOMMENDED_BY"),
            new Expression("TR.APPROVED_BY AS APPROVED_BY"),
            new Expression("TR.APPROVED_REMARKS AS APPROVED_REMARKS"),
            new Expression("TR.RECOMMENDED_REMARKS AS RECOMMENDED_REMARKS"),
            new Expression("TR.REMARKS AS REMARKS"),
            new Expression("TR.REQUESTED_TYPE AS REQUESTED_TYPE"),
            new Expression("(CASE WHEN LOWER(TR.REQUESTED_TYPE) = 'ad' THEN 'Advance' ELSE 'Expense' END) AS REQUESTED_TYPE"),
            new Expression("(CASE WHEN TR.STATUS = 'RQ' THEN 'Y' ELSE 'N' END) AS ALLOW_EDIT"),
            new Expression("(CASE WHEN TR.STATUS IN ('RQ','AP') THEN 'Y' ELSE 'N' END) AS ALLOW_DELETE"),
            new Expression("(CASE WHEN (TR.STATUS = 'AP' AND (SELECT COUNT(*) FROM HRIS_EMPLOYEE_TRAVEL_REQUEST WHERE REFERENCE_TRAVEL_ID =TR.TRAVEL_ID AND STATUS not in ('C','R') ) =0 ) THEN 'Y' ELSE 'N' END) AS ALLOW_EXPENSE_APPLY"),
            ], true);

        $select->from(['TR' => TravelRequest::TABLE_NAME])
            ->join(['E' => 'HRIS_EMPLOYEES'], 'E.EMPLOYEE_ID=TR.EMPLOYEE_ID', ["FULL_NAME" => new Expression("INITCAP(E.FULL_NAME)")], "left")
            ->join(['E2' => "HRIS_EMPLOYEES"], "E2.EMPLOYEE_ID=TR.RECOMMENDED_BY", ['RECOMMENDED_BY_NAME' => new Expression("INITCAP(E2.FULL_NAME)")], "left")
            ->join(['E3' => "HRIS_EMPLOYEES"], "E3.EMPLOYEE_ID=TR.APPROVED_BY", ['APPROVED_BY_NAME' => new Expression("INITCAP(E3.FULL_NAME)")], "left")
            ->join(['RA' => "HRIS_RECOMMENDER_APPROVER"], "RA.EMPLOYEE_ID=TR.EMPLOYEE_ID", ['RECOMMENDER_ID' => 'RECOMMEND_BY', 'APPROVER_ID' => 'APPROVED_BY'], "left")
            ->join(['RECM' => "HRIS_EMPLOYEES"], "RECM.EMPLOYEE_ID=RA.RECOMMEND_BY", ['RECOMMENDER_NAME' => new Expression("INITCAP(RECM.FULL_NAME)")], "left")
            ->join(['APRV' => "HRIS_EMPLOYEES"], "APRV.EMPLOYEE_ID=RA.APPROVED_BY", ['APPROVER_NAME' => new Expression("INITCAP(APRV.FULL_NAME)")], "left");

        $select->where([
            "E.EMPLOYEE_ID  = {$employeeId}"
        ]);


        if ($search['statusId'] != -1) {
            $select->where([
                "TR.STATUS" => $search['statusId']
            ]);
        }
        if ($search['statusId'] != 'C') {
            $select->where([
                "days_between(TR.REQUESTED_DATE,current_date) < (
                      CASE
                        WHEN TR.STATUS = 'C'
                        THEN 20
                        ELSE 365
                      END)"
            ]);
        }

        if ($search['fromDate'] != null) {
            $fromDate = $search['fromDate'];
            $select->where([
                "TR.FROM_DATE>=TO_DATE('{$fromDate}','DD-MON-YYYY')"
            ]);
        }

        if ($search['toDate'] != null) {
            $toDate = $search['toDate'];
            $select->where([
                "TR.TO_DATE>=TO_DATE('{$toDate}','DD-MON-YYYY')"
            ]);
        }

        if (isset($search['requestedType'])) {
            $select->where([
                "LOWER(TR.REQUESTED_TYPE)" => $search['requestedType']
            ]);
        }
        $select->order("TR.REQUESTED_DATE DESC");
        $statement = $sql->prepareStatementForSqlObject($select);
        // echo('<pre>');print_r($statement);print_r($boundedParameter);die;
        $result = $statement->execute();
        return $result;
    }
    
    public function checkAllowEdit($id){
        $boundedParameter = [];
        $boundedParameter['id'] = $id;
        $sql = "SELECT (CASE WHEN STATUS = 'RQ' THEN 'Y' ELSE 'N' END)"
                . " AS ALLOW_EDIT FROM HRIS_EMPLOYEE_TRAVEL_REQUEST WHERE "
                . "TRAVEL_ID = {$id}";

        // $boundedParameter = [];
        // $boundedParameter['id'] = $id;
        return $this->rawQuery($sql)[0]["ALLOW_EDIT"];
    }

    public function pushFileLink($data, $userID)
    {
        $fileName = $data['fileName'];
        $fileInDir = $data['filePath'];
        $sql = "INSERT INTO hris_travel_files(FILE_ID,TRAVEL_ID, FILE_NAME,UPLOADED_DATE, FILE_IN_DIR_NAME)
        VALUES((SELECT ifnull(max(file_ID), 0) + 1 FROM hris_travel_files),null, '$fileName', current_date, '$fileInDir')";

        $statement = $this->adapter->query($sql);
        $statement->execute();
        $sql = "SELECT * FROM hris_travel_files WHERE FILE_ID IN (SELECT MAX(FILE_ID) AS FILE_ID FROM hris_travel_files)";
        $statement = $this->adapter->query($sql);
        return Helper::extractDbData($statement->execute());
        // $fileName = $data['fileName'];
        // $fileInDir = $data['filePath'];
        // $sql = "INSERT INTO DC_REGISTRATION_DOCS(FILE_ID,FILE_NAME,DOC_DATE, FILE_IN_DIR_NAME, REG_ID) VALUES((SELECT nvl(max(file_ID), 0) + 1 FROM DC_FILES), '$fileName', trunc(sysdate), '$fileInDir', null)";
        // $statement = $this->adapter->query($sql);
        // $statement->execute();
        // $sql = "SELECT * FROM DC_FILES WHERE FILE_ID IN (SELECT MAX(FILE_ID) AS FILE_ID FROM DC_FILES)";
        // $statement = $this->adapter->query($sql);
        // return Helper::extractDbData($statement->execute());
    }

    public function getClassIdFromEmpId($id){
        $sql1 = "select TRAVEL_ALLOWANCE_CLASS from hris_employees 
        where employee_id = {$id}";

        $sql2 = "select pcm.class_id from hris_position_class_map pcm
        left join hris_employees he on (pcm.position_id = he.functional_level_id)
        where he.employee_id = {$id}";
        if($this->rawQuery($sql1)[0]["TRAVEL_ALLOWANCE_CLASS"]){
            return $this->rawQuery($sql1)[0]["TRAVEL_ALLOWANCE_CLASS"];
        }else{
            return $this->rawQuery($sql2)[0]["CLASS_ID"];
        }
        
    }

    public function getCongifId($travelType, $mot, $classId){
        if($travelType == "DOMESTIC"){
            $sql = "select config_id from hris_class_travel_config where
        travel_type = '{$travelType}' and domestic_type = '{$mot}' and class_id = {$classId}";
        }else{
            $sql = "select config_id from hris_class_travel_config where
        travel_type = '{$travelType}' and international_type = '{$mot}' and class_id = {$classId}";
        }   
        return $this->rawQuery($sql)[0]["CONFIG_ID"];
    }

    public function getRateFromConfigId($configId){
        $sql = "select rate from hris_class_travel_config where config_id = {$configId}";
        return $this->rawQuery($sql)[0]["RATE"];
    }

    public function getTotalExpenseAmount($travelId, $id, $isTeamLead){
        $sql = "select
        case when ('{$isTeamLead}' = 'Y')
           then (sum(total)+ sum((amount * exchange_rate))*0.25)
           -ifnull((select requested_amount from hris_employee_travel_request where travel_id = {$id}),0) 
           else (sum(total))-ifnull((select requested_amount from hris_employee_travel_request where travel_id = {$id}),0) 
           END as NRP_TOTAL  
           from hris_travel_expense       
           where travel_id = {$travelId}  and status='E'
   ";
        return $this->rawQuery($sql)[0]["NRP_TOTAL"];
    }

    public function linkTravelWithFiles($id = null)
    {
        if (!empty($_POST['fileUploadList'])) {
            if ($id == null) {
                $filesList = $_POST['fileUploadList'];
                $filesList = implode(',', $filesList);
                $sql = "UPDATE hris_travel_files SET TRAVEL_ID = (SELECT reference_travel_id FROM HRIS_EMPLOYEE_TRAVEL_REQUEST where travel_id = (select max(travel_id) from HRIS_EMPLOYEE_TRAVEL_REQUEST))
                        WHERE FILE_ID IN($filesList)";
                $statement = $this->adapter->query($sql);
                $statement->execute();
            } else {
                $filesList = $_POST['fileUploadList'];
                $filesList = implode(',', $filesList);
                $sql = "UPDATE hris_travel_files SET TRAVEL_ID = $id
                        WHERE FILE_ID IN($filesList)";
                $statement = $this->adapter->query($sql);
                $statement->execute();
            }
        }
        $sql="delete from hris_travel_files where travel_id is null";
        $statement = $this->adapter->query($sql);
        $statement->execute();
    }

    public function pullFilebyId($id)
    {

        $boundedParams = [];

        $sql = "select FILE_IN_DIR_NAME, File_Name from hris_travel_files where travel_id = {$id}";
        // $boundedParams['id'] = $this->rawQuery("SELECT reference_travel_id FROM HRIS_EMPLOYEE_TRAVEL_REQUEST where travel_id = {$id}")[0]['REFERENCE_TRAVEL_ID'];
        // print_r($sql);die;
        return $this->rawQuery($sql);
    }
    public function deletePreviouseLinkFiles($linkedId){
        $sql="update hris_travel_files set travel_id = null where travel_id = {$linkedId}";
        $statement = $this->adapter->query($sql);
        $statement->execute();
        return;
    }

    public function getTravelTypeDetail($travelTypeId){
        $sql="select INITCAP(HRIS_GET_FULL_FORM('$travelTypeId',
        'TRANSPORT_TYPE')) as detail from dummy";
        return $this->rawQuery($sql);
    }

    public function getRecommenderApproverList($empId){
        $sql="select * from hris_employees where status = 'E' and (functional_level_id <=6 
		) or 
		functional_level_id in (select functional_level_id from hris_functional_levels where 
		functional_level_no in ('10','11','12'))";
        return $this->rawQuery($sql);
    }

    public function insertJVdata($id, $jvNumber, $chequeNumber, $bank){
        $sql = "update hris_employee_travel_request set Jv_Number = '{$jvNumber}', Cheque_Number = '{$chequeNumber}', Bank_id ={$bank} where travel_id = {$id}";
        return $this->rawQuery($sql);

    }
    public function getJvDetails($id){
        $sql = "select tr.Jv_Number, tr.Cheque_Number, b.Bank_Name from hris_employee_travel_request tr
        left join Hris_banks b on (b.bank_id = tr.bank_id) where tr.travel_id = $id ";
        return $this->rawQuery($sql);
    }
    public function addAlternaterRecommenderApprover($empId, $recommenderId, $approverId){
        $sql="delete from HRIS_ALTERNATE_r_a where employee_id = $empId";
        $statement = $this->adapter->query($sql);
        $statement->execute();

        $sql="insert into HRIS_ALTERNATE_r_a values ($empId, $recommenderId, 'R')";
        $statement = $this->adapter->query($sql);
        $statement->execute();

        $sql="insert into HRIS_ALTERNATE_r_a values ($empId, $approverId, 'A')";
        $statement = $this->adapter->query($sql);
        $statement->execute();

        return;
    }

    public function getValueAdvanceForTravel($id){
        $sql="select requested_amount from hris_employee_travel_request where travel_id = 
        (select reference_travel_id from hris_employee_travel_request where travel_id = $id)";
        return $this->rawQuery($sql);
    }

    public function getAlternateApproverName($empId){
        $sql = "select e.full_name as name from HRIS_ALTERNATE_r_a ra left join
        hris_employees e on (e.employee_id = ra.r_a_id) where ra.employee_id = $empId and r_a_flag='A'";
        return $this->rawQuery($sql);
    }

    public function getAlternateRecommenderName($empId){
        $sql = "select e.full_name as name from HRIS_ALTERNATE_r_a ra left join
        hris_employees e on (e.employee_id = ra.r_a_id) where ra.employee_id = $empId and r_a_flag='R'";
        return $this->rawQuery($sql);
    }

    public function getTotalNoOfAttachment($id){
        $sql = "select count(*) from hris_travel_files where travel_id = (select reference_travel_id from
        hris_employee_travel_request where travel_id = $id)";
        return $this->rawQuery($sql);
    }

    public function validateTravelRequest($fromDate, $toDate, $employeeId) {
        $boundedParameter = [];
        $boundedParameter['fromDate']="to_date('{$fromDate}','DD-MON-YYYY')";
        $fromDate = $boundedParameter['fromDate'];
        $boundedParameter['toDate']="to_date('{$toDate}','DD-MON-YYYY')";
        $toDate = $boundedParameter['toDate'];

        // print_r( "SELECT HRIS_VALIDATE_TRAVEL_REQUEST($fromDate,$toDate,$employeeId) AS ERROR FROM DUMMY");die;
        $rawResult = $this->rawQuery( "SELECT HRIS_VALIDATE_TRAVEL_REQUEST($fromDate,$toDate,$employeeId) AS ERROR FROM DUMMY");
        
        return $rawResult[0];
    }

    public function getTravelFrontOrBack($id) {
        $boundedParameter = [];
        $boundedParameter['id']=$id;
        $sql = "SELECT FROM_DATE,CURRENT_DATE AS CURDATE,
            CASE WHEN
            STATUS IN ('RQ','RC') THEN 'NA'
            ELSE STATUS
            END
            AS TRAVEL_STATUS,
            DAYS_BETWEEN(FROM_DATE,CURRENT_DATE) AS DIFF,
                CASE  WHEN 
                 DAYS_BETWEEN(CURRENT_DATE,FROM_DATE)>0
                THEN
                'FD'
                ELSE
                'BD'
                END AS DATE_STATUS,
                CASE 
                WHEN STATUS IN ('RQ','RC') THEN
                'C'
                WHEN STATUS IN ('AP','CR') and  DAYS_BETWEEN(CURRENT_DATE,FROM_DATE)<=0 THEN
                'CP'
                 WHEN STATUS IN ('AP','CR') and  DAYS_BETWEEN(CURRENT_DATE,FROM_DATE)>0 THEN
                'C'
                ELSE
                STATUS
                END AS CANCEL_ACTION
                FROM HRIS_EMPLOYEE_TRAVEL_REQUEST WHERE TRAVEL_ID= $id";
                // echo'<pre>';print_r($sql);die;
        $statement = $this->adapter->query($sql);
        return $statement->execute()->current();
    }

}
