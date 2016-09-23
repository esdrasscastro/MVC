<?php
/**
 * Created by Grupo B+M
 * User: Esdras Castro
 * Date: 17/08/2016
 */

namespace Controle;


use Lib\Tools\Hash;
use Lib\Sistema;
use Lib\Tools\Session;

class Login extends Sistema
{
    public function index()
    {
        self::$showBreadcrumb = false;
        self::header('Acessar meu painel');
        require_once(self::$htmlPath."login/index.phtml");
        self::setJsScript('Login.init();');
        self::footer();
    }

    public function auth($code='')
    {
        if($_SERVER['REQUEST_METHOD']=='POST' and isset($_POST['login'])){
            $loginpost = $_POST['login'];
            if(!empty($loginpost['username']) and !empty($loginpost['password']) and !empty($loginpost['time']) and $code== Hash::rescue_key_generate($loginpost['time'])){
                $logar = self::logar($loginpost['username'], $loginpost['password']);
                if($logar == 1) {
                    $link = parent::$basePath;
                    if(parent::$usersPrivilege == parent::$privilegeAllowed[0]) $link .= "admin";
                    else if(parent::$usersPrivilege == parent::$privilegeAllowed[1]) $link .= "painel";
                    else if(parent::$usersPrivilege == parent::$privilegeAllowed[2]) $link .= "usuario";

                    echo json_encode(array('status' => 1, 'error' => false, 'message' => 'Redirecionando...', 'redirect'=>$link));
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
        self::redirect(self::$basePath.'login/');
    }

    public static function logar($username, $password='', $bysession=false)
    {
        $Login = new \Modelo\Login($username);
        $Users = $Login->results();

        if($Login->rowCount()){
            if($bysession){
                self::sessionGenerate($Users);
                return parent::$authenticated = true;
            }else{
                if($Users->getUsersAttempts() > 0) {
                    if (Hash::password_compare($password, $Users->getUsersHash(), $Users->getUsersPassword())) {
                        $Login->resetAttempts();
                        self::sessionGenerate($Users);
                        return self::$authenticated = true;
                    }
                }else{
                    self::$authenticated = false;
                    return -1;
                }
            }
        }

        $Login->subtractAttempts();

        return self::$authenticated = false;
    }

    public function sessionGenerate(\Modelo\Users $Users)
    {
        $sessionid = session_regenerate_id(true);
        parent::setUserPrivilege($Users->getUsersPrivilege());
        self::$sessionname = self::sessionName();
        Session::create(self::$sessionname,array(
            'sessionid'=> $sessionid,
            'username' => $Users->getUsersUsername(),
            'privilege'=> $Users->getUsersPrivilege(),
            'browser'=> $_SERVER['HTTP_USER_AGENT'],
            'userip'=> parent::getClientIp()
        ));
    }

    public static function sessionName()
    {
        return md5('_MVC_'.self::removeSpecialChar(self::$basePath));
    }


}