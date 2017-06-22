<?php

namespace point\web;

class Session_Plugin implements Session_Interface
{
    
    const SESSION_NAMESPACE = 'PLUGIN_SESSION';

    /**
     * @Autowired
     * @var \point\web\Config_Interface
     */
    private $_config;

    /**
     * @Autowired
     * @var \point\core\Runtime
     */
    private $_runtime;

    public function start(array $options = null)
    {
        // set session file path
        if (isset($this->_config['session']['path'])) {
            session_save_path($this->_config['session']['path']);
        }

        // set session timeout
        if (isset($this->_config['session']['timeout'])) {
            session_cache_expire($this->_config['session']['timeout']);
        }

        // set session name
        if (isset($this->_config['session']['name'])) {
            session_name($this->_config['session']['name']);
        }

        if (session_status() === PHP_SESSION_DISABLED) {
            throw new \Exception('PHP Session not enabled.');
        } else if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        if (!is_null($options)) {
            foreach ($options as $key => &$value) {
                $this->_options[$key] = $value;
            }
        }

        if (is_null($options)) {
            @session_start();
        } else {
            @session_start($this->_options);
        }

    }

    public function setExpire($expire)
    {
        session_set_cookie_params($expire);
        ini_set('session.gc_maxlifetime', $expire);
        session_cache_expire($expire);

        if (session_status() === PHP_SESSION_NONE)  $this->start();

        setcookie($this->_config['session']['name'], session_id(), time() + $expire);
    }

    public function clear($pluginId = null)
    {
        if (session_status() === PHP_SESSION_NONE)  $this->start();

        if (is_null($pluginId)) {
            $pluginId = $this->_runtime->getCurrentPluginId();
        }
        if (isset($_SESSION[self::SESSION_NAMESPACE][$pluginId])) {
            unset($_SESSION[self::SESSION_NAMESPACE][$pluginId]);
        }
    }

    public function delValue($key, $pluginId=null)
    {
        if (session_status() === PHP_SESSION_NONE  )  $this->start();

        if (is_null($pluginId)) {
            $pluginId = $this->_runtime->getCurrentPluginId();
        }
        if (isset($_SESSION[self::SESSION_NAMESPACE][$pluginId][$key])) {
            unset($_SESSION[self::SESSION_NAMESPACE][$pluginId][$key]);
        }
    }
    
    public function setValue($key, $value, $pluginId=null)
    {
        if (session_status() === PHP_SESSION_NONE  )  $this->start();

        if (is_null($pluginId)) {
            $pluginId = $this->_runtime->getCurrentPluginId();
        }
        $_SESSION[self::SESSION_NAMESPACE][$pluginId][$key] = $value;
    }

    public function getValue($key, $pluginId=null)
    {
        if (session_status() === PHP_SESSION_NONE  )  $this->start();
        if (is_null($pluginId)) {
            $pluginId = $this->_runtime->getCurrentPluginId();
        }
        if (
            isset($_SESSION[self::SESSION_NAMESPACE]) 
            && array_key_exists($pluginId, $_SESSION[self::SESSION_NAMESPACE]) 
            && array_key_exists($key, $_SESSION[self::SESSION_NAMESPACE][$pluginId])
        ) {
            return $_SESSION[self::SESSION_NAMESPACE][$pluginId][$key];
        }
        return null;
    }
    
    public function getValues($pluginId=null)
    {
        if (session_status() === PHP_SESSION_NONE  )  $this->start();

        if (is_null($pluginId)) {
            $pluginId = $this->_runtime->getCurrentPluginId();
        }
        if (isset($_SESSION[self::SESSION_NAMESPACE])
            && array_key_exists($pluginId, $_SESSION[self::SESSION_NAMESPACE])
        ) {
            return $_SESSION[self::SESSION_NAMESPACE][$pluginId];
        } else {
            return null;
        }
    }

    public function destroy()
    {
        session_destroy();
    }
}
