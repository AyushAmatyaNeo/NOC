<?php

namespace Application\Model;

class Model {

    public $mappings;
    private $updateKeys = [];

    public function exchangeArrayFromForm(array $data) {
        $entityKeys = array_keys($this->mappings);
        foreach ($entityKeys as $keys) {
            $this->{$keys} = !empty($data[$keys]) ? $data[$keys] : null;
        }
        $this->updateKeys = array_keys($data);
    }

    public function exchangeArrayFromDB(array $data) {
        foreach ($this->mappings as $key => $value) {
            $this->{$key} = !empty($data[$value]) ? $data[$value] : null;
        }
    }

    public function getArrayCopyForDB() {
        $tempArray = [];
        foreach ($this->mappings as $key => $value) {
            if (isset($this->{$key}) || in_array($key, $this->updateKeys)) {
                $tempValue = $this->{$key};
                $tempArray[$value] = $tempValue;
            }
        }
        return $tempArray;
    }

    public function getArrayCopy() {
        $tempArray = [];
        foreach ($this->mappings as $key => $value) {
            if (isset($this->{$key})) {
                $tempArray[$key] = $this->{$key};
            }
        }
        return $tempArray;
    }

}
