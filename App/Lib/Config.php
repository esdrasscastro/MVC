<?php
/**
 * Created by Grupo B+M
 * User: Esdras Castro
 * Date: 13/09/2016
 */

namespace Lib;


class Config
{
    protected static $securitycode = 'a25s*[_o@pS-b#1S3uzP&89.1)!hTr+1/2$sa0%{21jAgsE54sP@39YjQlSA6';
    protected static $controller = 'Home';
    protected static $method = 'index';
    protected static $params = [];
    protected static $urlReferrer = '';
    protected $debugError = false;
    protected static $htmlPath = '';
    protected static $adminPath = '';
    protected static $painelPath = '';
    protected static $usuarioPath = '';
    protected static $controlePath = '';
    protected static $modeloPath = '';
    protected static $publicoPath = '';
    protected static $errorPath = '';
    protected static $mediasPath = '';
    protected static $imagePath = '';
    protected static $jsPath = '';
    protected static $cssPath = '';
    protected static $absPath = '';
    protected static $title = "";
    protected static $javascript = [];
    protected static $css = [];
    protected static $jsScript = [];
    protected static $styleScript = [];
    protected static $authenticated = false;
    protected static $basePath = '';
    protected static $menudesktop = [];
    protected static $menumobile = [];
    protected static $privilegio = [];
    protected static $usersPrivilege = '';
    protected static $breadcrumb = [];
    protected static $showBreadcrumb = true;
    protected static $dsn = '';
    protected static $hostdb = 'localhost';
    protected static $userdb = 'root';
    protected static $passdb = '';
    protected static $dbname = 'acheimed';
    protected static $privilegeAllowed = array();

    protected static $sitename = "Acheimed";
    protected static $sitemail = "contato@acheimend.com.br";
    protected static $sitenoreply = "no-replay@acheimend.com.br";

    public function __construct($dir='', $basepath='')
    {
        self::$privilegeAllowed = array('administrador', 'prestador', 'usuario', 'visitante');
        self::$absPath = $dir;
        self::$basePath = $basepath;
        self::$controlePath = self::$absPath.'/App/Controle/';
        self::$modeloPath = self::$absPath.'/App/Modelo/';
        self::$publicoPath = self::$absPath.'/App/Publico/';
        self::$htmlPath = self::$absPath.'/App/Publico/html/';
        self::$errorPath = self::$absPath.'/App/Publico/error/';
        self::$adminPath = self::$absPath.'/App/Publico/admin/';
        self::$painelPath = self::$absPath.'/App/Publico/painel/';
        self::$usuarioPath = self::$absPath.'/App/Publico/usuario/';

        self::$mediasPath = self::$basePath.'medias/';
        self::$imagePath = self::$mediasPath.'images/';
        self::$jsPath = self::$mediasPath.'js/';
        self::$cssPath = self::$mediasPath.'css/';

        self::$dsn = "mysql:host=".self::$hostdb.";dbname=".self::$dbname.";charset=UTF8";
    }

    protected static function useDatabase($yes=true)
    {
        if($yes) self::connect();

    }

    protected static function connect()
    {
        Connection::connect(self::$dsn, self::$userdb, self::$passdb);
    }

    protected static function setUserPrivilege($userPrivilege='')
    {
        if(!empty($userPrivilege) and in_array($userPrivilege, self::$privilegeAllowed)) self::$usersPrivilege = $userPrivilege;
    }

    protected static function userHasPrivilege()
    {
        return in_array(self::$usersPrivilege, self::$privilegio);
    }

    protected static function setPrivilege($privilege='')
    {
        if(!empty($privilege) and in_array($privilege, self::$privilegeAllowed)) array_push(self::$privilegio, $privilege);
    }

    protected static function hasPrivilege($privilege='')
    {
        return (!empty($privilege) and in_array($privilege, self::$privilegeAllowed)) ? true : false;
    }
}