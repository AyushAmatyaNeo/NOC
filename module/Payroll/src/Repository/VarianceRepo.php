<?php

namespace Payroll\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\HrisRepository;
use Application\Repository\RepositoryInterface;
use Payroll\Model\VarianceSetup;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class VarianceRepo extends HrisRepository implements RepositoryInterface {

    protected $adapter;
    protected $tableGateway;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(VarianceSetup::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        return $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        return $this->tableGateway->update($model->getArrayCopyForDB(),[VarianceSetup::VARIANCE_ID=>$id]);
    }

    public function fetchAll() {
        $sql = "SELECT 
    V.* ,
    PAY_ID,
    PAY_EDESC,
    CASE VARIABLE_TYPE
WHEN 'S' THEN 'Salary Group'
WHEN 'V' THEN 'Variance'
WHEN 'O' THEN 'OT'
WHEN 'T' THEN 'Tax Group'
WHEN 'B' THEN 'Basic'
WHEN 'C' THEN 'Grade'
WHEN 'A' THEN 'Allowance'
WHEN 'G' THEN 'Gross'
WHEN 'Y' THEN 'Tax Yearly'
END
AS VARIABLE_TYPE_NAME
    FROM 
    Hris_Variance V
    LEFT JOIN 
    (SELECT 
    Vp.Variance_Id
    ,STRING_AGG(Ps.Pay_Id,','ORDER BY Ps.Pay_Id) AS PAY_ID,
    STRING_AGG(Ps.Pay_Edesc,','ORDER BY Ps.Pay_Id) AS PAY_EDESC
    FROM Hris_Variance_Payhead VP
    left join HRIS_PAY_SETUP PS ON (Vp.Pay_Id=Ps.Pay_Id)
    GROUP BY VARIANCE_ID) VD ON (VD.Variance_Id=V.Variance_Id)
    WHERE V.STATUS='E'";
 
        return EntityHelper::rawQueryResult($this->adapter, $sql);
    }

    public function fetchById($id) {

         $boundedParameter = [];
    $boundedParameter['id'] = $id;

        $sql = "SELECT 
    V.* ,
    PAY_ID,
    PAY_EDESC
    FROM 
    Hris_Variance V
    LEFT JOIN 
    (SELECT 
    Vp.Variance_Id
    ,STRING_AGG(Ps.Pay_Id,','ORDER BY Ps.Pay_Id) AS PAY_ID,
    STRING_AGG(Ps.Pay_Edesc,','ORDER BY Ps.Pay_Id) AS PAY_EDESC
    FROM Hris_Variance_Payhead VP
    left join HRIS_PAY_SETUP PS ON (Vp.Pay_Id=Ps.Pay_Id)
    GROUP BY VARIANCE_ID) VD ON (VD.Variance_Id=V.Variance_Id)
    WHERE V.STATUS='E' and V.VARIANCE_ID=?";
  
        return $this->rawQuery($sql, $boundedParameter)[0];
    }

    public function delete($id) {
        return $this->tableGateway->update([VarianceSetup::STATUS=>'D'],[VarianceSetup::VARIANCE_ID=>$id]);
    }

}
