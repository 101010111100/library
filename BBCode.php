<?php

class BBCode extends JBBCode\Parser {
    
    /**
     * Replace all BBCode or return text only.
     * 
     * @param string $text	input text
     * @param bolean $as	display as html, bbcode  or text
     * 
     * @return string a parsed html string or plain text
     */
    public static function replace($text, $as = NULL)
    {
        $bbc = new BBCode();
        $bbc->loadDefaultCodes();
        $bbc->parse(Tool::entities($text));
        
        switch ($as)
        {
            case 'bbc':
            case 'bbcode':
                return $bbc->getAsBBCode();
                break;
            case 'txt':
            case 'text':
                return $bbc->getAsText();
            default:
                return '<div class="bbcode">'.BBCode::nl2br($bbc->getAsHTML()).'</div>';
                break;
        }
    }
    
    /**
     * Adds a set of default, standard bbcode definitions commonly used across the web. 
     */
    public function loadDefaultCodes() {
        $this->addBBCode("b", "<strong>{param}</strong>");
        $this->addBBCode("i", "<em>{param}</em>");
        $this->addBBCode("u", "<u>{param}</u>");
        $this->addBBCode("url", "<a href=\"{param}\" target=\"_blank\" rel=\"nofollow\">{param}</a>");
        $this->addBBCode("url", "<a href=\"{option}\" target=\"_blank\" rel=\"nofollow\">{param}</a>", true);
        $this->addBBCode("img", "<img src=\"{param}\" />");
        $this->addBBCode("img", "<img src=\"{param}\" width=\"{option}\" />", true);
        $this->addBBCode("color", "<span style=\"color: {option}\">{param}</span>", true);
        
        $this->addBBCode("quote", '<blockquote>{param}</blockquote>');
        
        $this->addBBCode("code", '<pre class="log">{param}</pre>', false, false, 1);
        $this->addBBCode("code", '<pre class="{option}">{param}</pre>', true, false, 1);
        
        $langs = array ('c','cpp','csharp','css','flex','html','java','javascript|js','javascript_dom|js_dom','log','perl|pl','php','python|py','ruby|rb','sh|bash','sql','xml');
        
        foreach($langs as $lang)
        {
            $aliases = explode('|', $lang);
            foreach ($aliases as $alias)
                $this->addBBCode($alias, '<pre class="'.$alias.'">{param}</pre>', false, false, 1);
        }
    }
    
    function nl2br($string)
    {
        $string = preg_replace("/(\r\n|\n\r|\n|\r)/", "<br />", $string);
        $string = preg_replace(array('/(<br[^>]*>\s*){2,}/'), array('<br /><br />'), $string);
        return $string;
    }
}
