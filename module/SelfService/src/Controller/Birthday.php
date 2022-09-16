<?php

namespace SelfService\Controller;

use Application\Controller\HrisController;
use Application\Helper\Helper;
use Exception;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use SelfService\Model\BirthdayModel;
use SelfService\Form\BirthdayForm;
use SelfService\Repository\BirthdayRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
// use Zend\Mvc\Controller\AbstractActionController;

class Birthday extends HrisController {

    // private $adapter;
    // private $repository;
    // private $employeeId;

    function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new BirthdayRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
        $this->initializeForm(BirthdayForm::class);
    }

    public function indexAction() {
        $birthdays = $this->repository->getBirthdays();
        return Helper::addFlashMessagesToArray($this, [
                    'employeesBirthday' => $birthdays,
                    'currentEmployeeId' => $this->employeeId
        ]);
    }

    public function wishAction() {
        $id = (int) $this->params()->fromRoute("id");
        // if ($id !==0) {
        //     return $this->redirect()->toRoute('birthday');
        // }
        // print_r($id);die;

        
        $empDetails = $this->repository->getBirthdayEmpDet($id);
        $wishEmpDetail = $this->repository->getBirthdayEmpDet($this->employeeId);
        $request = $this->getRequest();
        $birthdayMessage = $this->repository->getBirthdayMessage($id);

        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $id = (int) $this->params()->fromRoute("id");
                $empDetails = $this->repository->getBirthdayEmpDet($id);
                $wishEmpDetail = $this->repository->getBirthdayEmpDet($this->employeeId);


                
                $birthdayModel = new BirthdayModel();
                $birthdayModel->exchangeArrayFromForm($this->form->getData());
                $data = $request->getPost();    //gives all the posted info
                $message = $data['message'];

                $birthdayModel->birthdayId = ((int) Helper::getMaxId($this->adapter, BirthdayModel::TABLE_NAME, BirthdayModel::BIRTHDAY_ID)) + 1;
                $birthdayModel->birthdayDate = $wishEmpDetail[0]['BIRTH_DATE'];
                $birthdayModel->fromEmployee = $this->employeeId;
                $birthdayModel->toEmployee = $id;
                $birthdayModel->createdDt = Helper::getcurrentExpressionDateTime();
                $birthdayModel->status = 'E';



                $this->repository->add($birthdayModel);
                try {
                    HeadNotification::pushNotification(NotificationEvents::BIRTHDAY_WISHED, $birthdayModel, $this->adapter, $this);
                } catch (Exception $e) {
                    
                }

                $this->flashmessenger()->addMessage("Birthday message created sucessfully");
                return $this->redirect()->toRoute("birthday", ['action' => 'wish', 'id' => $id]);
            }
        }

        $messagePosted = $this->repository->checkMessagePosted($this->employeeId, $id);

        $showMessageField = true;
        if ($id == $this->employeeId) {
            $showMessageField = false;
        }
        if ($messagePosted['C'] > 0) {
            $showMessageField = false;
        }

        return Helper::addFlashMessagesToArray($this, [
                    'brithdayEmpDtl' => $empDetails,
                    'wishEmpDetail' => $wishEmpDetail,
                    'birthdayMessage' => $birthdayMessage,
                    'showMessageField' => $showMessageField,
                    'form' => $this->form,
                    'id' => $id
        ]);
    }

}
