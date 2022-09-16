<?php
namespace Recruitment\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Repository\HrisRepository;
use Zend\Db\Adapter\AdapterInterface;
use Application\Model\Model;
use Recruitment\Model\Instruction;
use Symfony\Component\VarDumper\VarDumper;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Expression;
use Recruitment\Model\RecruitmentPersonal;
use Recruitment\Model\RecruitmentVacancy;
use SelfService\Model\ApplicationDocuments;
use Setup\Model\EmployeeFile;
use Recruitment\Model\UserApplicationModel;

class InstructionRepository extends HrisRepository{
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway('HRIS_REC_INSTRUCTIONS',$adapter);
    }

    public function insertTopInstruction($data)
    {
    //   echo '<pre>'; print_r($data); die;
        $this->tableGateway->insert($data); 
    }
    public function checkdata()
    {
        $sql = ("SELECT * from hris_rec_instructions WHERE INSTRUCTION_CODE = 'REC_INSTRUCTION_TOP'");
        $result = $this->rawQuery($sql);
        // var_dump($result); die;
        return $result;
    }
    public function checkLongdata()
    {
        $sql = ("SELECT * from hris_rec_instructions WHERE INSTRUCTION_CODE = 'REC_INSTRUCTION_BUTTOM'");
        $result = $this->rawQuery($sql);
        // var_dump($result); die;
        return $result;
    }
    public function checkName()
    {
        $sql = ("SELECT * from hris_rec_instructions WHERE INSTRUCTION_CODE = 'REC_OFFICE_NAME'");
        $result = $this->rawQuery($sql);
        // var_dump($result); die;
        return $result;
    }
    public function insertButtomInstruction($data)
    {
    //   echo '<pre>'; print_r($data); die;
        $this->tableGateway->insert($data); 
    }
    public function updateInstruction($data, $id)
    {
        $this->tableGateway->update($data, [Instruction::INSTRUCTION_ID => $id]);
    }
    public function getAllData()
    {
        $sql = ("SELECT * from hris_rec_instructions");
        $result = $this->rawQuery($sql);
        // var_dump($result); die;
        return $result;
    }
}   