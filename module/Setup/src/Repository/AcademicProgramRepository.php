<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/11/16
 * Time: 10:38 AM
 */
namespace Setup\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Setup\Model\AcademicProgram;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class AcademicProgramRepository implements RepositoryInterface {

    private $tableGateway;

    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway=new TableGateway(AcademicProgram::TABLE_NAME,$adapter);
    }

    public function add(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model,$id)
    {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array,[AcademicProgram::ACADEMIC_PROGRAM_ID=>$id]);
    }

    public function fetchAll()
    {
        return $this->tableGateway->select(function(Select $select){
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(AcademicProgram::class, [AcademicProgram::ACADEMIC_PROGRAM_NAME]), false);
            $select->where([AcademicProgram::STATUS=>'E']);
            $select->order(AcademicProgram::ACADEMIC_PROGRAM_NAME);
        });
    }


    public function fetchById($id)
    {
        $rowset= $this->tableGateway->select(function(Select $select)use($id){
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(AcademicProgram::class, [AcademicProgram::ACADEMIC_PROGRAM_NAME]), false);
            $select->where([AcademicProgram::ACADEMIC_PROGRAM_ID=>$id]);
        });
        return $rowset->current();
    }

    public function delete($id)
    {
        $this->tableGateway->update([AcademicProgram::STATUS=>'D'],[AcademicProgram::ACADEMIC_PROGRAM_ID=>$id]);
    }
}