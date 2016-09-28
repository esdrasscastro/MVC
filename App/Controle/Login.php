<?php
/**
 * Created by Grupo B+M
 * User: Esdras Castro
 * Date: 17/08/2016
 */

namespace Controle;


use Lib\Tools\Hash;
use Lib\Sistema;
use Lib\Tools\MailSender;
use Lib\Tools\Session;
use Modelo\Users;

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
                $dtime = date("Y-m-d H:i:s",date("U") - 300);

                if($Users->getUsersChangesTime() <= $dtime) $Login->resetAttempts();

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

    /**
     * Enviar uma senha de recuperacao
     * imprime um json
     *
     * @param string $fkey
     */
    public function recuperar($fkey='')
    {
        if(parent::siteRequest($fkey, 'recuperar_senha')){
            $email = filter_var(\Lib\Tools\Route::get('post')->users_username, FILTER_SANITIZE_EMAIL);
            if(!empty($email)){
                $Users = new Users();
                $Users->pegar($Users->prefix.'_username=:uname AND '.$Users->prefix.'_privilege<>"administrador"', array(':uname'=>$email));
                if($Users->rowCount()){
                    $Users = $Users->results();
                    /* Montar o layout do email*/
                    $code = Hash::rescue_key_generate($Users->getUsersUsername().date("Y-m-d")).'/'.$Users->getUsersId();
                    $mensagem= "";
                    require_once (parent::$htmlPath."login/template.recuperarsenha.phtml");
                    $MailSender = new MailSender();
                    $MailSender
                        ->addFrom(parent::$sitename, parent::$sitenoreply)
                        ->addTo($Users->getUsersName(), $Users->getUsersUsername())
                        ->subject("Recuperar senha")
                        ->message($mensagem)
                        ->send()
                    ;
                    if($MailSender->status()){
                        echo json_encode(array('status'=>true, 'error'=>false, 'message'=>'Enviamos um email com o link de redefinição de senha para o usuário informado.'));
                    }else{
                        echo json_encode(array('status'=>true, 'error'=>true, 'message'=>'Desculpe! Mas, não conseguimos enviar o email de redefinição de senha. Tente novamente mais tarde!'));
                    }
                }else{
                    echo json_encode(array('status'=>true, 'error'=>true, 'message'=>'O usuário informado não se encontra em nosso site.'));
                }
            }else{
                echo json_encode(array('status'=>true, 'error'=>true, 'message'=>'Informe um email válido.'));
            }
        }else{
            echo json_encode(array('status'=>true, 'error'=>true, 'message'=>'Esta sessão expirou, atualize a página e tente novamente.'));
        }
    }

    /**
     * Abre um formulário para alterar a senha de usuário
     *
     * @param string $fkey
     * @param int $uid
     */
    public function recuperarSenha($fkey='', $uid=0)
    {

        $fkey = filter_var($fkey, FILTER_SANITIZE_STRING);
        $uid = filter_var($uid, FILTER_SANITIZE_NUMBER_INT);
        parent::header("Recuperar Senha");
        if(!empty($fkey) and $uid > 0){
            $Users = new Users();
            if($Users->pegar($Users->prefix.'_id=:uid', array(':uid'=>$uid))->rowCount()){
                if(Hash::rescue_key_generate($Users->results()->getUsersUsername() . date("Y-m-d"))==$fkey){
                    parent::setJsScript("Login.init();");
                    require_once parent::$htmlPath."login/recuperarsenha.phtml";
                }else{
                    require_once parent::$htmlPath."login/linkexpirado.phtml";
                }
            }else{
                require_once parent::$htmlPath."login/linkinvalido.phtml";
            }
        }else{
            require_once parent::$htmlPath."login/linkinvalido.phtml";
        }
        parent::footer();
    }

    public function alterarsenha($fkey='', $uid=0)
    {
        $fkey = filter_var($fkey, FILTER_SANITIZE_STRING);
        $uid = filter_var($uid, FILTER_SANITIZE_NUMBER_INT);
        $senha = property_exists(\Lib\Tools\Route::get('post'), 'users_password')?\Lib\Tools\Route::get('post')->users_password : "";
        if(!empty($fkey) and $uid > 0 and !empty($senha)){
            $Users = new Users();
            if($Users->pegar($Users->prefix.'_id=:uid', array(':uid'=>$uid))->rowCount()){
                if(Hash::rescue_key_generate($Users->results()->getUsersUsername().date("Y-m-d"))==$fkey){
                    $Users = $Users->results();
                    if($Users->setUsersHash(Hash::generate_hash($senha))->setUsersPassword(Hash::password_create($senha, $Users->getUsersHash()))->atualizarSenha()){
                        echo json_encode(array('status'=>true, 'error'=>false, 'message'=>'Senha atualizada com sucesso!', 'fields'=>array(), 'errorInfo'=>$Users->getErrorInfo(), 'uid'=>$uid));
                    }else{
                        echo json_encode(array('status'=>true, 'error'=>true, 'message'=>'Erro ao tentar atualiza a sua senha. Atualize a página e tente novamente!', 'fields'=>array(), 'errorInfo'=>$Users->getErrorInfo(), 'uid'=>$uid));
                    }
                }else{
                    echo json_encode(array('status'=>true, 'error'=>true, 'message'=>'Este link de redefinição de senha expirou, faça uma nova solicitação.', 'fields'=>array(), 'errorInfo'=>$Users->getErrorInfo(), 'uid'=>$uid));
                }
            }else{
                echo json_encode(array('status'=>true, 'error'=>true, 'message'=>'Este link de redefinição de senha expirou, faça uma nova solicitação.', 'fields'=>array(), 'errorInfo'=>$Users->getErrorInfo(), 'uid'=>$uid));
            }
        }else{
            if(empty($fkey)){
                echo json_encode(array('status'=>true, 'error'=>true, 'message'=>'Este link de redefinição de senha expirou, faça uma nova solicitação.', 'fields'=>array(), 'errorInfo'=>array(), 'uid'=>$uid));
            }else{
                echo json_encode(array('status'=>true, 'error'=>true, 'message'=>'A senha não foi informada', 'fields'=>array(array('name'=>'users_password', 'message'=>'Informe uma senha.')), 'errorInfo'=>array(), 'uid'=>$uid));
            }
        }
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