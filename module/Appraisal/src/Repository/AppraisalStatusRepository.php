<?php
namespace Appraisal\Repository;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Appraisal\Model\AppraisalStatus;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Authentication\AuthenticationService;

class AppraisalStatusRepository implements RepositoryInterface{
    private $adapter;
    private $tableGateway;
    private $loggedEmployeeId;
    
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(AppraisalStatus::TABLE_NAME,$adapter);
        $auth = new AuthenticationService();
        $this->loggedEmployeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        
    }
    public function fetchByEmpAppId($employeeId,$appraisalId){
        $result = $this->tableGateway->select([AppraisalStatus::APPRAISAL_ID=>$appraisalId, AppraisalStatus::EMPLOYEE_ID=>$employeeId]);
        return $result->current(); 
    }
    public function updateAnnualRatingId($annualRatingKPI,$appraisalId,$employeeId){
        $this->tableGateway->update([AppraisalStatus::ANNUAL_RATING_KPI =>$annualRatingKPI],[AppraisalStatus::APPRAISAL_ID=>$appraisalId,AppraisalStatus::EMPLOYEE_ID=>$employeeId]);
    }
    public function updateAnnualRatingComId($annualRatingCompetency,$appraisalId,$employeeId){
        $this->tableGateway->update([AppraisalStatus::ANNUAL_RATING_COMPETENCY =>$annualRatingCompetency],[AppraisalStatus::APPRAISAL_ID=>$appraisalId,AppraisalStatus::EMPLOYEE_ID=>$employeeId]);
    }
    public function updateOverallRatingId($overallRating,$appraisalId,$employeeId){
        $this->tableGateway->update([AppraisalStatus::APPRAISER_OVERALL_RATING =>$overallRating],[AppraisalStatus::APPRAISAL_ID=>$appraisalId,AppraisalStatus::EMPLOYEE_ID=>$employeeId]);
    }
    public function updateColumnByEmpAppId($columnArray,$appraisalId,$employeeId){
        $this->tableGateway->update($columnArray,[AppraisalStatus::APPRAISAL_ID=>$appraisalId,AppraisalStatus::EMPLOYEE_ID=>$employeeId]);
    }
}
