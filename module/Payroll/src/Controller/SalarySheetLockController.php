<?php
namespace Payroll\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Repository\MonthRepository;
use Exception;
use Payroll\Repository\PayrollRepository;
use Payroll\Repository\RulesRepository;
use Payroll\Repository\SalarySheetRepo;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;

class SalarySheetLockController extends HrisController {

    private $salarySheetRepo;

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        //$this->initializeRepository(PayrollRepository::class);
        $this->salarySheetRepo = new SalarySheetRepo($adapter);
    }

    public function indexAction(){
        $ruleRepo = new RulesRepository($this->adapter);
        $data['salaryType'] = iterator_to_array($this->salarySheetRepo->fetchAllSalaryType(), false);
        $data['ruleList'] = iterator_to_array($ruleRepo->fetchAll(), false);
        $data['salarySheetList'] = iterator_to_array($this->salarySheetRepo->fetchAll(), false);
        $links['viewLink'] = $this->url()->fromRoute('salarySheet', ['action' => 'viewSalarySheet']);
        $links['getSearchDataLink'] = $this->url()->fromRoute('salarySheet', ['action' => 'getSearchData']);
        $links['getGroupListLink'] = $this->url()->fromRoute('salarySheet', ['action' => 'getGroupList']);
        $links['regenEmpSalSheLink'] = $this->url()->fromRoute('salarySheet', ['action' => 'regenEmpSalShe']);
        $data['links'] = $links;
        return $this->stickFlashMessagesTo(['data' => json_encode($data)]);
    }

    public function pullGroupEmployeeAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            $group=$data['group'];
            $monthId=$data['monthId'];
            $salaryTypeId=$data['salaryTypeId'];
            
            $valuesinCSV = "";
            for ($i = 0; $i < sizeof($group); $i++) {
                $value= $group[$i];
//                $value = isString ? "'{$group[$i]}'" : $group[$i];
                if ($i + 1 == sizeof($group)) {
                    $valuesinCSV .= "{$value}";
                } else {
                    $valuesinCSV .= "{$value},";
                }
            }
            
            $employeeList=$this->salarySheetRepo->fetchEmployeeByGroup($monthId,$valuesinCSV,$salaryTypeId, null);
            $sheetList=$this->salarySheetRepo->fetchGeneratedSheetByGroup($monthId,$valuesinCSV,$salaryTypeId);

            return new JsonModel(['success' => true, 'data' => $employeeList, 'sheetData' => $sheetList, 'message' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

    public function bulkApproveLockAction(){
        $data = $_POST['data'];
        $action = $_POST['action'];
        $col = null; $val = null;
        if($action == 'A'){ $col = 'APPROVED'; $val = 'Y'; }
        if($action == 'NA'){ $col = 'APPROVED'; $val = 'N'; }
        if($action == 'L'){ $col = 'LOCKED'; $val = 'Y'; }
        if($action == 'UL'){ $col = 'LOCKED'; $val = 'N'; }
        foreach ($data as $key) {
            $checkData = $this->salarySheetRepo->checkApproveLock($key);
            if($checkData[0]['LOCKED'] == 'Y'){ continue; }
            $this->salarySheetRepo->bulkApproveLock($key, $col, $val);
        }
        return new JSONModel(['success' => true]);
    }
	
	public function generateVoucherAction()
    {
        $data = $_POST['data'];
        print_r($data);die;
    }
}
