<?php

namespace Workforce\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\HrisRepository;
use Application\Repository\RepositoryInterface;
use Workforce\Model\WorkforceModel;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql; 

class WorkforceRepo extends HrisRepository implements RepositoryInterface
{

    protected $tableGateway;
    protected $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway = new TableGateway(WorkforceModel::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    //----------- Fetching data into select field ----------------------------
    // public function fetchSenderOrgs(){
    //     $sql = "select distinct sender_org from DC_REGISTRATION";
    //     return $this->rawQuery($sql);
    // }
    // public function fetchReceivingDept(){
    //     $sql = "select distinct hr.department_name from DC_REGISTRATION dc left join hris_departments hr on (dc.department_id = hr.department_id)";
    //     // $sql = "select distinct department_name from hris_department";
    //     return $this->rawQuery($sql);
    // }
    // public function fetchResponse(){
    //     $sql = "select distinct RESPONSE_FLAG from DC_REGISTRATION";
    //     return $this->rawQuery($sql);
    // }
    //---------------------------------------------------------------------------

    //--------------------------------- Fetching Data into table -----------------------------------------

    public function updateFile($id)
    {

        $sql = "Update dc_registration_docs set reg_draft_id = null where reg_draft_id =$id";
        $statement = $this->adapter->query($sql);
        $file_id = $statement->execute();
    }
    public function add(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id)
    {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [IncommingDocument::REG_DRAFT_ID => $id]);
        $this->linkDCRegistrationWithFiles($id);
    }

    public function fetchAll()
    {
        return $this->tableGateway->select(function (Select $select) {
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(IncommingDocument::class, [IncommingDocument::BRANCH_NAME]), false);
            $select->where([IncommingDocument::STATUS => EntityHelper::STATUS_ENABLED]);
            $select->order([IncommingDocument::BRANCH_WNAME => Select::ORDER_ASCENDING]);
        });
    }

    public function fetchById($id)
    {



        return $this->tableGateway->select(function(Select $select) use($id) {
            $select->columns(Helper::convertColumnDateFormat($this->adapter, new IncommingDocument(), [
                        'registrationDate', 'receivingLetterReferenceDate', 'documentDate'
                    ]), false);
            $select->where([IncommingDocument::REG_DRAFT_ID => $id]);
        })->current();
    }
    

    public function delete($id)
    {
        $this->tableGateway->update([IncommingDocument::STATUS => 'D'], [IncommingDocument::REG_DRAFT_ID => $id]);
    }

    public function getCurrent($position,$group,$service, $designation, $location, $department)
    {
        $positionCondition = "";
        $groupCondition = "";
        $serviceCondition = "";
        $designationCondition = "";
        $locationCondition = "";
        $departmentCondition = "";
        if($position != null && $position != ""){
            $positionCondition = "and position_id = {$position}";
        }
        if($group != null && $group != ""){
            $groupCondition = "and service_group_id = {$group}";
        }
        if($service != null && $service != ""){
            $serviceCondition = "and service_type_id = {$service}";
        }
        if($designation != null && $designation != ""){
            $designationCondition = "and designation_id = {$designation}";
        }
        if($location != null && $location != ""){
            $locationCondition = "and location_id = {$location}";
        }
        if($department != null && $department != "" && $department != -1){
            $departmentCondition = "and department_id = {$department}";
        }

        $sql = "select count(employee_id) as current from hris_employees 
                where 1=1 {$positionCondition} {$groupCondition} {$serviceCondition} {$designationCondition} {$locationCondition} {$departmentCondition}";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result)[0]['CURRENT'];
    }

    public function getPastValues($position,$group,$service, $designation, $location, $department){
        $departmentCondition="";
        if($department != null && $department != "" && $department != -1){
            $departmentCondition = "and department_id = {$department}";
        }
        $sql = "select location_id, department_id, POSITION_ID, service_group_ID, service_type_ID, DESIGNATION_ID, QUOTA from HRIS_WORKFORCE
        where location_id = {$location} {$departmentCondition}";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);

    }

    public function deleteFromTable($branch){
        $sql = "delete from HRIS_WORKFORCE where location_id = {$branch}";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }
    public function deleteFromTableWithDepartment($branch, $department){
        $sql = "delete from HRIS_WORKFORCE where location_id = {$branch} and department_id = {$department}";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    public function getHoWorkForceDataByDepartment($depId){
        $sql = "select
                    department_id,
                    functional_level_id,
                    service_type_id,
                    service_subgroup_id,
                    service_id,
                count(*) as current 
            from hris_employees 
            where location_id = (select
                    location_id 
                from hris_locations 
                where location_code = 'HOBBMH' and status ='E') 
            and department_id = {$depId} 
            and status = 'E' 
            group by department_id,
                    functional_level_id,
                    service_type_id,
                    service_subgroup_id,
                    service_id
            ";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    public function getWorkForceDataByBranch($branchId){
                    $sql = "select
                            branch_id,
                            functional_level_id,
                            service_type_id,
                            service_subgroup_id,
                    service_id,
                        count(*) as current 
                    from hris_employees 
                    where branch_id ={$branchId} 
                    and status = 'E' 
                    group by branch_id,
                            functional_level_id,
                            service_type_id,
                            service_subgroup_id,
                    service_id
                    ";
                    // print_r($sql);die;
                    $statement = $this->adapter->query($sql);
                    $result = $statement->execute();
                    return Helper::extractDbData($result);
    }

    public function getWorkForceDataByLocation($locationId){
                            $sql = "select
                            location_id,
                            functional_level_id,
                            service_type_id,
                            service_subgroup_id,
                    service_id,
                        count(*) as current 
                    from hris_employees 
                    where location_id = {$locationId}
                    and status = 'E' 
                    group by location_id,
                            functional_level_id,
                            service_type_id,
                            service_subgroup_id,
                    service_id
                            ";
            // print_r($sql);die;
            $statement = $this->adapter->query($sql);
            $result = $statement->execute();
            return Helper::extractDbData($result);

    }

    public function getQuotaDataByDepartment($depId){
        $sql = "select
        department_id,functional_level_id, service_type_id, quota,service_id, service_subgroup_id,
       (select count(*) from hris_employees
       where location_id = W.location_id
       and service_type_id = W.service_type_id
       and service_id = W.service_id
       and service_subgroup_id = w.service_subgroup_id 
       and functional_level_id = W.functional_level_id
       and status = 'E'
       and department_id = W.department_id) as current
       from hris_workforce W
       where W.department_id = {$depId}
       and W.location_id = (select
            location_id 
           from hris_locations 
           where location_code='HOBBMH' 
           and status ='E') 
       and W.status ='E'
       union
       select
                        E.department_id,
                        E.functional_level_id,
                        E.service_type_id,
                        ifnull(W.quota,0) as quota,
                        E.service_id,
                        E.service_subgroup_id,
                    count(*) as current 
                from hris_employees E
                left join hris_workforce W on (W.functional_level_id = E.functional_level_id 
                and E.service_type_id = W.service_type_id and W.department_id = E.department_id
                and E.location_id = W.location_id and W.status = 'E')
                and W.service_id = E.service_id
                and W.service_subgroup_id = E.service_subgroup_id 
                where E.location_id = (select
                        location_id 
                    from hris_locations
                    where location_code = 'HOBBMH' and status ='E') 
                and E.department_id = {$depId} 
                and E.status = 'E' 
                group by E.department_id,
                        E.functional_level_id,
                        E.service_type_id,
                       E.service_subgroup_id,
                        E.service_id, W.quota";
        // print_r($sql);die;
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }


    public function addHo(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function deletePastDataByDepartment($depId){
        $sql = "delete from hris_workforce where department_id = {$depId}";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    public function deletePastDataByBranch($branchId){
        $sql = "delete from hris_workforce where branch_id = {$branchId}";
        print_r($sql);die;
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    
    public function deletePastDataByLocation($locationId){
        $sql = "delete from hris_workforce where location_id = {$locationId}";
        // print_r($sql);die;
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    public function getQuotaDataByBranch($branchId){
        $sql = "select
        W.branch_id,
        W.functional_level_id,
        W.service_type_id,
        W.quota,
        W.service_id,
        W.service_subgroup_id,
        F.created_dt,
        (select
        count(*) 
       from hris_employees 
       where branch_id = W.branch_id 
       and service_type_id = W.service_type_id 
       and functional_level_id = W.functional_level_id 
       and service_id = W.service_id 
       and service_subgroup_id = w.service_subgroup_id 
       and status = 'E') as current 
   from hris_workforce W 
   left join hris_functional_levels F on (F.functional_level_id = W.functional_level_id) 
   where W.branch_id = {$branchId} 
   and W.status ='E' 
   union select
        E.BRANCH_id,
        E.functional_level_id,
        E.service_type_id,
        ifnull(W.quota,
       0) as quota,
        E.service_id,
        E.service_subgroup_id,
        F.created_dt,
        count(*) as current 
   from hris_employees E 
   left join hris_workforce W on (W.functional_level_id = E.functional_level_id 
       and E.service_type_id = W.service_type_id 
       and W.branch_id = E.branch_id 
       and E.location_id = W.location_id 
       and W.status = 'E' 
   and W.service_id = E.service_id 
   and W.service_subgroup_id = E.service_subgroup_id ) 
   left join hris_functional_levels F on (F.functional_level_id = E.functional_level_id) 
   where E.branch_id = {$branchId} 
   and E.status = 'E' 
   group by E.branch_id,
        E.functional_level_id,
        E.service_type_id,
        E.service_subgroup_id,
        E.service_id,
        F.created_dt,
        W.quota 
   order by F.created_dt";
        // print_r($sql);die;
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    
    public function getQuotaDataByLocation($locationId){
            $sql = "select
            W.location_id,
            W.functional_level_id,
            W.service_type_id,
            W.quota,
            W.service_id,
            W.service_subgroup_id ,
            F.created_dt,
    (select count(*) from hris_employees
    where location_id = W.location_id
    and service_type_id = W.service_type_id
    and functional_level_id = W.functional_level_id
    and status = 'E'
    and service_id = W.service_id 
	and service_subgroup_id = W.service_subgroup_id 
    and location_id = W.location_id) as current
    from hris_workforce W
   left join hris_functional_levels F on (F.functional_level_id = W.functional_level_id) 
    where W.location_id ={$locationId}
    and W.status ='E'
        union select
            E.location_id,
            E.functional_level_id,
            E.service_type_id,
            ifnull(W.quota,0) as quota,
            E.service_id,
            E.service_subgroup_id,
            F.created_dt,
            count(*) as current 
        from hris_employees E 
        left join hris_workforce W on (W.functional_level_id = E.functional_level_id 
            and E.service_type_id = W.service_type_id 
            and E.location_id = W.location_id 
            and W.status = 'E' 
        and W.service_id = E.service_id 
        and W.service_subgroup_id = E.service_subgroup_id )
   left join hris_functional_levels F on (F.functional_level_id = E.functional_level_id) 
        where E.location_id = {$locationId} 
        and E.status = 'E' 
        group by E.location_id,
            E.functional_level_id,
            E.service_type_id,
            E.service_subgroup_id,
            E.service_id,
            W.quota,F.created_dt
            order by F.created_dt";
        // print_r($sql);die;
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }


    public function addBranchOffice(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function addLocationOffice(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }


}
