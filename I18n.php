<?php

class I18n {
    
    public static $lang = 'en-us';
    public static $source = 'en-us';
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
        
        $messages = array();
        $langPath = '../app/messages/';
        $parts = explode('-', $lang);
        $path = implode(DIRECTORY_SEPARATOR, $parts);
        
        if ($lang != I18n::$source)
        {
            if (file_exists($langPath.$path.'.php'))
            {
                $messages = require $langPath.$path.'.php';
            }
            elseif (file_exists($langPath.$lang.'.php'))
            {
                $messages = require $langPath.$lang.'.php';
            }
            elseif (file_exists($langPath.$parts[0].'.php'))
            {
                $messages = require $langPath.$parts[0].'.php';
            }
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
        if (I18n::$lang != I18n::$source)
        {
            $translate = I18n::load(I18n::$lang);
            $string = $translate->_($string, $values);
        }

        return empty($values) ? $string : strtr($string, $values);
    }
}