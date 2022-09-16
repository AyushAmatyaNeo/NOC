<?php
namespace Cafeteria\Controller;

use Application\Controller\HrisController;
use Application\Helper\Helper;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Cafeteria\Form\CafeteriaMenuForm;
use Cafeteria\Form\CafeteriaScheduleForm;
use Cafeteria\Repository\CafeteriaSchedule;
use Cafeteria\Repository\CafeteriaMenu;
use Cafeteria\Repository\CafeteriaMap;
use Cafeteria\Model\CafeteriaMenuModel;
use Cafeteria\Model\CafeteriaScheduleModel;
use Zend\Authentication\Storage\StorageInterface;
use Zend\View\Model\JsonModel;

class CafeteriaSetupController extends HrisController{
    
    private $cafeteriaMenuForm;
    private $cafeteriaTimeForm;
    private $cafeteriaMenuRepo;
    private $cafeteriaTimeRepo;
    private $cafeteriaMapRepo;
    protected $adapter;
    
    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        $this->adapter = $adapter;
        parent::__construct($adapter, $storage);
       $this->cafeteriaTimeRepo = new CafeteriaSchedule($adapter);
       $this->cafeteriaMenuRepo = new CafeteriaMenu($adapter);
       $this->cafeteriaMapRepo = new CafeteriaMap($adapter);
    }
    public function initializeMenuForm(){
        $builder = new AnnotationBuilder();
        $form = new CafeteriaMenuForm();
        $this->cafeteriaMenuForm = $builder->createForm($form);
    }
    public function initializeTimeForm(){
        $builder = new AnnotationBuilder();
        $form = new CafeteriaScheduleForm();
        $this->cafeteriaTimeForm = $builder->createForm($form);
    }
    public function indexAction(){
        return $this->redirect()->toRoute('cafeteriasetup', array(
            'controller' => 'CafeteriaSetupController',
            'action' =>  'menu'
        ));
    }
    public function menuAction(){
        $this->initializeMenuForm();
        $request = $this->getRequest();
        $model = new CafeteriaMenuModel(); 
        if ($request->isPost()) {
            $this->cafeteriaMenuForm->setData($request->getPost());
            if ($this->cafeteriaMenuForm->isValid()) {
                try {
                    $model->exchangeArrayFromForm($this->cafeteriaMenuForm->getData());
                    $model->companyId = 1;
                    $model->status = 'E';
                    $model->createdBy = $this->employeeId;
                    if(!empty($_POST['id'])){
                        $model->id = $_POST['id'];
                        $this->cafeteriaMenuRepo->edit($model, $model->id);
                        $result = $this->cafeteriaMenuRepo->fetchMenuById($model->id);
                        $data = Helper::extractDbData($result);
                        return new JsonModel(['success' => true, 'data' => $data, 'message' => "menu Item edited successfully."]);
                    }
                    else{
                        $model->id = ((int) Helper::getMaxId($this->adapter, CafeteriaMenuModel::TABLE_NAME, CafeteriaMenuModel::MENU_ID)) + 1;
                        $this->cafeteriaMenuRepo->add($model);
                        $result = $this->cafeteriaMenuRepo->fetchMenuById($model->id);
                        $data = Helper::extractDbData($result);
                        return new JsonModel(['success' => true, 'data' => $data, 'message' => "New Item Added to menu successfully."]);
                    }
                } 
                catch (Exception $e) {
                    return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
                }
            }
        }
        
        $result = $this->cafeteriaMenuRepo->fetchMenu();
        $menu = Helper::extractDbData($result);  
        for($i = 0; $i < count($menu); $i++){
            if($menu[$i]['STATUS'] == 'D'){
                $menu[$i] = null;
            }
        }
        $message = count($menu) == 0 ? 'There are no items in the menu. Click above button to add items.' : '' ;
        
        return Helper::addFlashMessagesToArray($this, [
            'menu' => $menu,
            'message' => $message,
            'form' => $this->cafeteriaMenuForm
        ]);
    }
    public function scheduleAction(){
        $this->initializeTimeForm();
        $request = $this->getRequest();
        $model = new CafeteriaScheduleModel(); 
        if ($request->isPost()) {
            $this->cafeteriaTimeForm->setData($request->getPost());
            if ($this->cafeteriaTimeForm->isValid()) {
                try {
                    $model->exchangeArrayFromForm($this->cafeteriaTimeForm->getData());
                    $model->status = 'E';
                    $model->companyId = 1;
                    $model->createdBy = $this->employeeId;
                    if(!empty($_POST['id'])){
                        $model->id = $_POST['id'];
                        $this->cafeteriaTimeRepo->edit($model, $model->id);
                        $result = $this->cafeteriaTimeRepo->fetchScheduleById($model->id);
                        $data = Helper::extractDbData($result);
                        return new JsonModel(['success' => true, 'data' => $data, 'message' => "Time edited successfully."]);
                    }
                    else{
                        $model->id = ((int) Helper::getMaxId($this->adapter, CafeteriaScheduleModel::TABLE_NAME, CafeteriaScheduleModel::TIME_ID)) + 1;
                        $this->cafeteriaTimeRepo->add($model);
                        $result = $this->cafeteriaTimeRepo->fetchScheduleById($model->id);
                        $data = Helper::extractDbData($result);
                        return new JsonModel(['success' => true, 'data' => $data, 'message' => "Time Added to Time successfully."]);
                    }
                } 
                catch (Exception $e) {
                    return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
                }
            }
        }
        
        $result = $this->cafeteriaTimeRepo->fetchSchedules();
        $time = Helper::extractDbData($result);  
        for($i = 0; $i < count($time); $i++){
            if($time[$i]['STATUS'] == 'D'){
                $time[$i] = null;
            }
        }
        $message = count($time) == 0 ? 'There are no items in the menu. Click above button to add items.' : '' ;
        return Helper::addFlashMessagesToArray($this, [
            'time' => $time,
            'message' => $message,
            'form' => $this->cafeteriaTimeForm
        ]);
    }
    
    public function deleteMenuAction(){
        $model = new CafeteriaMenuModel();
        try {
            $model->id = $_POST['menuId'];
            $this->cafeteriaMenuRepo->delete($model->id);
            $this->flashmessenger()->addMessage("Deleted from menu successfully.");
            return $this->redirect()->toRoute('cafeteriasetup', array(
                'controller' => 'CafeteriaSetupController',
                'action' =>  'menu'
            ));
        } 
        catch (Exception $e) {
            $this->flashmessenger()->addMessage("Delete unsuccessfull.");
            return $this->redirect()->toRoute('cafeteriasetup', array(
                'controller' => 'CafeteriaSetupController',
                'action' =>  'menu'
            ));
        }
    }
    public function deleteScheduleAction(){
        $model = new CafeteriaScheduleModel();
        try {
            $model->id = $_POST['timeId'];
            $this->cafeteriaTimeRepo->delete($model->id);
            $this->flashmessenger()->addMessage("Deleted from schedule successfully.");
            return $this->redirect()->toRoute('cafeteriasetup', array(
                'controller' => 'CafeteriaSetupController',
                'action' =>  'schedule'
            ));
        } 
        catch (Exception $e) {
            $this->flashmessenger()->addMessage("Delete unsuccessfull.");
            return $this->redirect()->toRoute('cafeteriasetup', array(
                'controller' => 'CafeteriaSetupController',
                'action' =>  'schedule'
            ));
        }
    }
    
    public function mapAction(){
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $this->cafeteriaMapRepo->mapping($data['menu'], $data['time'], $data['type'], $this->employeeId);
            $this->flashmessenger()->addMessage("Success.");
            return $this->redirect()->toRoute('cafeteriasetup', array(
                'controller' => 'CafeteriaSetupController',
                'action' =>  'map'
            ));
        }
        
        $result = $this->cafeteriaMapRepo->fetchSchedules();
        $timeList = Helper::extractDbData($result);  
        
        $result = $this->cafeteriaMapRepo->fetchMenu();
        $menuList = Helper::extractDbData($result);  
        
        $mapList = []; 
        for($i = 0; $i < count($timeList); $i++){
            $result = $this->cafeteriaMapRepo->fetchMappingDetailsByTime($timeList[$i]['TIME_ID']);
            $mapList[$timeList[$i]['TIME_NAME']] = Helper::extractDbData($result);
        }
        return Helper::addFlashMessagesToArray($this, [
            'menuList' => $menuList,
            'timeList' => $timeList,
            'mapList' => $mapList
        ]);
    }
}