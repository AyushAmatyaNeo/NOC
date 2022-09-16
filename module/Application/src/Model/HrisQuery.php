<?php
namespace Application\Model;

use Zend\Db\Sql\Predicate\Predicate;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class HrisQuery {

    private $adapter;
    private $tableName;
    private $columnList;
    private $where;
    private $order;
    private $key;
    private $value;
    private $includeEmptyRow = false;
    private $predicate = Predicate::OP_AND;

    public static function singleton() {
        return new HrisQuery();
    }

    function getAdapter() {
        return $this->adapter;
    }

    function getTableName() {
        return $this->tableName;
    }

    function getColumnList() {
        return $this->columnList;
    }

    function getWhere() {
        return $this->where;
    }

    function getPredicate() {
        return $this->predicate;
    }

    function setAdapter($adapter) {
        $this->adapter = $adapter;
        return $this;
    }

    function setTableName($tableName) {
        $this->tableName = $tableName;
        return $this;
    }

    function setColumnList($columnList) {
        $this->columnList = $columnList;
        return $this;
    }

    function setWhere($where) {
        $this->where = $where;
        return $this;
    }

    function setOrder($order) {
        $this->order = $order;
        return $this;
    }

    function setPredicate($predicate) {
        $this->predicate = $predicate;
        return $this;
    }

    function setKeyValue($key, $value) {
        $this->key = $key;
        $this->value = $value;
        return $this;
    }

    function setIncludeEmptyRow($includeEmptyRow) {
        $this->includeEmptyRow = $includeEmptyRow;
        return $this;
    }

    function result(): array {
        $gateway = new TableGateway($this->tableName, $this->adapter);
        $columnList = $this->columnList;
        $where = $this->where;
        $order = $this->order;
        $predicate = $this->predicate;
        $iterator = $gateway->select(function(Select $select) use($columnList, $where, $predicate, $order) {
            $select->columns($columnList, false);
            if ($where != null) {
                $select->where($where, $predicate);
            }
            if ($order != null) {
                $select->order($order);
            }
        });

        $output = [];
        if ($this->includeEmptyRow) {
            if (gettype($this->includeEmptyRow) == 'array') {
                foreach ($this->includeEmptyRow as $key => $value) {
                    $output[$key] = $value;
                }
            } else {
                $output[null] = "----";
            }
        }
        foreach ($iterator as $item) {
            if ($this->key != null && $this->value != null) {
                $output[$item[$this->key]] = $item[$this->value];
            } else {
                $output[] = $item;
            }
        }
        return $output;
    }
}
