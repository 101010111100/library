<?php

class Auth
{
    private $_config = array(
        'hash_method'   =>  'sha256',
        'hash_key'      =>  'Shared secret key',
        'lifetime'      =>  1209600,
        'session_key'   =>  'auth_user',
        'session_roles' =>  TRUE,
    );
    
    private static $_instance;
    private $_session;
    
    /**
     * Singleton pattern
     *
     * @return Auth
     */
    public static function instance()
    {
        if( empty(self::$_instance) )
        {
            self::$_instance = new Auth;
        }

        return self::$_instance;
    }
    
    private function __construct()
    {
        if ($_config = \Phalcon\DI::getDefault()->getShared('config')->auth)
            foreach ($_config as $key => $value)
                $this->_config[$key] = $value;
        
        $this->_session = \Phalcon\DI::getDefault()->getShared('session');
    }
    
    private function __clone(){}

    /**
     * Checks if a session is active.
     *
     * @param   mixed    $role Role name string
     * @return  boolean
     */
    public function logged_in($role = NULL)
    {
        // Get the user from the session
        $user = $this->get_user();
        if ( ! $user)
                return FALSE;
        
        //if user exists in session
        if ($user)
        {
            // If we don't have a roll no further checking is needed
            if ( ! $role)
                return TRUE;

            // Get role
            $role = $this->_config['session_roles'] ? $user->roles->$role : Roles::findFirst(array('user_id=:user_id: AND name=:role:', 'bind' => array('user_id' => $user->id, 'role' => $role)));

            // Return true if user has role
            return $role ? TRUE : FALSE;
        }
    }
    
    /**
     * Gets the currently logged in user from the session.
     * Returns NULL if no user is currently logged in.
     *
     * @return  mixed
     */
    public function get_user()
    {
        $user = $this->_session->get($this->_config['session_key']);
        
        if ( ! $user)
        {
            // check for "remembered" login
            $user = $this->auto_login();
        }

        return $user;
    }
    
    /**
     * Refresh user data stored in the session from the database.
     * Returns NULL if no user is currently logged in.
     *
     * @return  mixed
     */
    public function refresh_user()
    {
        $user = $this->_session->get($this->_config['session_key']);
        
        if ( ! $user)
        {
            return NULL;
        }
        else
        {
            $user = Users::findFirst($user->id);
            $roles = Arr::from_model($user->getRoles(), 'name', 'id');
            
            // Regenerate session_id
            session_regenerate_id();

            // Store user in session
            $user = Arr::to_object(Arr::merge(get_object_vars($user), array('roles' => $roles)));
            $this->_session->set($this->_config['session_key'], $user);
            
            return $user;
        }
    }
    
    /**
     * Logs a user in, based on the authautologin cookie.
     *
     * @return  mixed
     */
    public function auto_login()
    {
        if ($token = Cookie::get('authautologin'))
        {
            // Load the token and user
            $token = Tokens::findFirst(array('token=:token:', 'bind' => array('token' => $token)));
            $user = $token->getUsers();
            
            // If the token and user exists
            if ($token && $user)
            {
                $roles = Arr::from_model($user->getRoles(), 'name', 'id');
                
                //If user has login role and tokens match, perform a login
                if (Arr::get($roles, 'login') && $token->user_agent === sha1(\Phalcon\DI::getDefault()->getShared('request')->getUserAgent()))
                {
                    // Save the token to create a new unique token
                    $token->token = $this->create_token();
                    $token->save();

                    // Set the new token
                    Cookie::set('authautologin', $token->token, $token->expires - time());
                    
                    // Finish the login
                    \Phalcon\DI::getDefault()->getShared('db')->execute('UPDATE `users` SET `logins` = ?, `last_login` = ? WHERE `id` = ?', array($user->logins+1, date('Y-m-d H:i:s'), $user->id));
                    
                    // Regenerate session_id
                    session_regenerate_id();

                    // Store user in session
                    $user = Arr::to_object(Arr::merge(get_object_vars($user), array('roles' => $roles)));
                    $this->_session->set($this->_config['session_key'], $user);

                    // Automatic login was successful
                    return $user;
                }

                // Token is invalid
                $token->delete();
            }
        }

        return FALSE;
    }
    
    /**
     * Attempt to log in a user by using an ORM object and plain-text password.
     *
     * @param   string   user to log in
     * @param   string   password to check against
     * @param   boolean  enable autologin
     * @return  boolean
     */
    public function login($user, $password, $remember = FALSE)
    {
        if ( ! is_object($user))
        {
            $username = $user;

            // Load the user
            $user = Users::findFirst(array('username=:username:', 'bind' => array('username' => $username)));
        }
        
        if ($user)
        {
            $roles = Arr::from_model($user->getRoles(), 'name', 'id');
            
            if (is_string($password))
            {
                // Create a hashed password
                $password = $this->hash($password);
            }

            // If the passwords match, perform a login
            if (Arr::get($roles, 'login') && $user->password === $password)
            {
                if ($remember === TRUE)
                {
                    // Create a new autologin token
                    $token = new Tokens();
                    $token->user_id = $user->id;
                    $token->user_agent = sha1(\Phalcon\DI::getDefault()->getShared('request')->getUserAgent());
                    $token->token = $this->create_token();
                    $token->created = time();
                    $token->expires = time() + $this->_config['lifetime'];
                    $token->create();

                    // Set the autologin cookie
                    Cookie::set('authautologin', $token->token, $this->_config['lifetime']);
                }

                // Finish the login
                \Phalcon\DI::getDefault()->getShared('db')->execute('UPDATE `users` SET `logins` = ?, `last_login` = ? WHERE `id` = ?', array($user->logins+1, date('Y-m-d H:i:s'), $user->id));

                // Regenerate session_id
                session_regenerate_id();

                // Store user in session
                $user = Arr::to_object(Arr::merge(get_object_vars($user), array('roles' => $roles)));
                $this->_session->set($this->_config['session_key'], $user);

                return TRUE;
            }
        }
        // Login failed
        return FALSE;
    }
    
    /**
     * Log out a user by removing the related session variables
     * Remove any autologin cookies.
     *
     * @param   boolean  $destroy     completely destroy the session
     * @param	boolean  $logout_all  remove all tokens for user
     * @return  boolean
     */
    public function logout($destroy = FALSE, $logout_all = FALSE)
    {
        if ($token = Cookie::get('authautologin'))
        {
            // Delete the autologin cookie to prevent re-login
            Cookie::delete('authautologin');

            // Clear the autologin token from the database
            $token = Tokens::findFirst(array('token=:token:', 'bind' => array('token' => $token)));

            if ($logout_all)
            {
                // Delete all user tokens
                foreach(Tokens::find(array('user_id=:user_id:', 'bind' => array('user_id' => $token->user_id) )) as $_token)
                {
                    $_token->delete();
                }
            }
            else
            {
                $token->delete();
            }
        }
        
        if ($destroy === TRUE)
        {
            // Destroy the session completely
            $this->_session->destroy();
        }
        else
        {
            // Remove the user from the session
            $this->_session->remove($this->_config['session_key']);

            // Regenerate session_id
            session_regenerate_id();
        }

        // Double check
        return ! $this->logged_in();
    }
    
    /**
     * Perform a hmac hash, using the configured method.
     *
     * @param   string  string to hash
     * @return  string
     */
    public function hash($str)
    {
        if ( !$this->_config['hash_key'])
            throw new \Phalcon\Exception('A valid hash key must be set in your auth config.');

        return hash_hmac($this->_config['hash_method'], $str, $this->_config['hash_key']);
    }
    
    /**
     * Create auto login token.
     *
     * @return  string
     */
    protected function create_token()
    {
        do
        {
            $token = sha1(uniqid($this->random_text(32), TRUE));
        }
        while(Tokens::findFirst(array('token=:token:', 'bind' => array('token' => $token)) ));

        return $token;
    }
    
    /**
     * Generate random text
     * 
     * @param   intiger     $length    text length
     * @return  string
     */
    public function random_text($length = 8)
    {
        $text = "";
        $possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";
        $maxlength = strlen($possible);

        if ($length > $maxlength)
        {
            $length = $maxlength;
        }

        $i = 0;
        while ($i < $length)
        {
            $char = substr($possible, mt_rand(0, $maxlength-1), 1);
            if (!strstr($text, $char))
            {
                $text .= $char;
                $i++;
            }
        }
        
        return $text;
    }
}