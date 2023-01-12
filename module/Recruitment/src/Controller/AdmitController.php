<?php
namespace Recruitment\Controller;

use Application\Controller\HrisController;
use Recruitment\Form\AdmitForm;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Authentication\Storage\StorageInterface;
use Application\Helper\Helper;
use Zend\View\Model\ViewModel;
use Recruitment\Model\AdmitModel;
use Recruitment\Repository\AdmitRepository;
use Application\Helper\EntityHelper;
use Zend\View\Model\JsonModel;
use Exception;

Class AdmitController extends HrisController{

    function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(AdmitRepository::class);
        $this->initializeForm(AdmitForm::class);
        
    }
    public function indexAction(){

        $request = $this->getRequest();
        $detail = $this->repository->fetch();
        $model  = new AdmitModel();

        $model->exchangeArrayFromDB($detail);
        $model->DeclarationText = base64_decode($detail['DECLARATION_TEXT']);
        $model->Terms           = base64_decode($detail['TERMS']);

        /**
         * GET FILE AND ID
         * */
        $file = $model->FileName;
        $id   = $model->AdmitSetupId;

        // echo Helper::UPLOAD_DIR; die;
        
        $this->form->bind($model);

        return new ViewModel(Helper::addFlashMessagesToArray(
                $this, [
                    'form' => $this->form,
                    // 'data' => $data,
                    'prevFile'=>$file,
                    'prevId' => $id
                ]
            )
        ); 
    }

    public function updateAction() {

        $request = $this->getRequest();

        if ($request->isPost()) {

            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );


            // echo "<pre>";
            // print_r($post);
            // echo Helper::UPLOAD_DIR;
            // die;

            if ($post['File']['error'] == 0) {

                /* create new name file */
                $filename     = uniqid() . "-" . time();
                $extension    = pathinfo( $post["File"]["name"], PATHINFO_EXTENSION ); // jpg

                $allow_extension = ['jpg','png','jpeg'];
                
                if (!in_array($extension, $allow_extension)) {
                    
                    $this->flashmessenger()->addMessage("{$extension} not accepted, Please upload jpg/jpeg/png types extensions!!!");
                    return $this->redirect()->toRoute("admit");
                
                }

                $basename     = $filename . "." . $extension; // 5dab1961e93a7_1571494241.jpg
                $source       = $post['File']["tmp_name"];
                $destination  = Helper::UPLOAD_DIR."/admit/{$basename}";
                $url          = $post['base_path']."/uploads/admit/{$basename}";

                /**
                 * UPLOADING IMAGES
                 * */
                $success      =  move_uploaded_file( $source, $destination);

                if (!empty($post['prev_image'])) {

                    $path  =  Helper::UPLOAD_DIR."/admit/".$prev_image;

                    if (file_exists($path)) {

                        unlink($path);

                    }

                }


                if ($success) {

                    if ($post['AdmitSetupId'] == 0) {

                        $admit_data = new AdmitModel();
                        $admit_data->exchangeArrayFromForm($post);
                        $admit_data->AdmitSetupId = ((int) Helper::getMaxId($this->adapter, AdmitModel::TABLE_NAME, AdmitModel::ADMIT_SETUP_ID)) + 1;
                        $admit_data->DeclarationText = base64_encode($post['DeclarationText']);
                        $admit_data->Terms = base64_encode($post['Terms']);
                        $admit_data->FileName  = $basename;
                        $admit_data->CreatedBy = $this->employeeId;
                        $admit_data->CreatedDt = Helper::getcurrentExpressionDate();
                        $admit_data->Status = 'E'; 
                        $admit_data->url = $url;
                        $this->repository->add($admit_data);

                    } else {

                        $admit_data = new AdmitModel();
                        $admit_data->exchangeArrayFromForm($post);
                        $admit_data->DeclarationText = base64_encode($post['DeclarationText']);
                        $admit_data->Terms = base64_encode($post['Terms']);
                        $admit_data->FileName  = $basename;
                        $admit_data->ModifiedBy = $this->employeeId;
                        $admit_data->ModifiedDt = Helper::getcurrentExpressionDate();
                        $admit_data->url = $url;

                        $this->repository->edit($admit_data, $post['AdmitSetupId']);

                    }
                    

                    $this->flashmessenger()->addMessage("Successfully Updated!!!");
                    return $this->redirect()->toRoute("admit");

                } else {

                    $this->flashmessenger()->addMessage("Unsuccessful Due to fail form validation!!!");
                        return $this->redirect()->toRoute("admit");

                }

            } else {

                $admit_data = new AdmitModel();
                $admit_data->exchangeArrayFromForm($post);
                $admit_data->DeclarationText = base64_encode($post['DeclarationText']);
                $admit_data->Terms = base64_encode($post['Terms']);
                $admit_data->ModifiedBy = $this->employeeId;
                $admit_data->ModifiedDt = Helper::getcurrentExpressionDate();

                $this->repository->edit($admit_data, $post['AdmitSetupId']);

                $this->flashmessenger()->addMessage("Successfully Updated!!!");
                    return $this->redirect()->toRoute("admit");

            }

        }
        

    }
    
}