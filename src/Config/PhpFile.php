<?php
namespace point\web;

class Config_PhpFile extends Config_Abstract
{
    public function loadfile($filepath)
    {
        if (is_file($filepath)) {
            $config = array();
            include $filepath;
            $this->_properties = $config;
        } else if (is_dir($filepath) && is_file($filepath . '/' . $this->_environment . '.php')) {
            $config = array();
            include $filepath . '/' . $this->_environment . '.php';
            $this->_properties = $config;
        } else {
            throw new \Exception('Configuration file not found: ' . $filepath);
        }
    }
}
