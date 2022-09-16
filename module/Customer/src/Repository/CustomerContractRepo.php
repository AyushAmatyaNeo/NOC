<?php

namespace Customer\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Customer\Model\Customer;
use Customer\Model\CustomerContract;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class CustomerContractRepo implements RepositoryInterface {

    private $adapter;
    private $gateway;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->gateway = new TableGateway(CustomerContract::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->gateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $id) {
        $this->gateway->update($model->getArrayCopyForDB(), [CustomerContract::CONTRACT_ID => $id]);
    }

    public function fetchAll() {
        $sql = "SELECT CC.CONTRACT_ID AS CONTRACT_ID,CC.CONTRACT_NAME AS CONTRACT_NAME,
                  C.CUSTOMER_ENAME,
                  TO_CHAR(CC.START_DATE,'DD-MON-YYYY') AS START_DATE_AD,
                  BS_DATE(CC.START_DATE)               AS START_DATE_BS,
                  TO_CHAR(CC.END_DATE,'DD-MON-YYYY')   AS END_DATE_AD,
                  BS_DATE(CC.END_DATE)                 AS END_DATE_BS,
                  CASE BILLING_MONTH 
                  WHEN 'N' THEN 'NEPALI'
                  ELSE 'ENGLISH' END  AS BILLING_MONTH,
                  FREEZED,
                  BILLING_TYPE,
                  CC.REMARKS
                FROM HRIS_CUSTOMER_CONTRACT CC
                JOIN HRIS_CUSTOMER C
                ON (CC.CUSTOMER_ID=C.CUSTOMER_ID)
                WHERE CC.STATUS      ='E'
                ORDER BY C.CUSTOMER_ENAME";
        $rawResult = EntityHelper::rawQueryResult($this->adapter, $sql);
        return $rawResult;
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(CustomerContract::class, NULL, [
                    CustomerContract::START_DATE,
                    CustomerContract::END_DATE,
                        ], NULL, NULL, NULL, 'CC'
                ), false);
        $select->from(['CC' => CustomerContract::TABLE_NAME])
                ->join(['C' => "HRIS_CUSTOMER"], "C." . Customer::CUSTOMER_ID . "=CC." . CustomerContract::CUSTOMER_ID, ['CUSTOMER_ENAME' => new Expression("INITCAP(C.CUSTOMER_ENAME)"),'ADDRESS' => new Expression("INITCAP(C.ADDRESS)"),'START_DATE_BS'=>new Expression("BS_DATE(CC.START_DATE)"),'END_DATE_BS'=>new Expression("BS_DATE(CC.END_DATE)")], 'left');
        
         $select->where([
            "CC.CONTRACT_ID=$id"
        ]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

}
