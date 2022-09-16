<?php
namespace Application\Repository;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;
use Application\Repository\RepositoryInterface;
use Application\Model\ForgotPassword;
use Application\Model\Model;
use Application\Helper\Helper;
use Application\Helper\EntityHelper;

class ForgotPasswordRepository implements RepositoryInterface{
    private $tableGateway;
    private $adapter;
    
    public function __construct(AdapterInterface $adapter) {
        $this->adapter  = $adapter;
        $this->tableGateway = new TableGateway(ForgotPassword::TABLE_NAME,$adapter);
    }

    public function add(Model $model) {
        $data = $model->getArrayCopyForDB();
        $this->tableGateway->insert($data);
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        
    }
    public function fetchByEmployeeId($employeeId){
        $expiryDate = new \DateTime('now');
        $dt = $expiryDate->format('d-M-y h:i A');
        $result = $this->tableGateway->select([ForgotPassword::EMPLOYEE_ID=>$employeeId,ForgotPassword::EXPIRY_DATE.">=TO_DATE('".$dt."','DD-MON-YYYY HH:MI AM')"]);
//        print_r($result->current()); die();
        return $result->current();
    }
}