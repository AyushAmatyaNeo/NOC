<?php

namespace Payroll\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Zend\Db\Adapter\AdapterInterface;
use Application\Repository\HrisRepository;
use Payroll\Model\FinanceData;

class FinanceDataRepository extends HrisRepository{

    protected $adapter;

    public function __construct(AdapterInterface $adapter) {
        parent::__construct($adapter, FinanceData::TABLE_NAME);
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
    }

    public function delete($id) {
        
    }

    public function fetchById($id) {}

    public function financialDataGlEntry($financeDataId){
        $boundedParameter = [];
        $boundedParameter['financeDataId'] = $financeDataId;
        $this->rawQuery("CALL HRIS_GL_ENTRY_PROC(?)", $boundedParameter);
    }

    public function fetchAllData(){
        $sql = "select * from HRIS_GL_ENTRY where 1=1 and sync = 'N'";
        return $this->rawQuery($sql, $boundedParameters);
    }
}
