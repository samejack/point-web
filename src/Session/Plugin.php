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

    public function start(array $options = null, $sessionId = null)
    {

        if (session_id() || headers_sent()) {
            return;
        }

        // customize session id
        if (!is_null($sessionId) && !empty($sessionId)) {
            session_id($sessionId);
        }
        
        // set session file path
        if (isset($this->_config['session']['path'])) {
            session_save_path($this->_config['session']['save_path']);
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

        if (preg_match('/^7\.3.*$/', phpversion()) !== 0) {
            setcookie(
                $this->_config['session']['name'],
                session_id(),
                [
                    'lifetime' => $this->_config['session']['lifetime'],
                    'domain' => $this->_config['session']['domain'],
                    'secure' => $this->_config['session']['secure'],
                    'httponly' => $this->_config['session']['httponly'],
                    'samesite' => $this->_config['session']['samesite']
                ]
            );
        } else {
            $path = $this->_config['session']['path'];

            $sameSite = is_null($path) ? null : $path . '; SameSite=' . $this->_config['session']['samesite'];

            // iPhone 如果送出 SameSite=None 會變成 Strict
            $headers = function_exists('getallheaders') ? getallheaders() : [];
            if ($sameSite === 'None' && strpos($headers['User-Agent'], 'iPhone OS 12') !== false) {
                $sameSite = null;
            }

            session_set_cookie_params(
                $this->_config['session']['lifetime'],
                $sameSite,
                $this->_config['session']['domain'],
                $this->_config['session']['secure'],
                $this->_config['session']['httponly']
            );
        }

        if (is_null($options)) {
            @session_start();
        } else {
            @session_start($options);
        }
    }

    public function setExpire($expire)
    {
        session_set_cookie_params($expire);
        ini_set('session.gc_maxlifetime', $expire);
        session_cache_expire($expire);

        if (session_status() === PHP_SESSION_NONE)  $this->start();
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
    
    public function setValue($key, $value, $pluginId = null)
    {
        if (session_status() === PHP_SESSION_NONE) $this->start();
        if (is_null($pluginId)) {
            $pluginId = $this->_runtime->getCurrentPluginId();
        }
        $_SESSION[self::SESSION_NAMESPACE][$pluginId][$key] = $value;
    }

    public function getValue($key, $pluginId=null)
    {
        if (session_status() === PHP_SESSION_NONE)  $this->start();
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
