<?php
namespace app\sso\controller;

use app\sso\logic\SSOServer;

class Index
{
    public function index()
    {
        $ssoServer = new SSOServer();        
        $command = isset($_REQUEST['command']) ? $_REQUEST['command'] : null;
        if (!$command || !method_exists($ssoServer, $command)) {
//            header("HTTP/1.1 404 Not Found");
//            header('Content-type: application/json; charset=UTF-8');
            return json(['error' => 'Unknown command']);
        }
       
        $result = $ssoServer->$command();
        debug_log($result);
        //return json($result);
    }
}
