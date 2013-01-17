<?php

class Lipsum
{ 
    /** 
     * Contains the lorem ipsum text with each paragraph 
     * stored as an index. 
     * 
     * @access    public 
     * @var       string 
     */
    private static $text = array(
        "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed blandit tempus molestie. In pretium iaculis lorem id ultricies. Curabitur lobortis sapien ac magna ullamcorper faucibus. Aenean ac nibh diam. Donec id felis nunc, et bibendum erat. Nulla faucibus, lacus iaculis aliquet ultricies, diam nibh auctor risus, eget placerat est orci sed arcu. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Etiam diam tellus, scelerisque ut imperdiet eget, faucibus vel sapien. Sed vitae libero vel nisl tincidunt sodales. Donec scelerisque sodales velit, et interdum orci commodo ac. Maecenas malesuada condimentum turpis vel convallis. Integer sed mi id est scelerisque varius. Nulla facilisi.",
        "In mollis luctus libero nec euismod. Proin pellentesque rutrum tortor id venenatis. Integer eu lorem quis odio lacinia sollicitudin. Aenean leo urna, dictum at adipiscing et, posuere eget ipsum. Cras id dolor est. Curabitur at lorem eget neque gravida imperdiet nec quis lacus. Proin et facilisis justo. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Suspendisse ut mauris vel est cursus ornare non id nisl. Aenean porttitor fringilla lectus, vitae lobortis nisi interdum id. Nullam semper, ante id dictum consectetur, elit velit accumsan urna, eu faucibus lectus justo id nibh. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Donec sed arcu purus.",
        "Etiam erat mauris, ullamcorper quis fringilla at, venenatis sed eros. Mauris et nisl non libero scelerisque volutpat. Mauris luctus metus quis odio sagittis luctus. Pellentesque et arcu congue tortor tincidunt venenatis. Praesent eget diam sed nunc vulputate fringilla. Pellentesque sit amet quam vel mi tristique tristique. Praesent dictum gravida dolor vitae pulvinar. In hac habitasse platea dictumst. Integer sit amet feugiat erat. Integer vitae lacus suscipit dolor adipiscing egestas sed sed libero. Sed viverra elit eget dolor tristique interdum. Curabitur tristique risus vel ante commodo sed tincidunt nisi mollis. Maecenas malesuada placerat nunc, quis placerat nisi semper nec. Etiam sollicitudin, tortor at vulputate euismod, ligula augue ornare augue, nec bibendum orci nulla id metus. Nulla velit augue, interdum vitae dapibus et, faucibus ut ligula.",
        "Duis sed nisl ac mauris semper cursus. Vivamus sit amet libero in dolor euismod bibendum sed in massa. Nulla iaculis bibendum tempus. Pellentesque condimentum turpis nec tortor interdum ut blandit mauris vehicula. Fusce scelerisque odio vel dui luctus vel viverra odio semper. Nunc euismod luctus sapien, eu euismod lorem iaculis id. Pellentesque suscipit, lectus id volutpat posuere, nulla risus egestas erat, vitae cursus orci augue eu tellus. Donec quis elit in neque vulputate dignissim a eu justo. Ut tempus magna non tellus ultrices rhoncus. Duis sagittis blandit ultricies. Aliquam est sem, feugiat eu suscipit a, ultricies vel felis. Maecenas vel pharetra nisl.",
        "Vestibulum ut eros id diam viverra malesuada. Praesent vel odio odio. Nulla molestie, lectus ut vulputate posuere, sapien felis pretium magna, feugiat laoreet arcu mauris a nibh. Etiam sed tellus metus. Nam vehicula justo in odio venenatis lobortis et vel ipsum. Fusce facilisis sem non sem dictum eu congue diam euismod. Aenean at lectus nulla, nec dignissim elit. Donec viverra nunc sed tortor vehicula eu feugiat dui varius. Aenean orci tellus, adipiscing consequat bibendum placerat, molestie eu lectus. Nulla quis nunc orci. Nullam placerat nulla vitae nulla bibendum sit amet auctor risus pharetra. Sed semper lectus a purus convallis aliquam. Vestibulum massa mauris, facilisis a tincidunt eget, semper ut orci.",
        "Pellentesque mi mi, pretium in scelerisque nec, facilisis id massa. Cras feugiat dui id dui volutpat malesuada posuere eros faucibus. Vestibulum mattis suscipit aliquam. Phasellus aliquet cursus ipsum sed cursus. Ut molestie laoreet elementum. Etiam varius orci sit amet nibh luctus tincidunt quis sed nisi. Maecenas leo nisi, ornare id fermentum sit amet, ornare at sapien. Duis pellentesque, felis vitae euismod sagittis, leo lorem vestibulum neque, in gravida urna orci vel dui. Nam ligula justo, consequat at pharetra eget, auctor in sem. In placerat quam nec felis sollicitudin vitae malesuada metus eleifend. Vivamus commodo ipsum mollis risus sodales malesuada. Duis et imperdiet elit. Proin sapien lorem, fermentum vel lacinia vitae, bibendum in erat. Etiam tristique, velit non mollis blandit, turpis eros rhoncus mi, iaculis tristique est velit at lectus. Morbi commodo felis sed ligula pellentesque sed dignissim nulla eleifend.",
        "Cras molestie tortor sit amet ante ornare sit amet molestie odio congue. Cras in arcu et nibh mattis dictum id ac eros. Pellentesque diam orci, commodo in posuere laoreet, imperdiet eget sem. Morbi iaculis gravida sollicitudin. Nam eu justo a orci malesuada accumsan. Pellentesque nec justo sapien. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Ut dapibus tempor lorem nec sagittis. Donec a sapien mi. Suspendisse tempus elit nec lorem fringilla aliquet. Aliquam vitae libero eu metus viverra malesuada. Cras vel ipsum ligula. Nullam nec nunc id est tincidunt vulputate nec eget nisi.",
        "Morbi nisl lectus, tincidunt a venenatis non, iaculis quis dolor. Vestibulum convallis blandit eros a dictum. Mauris bibendum bibendum metus, ac placerat lectus adipiscing et. Donec et risus massa. Quisque nibh leo, cursus in tristique a, convallis non diam. Cras suscipit metus quis tellus faucibus mollis. Morbi ligula augue, lobortis sed auctor sit amet, dignissim in nisi. Nulla nec cursus tortor. Sed mollis imperdiet odio ac volutpat. Aliquam quis adipiscing tellus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec mattis sapien eget odio condimentum tincidunt. Pellentesque vel blandit nibh. Phasellus condimentum rhoncus nulla non aliquam. Integer semper, lectus at egestas convallis, ipsum velit sagittis ante, ut pellentesque nunc nisi vitae sem. Maecenas ante nisi, convallis quis porta a, sagittis vitae erat.",
        "Donec ultricies, purus ac ornare varius, libero augue cursus lorem, ac ultricies justo mi et diam. Donec sapien massa, posuere non mattis sed, tincidunt et ante. Suspendisse accumsan, est vitae commodo dapibus, nunc nibh semper magna, luctus euismod turpis ante sed neque. Mauris mi diam, vehicula at vulputate at, ultrices non metus. Duis consectetur turpis sit amet eros adipiscing elementum commodo quam ultricies. Cras tincidunt, ipsum nec fermentum placerat, lectus felis tincidunt lacus, vel faucibus quam nisi a nibh. Etiam posuere congue volutpat. Phasellus auctor lorem sed tortor consectetur non dictum mauris varius. Nullam rutrum mollis massa, id vestibulum massa tempor non. Pellentesque dolor urna, aliquam at ornare vel, tempus sed urna. Etiam at nibh faucibus risus porttitor pulvinar sagittis a tortor. Nulla condimentum nunc at purus sollicitudin ac tincidunt ante pretium. In in nisi nunc, et auctor sem. Nulla a justo quam.",
        "Pellentesque placerat sem ac augue tempor congue. Phasellus eu sem quis diam interdum pulvinar. Phasellus dictum porttitor enim non tempor. Donec eleifend porta sapien nec hendrerit. Duis eros neque, pellentesque et vulputate nec, hendrerit vitae mi. Vivamus tortor ipsum, faucibus eu consectetur in, elementum varius leo. Fusce sem odio, consectetur sit amet cursus non, venenatis eu felis. Maecenas eros quam, elementum a hendrerit sed, laoreet non erat. Vivamus nec elit massa. Ut sagittis placerat augue et egestas. Nulla pharetra condimentum nisl, non volutpat quam varius eget.",
        "Suspendisse potenti. Integer elementum pretium lobortis. Nullam blandit libero nec ante aliquet in auctor felis facilisis. Proin non arcu est, sit amet fermentum nisi. Nullam sed felis orci, sed gravida elit. Mauris velit arcu, consectetur a fringilla id, fringilla nec turpis. Fusce dignissim pharetra nisl, sit amet consequat quam tincidunt sed. Cras volutpat nunc leo, eu euismod neque. Nulla nec nisl nulla, et porta dolor. Proin eu metus a purus consequat mattis.",
        "Fusce sapien ipsum, viverra nec dictum vel, dignissim eget nibh. In hac habitasse platea dictumst. Integer auctor ullamcorper condimentum. Aenean ultrices suscipit consectetur. Integer fermentum quam id ipsum dapibus eu molestie eros porttitor. Proin ac nisl eu dui vehicula aliquet.",
        "Integer congue, est nec commodo suscipit, dolor turpis suscipit lacus, vel fringilla urna justo ut quam. Mauris sollicitudin suscipit est ac eleifend. Etiam eget ipsum eget justo aliquet facilisis. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Proin vel tortor augue, a lobortis neque. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean quis felis ornare ipsum faucibus porta. Mauris rhoncus ullamcorper sapien.",
        "Fusce sodales nibh nec dolor viverra a luctus arcu laoreet. Aenean sodales tristique tincidunt. Proin sit amet urna sem, in venenatis neque. Aliquam erat volutpat. Nulla diam est, ornare in volutpat id, luctus vel leo. Ut nunc libero, sollicitudin sed varius vitae, placerat eget mauris. Nulla laoreet velit eget odio ullamcorper sit amet consectetur lacus pretium. Proin quis neque laoreet urna accumsan consectetur quis et lectus. Vestibulum euismod pharetra purus vitae tincidunt.",
        "Ut facilisis ligula eget libero varius sit amet tincidunt purus consectetur. Mauris condimentum aliquam justo, lacinia imperdiet mi iaculis quis. Suspendisse ornare felis vitae magna sollicitudin varius. Fusce condimentum ornare ante et consequat. Nunc cursus varius leo, pellentesque venenatis purus pellentesque a.",
        "Integer non odio a massa mollis euismod. Nullam aliquet euismod ornare. Phasellus condimentum, nunc nec auctor auctor, mauris mauris semper orci, a faucibus ligula augue volutpat metus. Vivamus mollis augue eu leo molestie rutrum. Maecenas mollis odio sit amet nibh posuere iaculis. Quisque non sapien elit, ac ornare neque. Cras ligula metus, commodo ut ullamcorper eu, interdum eu urna. Praesent hendrerit nisl in enim interdum ut cursus enim consequat. Proin venenatis fermentum nunc ut facilisis. Aliquam ornare interdum mollis.",
        "Curabitur imperdiet auctor sapien, non posuere libero ornare ut. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Aenean tempor, ligula sit amet consectetur imperdiet, nunc diam ultrices ligula, nec cursus turpis est et mauris. Nunc eu risus in est pellentesque rutrum non eget ante. Donec elit neque, ultrices eget dapibus scelerisque, vehicula ut urna. Sed vehicula, nibh quis aliquet fringilla, leo mauris laoreet odio, a posuere ipsum odio eget velit. Sed nec sagittis lorem. Praesent venenatis nulla nec justo adipiscing vitae semper mauris suscipit. Maecenas nisl lectus, mollis quis dictum nec, egestas ac est."
    );
     
    /** 
     * Contains configuration for display arguments
     * 
     * @access    protected
     * @var       array
     */ 
    private static $_config = array(
        'arguments' => array(
            'characters' => array('chars', 'char', 'byte', 'bytes', 'characters'),
            'words' => array('words', 'word'),
            'sentences' => array('sentence', 'sentences', 'sent'),
            'paragraphs' => array('para', 'paras', 'paragraphs'),
        )
    );

    /** 
     * Displays dummy text
     * 
     * Takes in an array of options to determine how many words, characters,
     * or paragraphs to display.  Since the helper only contains 6 paragraphs,
     * it will loop around the paragraphs
     * 
     * @access    public
     * @param     $unit Characters (chars), Words (words) or Paragraphs (paragraphs)
     * @param     $amount Number of units to display
     * @param     $print Flag to print or return
     */
    public static function display($unit, $amount, $html = false, $print = true, $random = true)
    {
        extract(self::$_config['arguments']);
        
        if ($random) shuffle(self::$text);
        $lorem = implode('|', self::$text);
        
        if( in_array($unit, $characters) )
        {
            $text = substr($lorem, 0, $amount);
            if( $text[strlen($text)-1] != '.' && $text[strlen($text)-1] != ' ' )
            {
                $text .= '.';
            }
        }
        else
        {
            if( in_array($unit, $words) )
            { 
                $splitBy = ' ';
            }
            elseif( in_array($unit, $sentences) )
            {
                $splitBy = '.';
            }
            else
            {
                $splitBy = '|';
            }
            
            $pieces = explode($splitBy, $lorem);
            $count = sizeof($pieces);
            
            while( $amount > $count )
            {
                $morePieces = explode($splitBy, $lorem);
                $pieces = array_merge($pieces, $morePieces);
                $count = sizeof($pieces);
            }
            array_splice($pieces, $amount);
            $text = implode($splitBy, $pieces);
        }
        
        $text = $html ? str_replace('|', '</p><p>', $text) : str_replace('|', ' ', $text);
        $text = $html ? trim("<p>$text</p>") : trim($text);
        
        if( $print )
        {
            echo $text;
        }
        else
        {
            return $text;
        }
    }
}
?>