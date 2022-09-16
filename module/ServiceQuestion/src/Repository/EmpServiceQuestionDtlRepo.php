<?php

namespace ServiceQuestion\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use ServiceQuestion\Model\EmpServiceQuestionDtl;

class EmpServiceQuestionDtlRepo implements RepositoryInterface
{
    private $tableGateway;
    private $adapter;
    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->tableGateway=new TableGateway(EmpServiceQuestionDtl::TABLE_NAME,$adapter);
    }

    public function add(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model,$id)
    {
        $array=$model->getArrayCopyForDB();
        $this->tableGateway->update( $array,[EmpServiceQuestionDtl::EMP_QA_ID=>$id['empQaId'],EmpServiceQuestionDtl::QA_ID=>$id['qaId']]);
    }

    public function fetchAll()
    {
        return $this->tableGateway->select();
    }

    public function fetchById($id)
    {
        $rowset= $this->tableGateway->select([EmpServiceQuestionDtl::EMP_QA_ID=>$id['empQaId'],EmpServiceQuestionDtl::QA_ID=>$id['qaId']]);
        return $rowset->current();
    }
    public function fetchActiveRecord()
    {
        return  $rowset= $this->tableGateway->select([EmpServiceQuestionDtl::STATUS=>'E']);
    }

    public function delete($id)
    {
        $this->tableGateway->update([EmpServiceQuestionDtl::STATUS=>'D'],[EmpServiceQuestionDtl::EMP_QA_ID=>$id['empQaId'],EmpServiceQuestionDtl::QA_ID=>$id['qaId']]);
    }
    public function fetchByEmpQaIdQaId($qaId,$empQaId){
        $rowset= $this->tableGateway->select([EmpServiceQuestionDtl::EMP_QA_ID=>$empQaId,EmpServiceQuestionDtl::QA_ID=>$qaId, EmpServiceQuestionDtl::STATUS=>'E']);
        return $rowset->current();
    }
}