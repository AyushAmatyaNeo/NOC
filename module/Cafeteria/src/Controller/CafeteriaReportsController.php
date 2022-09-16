<?php
namespace Cafeteria\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper as ApplicationHelper;
use Application\Helper\Helper;
use Cafeteria\Repository\CafeteriaMap;
use Cafeteria\Repository\CafeteriaReports;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;

class CafeteriaReportsController extends HrisController{
    
    protected $adapter;
    private $cafeteriaMapRepo;
    
    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        $this->adapter = $adapter;
        parent::__construct($adapter, $storage);
        $this->initializeRepository(CafeteriaReports::class);
        $this->cafeteriaMapRepo = new CafeteriaMap($adapter);
    }

    public function indexAction(){
        
    }
    
    public function canteenReportAction(){
        $modifiedAcl=$this->acl;
        $modifiedAcl['CONTROL']='F';
        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
            $result = $this->repository->fetchEmployeeWiseDetails($data);
            $reportData = Helper::extractDbData($result);
            return new JsonModel(['success' => true, 'data' => $reportData, 'message' => ""]);
        }
        
        $result = $this->cafeteriaMapRepo->fetchSchedules();
        $timeList = Helper::extractDbData($result);
        
        return $this->stickFlashMessagesTo([
                'timeList' => $timeList,
                'searchValues' => ApplicationHelper::getSearchData($this->adapter),
                'acl' => $modifiedAcl,
                'employeeDetail' => $this->storageData['employee_detail']
        ]);
    }
}