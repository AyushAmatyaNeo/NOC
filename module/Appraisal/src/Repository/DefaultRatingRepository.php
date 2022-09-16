<?php
namespace Appraisal\Repository;

use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Appraisal\Model\DefaultRating;
use Appraisal\Model\Type;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class DefaultRatingRepository implements RepositoryInterface{
    private $tableGateway;
    private $adapter;
    private $loggedInEmployeeId;
    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(DefaultRating::TABLE_NAME,$adapter);
        $this->adapter = $adapter;
        $auth = new AuthenticationService();
        $this->loggedInEmployeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        $this->tableGateway->update([DefaultRating::STATUS=>'D', DefaultRating::MODIFIED_BY=>$this->loggedInEmployeeId,DefaultRating::MODIFIED_DATE=> Helper::getcurrentExpressionDate()],[DefaultRating::ID=>$id]);
    }

    public function edit(Model $model, $id) {
        $this->tableGateway->update($model->getArrayCopyForDB(),[DefaultRating::ID=>$id]);
    }

    public function fetchAll() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(["DR"=> DefaultRating::TABLE_NAME])
                ->join(["AT"=> Type::TABLE_NAME], "DR.".DefaultRating::APPRAISAL_TYPE_ID."="."AT.".Type::APPRAISAL_TYPE_ID,[Type::APPRAISAL_TYPE_EDESC],"left");
        $select->where(["DR.".DefaultRating::STATUS=>'E']);
        $select->order("DR.".DefaultRating::ID." DESC");
        $query = $sql->prepareStatementForSqlObject($select);
        $result = $query->execute();
        return $result;
    }

    public function fetchById($id) {
        $result = $this->tableGateway->select([DefaultRating::ID=>$id])->current()->getArrayCopy();
        $result['DESIGNATION_IDS']= json_decode($result['DESIGNATION_IDS']);
        $result['POSITION_IDS']= json_decode($result['POSITION_IDS']);
        return $result;
    }
    public function fetechByAppraisalTypeId($appraisalTypeId){
        $result = $this->tableGateway->select([DefaultRating::APPRAISAL_TYPE_ID=>$appraisalTypeId,DefaultRating::STATUS=>'E']);
        return $result; 
    }
}


