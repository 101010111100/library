<?php

class I18n {
    
    public static $lang = 'en';
    protected static $_cache = array();
    
    public static function lang($lang = NULL)
    {
        if ($lang)
        {
            // Normalize the language
            self::$lang = strtolower(str_replace(array(' ', '_'), '-', $lang));
        }

        return self::$lang;
    }
    
    public function load($lang)
    {
        if (isset(I18n::$_cache[$lang]))
        {
            return I18n::$_cache[$lang];
        }

        $langPath = '../app/messages/';
        if ($lang != 'en' && file_exists($langPath.$lang.'.php'))
        {
            $messages = require $langPath.$lang.'.php';
        }
        else
        {
            $messages = array();            
        }

        $translate = new Phalcon\Translate\Adapter\NativeArray(array(
            "content" => $messages
        ));
        
        return I18n::$_cache[$lang] = $translate;
    }
    
}

if ( ! function_exists('__'))
{
    function __($string, array $values = NULL)
    {
        if (I18n::$lang != 'en')
        {
            $translate = I18n::load(I18n::$lang);
            $string = $translate->_($string, $values);
        }

        return empty($values) ? $string : strtr($string, $values);
    }
}