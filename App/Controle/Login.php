<?php
/**
 * Created by Grupo B+M
 * User: Esdras Castro
 * Date: 17/08/2016
 */

namespace Controle;


use Lib\Hash;
use Lib\Sistema;
use Lib\Session;

class Login extends Sistema
{
    public function index()
    {
        self::$showBreadcrumb = false;
        self::header('Home');
        require_once(self::$htmlPath."login/index.phtml");
        self::setJsScript('Login.init();');
        self::footer();
    }

    public function auth($code='')
    {
        if($_SERVER['REQUEST_METHOD']=='POST' and isset($_POST['login'])){
            $loginpost = $_POST['login'];
            if(!empty($loginpost['username']) and !empty($loginpost['password']) and !empty($loginpost['time']) and $code==\Lib\Hash::rescue_key_generate($loginpost['time'])){
                $logar = self::logar($loginpost['username'], $loginpost['password']);
                if($logar == 1) {
                    echo json_encode(array('status' => 1, 'error' => false, 'message' => 'Redirecionando...'));
                }else if($logar == -1){
                    echo json_encode(array('status' => 0, 'error' => false, 'message' => 'Número máximo de tentatívas esgotadas. Tente daqui a 5 minutos!'));
                }else{
                    echo json_encode(array('status' => 0, 'error' => false, 'message' => 'Usuário ou senha inválido.'));
                }
            }else{
                echo json_encode(array('status'=>1, 'error'=>false,'message'=>'Os campos usuário e senha são obrigatórios.'));
            }
        }else{
            $session = Session::get(self::sessionName());

            print_r($session);

            if(!empty($session)){
                if(self::logar($session['username'], '', true)) {
                    self::redirect(self::$urlReferrer);
                }else{
                    self::redirect(self::$basePath.'login/');
                }
            }else{
                self::redirect(self::$basePath.'login/');
            }
        }
    }

    public function hasAuth(){return false;}

    public static function out()
    {
        session_unset();
        self::redirect(self::$basePath.'sistema/login/');
    }

    public static function logar($username, $password='', $bysession=false)
    {
        $mlogin = new \Modelo\Login();
        $mlogin->getUser($username);

        if($mlogin->rowCount() == 1){
            if($bysession){
                self::sessionGenerate($mlogin->results());
                $user = $mlogin->results();
                self::$privilegio = $user->getPrivilege();
                return self::$authenticated = true;
            }else{
                $user = $mlogin->results();

                if($user->getAttempts() > 0) {
                    if (Hash::password_compare($password, $user->getHash(), $user->getPassword())) {
                        self::$privilegio = $user->getPrivilege();
                        self::sessionGenerate($mlogin->results());
                        self::cleatAttempts($username);
                        return self::$authenticated = true;
                    }
                }else{
                    self::$authenticated = false;
                    return -1;
                }
            }
        }

        self::subtractAttempts($username, 1);

        return self::$authenticated = false;
    }

    public function subtractAttempts($username, $amount)
    {
        $mlogin = new \Modelo\Login();
        return $mlogin->removeAttempts($username, $amount);
    }

    public function sessionGenerate(\Modelo\Login $login)
    {
        $sessionid = session_regenerate_id(true);
        self::$sessionname = self::sessionName();
        Session::create(self::$sessionname,array(
            'sessionid'=> $sessionid,
            'username' => $login->getUsername(),
            'privilege'=> $login->getPrivilege(),
            'browser'=> $_SERVER['HTTP_USER_AGENT'],
            'userip'=> self::get_client_ip()
        ));
    }

    public function cleatAttempts($username)
    {
        $mlogin = new \Modelo\Login();
        return $mlogin->resetAttempts($username);
    }

    public static function sessionName()
    {
        return md5('USER'.self::removeSpecialChar(self::$basePath));
    }

    public function get_client_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
}