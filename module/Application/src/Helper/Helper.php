<?php
namespace Application\Helper;

use Application\Model\Model;
use DateTime;
use Symfony\Component\VarDumper\VarDumper;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;

class Helper {

    const ORACLE_DATE_FORMAT = "DD-MON-YYYY";
    const ORACLE_TIME_FORMAT = "HH:MI AM";
    const ORACLE_TIMESTAMP_FORMAT = "HH24:MI";
    const MYSQL_DATE_FORMAT = "";
    const PHP_DATE_FORMAT = "d-M-Y";
    const PHP_TIME_FORMAT = "h:i A";
    const FLOAT_ROUNDING_DIGIT_NO = 2;
    const UPLOAD_DIR = __DIR__ . "/../../../../public/uploads";
    const UPLOAD_DIR_EMP = __DIR__ . "/../../../../public/uploads/profile";
    const SH_DIR = __DIR__ . "/../../../../public/sh";

//  method to add flashmessage to view
    public static function addFlashMessagesToArray($context, $return) {
        $flashMessenger = $context->flashMessenger();
        if ($flashMessenger->hasMessages()) {
            $return['messages'] = $flashMessenger->getMessages();
        }
        return $return;
    }

//  method to generate maxId
    public static function getMaxId(AdapterInterface $adapter, $tableName, $columnName) {
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from($tableName);
        $select->columns(["MAX({$columnName}) AS MAX_{$columnName}"], false);

        $statement = $sql->prepareStatementForSqlObject($select);

        $result = $statement->execute();
        $row = $result->current();
        return $row["MAX_{$columnName}"];
    }

    public static function convertColumnDateFormat(AdapterInterface $adapter, Model $table, $attrs = null, $timeAttrs = null, $shortForm = null, $timeIntervalAttrs = null) {
        $temp = get_object_vars($table);
        if ($attrs != null) {
            foreach ($attrs as $attr) {
                unset($temp[$attr]);
            }
        }

        if ($timeAttrs != null) {
            foreach ($timeAttrs as $attr) {
                unset($temp[$attr]);
            }
        }

        unset($temp['mappings']);

        if ($timeAttrs != null) {
            foreach ($timeAttrs as $attr) {
                unset($temp[$attr]);
            }
        }
        if ($timeIntervalAttrs != null) {
            foreach ($timeIntervalAttrs as $attr) {
                unset($temp[$attr]);
            }
        }

        $attributes = array_keys($temp);

        $tempCols = [];

        if ($attrs != null) {
            foreach ($attrs as $attr) {
//                array_push($tempCols, Helper::appendDateFormat($adapter, $table->mappings[$attr], self::ORACLE_DATE_FORMAT));
                array_push($tempCols, Helper::appendDateFormat($adapter, $table->mappings[$attr], self::ORACLE_DATE_FORMAT, $shortForm));
            }
        }

        if ($timeAttrs != null) {
            foreach ($timeAttrs as $attr) {
                array_push($tempCols, Helper::appendDateFormat($adapter, $table->mappings[$attr], self::ORACLE_TIME_FORMAT, $shortForm));
            }
        }

        if ($timeIntervalAttrs != null) {
            foreach ($timeIntervalAttrs as $attr) {
                array_push($tempCols, Helper::appendDateFormat($adapter, $table->mappings[$attr], self::ORACLE_TIMESTAMP_FORMAT, $shortForm));
            }
        }

        foreach ($attributes as $attribute) {
            array_push($tempCols, Helper::columnExpression($table->mappings[$attribute], $shortForm, null));
        }
        return $tempCols;
    }

    public static function appendDateFormat($adapter, $columnName, $format, $shortForm = null) {
        $pre = "";
        if ($shortForm != null && strlen($shortForm) != 0) {
            $pre = $shortForm . ".";
        }
        $tempStr = "";
        switch (strtolower($adapter->getPlatform()->getName())) {
            case strtolower("Oracle"):
                $tempStr = "INITCAP(TO_CHAR({$pre}{$columnName}, '{$format}')) AS {$columnName}";
                break;

            case strtolower("Mysql"):

                break;

            case strtolower("odbc"):
                $tempStr = "INITCAP(TO_CHAR({$pre}{$columnName}, '{$format}')) AS {$columnName}";
                break;

        }
        return $tempStr;
    }

    public static function dateExpression($columnName, $shortForm = null) {
        $format = Helper::ORACLE_DATE_FORMAT;
        $pre = "";
        if ($shortForm != null && strlen($shortForm) != 0) {
            $pre = $shortForm . ".";
        }
        $tempStr = "INITCAP(TO_CHAR({$pre}{$columnName}, '{$format}')) AS {$columnName}";
        return new Expression($tempStr);
    }

    public static function timeExpression($columnName, $shortForm = null) {
        $format = Helper::ORACLE_TIME_FORMAT;
        $pre = "";
        if ($shortForm != null && strlen($shortForm) != 0) {
            $pre = $shortForm . ".";
        }
        $tempStr = "INITCAP(TO_CHAR({$pre}{$columnName}, '{$format}')) AS {$columnName}";
        return new Expression($tempStr);
    }

    public static function datetimeExpression($columnName, $shortForm = null) {
        $format = Helper::ORACLE_DATE_FORMAT . " " . self::ORACLE_TIME_FORMAT;
        $pre = "";
        if ($shortForm != null && strlen($shortForm) != 0) {
            $pre = $shortForm . ".";
        }
        $tempStr = "INITCAP(TO_CHAR({$pre}{$columnName}, '{$format}')) AS {$columnName}";
        return new Expression($tempStr);
    }

    public static function columnExpression($columnName, $shortForm = null, $function = null, $customColumnName = null) {
        $pre = "";
        if ($shortForm != null && strlen($shortForm) != 0) {
            $pre = $shortForm . ".";
        }

        if ($customColumnName == null) {
            $customColumnName = $columnName;
        }
        if ($function == NULL) {
            $tempStr = "{$pre}{$columnName} AS {$columnName}";
        } else {
            $tempStr = "${function}({$pre}{$columnName}) AS {$customColumnName}";
        }
        return new Expression($tempStr);
    }

    public static function getExpressionDate($dateStr) {
        $format = Helper::ORACLE_DATE_FORMAT;
        return new Expression("TO_DATE('{$dateStr}', '{$format}')"
        );
    }

    public static function getExpressionDateHana($dateStr) {
        $format = Helper::ORACLE_DATE_FORMAT;
        return new Expression("TO_DATE('{$dateStr}', 'YYYY-MM-DD')"
        );
    }

    public static function getExpressionDateHanaDDMONYYYY($dateStr) {
         return new Expression("TO_DATE('{$dateStr}', 'DD-MON-YYYY')"
        );
    }

    public static function getExpressionTime($dateStr, $format = null) {
        if ($format == null) {
            $format = Helper::ORACLE_TIME_FORMAT;
        }
        return new Expression("TO_TIME('{$dateStr}', '{$format}')");
    }

    public static function getExpressionTimeHANA($dateStr, $format = null) {
        if ($format == null) {
            $format = Helper::ORACLE_TIME_FORMAT;
        }
        return new Expression("TO_TIMESTAMP('{$dateStr}', 'HH:MI AM')");
    }

    public static function getExpressionDateTime($dateStr, $format = null) {
        if ($format == null) {
            $format = Helper::ORACLE_DATE_FORMAT . " " . Helper::ORACLE_TIME_FORMAT;
        }
        return new Expression("TO_TIMESTAMP('{$dateStr}', '{$format}')");
    }

    public static function getExpressionDateTimeHana($timeStr, $dateStr = null, $format = null) {
        if ($format == null) {
            $format = Helper::ORACLE_DATE_FORMAT . " " . Helper::ORACLE_TIME_FORMAT;
        }
        if($timeStr == null || $timeStr == ''){
            return new Expression("TO_TIME('{$timeStr}', '{$format}')");
        }
        else{
            return new Expression("TO_TIMESTAMP('{$dateStr} {$timeStr}', '{$format}')");
        }
        
    }

    public static function getTimestampHana($timeStr, $dateStr = null, $format = null) {
        if ($format == null) {
            $format = Helper::ORACLE_DATE_FORMAT . " " . Helper::ORACLE_TIME_FORMAT;
        }
        if($timeStr == null || $timeStr == ''){
            return "TO_TIME('{$timeStr}', '{$format}')";
        }
        else{
            return "TO_TIMESTAMP('{$dateStr} {$timeStr}', '{$format}')";
        }
        
    }

    public static function getcurrentExpressionDate() {
        $currentDate = date(self::PHP_DATE_FORMAT);
        return self::getExpressionDate($currentDate);
    }

    public static function getcurrentExpressionTime() {
        $currentTime = date(self::PHP_TIME_FORMAT);
        return self::getExpressionTime($currentTime);
    }

    public static function getcurrentExpressionDateTime() {
        date_default_timezone_set('Asia/Kathmandu');
        $currentDate = date(self::PHP_DATE_FORMAT . " " . self::PHP_TIME_FORMAT);
        return self::getExpressionDateTime($currentDate);
    }

    public static function getcurrentMonthDayExpression() {
        $currentDate = date('d-M');
        $format = 'DD-MON';
        return new Expression("TO_DATE('{$currentDate}','{$format}')");
    }

    public static function getCurrentDate() {
        $currentDate = date(self::PHP_DATE_FORMAT);
        return $currentDate;
    }

    public static function renderCustomView() {
        return function ($object) {
            $elems = $object->getValueOptions();
            $counter = 1;
            $name = $object->getName();
            $atts = $object->getAttributes();
            $disabled = '';
            if (in_array('disabled', $atts)) {
                $disabled = 'disabled';
            }
            foreach ($elems as $key => $value) {
                $temp = (($object->getValue() == "") && ($counter == $object->getCheckedValue())) || ($object->getValue() == $key) ? 'checked=checked' : '';

                echo "<div class = 'md-radio'> <input {$temp} {$disabled} type = 'radio' value = '{$key}' name = '{$name}' id = '{$name}+{$value}' class = 'md-radiobtn radioButton'><label for = '$name+$value'>
                            <span></span>
                            <span class = 'check'></span>
                            <span class = 'box'></span> $value
                        </label> </div>";
                $counter++;
            }
        };
    }

    public static function renderCustomViewForCheckbox() {
        return function ($object) {
            $elems = $object->getValueOptions();
            $counter = 1;
            $name = $object->getName();
            $atts = $object->getAttributes();
            $disabled = '';
            if (in_array('disabled', $atts)) {
                $disabled = 'disabled';
            }
            foreach ($elems as $key => $value) {
                $temp = '';
                if ($object->getValue() == "") {
                    if ($counter == $object->getCheckedValue()) {
                        $temp = 'checked=checked';
                    }
                } else {
                    if (in_array($key, $object->getValue())) {
                        $temp = 'checked=checked';
                    }
                }
                echo "<div class = 'md-checkbox'>";
                echo "<input $temp $disabled type = 'checkbox' value = '$key' name = '$name' id = '$name+$value' class = 'md-check'>";

                echo "<label for = '$name+$value'>
                <span></span>
                <span class = 'check'></span>
                <span class = 'box'></span> $value
                </label>";
                echo "</div>";
                $counter++;
            }
        };
    }

    public static function hydrate($class, ResultSet $resultSet) {
        $tempArray = [];
        foreach ($resultSet as $item) {
            $model = new $class();
            $model->exchangeArrayFromDB(
                $item->getArrayCopy());
            array_push($tempArray, $model);
        }
        return $tempArray;
    }
    /*
     * This function return the raw result in array or object array form
     */

    public static function extractDbData($rawArray, bool $inArray = false, string $arrangeWithKey = null): array {
        $extractedArray = [];
        foreach ($rawArray as $item) {
            if ($inArray) {
                $item = $item->getArrayCopy();
            }
            if ($arrangeWithKey == null) {
                array_push($extractedArray, $item);
            } else {
                $extractedArray[$item[$arrangeWithKey]] = $item;
            }
        }
        return $extractedArray;
    }

    public static function generateUniqueName() {
        $date = new DateTime();
        $t = $date->getTimestamp();
        return $t + rand(0, 1000);
    }

    public static function maintainFloatNumberFormat($floatNumber) {
        return number_format($floatNumber, self::FLOAT_ROUNDING_DIGIT_NO, '.', '');
    }

    public static function hoursToMinutes($formattedHours) {
        if (isset($formattedHours) && strpos($formattedHours, ":") > 0) {
            list($hours, $minutes) = explode(':', $formattedHours);
            return $hours * 60 + $minutes;
        } else {
            return 0;
        }
    }

    public static function minutesToHours($minutes) {
        $hours = (int) ($minutes / 60);
        $minutes -= $hours * 60;
        return sprintf("%d:%02.0f", $hours, $minutes);
    }

    public static function dateDiff($date1, $date2) {
        $fromDate = \DateTime::createFromFormat(Helper::PHP_DATE_FORMAT, $date1);
        $toDate = \DateTime::createFromFormat(Helper::PHP_DATE_FORMAT, $date2);
        $interval = $fromDate->diff($toDate);
        return $interval->format('%a');
    }

    public static function encryptPassword($password) {
        return new Expression("FN_ENCRYPT_PASSWORD('$password')");
    }

    public static function uploadFilesVacancy($receivedFiles,$folder) {
        $citizenshipDir = getcwd() . '/public/uploads/documents/'.$folder;
        $empFileDir = getcwd() . '/public/uploads';

        if (!file_exists($citizenshipDir)) {
            mkdir($citizenshipDir, 0777, true);
        }
            $imageName = $receivedFiles['name'];
            $newImageName = time().$receivedFiles['name'];
            $extension = explode('.', $receivedFiles['name']);
            $extension = end($extension);
            $path = 'uploads/documents/'.$folder. "/";
            $empFilePath = $empFileDir. "/" . $newImageName;
            $movingPath = $citizenshipDir. "/" . $newImageName;

        $filesStoring = [
            'imageName' => $imageName,
            'newImageName' => $newImageName,
            'tmp_name' => $receivedFiles['tmp_name'],
            'path' => $path,
            'extension' => $extension,
            'empFilePath' => $empFilePath,
            'movingPath' => $movingPath,
            'folder' => $folder
        ];
        return $filesStoring;
    }

    /**
     * Converts english number to nepali and viceversa
     */
    public static function numConverter($number, $to="english"){
        $eng_number = array(
            "0",
            "1",
            "2",
            "3",
            "4",
            "5",
            "6",
            "7",
            "8",
            "9"
        );
        $nep_number = array(
            "०",
            "१",
            "२",
            "३",
            "४",
            "५",
            "६",
            "७",
            "८",
            "९"
        );

        if($to=="english"){
            return str_replace($nep_number, $eng_number, $number);
        }else{
            return str_replace($eng_number, $nep_number, $number);
        }
    }

    /**
     * Converts Nepali date to english and vice versa
    */
    public static function convertDateLanguage($date, $language){

        if($date){
            if($language =="nepali"){
                $dateArray = explode('/', date_format(date_create($date), "Y/m/d"));
            }else{
                $dateArray = explode('/', $date);
            }

            $dateConversion = Helper::numConverter($dateArray[0], $language) . '/' . 
                                Helper::numConverter($dateArray[1], $language)  . '/' . 
                                Helper::numConverter($dateArray[2], $language);

            return $dateConversion;
        }
    }

    /**
     * Converts Nepali time to english and vice versa
    */
    public static function convertTimeLanguage($time, $language){
        if($time){
            $timeArr = explode(':', $time);

            $timeConversion = Helper::numConverter($timeArr[0], $language) . ':' . 
                                Helper::numConverter($timeArr[1], $language);

            return $timeConversion;
        }
    }
}
