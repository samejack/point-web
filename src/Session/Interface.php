<?php

namespace point\web;

interface Session_Interface{

    public function start(array $options = null);

    public function clear($pluginId = null);

    public function delValue($key, $pluginId = null);

    public function setValue($key, $value, $pluginId = null);

    public function getValue($key, $pluginId = null);

    public function getValues($pluginId = null);

    public function destroy();

}
