<?php

namespace System\Repository\Oracle;

use Application\Model\Model;
use Application\Model\Preference;
use Zend\Db\Adapter\AdapterInterface;

class SystemSettingRepository {

    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function fetch() {
        $preference = new Preference();
        $valuesinCSV = "";
        $i = 0;
        $size = sizeof($preference->mappings);
        foreach ($preference->mappings as $key => $value) {
            if ($i + 1 == $size) {
                $valuesinCSV .= "'{$value}' AS {$value}";
            } else {
                $valuesinCSV .= "'{$value}' AS {$value},";
            }
            $i++;
        }
        $sql = "SELECT *
                FROM
                  (SELECT * FROM HRIS_PREFERENCES
                  ) PIVOT ( MAX(VALUE) FOR KEY IN ({$valuesinCSV}) )";
                  
                  
//                  $sql="select * from hris_branches";
        $statement = $this->adapter->query($sql);
        $iterator = $statement->execute();
//        var_dump(iterator_to_array($iterator, false));
        
//        echo '<pre>';
//        foreach ($iterator as $data){
//            print_r($data);
//        }
        
//        die();
        return iterator_to_array($iterator, false)[0];
    }

    public function edit(Model $model) {
        foreach ($model->mappings as $key => $value) {
            $v = $model->{$key};
            $statement = $this->adapter->query("
                DECLARE
                  V_KEY VARCHAR2(100 BYTE);
                BEGIN
                  SELECT KEY INTO V_KEY FROM HRIS_PREFERENCES WHERE KEY = '{$value}';
                  UPDATE HRIS_PREFERENCES SET VALUE = '{$v}' WHERE KEY ='{$value}';
                EXCEPTION
                WHEN NO_DATA_FOUND THEN
                  INSERT INTO HRIS_PREFERENCES
                    (KEY,VALUE
                    ) VALUES
                    ('{$value}','{$v}'
                    );
                END; ");

            $statement->execute();
        }
    }

}
