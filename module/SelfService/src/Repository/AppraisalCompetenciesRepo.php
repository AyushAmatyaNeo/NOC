<?php
namespace SelfService\Repository;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use SelfService\Model\AppraisalCompetencies;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Zend\Authentication\AuthenticationService;

class AppraisalCompetenciesRepo implements RepositoryInterface{
    private $adapter;
    private $tableGateway;
    private $employeeId;
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(AppraisalCompetencies::TABLE_NAME,$adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function add(Model $model) {
//        print_r($model->getArrayCopyForDB()); die();
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        $this->tableGateway->update(
                [
                    AppraisalCompetencies::STATUS=>'D', 
                    AppraisalCompetencies::MODIFIED_BY=>$this->employeeId, 
                    AppraisalCompetencies::MODIFIED_DATE=>Helper::getcurrentExpressionDate()
                ],
                [AppraisalCompetencies::SNO=>$id]
                );
    }

    public function edit(Model $model, $id) {
        $this->tableGateway->update($model->getArrayCopyForDB(),[AppraisalCompetencies::SNO=>$id]);
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        
    }
    public function fetchByAppEmpId($employeeId,$appraisalId){
        $sql =  new Sql($this->adapter);
        $select = $sql->select();
        $select->from(AppraisalCompetencies::TABLE_NAME);
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(AppraisalCompetencies::class,
                null,null,null,null,null),false);
        $select->where([AppraisalCompetencies::APPRAISAL_ID=>$appraisalId,AppraisalCompetencies::EMPLOYEE_ID=>$employeeId,AppraisalCompetencies::STATUS=>"E"]);
        $select->order(AppraisalCompetencies::SNO);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }
    public function countCompetenciesRatingDtl($employeeId,$appraisalId){
        $sql = "SELECT count(*) as num
FROM HRIS_APPRAISAL_COMPETENCY
WHERE APPRAISAL_ID   =".$appraisalId."
AND EMPLOYEE_ID      =".$employeeId."
AND STATUS = 'E'
AND RATING IS NOT NULL
GROUP BY APPRAISAL_ID, EMPLOYEE_ID";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result->current();
    }
    public function updateColumnByEmpAppId($data,$employeeId,$appraisalId){
        $this->tableGateway->update($data,[AppraisalCompetencies::EMPLOYEE_ID=>$employeeId, AppraisalCompetencies::APPRAISAL_ID=>$appraisalId]);
    }
}

