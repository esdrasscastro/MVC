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
            $this->error404();
        }
    }

    public function error404()
    {
        self::header('Página não encontrada');
        require_once parent::$errorPath.'404.phtml';
        self::footer();
    }

    public function error505()
    {
        self::header('Permissão negada!');
        require_once parent::$errorPath.'505.phtml';
        self::footer();
    }
}