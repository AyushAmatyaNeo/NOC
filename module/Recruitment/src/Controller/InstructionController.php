<?php

namespace Recruitment\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Custom\CustomViewModel;
use Recruitment\Model\OptionsModel;
use Setup\Model\Gender;
use Setup\Model\Designation;
use Setup\Model\Department;
use Recruitment\Repository\InstructionRepository;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\View\Model\ViewModel;
use Exception;
use Zend\View\Model\JsonModel;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;


class InstructionController extends HrisController
{

    protected $stageRepository;

    function __construct(AdapterInterface $adapter, StorageInterface $storage)
    {

        parent::__construct($adapter, $storage);
        $this->initializeRepository(InstructionRepository::class);
    }
    public function addAction()
    {
        $request = $this->getRequest();
        $postData = $request->getPost();
        $shortIns = $this->repository->checkdata();
        $longIns = $this->repository->checkLongdata();
        $nameCheck = $this->repository->checkName();
        if ($request->isPost()) {
            if ( $postData['shortdesc']) {
                if ($shortIns == null) {
                    $data = array(
                        'INSTRUCTION_ID' => ((int) Helper::getMaxId($this->adapter, 'HRIS_REC_INSTRUCTIONS', 'INSTRUCTION_ID')) + 1,
                        'INSTRUCTION_CODE' => 'REC_INSTRUCTION_TOP',
                        'DESCRIPTION_NDESC' => $postData['shortdesc'],
                        'STATUS' => 'E',
                        'CREATED_DT' =>  date('Y-m-d'),
                    );  
                    $this->repository->insertTopInstruction($data); 
                } else {
                    $data = array(
                        'DESCRIPTION_NDESC' => $postData['shortdesc'],
                        'STATUS' => 'E',
                        'MODIFIEd_DT' =>  date('Y-m-d'),
                    ); 
                    $this->repository->updateInstruction($data,$shortIns[0]['INSTRUCTION_ID']); 
                }
                
            }
            if ( $postData['instruction']) {
                if ($longIns == null) {
                    $data = array(
                        'INSTRUCTION_ID' => ((int) Helper::getMaxId($this->adapter, 'HRIS_REC_INSTRUCTIONS', 'INSTRUCTION_ID')) + 1,
                        'INSTRUCTION_CODE' => 'REC_INSTRUCTION_BUTTOM',
                        'DESCRIPTION_NDESC' => $postData['instruction'],
                        'STATUS' => 'E',
                        'CREATED_DT' =>  date('Y-m-d'),
                    );  
                    $this->repository->insertButtomInstruction($data); 
                } else {
                    
                    $data = array(
                        'DESCRIPTION_NDESC' => $postData['instruction'],
                        'STATUS' => 'E',
                        'MODIFIEd_DT' =>  date('Y-m-d'),
                    ); 
                    $this->repository->updateInstruction($data,$longIns[0]['INSTRUCTION_ID']); 
                }
                
            }
            if ( $postData['karyalaya_name']) {
                if ($nameCheck == null) {
                    $data = array(
                        'INSTRUCTION_ID' => ((int) Helper::getMaxId($this->adapter, 'HRIS_REC_INSTRUCTIONS', 'INSTRUCTION_ID')) + 1,
                        'INSTRUCTION_CODE' => 'REC_OFFICE_NAME',
                        'DESCRIPTION_NDESC' => $postData['karyalaya_name'],
                        'STATUS' => 'E',
                        'CREATED_DT' =>  date('Y-m-d'),
                    );  
                    $this->repository->insertButtomInstruction($data); 
                } else {
                    $data = array(
                        'DESCRIPTION_NDESC' => $postData['karyalaya_name'],
                        'STATUS' => 'E',
                        'MODIFIEd_DT' =>  date('Y-m-d'),
                    ); 
                    $this->repository->updateInstruction($data,$nameCheck[0]['INSTRUCTION_ID']); 
                }
            }
            
        }
        return new ViewModel(
            Helper::addFlashMessagesToArray(
                $this,
                [
                    'longIns' => $longIns[0]['DESCRIPTION_NDESC'],
                    'shortIns' => $shortIns[0]['DESCRIPTION_NDESC'],
                    'nameCheck'=> $nameCheck[0]['DESCRIPTION_NDESC'],
                    'messages' => $this->flashmessenger()->getMessages(),
                ]
            )
        );
    }
    public function indexAction(){
        $shortIns = $this->repository->checkdata();
        $longIns = $this->repository->checkLongdata();
        return new ViewModel(
            Helper::addFlashMessagesToArray(
                $this,
                [
                    'longIns' => $$longIns[0]['DESCRIPTION_NDESC'],
                    'shortIns' => $shortIns[0]['DESCRIPTION_NDESC'],
                ]
            )
        );
    }
   
    public function getInsAction()
    {
        $data = $this->repository->getAllData();
        $longIns = "";
        $shortIns = "";
        if ($data != null) {
           foreach ($data as $value) {
               if ($value['INSTRUCTION_CODE']) {
                   if($value['INSTRUCTION_CODE'] == 'REC_INSTRUCTION_BUTTOM') {
                        $longIns = $value['DESCRIPTION_NDESC'];
                   }else {
                       $shortIns = $value['DESCRIPTION_NDESC'];
                   }
               }
           }
        }
        return new JsonModel(['success' => 'true','shortIns' => $shortIns, 'longIns' => $longIns ]);
    }
}
