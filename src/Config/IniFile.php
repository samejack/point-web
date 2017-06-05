<?php
namespace point\web;

class Config_IniFile extends Config_Abstract
{

    /**
     * Convert properties to array
     *
     * @param $properties
     * @return array
     */
    private function _toArray($properties)
    {
        $newArray = array();
        foreach ($properties as $key => $value) {
            $point = &$newArray;
            $subKeys = explode('.', $key);
            for ($i = 0, $c = count($subKeys)-1; $i < $c; $i++) {
                $subKey = $subKeys[$i];
                $point = &$point[$subKey];
            }
            $point[$subKeys[count($subKeys)-1]] = $value;
        }
        return $newArray;
    }
    
    public function loadfile($filepath)
    {
        $array = parse_ini_file($filepath, true, INI_SCANNER_RAW);
        if (is_array($array) && array_key_exists($this->_environment, $array)) {
            // TODO 實作繼承
            $this->_properties = $this->_toArray($array[$this->_environment]);
        } else {
            throw new \Exception('Environment configs not found. ('.$this->_environment.')');
        }
    }
}