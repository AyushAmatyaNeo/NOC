<?php
namespace Recruitment\Repository;

use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\HrisRepository;
use Recruitment\Model\UserApplicationModel;
use Recruitment\Model\VenueAssignModel;
use Recruitment\Model\VenueSetupModel;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class VenueRepository extends HrisRepository{

    // Table gateway for venue assign table
    private $assignGateway;
    private $vacancyTable;

    public function __construct(AdapterInterface $adapter, $tableName = null) {
        parent::__construct($adapter, VenueSetupModel::TABLE_NAME);

        $this->assignGateway = new TableGateway(VenueAssignModel::TABLE_NAME, $adapter);
        $this->vacancyTable = UserApplicationModel::TABLE_NAME;
    }

    /**
     * Venue Setup functions
     */
    public function addVenue($data)
    {
        $array = $data->getArrayCopyForDb();
        $this->tableGateway->insert($array);
    }

    public function allVenue()
    {
        return $this->tableGateway->select();
    }

    public function fetchVenueById($id)
    {
        return $this->tableGateway->select([VenueSetupModel::VENUE_SETUP_ID=>$id])->current();
    }

    public function editVenue(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [VenueSetupModel::VENUE_SETUP_ID => $id]);
    }

    /**
     * Venue Assign functions
     */

    public function addVenueAssign($data)
    {
        $array = $data->getArrayCopyForDb();
        $this->assignGateway->insert($array);
    }

    public function updateUserVenueData($vacancyIds, $column, $venueId)
    {
        $sql = "
            UPDATE {$this->vacancyTable}
            SET {$column} = {$venueId}
            WHERE ad_no IN ({$vacancyIds})
            and stage_id = 8 and payment_paid = 'Y' and payment_verified = 'Y'
        ";

        $this->adapter->query($sql)->execute();
    }

    public function allVenueAssign()
    {
        //return $this->assignGateway->select();

        $sql = new Sql($this->adapter);
        $select = $sql->select();

        $select->columns([
            new Expression("VENUE.VENUE_NAME AS VENUE_NAME"),
            new Expression("ASSIGN.EXAM_TYPE AS EXAM_TYPE"),
            new Expression("ASSIGN.START_TIME AS START_TIME"),
            new Expression("ASSIGN.END_TIME AS END_TIME"),
            new Expression("ASSIGN.EXAM_DATE AS EXAM_DATE"),
            new Expression("ASSIGN.STATUS AS STATUS"),
            new Expression("ASSIGN.VENUE_ASSIGN_ID AS VENUE_ASSIGN_ID"),
            new Expression("GET_VACANCY_ASSIGN(ASSIGN.VENUE_ASSIGN_ID) AS ASSIGNED_VACANCIES"),
        ], true);

        $select->from(['ASSIGN' => VenueAssignModel::TABLE_NAME])
               ->join(['VENUE' => VenueSetupModel::TABLE_NAME], 'VENUE.VENUE_SETUP_ID=ASSIGN.VENUE_SETUP_ID', 'STATUS', 'left');
        
        $boundedParameter = [];
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute($boundedParameter);
        return $result;

    }

    public function fetchVenueAssignById($id)
    {
        return $this->assignGateway->select([VenueAssignModel::VENUE_ASSIGN_ID=>$id])->current();
    }

    public function editVenueAssign(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        $this->assignGateway->update($array, [VenueAssignModel::VENUE_ASSIGN_ID => $id]);
    }

}