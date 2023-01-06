<?php

namespace Recruitment\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Recruitment\Form\VenueAssignForm;
use Recruitment\Form\VenueSetupForm;
use Recruitment\Model\VenueAssignModel;
use Recruitment\Model\VenueSetupModel;
use Recruitment\Repository\VenueRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class VenueController extends HrisController {
    
    private $assignForm;

    function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(VenueRepository::class);
        $this->initializeForm(VenueSetupForm::class);

        // Initialize assign form
        $builder = new AnnotationBuilder();
        $this->assignForm = $builder->createForm(new VenueAssignForm);
    }


    /**
     * Venue Setup Functions
     */

    public function venueSetupAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) 
        {
            // Get data from database
            $list = iterator_to_array($this->repository->allVenue(), false);

            return new JsonModel(['success' => true, 'data' => $this->decodeBase64List($list, 'VENUE_NAME'), 'error' => '']);
        }
    }

    public function venueSetupAddAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) 
        {

            $postData = $request->getPost();

            $this->form->setData($postData);

            if ($this->form->isValid()) {

                $venueData = new VenueSetupModel();
                $venueData->exchangeArrayFromForm($this->form->getData());
                $venueData->venueName = base64_encode($venueData->venueName);
                $venueData->venueSetupId = ((int) Helper::getMaxId($this->adapter, VenueSetupModel::TABLE_NAME, VenueSetupModel::VENUE_SETUP_ID)) + 1;
                $venueData->status = 'E';
                $venueData->createdBy = $this->employeeId;
                $venueData->createdDate = Helper::getcurrentExpressionDate();

                $this->repository->addVenue($venueData);
                $this->flashmessenger()->addMessage("Venue Successfully added!!");
                return $this->redirect()->toRoute("venue", array("action"=>"venueSetup"));

            }

        }

        return new ViewModel(Helper::addFlashMessagesToArray(
                $this, [
                    'form' => $this->form,
                ]
            )
        );
    }

    public function venueSetupEditAction()
    {
        $request = $this->getRequest();

        $id = (int) $this->params()->fromRoute('id');

        if ($id === 0)
        {
            return $this->redirect()->toRoute("venue", array("action"=>"venueSetup"));
        }

        if ($request->isPost())
        {

            $venueData = new VenueSetupModel();
            $postData = $request->getPost();

            $this->form->setData($postData);

            if ($this->form->isValid())
            {
                $venueData->exchangeArrayFromForm($this->form->getData());
                $venueData->venueName = base64_encode($venueData->venueName);
                $venueData->modifiedDate = Helper::getcurrentExpressionDate();

                $this->repository->editVenue($venueData, $id);

                $this->flashmessenger()->addMessage("Venue Edited Successfully!");
                return $this->redirect()->toRoute("venue", array("action"=>"venueSetup"));

            }
        }

        $data = iterator_to_array($this->repository->fetchVenueById($id));

        $model = new VenueSetupModel();
        $model->exchangeArrayFromDB($data);
        $model->venueName = base64_decode($model->venueName);
        $this->form->bind($model);

        return Helper::addFlashMessagesToArray($this, [
            'form' => $this->form,
            'id' => $id
        ]);
    }


    /**
     * 
     * Venue Assign Functions
     * 
     */

    public function venueAssignAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) 
        {
            // Get data from database
            $list = iterator_to_array($this->repository->allVenueAssign(), false);

            return new JsonModel(['success' => true, 'data' => $this->decodeBase64List($list, 'VENUE_NAME'), 'error' => '']);
        }
    }

    public function venueAssignAddAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) 
        {

            $postData = (array) $request->getPost();

            $this->assignForm->setData($postData);

            if ($this->assignForm->isValid()) {

                // Validate start index and end index
                if($message = $this->assignValidation($postData))
                {
                    $this->flashmessenger()->addMessage($message);
                    return $this->redirect()->toRoute("venue", array("action"=>"venueAssignAdd"));
                }

                // Insert new venue assign model to table
                $venueAssignData = new VenueAssignModel();
                $venueAssignData->exchangeArrayFromForm($this->assignForm->getData());
                $venueAssignData->venueAssignId = ((int) Helper::getMaxId($this->adapter, VenueAssignModel::TABLE_NAME, VenueAssignModel::VENUE_ASSIGN_ID)) + 1;
                $venueAssignData->createdBy = $this->employeeId;
                $venueAssignData->createdDate = Helper::getcurrentExpressionDate();
                $venueAssignData->status = 'E';

                $vacancyIds = implode(',', $postData['vacancies']);
                $venueAssignData->vacancyIds = $vacancyIds;

                $this->repository->addVenueAssign($venueAssignData);

                // Update each individual's application data
                $this->repository->updateUserVenueData($vacancyIds, $this->assignForm->getData()['examType'].'_VENUE_ID', $venueAssignData->venueAssignId);

                $this->flashmessenger()->addMessage("Venue Successfully Assigned!!");
                return $this->redirect()->toRoute("venue", array("action"=>"venueAssign"));

            }

        }

        $venueList = EntityHelper::getTableKVListWithSortOption($this->adapter, VenueSetupModel::TABLE_NAME, VenueSetupModel::VENUE_SETUP_ID, [VenueSetupModel::VENUE_NAME], ["STATUS" => "E"], VenueSetupModel::VENUE_NAME, "ASC", null, [null => '---'], true);

        return new ViewModel(Helper::addFlashMessagesToArray(
                $this, [
                    'form' => $this->assignForm,
                    'venueList' => $this->decodeBase64List($venueList, null, 1),
                    'Adno' => EntityHelper::getTableList($this->adapter, 'HRIS_REC_VACANCY', ['VACANCY_ID','AD_NO'], ['STATUS' => 'E'],'',"TO_INT(SUBSTR_BEFORE(AD_NO,'/'))", 'ASC'),
                ]
            )
        );
    }

    public function venueAssignEditAction()
    {
        $request = $this->getRequest();

        $id = (int) $this->params()->fromRoute('id');

        if ($id === 0)
        {
            return $this->redirect()->toRoute("venue", array("action"=>"venueAssign"));
        }
        
        if ($request->isPost()) 
        {
            $postData = $request->getPost();

            $this->assignForm->setData($postData);

            if ($this->assignForm->isValid()) {

                // Validate start index and end index
                if($message = $this->assignValidation($postData))
                {
                    $this->flashmessenger()->addMessage($message);
                    return $this->redirect()->toRoute("venue", array("action"=>"venueAssignEdit", "id"=>$id));
                }

                // Revert previous data in vacancy application table
                $prevData = iterator_to_array($this->repository->fetchVenueAssignById($id));
                //$this->repository->updateUserVenueData($prevData['START_INDEX'], $prevData['END_INDEX'], $prevData['EXAM_TYPE'].'_VENUE_ID', $prevData['ASSIGN_TYPE'], 'null');
                $this->repository->updateUserVenueData($prevData['VACANCY_IDS'], $prevData['EXAM_TYPE'].'_VENUE_ID', 'null');

                // Update actual assign model
                $venueAssignData = new VenueAssignModel();
                $venueAssignData->exchangeArrayFromForm($this->assignForm->getData());
                $venueAssignData->modifiedDate = Helper::getcurrentExpressionDate();

                $vacancyIds = implode(',', $postData['vacancies']);
                $venueAssignData->vacancyIds = $vacancyIds;

                $this->repository->editVenueAssign($venueAssignData, $id);

                // Update each individual's application data
                $this->repository->updateUserVenueData($vacancyIds, $this->assignForm->getData()['examType'].'_VENUE_ID', $venueAssignData->venueAssignId);

                $this->flashmessenger()->addMessage("Venue Assign Edited Successfully!");
                return $this->redirect()->toRoute("venue", array("action"=>"venueAssign"));
            }

        }

        $data = iterator_to_array($this->repository->fetchVenueAssignById($id));

        $model = new VenueAssignModel();
        $model->exchangeArrayFromDB($data);
        $this->assignForm->bind($model);

        $venueList = EntityHelper::getTableKVListWithSortOption($this->adapter, VenueSetupModel::TABLE_NAME, VenueSetupModel::VENUE_SETUP_ID, [VenueSetupModel::VENUE_NAME], ["STATUS" => "E"], VenueSetupModel::VENUE_NAME, "ASC", null, [null => '---'], true);

        $selectedArray = explode(',', $model->vacancyIds);

        return Helper::addFlashMessagesToArray($this, [
            'form' => $this->assignForm,
            'id' => $id,
            'venueList' => $this->decodeBase64List($venueList, null, 1),
            'Adno' => EntityHelper::getTableList($this->adapter, 'HRIS_REC_VACANCY', ['VACANCY_ID','AD_NO'], ['STATUS' => 'E'],'',"TO_INT(SUBSTR_BEFORE(AD_NO,'/'))", 'ASC'),
            'selectedArray' => $selectedArray,
        ]);
    }

    private function assignValidation($postData)
    {
        if($postData['startIndex'] > $postData['endIndex']){
            return "Start Index cannot be greater than end index";
        }

        // TODO: Validate start time and end time

        return null;
    }

    // Start at variable is used to by pass certain useless data at beginning index
    private function decodeBase64List($list, $index=null, $startAt=0)
    {
        // decode all names from database
        for($i=$startAt; $i<count($list); $i++)
        {
            if($index){
                
                $list[$i][$index] = base64_decode($list[$i][$index]);
            }else{
                $list[$i] = base64_decode($list[$i]);
            }
        }

        return $list;
    }
}
