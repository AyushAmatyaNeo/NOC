<?php
namespace Cafeteria\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Zend\Db\Adapter\AdapterInterface;
use Cafeteria\Repository\CafeteriaActivity;
use Cafeteria\Repository\CafeteriaMap;
use Zend\Authentication\Storage\StorageInterface;
use Zend\View\Model\JsonModel;

class CafeteriaActivityController extends HrisController{
    
    protected $adapter;
    private $cafeteriaMapRepo;
    
    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        $this->adapter = $adapter;
        parent::__construct($adapter, $storage);
        $this->initializeRepository(CafeteriaActivity::class);
        $this->cafeteriaMapRepo = new CafeteriaMap($this->adapter);
    }

    public function indexAction(){
        return $this->redirect()->toRoute('cafeteria-activity', array(
            'controller' => 'CafeteriaActivityController',
            'action' =>  'activity'
        ));
    }
    
    public function activityAction(){
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $result = $this->repository->getNextLogNo();
            $logNo = Helper::extractDbData($result)[0]['LOG_NO'];
            $this->repository->saveLog($data['data'], $logNo, $this->employeeId);
            $this->repository->saveLogDetails($data['data'], $logNo, $this->employeeId);
            return new JSONModel(['success' => true]);            
        }
        
        $result = $this->repository->fetchTimes();
        $timeList = Helper::extractDbData($result);
        
        $result = $this->cafeteriaMapRepo->fetchMenu();
        $menuList = Helper::extractDbData($result);
        
        $mapList = []; 
        for($i = 0; $i < count($timeList); $i++){
            $result = $this->cafeteriaMapRepo->fetchMappingDetailsByTime($timeList[$i]['TIME_ID']);
            $mapList[$timeList[$i]['TIME_NAME']] = Helper::extractDbData($result);
        }

        $result = $this->repository->getEmployeesDetails();
        $details = Helper::extractDbData($result);

        return Helper::addFlashMessagesToArray($this, [
            'timeList' => $timeList,
            'menuList' => $menuList,
            'mapList' => $mapList,
            'employeeDetails' => $details,
            'acl' => $this->acl
        ]);
    }

    public function fetchPresentStatusAction(){
        $request = $this->getRequest();
        $data = $request->getPost();
        $date = $data['date']!=null || $data['date']!='' ? $data['date'] : date('d-M-Y', strtotime('now'));
        $data = Helper::extractDbData($this->repository->getPresentStatus($date));
        return new JsonModel(['success' => true, 'data' => $data]);
    }
}