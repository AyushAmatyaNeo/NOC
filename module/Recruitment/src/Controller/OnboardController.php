<?php
namespace Recruitment\Controller;
use Application\Controller\HrisController;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Authentication\Storage\StorageInterface;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Zend\View\Model\JsonModel;
use Exception;
use Recruitment\Helper\AppHelper;
use Recruitment\Model\StageModel;
use Setup\Model\Designation;
use Setup\Model\Department;
use Recruitment\Model\SkillModel;
use Recruitment\Model\OptionsModel;
use Recruitment\Repository\OnboardRepository;

class OnboardController extends HrisController
{
    function __construct(AdapterInterface $adapter, StorageInterface $storage) {

        parent::__construct($adapter, $storage);
        $this->initializeRepository(OnboardRepository::class);
    }

    public function indexAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $data = $this->repository->getAllOnstageData();
                $listOpen = iterator_to_array($data, false);
                return new JsonModel(['success' => true,'data' => $listOpen, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        // $result = $this->repository->jvTableFlag();
        // $displayJVFlag = Helper::extractDbData($result)[0]['JV_TABLE_FLAG'];
        // $this->acl['JV_FLAG'] = $displayJVFlag;
        return Helper::addFlashMessagesToArray($this, ['acl' => $this->acl]);
    }   
    public function onboardEmployeeAction()
    {
        try {
            $request = $this->getRequest();
            $postedData = $request->getPost();
            foreach ($postedData['id'] as $value) {
                $data = $this->repository->tranferApplicants($value);
            }
            return new JsonModel(['success' => true, 'data' => $data, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }
}
