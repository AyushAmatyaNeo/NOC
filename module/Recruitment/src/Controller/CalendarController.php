<?php
namespace Recruitment\Controller;
use Application\Controller\HrisController;
use Zend\Db\Adapter\AdapterInterface;
use Recruitment\Repository\VacancyRepository;
use Recruitment\Repository\CalendarRepository;
use Recruitment\Model\RecruitmentVacancy as RecruitmentVacancyModel;
use Zend\Authentication\Storage\StorageInterface;

class CalendarController extends HrisController
{
    function __construct(AdapterInterface $adapter, StorageInterface $storage) {

        parent::__construct($adapter, $storage);
        $this->initializeRepository(CalendarRepository::class);
    }

    public function indexAction()
    {   
        $details = $this->repository->getAllData();
        foreach($details as $detail){
            $end_date = explode('-',$detail["END_DATE"]);
            if($end_date[2] <= 30){
                $end_date[2] = $end_date[2]+1;
            }
            $detail["END_DATE"] = implode('-',$end_date);
            // Extended Date     
            $ext_date = explode('-',$detail["EXTENDED_DATE"]);
            if($ext_date[2] <= 30){
                $ext_date[2] = $ext_date[2]+1;
            }elseif($ext_date[2] > 30){
                $ext_date[2] = 1;
                $ext_date[1] = $ext_date[1] +1;
            } 
            // echo '<pre>'; print_r($ext_date);
            $detail["EXTENDED_DATE"] = implode('-',$ext_date);  
            // echo '<pre>'; print_r($detail);         
            $data[] = array(
                // 'id'        => $detail['VACANCY_ID'],
                'title'     => 'Opening Number:- '. $detail["OPENING_NO"],
                'start'     => $detail["START_DATE"],
                'end'       => $detail["END_DATE"],
                // 'extended'       => $detail["EXTENDED_DATE"],
                'vacancy_no' => $detail["VACANCY_TOTAL_NO"],
                'url'       => 'opening/view/'.$detail["OPENING_ID"],
                // 'color'     => 'purple'
            );
            $data[] = array(
                // 'id'        => $detail['VACANCY_ID'],
                'title'     => 'Opening Number:- '. $detail["OPENING_NO"],
                // 'start'     => $detail["START_DATE"],
                'start'       => $detail["END_DATE"],
                'end'       => $detail["EXTENDED_DATE"],
                'vacancy_no' => $detail["VACANCY_TOTAL_NO"],
                'url'       => 'opening/view/'.$detail["OPENING_ID"],
                'color'     => 'purple'
            );
            
        }   
        return $this->stickFlashMessagesTo([
            'detail' => $data
        ]);
    }   
    
}
