<?php

namespace Payroll\Repository;

use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\HrisRepository;
use Payroll\Model\SpecialRules;
use Traversable;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;

class SpecialRulesRepo extends HrisRepository {
protected $adapter;
    public function __construct(AdapterInterface $adapter, $tableName = null) {
        if ($tableName == null) {
            $tableName = SpecialRules::TABLE_NAME;
        }
        parent::__construct($adapter, $tableName);
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        return $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        
    }

    public function update(Model $model, $payId, $salaryType){
      $this->tableGateway->update($model->getArrayCopyForDB(), [SpecialRules::PAY_ID => $payId, SpecialRules::SALARY_TYPE_ID => $salaryType]);
    }

    public function delete($id) {
        $rule = new Rules();
        $rule->modifiedDt = Helper::getcurrentExpressionDate();
        $rule->status = 'D';
        $this->tableGateway->update($rule->getArrayCopyForDB(), [Rules::PAY_ID => $id]);
    }

    public function fetchSpecialRules($id){
         $boundedParameter = [];
        $boundedParameter['id'] = $id; 

      $sql = "SELECT PAY_ID, SALARY_TYPE_ID, FORMULA, FLAG FROM HRIS_PAY_SETUP_SPECIAL WHERE PAY_ID = ?";
     
      return $this->rawQuery($sql, $boundedParameter);
    }

    public function fetchSalaryTypes(){
      $sql = "SELECT * FROM HRIS_SALARY_TYPE ORDER BY SALARY_TYPE_ID";
      return $this->rawQuery($sql);
    }

    public function checkSpecialRuleExists($salaryType, $payId){


             $boundedParameter = [];
              $boundedParameter['salaryType'] = $salaryType;
        $boundedParameter['payId'] = $payId;
       

      $sql = "select (case 
            when exists (select 1 
                        from HRIS_PAY_SETUP_SPECIAL 
                        where SALARY_TYPE_ID = ?
                        AND PAY_ID = ?) 
                then 'Y' 
                else 'N' 
            end) as RECORD_EXISTS
          from DUMMY";
       
      return $this->rawQuery($sql, $boundedParameter);
    }
}
