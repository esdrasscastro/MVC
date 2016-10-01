<?php

/**
 * Created by Grupo B+M
 * User: Esdras Castro
 * Date: 17/08/2016
 */
namespace Lib;

use BadFunctionCallException;
use Controle\Error;
use Controle\Login;
use Lib\Tools\Session;

class Sistema extends Config
{
    protected static $sessionname;

    public function __construct($dir='', $basepath='')
    {
        parent::__construct($dir, $basepath);
        parent::useDatabase(false);

        $this->prepareUrl();

        try {
            $class = 'Controle\\'.self::$controller;
            if (class_exists($class)) {
                /* Verifica se a página precisa ser altenticada */
                if($class::hasAuth()){
                    if(self::auth()){
                        self::setBreadcrumb(parent::$basePath.'/'.strtolower(self::$controller),self::$controller);
                        call_user_func_array([$class, parent::$method], parent::$params);
                    }else{
                        self::$showBreadcrumb = false;
                        call_user_func_array(['Controle\\Error', 'error505'], []);
                    }
                }else{
                    self::setBreadcrumb(parent::$basePath.'/'.strtolower(self::$controller),self::$controller);
                    call_user_func_array([$class, parent::$method], parent::$params);
                }
            } else {
                self::$showBreadcrumb = false;
                call_user_func_array(['Controle\\Error', 'error404'], []);
            }
        }catch(BadFunctionCallException $err){
            if($this->debugError){
                echo "<pre>";
                print_r($err->getTrace());
                echo "</pre>";
            }
        }
    }

    /**
     * Se a página precisar de autenticação vai chamar este método, que redireciona para o painel ou para o login
     * ou mante na página que está tentando acessar
     */
    protected static function auth()
    {
        $session = Session::get(Login::sessionName());

        if(isset($session['username'])) $username = $session['username'];
        else $username = '';

        if(parent::$controller!='Login'){
            if(empty($username) || !(bool)Login::logar($username,'',true)) {
                self::redirect(parent::$basePath . 'login/');
            }else return true;
        }else{
            if(Login::logar($username,'',true)) {
                self::redirect(parent::$absPath);
            }
        }

        return false;
    }

    /**
     * @param $href
     * @param $name
     * @param string $class
     */
    public static function setBreadcrumb($href, $name, $class='')
    {
        array_push(parent::$breadcrumb, "<a href='{$href}' class='breadcrumb {$class}'>{$name}</a>");
    }

    /**
     * @param $href
     * @param $name
     * @param string $class
     */
    public static function setBreadcrumbFirst($href, $name, $class='')
    {
        array_unshift(parent::$breadcrumb, "<a href='{$href}' class='breadcrumb {$class}'>{$name}</a>");
    }

    /**
     * @return string
     */
    public static function getBreadcrumb()
    {
        $html = '';
        foreach (parent::$breadcrumb as $val){
            $html .= $val."\n";
        }

        return $html;
    }

    /*** Menu Desktop ***/
    public static function setMenuDesktop($url='', $iconname='', $name='', $title='', $active=false, $urllocal=true, $classes='')
    {
        $icon = !empty($iconname)?"<i class='material-icons left'>{$iconname}</i>":'';
        $url = $urllocal?parent::$basePath.$url:$url;
        $active = $active?"class='active'":"";
        $classes = !empty($classes)?"class='{$classes}'":"";

        array_push(parent::$menudesktop, "<li {$active}><a href='{$url}' {$classes} title='{$title}'>{$icon} {$name}</a>");

        return __CLASS__;
    }

    /**
     * @return string
     */
    public static function getMenuDesktop()
    {
        $html = "";
        foreach (parent::$menudesktop as $val){
            $html .= "{$val}\n";
        }

        return "<ul class='right hide-on-med-and-down Hnav'>{$html}</ul>";
    }
    /*** Fim Menu Desktop ***/
    /*** Menu Mobile ***/
    public static function setMenuMobile($url, $iconname, $name, $title='', $active=false, $urllocal=true, $classes='')
    {
        $icon = !empty($iconname)?"<i class='material-icons left'>{$iconname}</i>":'';
        $url = $urllocal?parent::$basePath.$url:$url;
        $active = $active?"class='active'":"";
        $classes = !empty($classes)?"class='{$classes}'":"";

        array_push(parent::$menumobile, "<li {$active}><a href='{$url}' {$classes} title='{$title}'>{$icon} {$name}</a>");

        return __CLASS__;
    }

    /**
     * @return string
     */
    public static function getMenuMobile()
    {
        $html = "";
        foreach (parent::$menumobile as $val){
            $html .= "{$val}\n";
        }

        return "<ul id='slide-out' class='side-nav'><li class='title-slide'>Menu</li>{$html}</ul>";
    }
    /*** Fim Menu Mobile ***/

    /*** Javascript ***/
    /**
     * @param $src
     * @return $this
     */
    public static function setJs($src)
    {
        if(!empty($src)) array_push(parent::$javascript, '<script src="'.$src.'" type="text/javascript"></script>');
        return __CLASS__;
    }

    /**
     * @param $string
     * @return $this
     */
    public static function setJsScript($string)
    {
        if(!empty($string)) array_push(parent::$jsScript, $string);
        return __CLASS__;
    }

    /**
     * @param string $variablename
     * @return string
     */
    public static function getJsScript($variablename='javascript'){
        $html = '';
        foreach (parent::$$variablename as $val){
            $html .= "{$val}\n";
        }

        return $html;
    }
    /*** Fim Javascript ***/
    /*** Style Script ***/
    /**
     * @param $href
     * @return $this
     */
    public static function setCss($href)
    {
        if(!empty($href)) array_push(parent::$css, '<link href="'.$href.'" rel="stylesheet" type="text/css" />');
        return __CLASS__;
    }

    /**
     * @param $string
     * @return $this
     */
    public static function setStyleScript($string)
    {
        if(!empty($string)) array_push(parent::$styleScript, $string);
        return __CLASS__;
    }

    /**
     * @param string $variablename
     * @return string
     */
    public static function getStyleScript($variablename='styleScript'){
        $html = '';
        foreach (parent::$$variablename as $val){
            $html .= "{$val}\n";
        }

        return $html;
    }
    /*** Fim Style Script ***/

    /**
     * @param $href
     */
    protected static function redirect($href)
    {
        $href = filter_var(strtolower(trim(trim($href),'/')), FILTER_SANITIZE_URL);
        if(!empty($href)) header('Location: '.$href);
    }

    /**
     * @param string $url
     */
    protected static function prepareUrl($url='')
    {
        if(isset($_SERVER['HTTP_REFERER'])) parent::$urlReferrer = $_SERVER['HTTP_REFERER'];

        if(empty($url)) $url = isset($_REQUEST['url'])?filter_var(strtolower(trim(trim($_REQUEST['url']),'/')), FILTER_SANITIZE_URL):'';

        if(!empty($url)){
            $urlSplit = explode('/', $url);
            if(count($urlSplit) > 0){
                self::setController($urlSplit[0]);
                unset($urlSplit[0]);

                if(isset($urlSplit[1])){
                    self::setMethod($urlSplit[1]);
                    unset($urlSplit[1]);

                    if(!empty($urlSplit)){
                        self::setParams($urlSplit);
                    }
                }
            }
        }
    }

    /**
     * @param string $controller
     * @return $this
     */
    protected static function setController($controller='Home')
    {
        $controller = ucfirst(self::removeSpecialChar(trim($controller)));
        if(!empty($controller)){
            parent::$controller = $controller;
        }

        return __CLASS__;
    }

    /**
     * @param string $method
     * @return $this
     */
    protected static function setMethod($method='index')
    {
        $method = self::removeSpecialChar(trim($method));

        if(!empty($method)){
            self::$method = $method;
        }

        return __CLASS__;
    }

    /**
     * @param array $arr
     * @return $this
     */
    protected static function setParams(array $arr)
    {
        if(!empty($arr)){
            parent::$params = array_filter($arr);
        }

        return __CLASS__;
    }

    /**
     * @param $string
     * @return mixed
     */
    protected static function onlyNumber($string)
    {
        return preg_replace("/[^0-9]/", "", $string);
    }

    /**
     * @param $string
     * @return mixed
     */
    protected static function removeSpecialChar($string)
    {
        return preg_replace("/[^a-zA-Z0-9]/", "", $string);
    }

    /**
     * @param string $title
     */
    protected static function header($title='')
    {
        parent::$title = $title;
        /*
         * Menu do usuário quando estiver logado
         * Implementar quais menus serão mostrados dependendo do privilágio do usuário
         *
         * Se não estiver logado, mostra o menu normal.
         */
        if(parent::$authenticated) {
            /* DESKTOP MENU */

            /* MODILE MENU */

        }else{
            /* DESKTOP MENU */
            self::setMenuDesktop('quemsomos/', '', 'Quem somos', 'Saiba mais sobre o Acheimed', false, true, parent::$controller=='Home'?'link':'');
            self::setMenuDesktop('cadastrar/', '', 'Quero me associar', 'Entre para o Acheimed', false, true, parent::$controller=='Home'?'btn_menu':'');
            /* MODILE MENU */
            self::setMenuMobile('quemsomos/', '', 'Quem somos', 'Saiba mais sobre o Acheimed', false, true,  parent::$controller=='Home'?'link':'');
            self::setMenuMobile('cadastrar/', '', 'Quero me associar', 'Entre para o Acheimed', false, true,  parent::$controller=='Home'?'btn_menu':'');
        }

        self::setCss(parent::$cssPath.'materialize.css');
        self::setCss(parent::$cssPath.'materialdesignicons.css');
        self::setCss(parent::$cssPath.'geral.css');

        require_once parent::$publicoPath.'header.php';
    }

    /**
     * Verifica se a chave fkey existe (ajuda a previnir csrf) e
     * se essa chave é valida (utilizar a string usada na criacao do hash rescueword)
     * e se o script foi solicitado pelo site e não externamente
     *
     * @param string $fkey
     * @param string $rescueword
     * @return bool
     */
    protected static function siteRequest($fkey='', $rescueword=''){
        return (!empty($fkey) and \Lib\Tools\Hash::rescue_key_generate($rescueword)==$fkey and \Lib\Tools\Route::isSiteRequest());
    }

    /**
     *
     */
    protected static function footer()
    {
        self::setJs('https://code.jquery.com/jquery-2.1.1.min.js');
        self::setJs(parent::$jsPath.'bin/materialize.js');
        /*self::setJs(self::$jsPath."meiomask.js");*/
        self::setJs(self::$jsPath."jquery.mask.min.js");
        /*self::setJs(self::$jsPath."jquery.formatter.min.js");*/
        self::setJs(self::$jsPath."jquery.validate.min.js");
        self::setJs(parent::$jsPath.'sistema.js');
        self::setJsScript("$('.button-collapse').sideNav({menuWidth: 300,edge: 'left',closeOnClick: true});");
        self::setJsScript('$(document).ready(function(){$(".parallax").parallax();});');
        self::setJsScript('$(document).ready(function(){$("select").material_select();});');
        self::setJsScript("$('.tooltipped').tooltip({delay: 80});");

        require_once parent::$publicoPath.'footer.php';
    }

    /**
     * Verifica se um número de CPF é válido
     *
     * @param string $cpf
     * @return bool
     */
    protected static function validaCpf($cpf='')
    {
        $cpf = filter_var($cpf, FILTER_SANITIZE_STRING);
        $cpf = preg_replace("/[^0-9]/", "", $cpf);
        $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);
        $invalid = array(00000000000,11111111111,22222222222,44444444444,55555555555,66666666666,77777777777,88888888888,99999999999);

        if(!empty($cpf)){
            /* verifica se CPF tem 11 números */
            if(strlen($cpf) != 11) return false;
            else{
                if(in_array($cpf, $invalid)) return false;
                else{
                    /* Valida o número de CPF */
                    for ($t = 9; $t < 11; $t++) {
                        for ($d = 0, $c = 0; $c < $t; $c++) {
                            $d += $cpf{$c} * (($t + 1) - $c);
                        }
                        $d = ((10 * $d) % 11) % 10;
                        if ($cpf{$c} != $d) {
                            return false;
                        }
                    }

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Verifica se um número CPNJ é válido
     *
     * @param $cnpj
     * @return bool
     */
    protected static function validaCnpj($cnpj='')
    {
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
        $cpf = str_pad($cnpj, 14, '0', STR_PAD_LEFT);
        /* Valida tamanho */
        if (strlen($cnpj) != 14)
            return false;
        /* Valida primeiro dígito verificador */
        for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++)
        {
            $soma += $cnpj{$i} * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $resto = $soma % 11;
        if ($cnpj{12} != ($resto < 2 ? 0 : 11 - $resto))
            return false;
        /* Valida segundo dígito verificador */
        for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++)
        {
            $soma += $cnpj{$i} * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $resto = $soma % 11;
        return $cnpj{13} == ($resto < 2 ? 0 : 11 - $resto);
    }

    /**
     * Retorna o IP do usuário
     *
     * @return string
     */
    protected static function getClientIp() {
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