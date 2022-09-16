<?php
namespace Recruitment\Controller;

use Application\Controller\HrisController;
use Recruitment\Repository\ReportRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Authentication\Storage\StorageInterface;
use Application\Helper\Helper;
use Zend\View\Model\ViewModel;
use Application\Helper\EntityHelper;
use Zend\View\Model\JsonModel;
use Exception;

class ReportController extends HrisController
{
    function __construct(AdapterInterface $adapter, StorageInterface $storage) {

        parent::__construct($adapter, $storage);
        $this->initializeRepository(ReportRepository::class);
        // $this->initializeForm(OptionsForm::class);
        
    }
    public function indexAction()
    {
        $request = $this->getRequest();
                if ($request->isPost()) {
                    try {
                        $data = (array) $request->getPost();
                        // echo '<pre>'; print_r($data); die;              
                        $rawList = $this->repository->getFilteredRecords($data);                        
                        $list = iterator_to_array($rawList, false);
                        // echo '<pre>'; print_r($list); die;              
                        return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
                    } catch (Exception $e) {
                        return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
                    }
                }
        $OpeningVacancyNo = EntityHelper::getTableList($this->adapter, 'HRIS_REC_OPENINGS', ['OPENING_ID','VACANCY_TOTAL_NO','OPENING_NO'], ['STATUS' => 'E']);
        $statusSE = $this->getRecStatusSelectElement(['name' => 'status', 'id' => 'status', 'class' => 'form-control reset-field', 'label' => 'Status']);        
        return $this->stickFlashMessagesTo([
            'status' => $statusSE,
            'openings' => $OpeningVacancyNo,
            'Stages' => EntityHelper::getTableList($this->adapter, 'HRIS_REC_STAGES', ['REC_STAGE_ID','STAGE_EDESC'], ['STATUS' => 'E'],'','ORDER_NO'),
        ]);
    }
}