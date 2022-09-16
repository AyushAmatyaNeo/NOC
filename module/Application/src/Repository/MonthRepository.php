<?php

namespace Application\Repository;

use Application\Helper\Helper;
use Application\Model\Model;
use Application\Model\Months;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class MonthRepository implements RepositoryInterface {

    private $gateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->gateway = new TableGateway(Months::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
        $rowset = $this->gateway->select(function (Select $select) {
            $select->columns(Helper::convertColumnDateFormat($this->adapter, new Months(), [
                        'fromDate',
                        'toDate',
                    ]), false);

            $select->where([Months::STATUS => 'E']);
        });
        return $rowset;
    }

    public function fetchByMonthId(int $id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(Months::TABLE_NAME);
        $select->columns(Helper::convertColumnDateFormat($this->adapter, new Months(), [
                    'fromDate',
                    'toDate',
                ]), false);

        $select->where([Months::MONTH_ID => $id]);
        $select->where([Months::STATUS => 'E']);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    public function fetchById($id) {
        return $this->gateway->select(function(Select $select) use($id) {
                    $select->where([Months::FISCAL_YEAR_ID => $id]);
                    $select->where([Months::STATUS => 'E']);
                    $select->order([Months::FROM_DATE => Select::ORDER_ASCENDING]);
                });
    }

    public function fetchByDate(Expression $currentDate) {
        return $this->gateway->select(function(Select $select) use($currentDate) {
                    //$select->columns(Helper::convertColumnDateFormat($this->adapter, new Months(), ['fromDate', 'toDate'], null, null), FALSE);
                    $select->where([Months::STATUS => 'E']);
                    $select->where([Months::FROM_DATE . " <=" . $currentDate->getExpression(), Months::TO_DATE . " >= " . $currentDate->getExpression()]);
                })->current();
    }

    public function getCurrentFiscalYear() {
        $dateFormat = Helper::ORACLE_DATE_FORMAT;
        $sql = <<<EOT
                    SELECT START_DATE AS START_DATE,
                      END_DATE       AS END_DATE,
                      FISCAL_YEAR_ID
                    FROM HRIS_FISCAL_YEARS
                    WHERE current_date BETWEEN START_DATE AND END_DATE              
EOT;
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result->current();
    }

    public function getCurrentDateBS() {
        $sql = "SELECT BS_DATE(current_date) AS BS_DATE FROM DUMMY";        
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result->current();
    }

    public function getCurrentMonth() {
        $sql = <<<EOT
            SELECT MONTH_ID,
              FISCAL_YEAR_ID,
              FISCAL_YEAR_MONTH_NO,
              YEAR,
              MONTH_NO,
              MONTH_EDESC,
              MONTH_NDESC,
              FROM_DATE,
              INITCAP(TO_CHAR(FROM_DATE,'DD-MON-YYYY')) AS FROM_DATE_AD,
              BS_DATE(FROM_DATE) AS FROM_DATE_BS,
              TO_DATE ,
              INITCAP(TO_CHAR(TO_DATE,'DD-MON-YYYY')) AS TO_DATE_AD,
              BS_DATE(TO_DATE) AS TO_DATE_BS
            FROM HRIS_MONTH_CODE
            WHERE CURRENT_DATE BETWEEN FROM_DATE AND TO_DATE
EOT;
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result->current();
    }

}
