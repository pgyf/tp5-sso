<?php
namespace app\sso\logic;

use Jasny\ValidationResult;

/**
 * Description of SSOServer
 *
 * @author     lyf <381296986@qq.com>
 * @date       2016-11-12
 */
class SSOServer  extends \Jasny\SSO\Server {
    
    
    
    
    /**
     * Registered brokers
     * @var array
     */
    private static $brokers = [
        'Alice'   => ['secret'=>'8iwzik1bwd'],
        'Greg'    => ['secret'=>'7pypoox2pc'],
        'Julias'  => ['secret'=>'ceda63kmhp']
    ];
    
    
    /**
     * System users
     * @var array
     */
    private static $users = [
        'jackie' => [
            'fullname' => 'Jackie Black',
            'email' => 'jackie.black@example.com',
            'password' => '$2y$10$lVUeiphXLAm4pz6l7lF9i.6IelAqRxV4gCBu8GBGhCpaRb6o0qzUO' // jackie123
        ],
        'john' => [
            'fullname' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => '$2y$10$RU85KDMhbh8pDhpvzL6C5.kD3qWpzXARZBzJ5oJ2mFoW7Ren.apC2' // john123
        ],
    ];
    

    /**
     * Get the API secret of a broker and other info
     *
     * @param string $brokerId
     * @return array
     */
    protected function getBrokerInfo($brokerId)
    {
        return isset(self::$brokers[$brokerId]) ? self::$brokers[$brokerId] : null;
    }

     /**
     * Authenticate using user credentials
     *
     * @param string $username
     * @param string $password
     * @return ValidationResult
     */
    protected function authenticate($username, $password)
    {
        if (!isset($username)) {
            return ValidationResult::error("username isn't set");
        }
        
        if (!isset($password)) {
            return ValidationResult::error("password isn't set");
        } 
        
        if (!isset(self::$users[$username]) || !password_verify($password, self::$users[$username]['password'])) {
            return ValidationResult::error("Invalid credentials");
        }

        return ValidationResult::success();
    }

    
    /**
     * Create a cache to store the broker session id.
     *
     * @return Cache
     */
    protected function createCacheAdapter()
    {
        return \think\Cache::connect(config('cache.sso'));
        //return \think\Cache::store('file');
    }
    
    

    /**
     * Get the user information
     *
     * @return array
     */
    protected function getUserInfo($username)
    {
        if (!isset(self::$users[$username])){
            return null;
        }
        $user = compact('username') + self::$users[$username];
        unset($user['password']);
        return $user;
    }
    
    
    /**
     * Log out
     */
    public function logout()
    {   
        $this->startBrokerSession();
        $this->setSessionData('sso_user', null);
        header('Content-type: application/json; charset=UTF-8');
        //http_response_code(204);
        echo json_encode(['success' => 1]);
        exit;
    }

    /**
     * Ouput user information as json.
     */
    public function userInfo()
    {
        $this->startBrokerSession();
        $user = null;

        $username = $this->getSessionData('sso_user');

        if ($username) {
            $user = $this->getUserInfo($username);
            if (!$user) return $this->fail("User not found", 500); // Shouldn't happen
        }

        header('Content-type: application/json; charset=UTF-8');
        echo json_encode($user);
        exit;
    }
    
    
    
    
}
