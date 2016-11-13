<?php
namespace app\h5\controller;

use Jasny\SSO\NotAttachedException;
use Jasny\SSO\Exception as SsoException;

class User
{
    
    public function index() {
        if (input('sso_error')) {
            return redirect('user/error', ['sso_error' => input('sso_error')], 307);
            //header("Location: error.php?sso_error=" . $_GET['sso_error'], true, 307);
            //exit;
        }
        $SSO_SERVER = "http://127.0.0.50/sso/index/index";
        $SSO_BROKER_ID = "Greg";
        $SSO_BROKER_SECRET = "7pypoox2pc";
        
        $broker = new \Jasny\SSO\Broker($SSO_SERVER, $SSO_BROKER_ID, $SSO_BROKER_SECRET);
        $broker->attach(true);
        $user = null;
        try {
            if (!empty(input('logout'))) {
                $broker->logout();
                $user = null;
            }else{
                $user = $broker->getUserInfo();
            }
        } catch (NotAttachedException $e) {
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        } catch (SsoException $e) {
            return redirect('user/error', ['sso_error' => urlencode($e->getMessage())], 307);
            //header("Location: error.php?sso_error=" . $e->getMessage(), true, 307);
        }

        if (!$user) {
            return redirect('user/login', 307);
            //header("Location: login.php", true, 307);
            exit;
        }
        return view('index', ['user' => $user]);
    }
    
    
    public function login()
    {
        $SSO_SERVER = "http://127.0.0.50/sso/index/index";
        $SSO_BROKER_ID = "Greg";
        $SSO_BROKER_SECRET = "7pypoox2pc";
        
        $errmsg = '';
        
        $broker = new \Jasny\SSO\Broker($SSO_SERVER, $SSO_BROKER_ID, $SSO_BROKER_SECRET);
        $broker->attach(true);
        $user = $broker->getUserInfo();
        if($user){
            return redirect('user/index');
        }
        $request = request();
        if($request->isPOST()){
            //var_dump($broker->login(input('username'), input('password')));exit;
            try {
                if (!empty(input('logout'))) {
                    $broker->logout();
                } elseif ($user = $broker->login(input('username'), input('password'))) {
                    //header("Location: index.php", true, 303);
                    var_dump($user);exit;
                    exit;
                }
                
                $errmsg = "Login failed";
            } catch (NotAttachedException $e) {
                $errmsg = "Login failed";
                //header('Location: ' . $_SERVER['REQUEST_URI']);
                //exit;
            } catch (SsoException $e) {
                $errmsg = $e->getMessage();
            }
        }
        return view('login',['errmsg' => $errmsg]);

    }
    
    public function error(){
//        $SSO_SERVER = "http://127.0.0.50/sso/index/index";
//        $SSO_BROKER_ID = "Greg";
//        $SSO_BROKER_SECRET = "7pypoox2pc";
//        
//        $broker = new \Jasny\SSO\Broker($SSO_SERVER, $SSO_BROKER_ID, $SSO_BROKER_SECRET);
        $error = input('sso_error');
        return view('error', ['error' => $error]);
    }
}
