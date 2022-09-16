<?php

namespace Setup\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\AdapterInterface;
use Setup\Model\LoanRestriction;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;

class LoanRestrictionRepository implements RepositoryInterface {

    private $adapter;
    private $tableGateway;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(LoanRestriction::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [LoanRestriction::LOAN_ID => $id,LoanRestriction::RESTRICTION_TYPE=>$array['RESTRICTION_TYPE']]);
    }

    public function delete($id) {
        $this->tableGateway->update([LoanRestriction::STATUS => 'D'], [LoanRestriction::RESTRICTION_ID => $id]);
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        $row = $this->tableGateway->select([LoanRestriction::RESTRICTION_ID=>$id]);
	return $row->current();
    }
    
    public function getByLoanId($id){
        $result = $this->tableGateway->select([LoanRestriction::LOAN_ID=>$id,LoanRestriction::STATUS=>'E']);
        $list = [];
        foreach($result as $key=>$value){
            $list[$value['RESTRICTION_TYPE']] = $value['VALUE'];           
            //array_push($list, $value);
        }
        
        return $list;
    }
    
    public function udateByRestrictionTypeLoanId(Model $loanRestrictionModel,$loanRestrictionType,$id){
       print "<pre>";
       print_r($loanRestrictionModel->getArrayCopyForDB); die();
    }

}
