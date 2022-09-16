<?php
namespace Recruitment\Repository;
use Application\Repository\HrisRepository;
use Application\Helper\Helper;

class CalendarRepository extends HrisRepository{
    public function getAllData(){        
        $sql = "SELECT OPENING_ID,OPENING_NO,VACANCY_TOTAL_NO,START_DATE,END_DATE,EXTENDED_DATE,VACANCY_TOTAL_NO from HRIS_REC_OPENINGS where
        status ='E' ORDER BY OPENING_ID ASC";  
        $statement = $this->adapter->query($sql);
        $result = Helper::extractDbData($statement->execute());        
        // echo '<pre>';print_r($result); die();
        return $result;
    }
}