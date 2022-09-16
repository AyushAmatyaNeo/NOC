<?php
namespace Recruitment\Helper;

use Exception;
use ReflectionClass;
use Application\Helper\Helper;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Predicate\Predicate;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;


class AppHelper{

    public static function getTableList(AdapterInterface $adapter, string $tableName, array $columnList, array $where = null, string $predicate = Predicate::OP_AND,$orderBy=null) {
        $gateway = new TableGateway($tableName, $adapter);
        $zendResult = $gateway->select(function(Select $select) use($columnList, $where, $predicate,$orderBy) {
            $select->columns($columnList, false);
            if ($where != null) {
                $select->where($where, $predicate);
            }
            if ($orderBy != null) {
            $select->order($orderBy);
            }
        });
        return Helper::extractDbData($zendResult, true);
    }

    public static function ApplicationData($adapter){
        $registration = self::getTableList($adapter, 'HRIS_REC_USERS_REGISTRATION', ['REGISTRATION_ID', 'USER_ID','RELIGION','RELIGION_INPUT','REGION','REGION_INPUT'], ['STATUS' => "E"]);

        $searchValues = [
            'registration' => $registration,
            
        ];
        /* end of search values */

        return $searchValues;
    }
}