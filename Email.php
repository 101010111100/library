<?php 
class Email extends Email_PHPMailer {
    
    private $_config  = array(
        'CharSet'       =>  'utf-8',
        'ContentType'   =>  'text/html',
        'Encoding'      =>  '8bit',
        'From'          =>  'info@example.com',
        'FromName'      =>  'Info',
        'Mailer'        =>  'smtp',
        'Host'          =>  'mail.example.com',
        'Port'          =>  465,
        'SMTPSecure'    =>  'tls',
        'SMTPAuth'      =>  TRUE,
        'Username'      =>  'user',
        'Password'      =>  'pass',
    );

    public function __construct($config = array())
    {
        $email = new Email_PHPMailer();
        
        if ($_config = \Phalcon\DI::getDefault()->getShared('config')->email)
            foreach ($_config as $key => $value)
                $this->_config[$key] = $value;
        
        foreach (Arr::merge($this->_config, $config) as $key => $value)
            $this->$key = $value;
        
        return $email;
    }
    
    public function prepare($subject, $to, $view = '', $data = array())
    {
        $site = array(
            'domain' => \Phalcon\DI::getDefault()->getShared('config')->site->domain,
            'name' => \Phalcon\DI::getDefault()->getShared('config')->site->name,
            'url' => \Phalcon\DI::getDefault()->getShared('config')->site->url
        );
        
        $this->Subject = $subject;
        $this->AddAddress($to);

        $template = new \Phalcon\Mvc\View();
        $template->setViewsDir(ROOT_PATH . '/app/views/');
        $template->setMainView(NULL);
        
        $template->setVar('site', $site);
        $template->setVar('data', $data);
        $template->setVar('content', $template->getRender('email', $view));
        
        $body = $template->getRender('email', 'template');
        
        $this->MsgHTML($body);
        
        return $body;
    }
} // End Email