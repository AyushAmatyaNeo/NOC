<?php
namespace Recruitment\Repository;

use Application\Model\Model;
use Application\Repository\HrisRepository;
use Recruitment\Model\UserInformationModel;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;

class UserInformationRepository extends HrisRepository{

    public function __construct(AdapterInterface $adapter, $tableName = null) {
        parent::__construct($adapter, UserInformationModel::TABLE_NAME);
    }

    public function allUsers()
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();

        $select->columns([
            new Expression("UN.FIRST_NAME || ' ' || ifnull(UN.MIDDLE_NAME,'') || ' ' || UN.LAST_NAME    AS FULL_NAME"),
            new Expression("UN.MOBILE_NO    AS MOBILE_NO"),
            new Expression("UN.EMAIL_ID    AS EMAIL_ID"),
            new Expression("UN.USERNAME    AS USERNAME"),
            new Expression("UN.PASSWORD    AS PASSWORD"),
            new Expression("UN.USER_ID    AS USER_ID"),
            new Expression("APP.APPLICATION_ID AS APPLICATION_ID"),
        ], true);

        $select->from(['UN' => UserInformationModel::TABLE_NAME])
               ->join(['APP' => 'HRIS_REC_VACANCY_APPLICATION'],'APP.USER_ID=UN.USER_ID', 'STATUS', 'left')
               ->where(["APP.APPLICATION_ID is not null"]);
        

        $boundedParameter = [];
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute($boundedParameter);
        return $result;
    }

    public function fetchUserById($id)
    {
        return $this->tableGateway->select([UserInformationModel::USER_ID=>$id])->current();
    }

    public function checkUserNameAvailability($userName,$userId) 
    {
        $tableName = UserInformationModel::TABLE_NAME;

        $boundedParameter = [];
        $boundedParameter['userName']=$userName;
        $sql = "SELECT * FROM {$tableName} WHERE LOWER(USERNAME)=LOWER(?) ";
        
        if($userId){
            $boundedParameter['userId']=$userId;
            $sql .= "AND USER_ID!=?";
        }
        
        $statement = $this->adapter->query($sql);
        $result = $statement->execute($boundedParameter);
        return $result->current();
    }

    public function edit(Model $model, $id) {
        $this->tableGateway->update($model->getArrayCopyForDB(), [UserInformationModel::USER_ID => $id]);
    }

}