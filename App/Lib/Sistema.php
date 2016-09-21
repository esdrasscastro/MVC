<?php

/**
 * Created by Grupo B+M
 * User: Esdras Castro
 * Date: 17/08/2016
 */
namespace Lib;

use BadFunctionCallException;
use Controle\Login;

class Sistema extends Config
{
    protected static $sessionname;

    public function __construct($dir='', $basepath='')
    {
        parent::__construct($dir, $basepath);

        $this->prepareUrl();

        try {
            $class = 'Controle\\'.self::$controller;
            if (class_exists($class)) {
                /* Verifica se a página precisa ser altenticada */
                if($class::hasAuth()){
                    self::auth();
                }else{
                    self::setBreadcrumb(parent::$basePath.'/'.strtolower(self::$controller),self::$controller);
                    call_user_func_array([$class, $this->method], $this->params);
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
    protected function auth()
    {
        $session = Session::get(Login::sessionName());
        if(isset($session['username'])) $username = $session['username'];
        else $username = '';

        if(parent::$controller!='Login'){
            if(!\Controle\Login::logar($username,'',true)) {
                self::redirect(parent::$basePath . 'login/');
            }
        }else{
            if(\Controle\Login::logar($username,'',true)) {
                self::redirect(parent::$absPath.'/');
            }
        }
    }

    /**
     * @param $href
     * @param $name
     * @param string $class
     */
    public function setBreadcrumb($href, $name, $class='')
    {
        array_push(parent::$breadcrumb, "<a href='{$href}' class='breadcrumb {$class}'>{$name}</a>");
    }

    /**
     * @param $href
     * @param $name
     * @param string $class
     */
    public function setBreadcrumbFirst($href, $name, $class='')
    {
        array_unshift(parent::$breadcrumb, "<a href='{$href}' class='breadcrumb {$class}'>{$name}</a>");
    }

    /**
     * @return string
     */
    public function getBreadcrumb()
    {
        $html = '';
        foreach (parent::$breadcrumb as $val){
            $html .= $val."\n";
        }

        return $html;
    }

    /*** Menu Desktop ***/
    public function setMenuDesktop($url='', $iconname='', $name='', $title='', $active=false, $urllocal=true, $classes='')
    {
        $icon = !empty($iconname)?"<i class='material-icons left'>{$iconname}</i>":'';
        $url = $urllocal?parent::$basePath.$url:$url;
        $active = $active?"class='active'":"";
        $classes = !empty($classes)?"class='{$classes}'":"";

        array_push(parent::$menudesktop, "<li {$active}><a href='{$url}' {$classes} title='{$title}'>{$icon} {$name}</a>");

        return $this;
    }

    /**
     * @return string
     */
    public function getMenuDesktop()
    {
        $html = "";
        foreach (parent::$menudesktop as $val){
            $html .= "{$val}\n";
        }

        return "<ul class='right hide-on-med-and-down Hnav'>{$html}</ul>";
    }
    /*** Fim Menu Desktop ***/
    /*** Menu Mobile ***/
    public function setMenuMobile($url, $iconname, $name, $title='', $active=false, $urllocal=true, $classes='')
    {
        $icon = !empty($iconname)?"<i class='material-icons left'>{$iconname}</i>":'';
        $url = $urllocal?parent::$basePath.$url:$url;
        $active = $active?"class='active'":"";
        $classes = !empty($classes)?"class='{$classes}'":"";

        array_push(parent::$menumobile, "<li {$active}><a href='{$url}' {$classes} title='{$title}'>{$icon} {$name}</a>");

        return $this;
    }

    /**
     * @return string
     */
    public function getMenuMobile()
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
    public function setJs($src)
    {
        if(!empty($src)) array_push(parent::$javascript, '<script src="'.$src.'" type="text/javascript"></script>');
        return $this;
    }

    /**
     * @param $string
     * @return $this
     */
    public function setJsScript($string)
    {
        if(!empty($string)) array_push(parent::$jsScript, $string);
        return $this;
    }

    /**
     * @param string $variablename
     * @return string
     */
    public function getJsScript($variablename='javascript'){
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
    public function setCss($href)
    {
        if(!empty($href)) array_push(parent::$css, '<link href="'.$href.'" rel="stylesheet" type="text/css" />');
        return $this;
    }

    /**
     * @param $string
     * @return $this
     */
    public function setStyleScript($string)
    {
        if(!empty($string)) array_push(parent::$styleScript, $string);
        return $this;
    }

    /**
     * @param string $variablename
     * @return string
     */
    public function getStyleScript($variablename='styleScript'){
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
    protected function redirect($href)
    {
        $href = filter_var(strtolower(trim(trim($href),'/')), FILTER_SANITIZE_URL);
        if(!empty($href)) header('Location: '.$href);
    }

    /**
     * @param string $url
     */
    protected function prepareUrl($url='')
    {
        $this->urlReferrer = $_SERVER['URL_REFERRER'];

        if(empty($url)) $url = isset($_REQUEST['url'])?filter_var(strtolower(trim(trim($_REQUEST['url']),'/')), FILTER_SANITIZE_URL):'';

        if(!empty($url)){
            $urlSplit = explode('/', $url);
            if(count($urlSplit) > 0){
                $this->setController($urlSplit[0]);
                unset($urlSplit[0]);

                if(isset($urlSplit[1])){
                    $this->setMethod($urlSplit[1]);
                    unset($urlSplit[1]);

                    if(!empty($urlSplit)){
                        $this->setParams($urlSplit);
                    }
                }
            }
        }
    }

    /**
     * @param string $controller
     * @return $this
     */
    protected function setController($controller='Home')
    {
        $controller = ucfirst($this->removeSpecialChar(trim($controller)));
        if(!empty($controller)){
            parent::$controller = $controller;
        }

        return $this;
    }

    /**
     * @param string $method
     * @return $this
     */
    protected function setMethod($method='index')
    {
        $method = $this->removeSpecialChar(trim($method));

        if(!empty($method)){
            $this->method = $method;
        }

        return $this;
    }

    /**
     * @param array $arr
     * @return $this
     */
    protected function setParams(array $arr)
    {
        if(!empty($arr)){
            $this->params = array_filter($arr);
        }

        return $this;
    }

    /**
     * @param $string
     * @return mixed
     */
    protected function onlyNumber($string)
    {
        return preg_replace("/[^0-9]/", "", $string);
    }

    /**
     * @param $string
     * @return mixed
     */
    protected function removeSpecialChar($string)
    {
        return preg_replace("/[^a-zA-Z0-9]/", "", $string);
    }

    /**
     * @param string $title
     */
    protected function header($title='')
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
    protected function siteRequest($fkey='', $rescueword=''){
        return (!empty($fkey) and \Lib\Tools\Hash::rescue_key_generate($rescueword)==$fkey and \Lib\Tools\Route::isSiteRequest());
    }

    /**
     *
     */
    protected function footer()
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
}