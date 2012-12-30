<?php 
class Email {
    
    public static $_driver = 'PHPMailer';
    private static $_config  = array(
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
    
    public static function factory($config = array(), $driver = NULL)
    {
        if ($driver === NULL)
        {
            // Use the default driver
            $driver = self::$_driver;
        }
        // Set the class name
        $class = 'Email_'.$driver;
        
        $email = new $class();
        
        foreach (Arr::merge(self::$_config, $config) as $key => $value)
            $email->$key = $value;
        
        return $email;
    }
} // End Email