<?php

abstract class Auth
{
    private static $_config = array(
        'hash_method'   =>  'sha256',
        'hash_key'      =>  '2fea1f3e86b30468e9a74498b65c795201e9d56a73f7646b48660f3896883427',
        'lifetime'      =>  1209600,
        'session_key'   =>  'auth_user',
        'session_roles' =>  TRUE,
    );
    
    /**
     * Perform a hmac hash, using the configured method.
     *
     * @param   string  string to hash
     * @return  string
     */
    public function hash($str)
    {
        if ( ! self::$_config['hash_key'])
            throw new \Phalcon\Exception('A valid hash key must be set in your auth config.');

        return hash_hmac(self::$_config['hash_method'], $str, self::$_config['hash_key']);
    }
    
    /**
     * Gets the currently logged in user from the session.
     * Returns NULL if no user is currently logged in.
     *
     * @return  mixed
     */
    public function get_user()
    {
        $user = $this->session->get(self::$_config['session_key']);

        if ( ! $user)
        {
            // check for "remembered" login
            $user = self::auto_login();
        }

        return $user;
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
            $user = $token->getUsers()->getFirst();
            
            if ($token && $user)
            {
                if ($token->user_agent === sha1($this->request->getUserAgent()))
                {
                    // Save the token to create a new unique token
                    $token->save();

                    // Set the new token
                    Cookie::set('authautologin', $token->token, $token->expires - time());

                    // Complete the login with the found data
                    //$this->complete_login($token->user);
                    
                    // Finish the login
                    // Regenerate session_id
                    session_regenerate_id();

                    // Store user in session
                    $user = Arr::convert(Arr::merge(get_object_vars($user), array('roles' => $roles_arr)));
                    $this->session->set(self::$_config['session_key'], $user);

                    // Automatic login was successful
                    return $token->user;
                }

                // Token is invalid
                $token->delete();
            }
        }

        return FALSE;
    }
    
    protected function create_token()
    {
        do
        {
            $token = sha1(uniqid(Auth::random_text(32), TRUE));
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
    public static function random_text($length = 8)
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
        if (is_string($password))
        {
            // Create a hashed password
            $password = self::hash($password);
        }

        //$role = Roles::findFirst(array('user_id=:user_id: AND name="login"', 'bind' => array('user_id' => $user->id)));
        //$role = $user->getRoles("name='login'")->getFirst();
        $roles = $user->getRoles();
        $login = FALSE;
        $roles_arr = array();
        
        foreach ($roles as $role)
        {
            if ($role->name == 'login')
                $login = TRUE;
            $roles_arr [$role->name]= TRUE;
        }

        // If the passwords match, perform a login
        if ($user && $login && $user->password === $password)
        {
            if ($remember === TRUE)
            {
                // Create a new autologin token
                $token = new Tokens();
                $token->user_id = $user->id;
                $token->user_agent = sha1($this->request->getUserAgent());
                $token->token = self::create_token();
                $token->created = time();
                $token->expires = time() + self::$_config['lifetime'];
                echo Debug::dump($token->create(), 'created');
                exit();
                //$token->save();
                
                // Set the autologin cookie
                Cookie::set('authautologin', $token->token, self::$_config['lifetime']);
            }

            // Finish the login
            // Regenerate session_id
            session_regenerate_id();

            // Store user in session
            $user = Arr::to_object(Arr::merge(get_object_vars($user), array('roles' => $roles_arr)));
            $this->session->set(self::$_config['session_key'], $user);
            
            return TRUE;
        }

        // Login failed
        return FALSE;
    }
    
    /**
     * Log out a user by removing the related session variables.
     *
     * @param   boolean  completely destroy the session
     * @return  boolean
     */
    public function logout($destroy = FALSE)
    {
        if ($destroy === TRUE)
        {
            // Destroy the session completely
            $this->session->destroy();
        }
        else
        {
            // Remove the user from the session
            $this->session->remove(self::$_config['session_key']);

            // Regenerate session_id
            session_regenerate_id();
        }

        // Double check
        return ! self::logged_in();
    }

    /**
     * Checks if a session is active.
     *
     * @param   mixed    $role Role name string
     * @return  boolean
     */
    public function logged_in($role = NULL)
    {
        // Get the user from the session
        $user = self::get_user();
        if ( ! $user)
                return FALSE;
        
        //if ($user instanceof Users)
        if ($user)
        {
            // If we don't have a roll no further checking is needed
            if ( ! $role)
                return TRUE;

            // Get role
            $role = self::$_config['session_roles'] ? $user->roles->$role : Roles::findFirst(array('user_id=:user_id: AND name=:role:', 'bind' => array('user_id' => $user->id, 'role' => $role)));

            // Return true if user has role
            return $role ? TRUE : FALSE;
        }
    }
}