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

    public static function DateDiff($start_date, $end_date) {

        $diff = abs(strtotime($end_date) - strtotime($start_date));

        $years = floor($diff / (365*60*60*24));
        $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
        $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

        $result = sprintf("%d years, %d months, %d days\n", $years, $months, $days);

        return $result;
    }

    public static function DateDiffWithDays($totalDays) {

        // $diff = abs(strtotime($end_date) - strtotime($start_date));
        $years = floor($totalDays / (365));
        $months = floor(($totalDays - $years * 365) / (30));
        $days = floor(($totalDays - $years * 365 - $months*30));

        $result = sprintf("%d years, %d months, %d days\n", $years, $months, $days);

        return $result;
    }


}