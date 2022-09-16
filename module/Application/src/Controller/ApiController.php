<?php

namespace Application\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Repository\ApiRepository;
use Exception;
use Setup\Model\Branch;
use Setup\Model\Company;
use Setup\Model\Department;
use Setup\Model\Designation;
use Setup\Model\District;
use Setup\Model\Gender;
use Setup\Model\HrEmployees;
use Setup\Model\Position;
use Setup\Model\ServiceType;
use Setup\Model\Zones;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractRestfulController;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ApiController extends AbstractRestfulController {

    private $adapter;
    private $repository;

    public function __construct(AdapterInterface $adapter) {
		die();
        $this->adapter = $adapter;
        $this->repository = new ApiRepository($adapter);
    }

    public function indexAction() {
        print "Welcome";
        exit;
    }

    public function employeeAction() {
        try {
//            throw new Exception("test");
            $request = $this->getRequest();
            $requestType = $request->getMethod();
            $data = [];

            switch ($requestType) {
                case Request::METHOD_POST:
                    $postData = $request->getPost();
                    $data = $this->addEmployee($postData->getArrayCopy());
//                    http_response_code(201);
                    break;

                case Request::METHOD_GET:
                    $id = $this->params()->fromRoute('id');
                    $data = $this->fetchemployeeList($id);
                    break;

                case Request::METHOD_PUT:
                    $id = $this->params()->fromRoute('id');
                    if ($id == 0 || $id == NULL) {
                        throw new Exception('id cannot be null or zero');
                    }
                    $editData = array();
                    parse_str($request->getContent(), $editData);
                    $data = $this->editEmployee($editData, $id);
                    break;

                case Request::METHOD_DELETE:
                    $id = $this->params()->fromRoute('id');
                    if ($id == 0 || $id == NULL) {
                        throw new Exception('id cannot be null or zero');
                    }
                    $data = $this->deleteEmployee($id);
                    break;

                default:
                    throw new Exception('the request  is unknown');
            }
            return new CustomViewModel($data);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'error' => $e->getMessage()]);
//            return new CustomViewModel($e->getMessage());
        }
    }
                
    public function setupAction() {

        try {

            $request = $this->getRequest();
            $requestType = $request->getMethod();
            $data = [];

            if ($requestType == Request::METHOD_GET) {
                $name = $this->params()->fromRoute('id');
                if ($name == null) {
                    $data = $this->fetchAllSetup();
                } else {
                    switch ($name) {
                        case 'company':
                            $data = $this->fetchAllCompany();
                            break;

                        case 'gender':
                            $data = $this->fetchAllGender();
                            break;

                        case 'religion':
                            $data = $this->fetchAllReligion();
                            break;

                        case 'bloodGroup':
                            $data = $this->fetchAllBloodGroup();
                            break;

                        case 'country':
                            $data = $this->fetchAllCountry();
                            break;

                        case 'district':
                            $data = $this->fetchAllDistrict();
                            break;

                        case 'zone':
                            $data = $this->fetchAllZone();
                            break;

                        case 'branch':
                            $data = $this->fetchAllbranch();
                            break;

                        case 'department':
                            $data = $this->fetchAllDepartment();
                            break;

                        case 'designation':
                            $data = $this->fetchAllDesignation();
                            break;

                        case 'position':
                            $data = $this->fetchAllPosition();
                            break;

                        case 'serviceType':
                            $data = $this->fetchAllServiceType();
                            break;

                        default:
                            throw new Exception('parameter is unkown');
                    }
                }
            } else {
                throw new Exception('the request  is unknown');
            }

            return new CustomViewModel($data);
        } catch (Exception $e) {
            return new CustomViewModel(['sucess' => false, 'error' => $e->getMessage()]);
        }
    }

    public function fetchemployeeList($id) {
        $data = $this->repository->fetchAllEmployee($id);
        return $data;
    }

    public function addEmployee($postData) {

        $employeeModel = new HrEmployees();

        $employeeModel->exchangeArrayFromDB($postData);

        $employeeModel->employeeId = ((int) Helper::getMaxId($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID")) + 1;
        $employeeModel->status = 'E';
        $employeeModel->createdDt = Helper::getcurrentExpressionDate();
//        $employeeModel->addrPermCountryId = 168;
//        $employeeModel->addrTempCountryId = 168;

        $returnData = $this->repository->add($employeeModel);

        return $returnData;
    }

    public function deleteEmployee($id) {
        $returnData = $this->repository->delete($id);
        return $returnData;
    }

    public function editEmployee($editData, $id) {

        $employeeModel = new HrEmployees();
        $employeeModel->exchangeArrayFromDB($editData);
        $employeeModel->modifiedDt = Helper::getcurrentExpressionDate();
        $returnData = $this->repository->edit($employeeModel, $id);
        return $returnData;
    }

    //setup functions

    public function fetchAllCompany() {
        try {
            $company = EntityHelper::getTableList($this->adapter, Company::TABLE_NAME, [Company::COMPANY_ID, Company::COMPANY_NAME]);
            return ['success' => true, 'data' => $company];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function fetchAllGender() {
        try {
            $gender = EntityHelper::getTableList($this->adapter, Gender::TABLE_NAME, [Gender::GENDER_ID, Gender::GENDER_NAME]);
            return ['success' => true, 'data' => $gender];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function fetchAllReligion() {
        try {
            $religion = EntityHelper::getTableList($this->adapter, 'HRIS_RELIGIONS', ['RELIGION_ID', 'RELIGION_NAME']);
            return ['success' => true, 'data' => $religion];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function fetchAllBloodGroup() {
        try {
            $bloodGroup = EntityHelper::getTableList($this->adapter, 'HRIS_BLOOD_GROUPS', ['BLOOD_GROUP_ID', 'BLOOD_GROUP_CODE']);
            return ['success' => true, 'data' => $bloodGroup];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function fetchAllCountry() {
        try {
            $country = EntityHelper::getTableList($this->adapter, 'HRIS_COUNTRIES', ['COUNTRY_ID', 'COUNTRY_NAME']);
            return ['success' => true, 'data' => $country];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function fetchAllDistrict() {
        try {
            $district = EntityHelper::getTableList($this->adapter, District::TABLE_NAME, [District::DISTRICT_ID, District::DISTRICT_NAME, District::ZONE_ID]);
            return ['success' => true, 'data' => $district];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function fetchAllZone() {
        try {
            $zone = EntityHelper::getTableList($this->adapter, Zones::TABLE_NAME, [Zones::ZONE_ID, Zones::ZONE_NAME]);
            return ['success' => true, 'data' => $zone];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function fetchAllbranch() {
        try {
            $branch = EntityHelper::getTableList($this->adapter, Branch::TABLE_NAME, [Branch::BRANCH_ID, Branch::BRANCH_NAME]);
            return ['success' => true, 'data' => $branch];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function fetchAllDepartment() {
        try {
            $department = EntityHelper::getTableList($this->adapter, Department::TABLE_NAME, [Department::DEPARTMENT_ID, Department::DEPARTMENT_NAME]);
            return ['success' => true, 'data' => $department];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function fetchAllDesignation() {
        try {
            $designation = EntityHelper::getTableList($this->adapter, Designation::TABLE_NAME, [Designation::DESIGNATION_ID, Designation::DESIGNATION_TITLE]);
            return ['success' => true, 'data' => $designation];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function fetchAllPosition() {
        try {
            $position = EntityHelper::getTableList($this->adapter, Position::TABLE_NAME, [Position::POSITION_ID, Position::POSITION_NAME, Position::LEVEL_NO]);
            return ['success' => true, 'data' => $position];
        } catch (Exception $e) {
            return ['success' => true, 'data' => $position];
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function fetchAllServiceType() {
        try {
            $serviceType = EntityHelper::getTableList($this->adapter, ServiceType::TABLE_NAME, [ServiceType::SERVICE_TYPE_ID, ServiceType::SERVICE_TYPE_NAME]);
            return ['success' => true, 'data' => $serviceType];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function fetchAllSetup() {
        try {
            $allSetup = [
                'company' => EntityHelper::getTableList($this->adapter, Company::TABLE_NAME, [Company::COMPANY_ID, Company::COMPANY_NAME]),
                'gender' => EntityHelper::getTableList($this->adapter, Gender::TABLE_NAME, [Gender::GENDER_ID, Gender::GENDER_NAME]),
                'religion' => EntityHelper::getTableList($this->adapter, 'HRIS_RELIGIONS', ['RELIGION_ID', 'RELIGION_NAME']),
                'bloodGroup' => EntityHelper::getTableList($this->adapter, 'HRIS_BLOOD_GROUPS', ['BLOOD_GROUP_ID', 'BLOOD_GROUP_CODE']),
                'country' => EntityHelper::getTableList($this->adapter, 'HRIS_COUNTRIES', ['COUNTRY_ID', 'COUNTRY_NAME']),
                'district' => EntityHelper::getTableList($this->adapter, District::TABLE_NAME, [District::DISTRICT_ID, District::DISTRICT_NAME, District::ZONE_ID]),
                'zone' => EntityHelper::getTableList($this->adapter, Zones::TABLE_NAME, [Zones::ZONE_ID, Zones::ZONE_NAME]),
                'branch' => EntityHelper::getTableList($this->adapter, Branch::TABLE_NAME, [Branch::BRANCH_ID, Branch::BRANCH_NAME]),
                'department' => EntityHelper::getTableList($this->adapter, Department::TABLE_NAME, [Department::DEPARTMENT_ID, Department::DEPARTMENT_NAME]),
                'designation' => EntityHelper::getTableList($this->adapter, Designation::TABLE_NAME, [Designation::DESIGNATION_ID, Designation::DESIGNATION_TITLE]),
                'position' => EntityHelper::getTableList($this->adapter, Position::TABLE_NAME, [Position::POSITION_ID, Position::POSITION_NAME, Position::LEVEL_NO]),
                'serviceType' => EntityHelper::getTableList($this->adapter, ServiceType::TABLE_NAME, [ServiceType::SERVICE_TYPE_ID, ServiceType::SERVICE_TYPE_NAME]),
            ];


            return ['success' => true, 'data' => $allSetup];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

}
