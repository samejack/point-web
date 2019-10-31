<?php

namespace point\web;

/**
 * 驗證 XSS 攻擊字串
 * 
 * @author sj
 */
class Validate_Rule_XssAttack extends Validate_Base
{
    /**
     * 驗證 XSS 攻擊字串
     *
     * @param mixed   [$args]
     * @param string  [$message]
     */
    public function __construct($args = null, $message = '')
    {
        parent::__construct($args, $message);
    }

    private static $_attackStringPreg = array(
        '/<script\s*[^>]*>.*<\/script[^>]*>/isU',
        '/<script\s*[^>]*>/isU',
        '/%3Cscript\s*%3E/isU',
        '/<\?(php)?(\s*|=).*\?>/isU',
        '/<frameset\s[^>]*>.*<\/frameset[^>]*>/isU',
        '/<frame\s[^>]*>.*<\/frame[^>]*>/isU',
        '/<iframe\s[^>]*>.*<\/iframe[^>]*>/isU',
        '/^.*<body\s*[^>]*>/isU',
        '/<\/body>.*$/isU',
        '/<object\s*[^>]*>.*<\/object[^>]*>/isU',
        '/<applet\s*[^>]*>.*<\/applet[^>]*>/isU',
        '/<form\s*[^>]*>.*<\/form[^>]*>/isU',
        '/<link\s*[^>]*>/isU',
        '/<[^<]+on\w+\s*=\s*(".*[^\\]"|\'.*[^\\]\'|[^\s]*|\w+)[^>]*>+/isU',
        '/\w+\s*=\s*["\']?\s*(javascript|vbscript|mocha|livescript):[^\'">]*["\']?/isU',
        '/:\s*expression\s*\(/isU'
    );

    public function validate($input, array &$columns)
    {
        foreach (self::$_attackStringPreg as $pregString) {
            if (preg_match($pregString, $input) > 0) {
                return false;
            }
        }
        return true;
    }
}
