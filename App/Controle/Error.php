<?php
/**
 * Created by Grupo B+M
 * User: Esdras Castro
 * Date: 17/08/2016
 */

namespace Controle;


use Lib\Sistema;

class Error extends Sistema
{
    public function __construct($code)
    {
        if(method_exists($this, 'error'.$code)){
            call_user_func_array([$this, 'error'.$code], []);
        }else{
            self::error404();
        }
    }

    public static function error404()
    {
        parent::header('Página não encontrada');
        require_once parent::$errorPath.'404.phtml';
        parent::footer();
    }

    public static function error601()
    {
        parent::header('Erro de envio');
        require_once parent::$errorPath.'601.phtml';
        parent::footer();
    }

    public static function error505()
    {
        self::header('Permissão negada!');
        require_once parent::$errorPath.'505.phtml';
        self::footer();
    }
}