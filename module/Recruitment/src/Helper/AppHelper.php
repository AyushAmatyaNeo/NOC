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

    public static function DateDiffByNepaliDate($start_date, $end_date) {

        $date1 = date_create($start_date);
        $date2 = date_create($end_date);

        /***
        * GET DIFFERENCE IN NEPALI
        */
        $intval = date_diff($date2, $date1);

        // TOTAL DAYS
        $days =  $intval->format("%a");


        /**
        * FINDING AGE BY ADDING ABOVE DAYS WITH TILL CERTAIN DATE
        */
        // $days = 151;

        $start_date = new \DateTime($end_date);
        $end_date = (new \DateTime($end_date))->add(new \DateInterval("P{$days}D") );
        $dd = date_diff($start_date,$end_date);
        $age = $dd->y." years ".$dd->m." months ".$dd->d." days";

        return $age;

    }

    public static function GetRankValue($rank_value, $rank_type) {

        $result = '';

        if ($rank_type == 'Percentage') {
            
            $result = $rank_value;

        }

        if ($rank_type == 'Division/grade') {

            if ($rank_value > 100) {

                $result = $rank_value;

            }

            if ($rank_value < 100 AND $rank_value > 3) {

                $result = $rank_value . '%';

            }

            if ($rank_value <= 3) {

                $result = $rank_value . 'Division';

            }
            

        }

        if ($rank_type == 'GPA') {
            
            $result = number_format($rank_value, 2);

            if  (is_float($rank_value)) {

                $result = $rank_value;

            }

        }

        return $result;

    }


    public static function StageSelectorModifier($stage_id) {

        /**
         * UPDATING STAGE MUST CHANGE [ALLOW_EDIT | IS_VERIFIED | IS_APPROVED]
                6 => ALLOW EDIT
                7 => VERIFIED
                9 => REJECT
                8 => APPROVED
                2 => POST
                1 => OPEN
                3 => CLOSED
         */

        $updateStageData = [
            'ALLOW_EDIT'  => '',
            'IS_VERIFIED' => '',
            'IS_APPROVED' => '',
            'STAGE_ID' =>  $stage_id,
        ];

        if ($stage_id == 2) {

            $updateStageData['ALLOW_EDIT']  = 'N';
            $updateStageData['IS_VERIFIED'] = 'N';
            $updateStageData['IS_APPROVED'] = 'N';

        } elseif ($stage_id == 6) {
           
            $updateStageData['ALLOW_EDIT']  = 'Y';
            $updateStageData['IS_VERIFIED'] = 'N';
            $updateStageData['IS_APPROVED'] = 'N';

        } elseif ($stage_id == 7) {
           
            $updateStageData['ALLOW_EDIT']  = 'N';
            $updateStageData['IS_VERIFIED'] = 'Y';
            $updateStageData['IS_APPROVED'] = 'N';

        } elseif ($stage_id == 8) {
           
            $updateStageData['ALLOW_EDIT']  = 'N';
            $updateStageData['IS_VERIFIED'] = 'N';
            $updateStageData['IS_APPROVED'] = 'Y';

        } elseif ($stage_id == 9) {
           
            $updateStageData['ALLOW_EDIT']  = 'N';
            $updateStageData['IS_VERIFIED'] = 'N';
            $updateStageData['IS_APPROVED'] = 'N';

        }

        return $updateStageData;

    }
 }
