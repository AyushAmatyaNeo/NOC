<?php
namespace AttendanceManagement\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\HrisQuery;
use AttendanceManagement\Repository\WhereaboutsAssignRepository;
use TheSeer\Tokenizer\Exception;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\View\Model\JsonModel;

class Whereabouts extends HrisController {
    protected $adapter;
    
    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        $this->adapter = $adapter;
        parent::__construct($adapter, $storage);
        $this->initializeRepository(WhereaboutsAssignRepository::class);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $data = $request->getPost();
                $rawList = $this->repository->getEmployeeList($data['data']);
                $list = Helper::extractDbData($rawList);
                return new JsonModel([
                    "success" => true,
                    "data" => $list,
                    "message" => null,
                ]);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
            }
        }

        return $this->stickFlashMessagesTo([
            'searchValues' => EntityHelper::getSearchData($this->adapter),
            'acl' => $this->acl,
            'employeeDetail' => $this->storageData['employee_detail']
        ]);
    }

    public function assignAction() {

        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            $empList=$data['data'];

            foreach($empList as $list){
                $employeeId=$list['employeeId'];
                $updateData = array();
                $updateData['isChecked'] = $list['isChecked'];
                $updateData['orderBy'] = $list['orderBy'];
                $this->repository->updateStatus($employeeId);
                if($updateData['isChecked'] == 'true'){
                $this->repository->updateWhereabouts($employeeId, $updateData);
                }
            }

            return new JsonModel([
                "success" => true,
                "message" => null,
            ]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

}
