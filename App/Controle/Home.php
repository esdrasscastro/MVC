<?php
/**
 * Created by Grupo B+M
 * User: Esdras Castro
 * Date: 17/08/2016
 */

namespace Controle;

use Lib\Sistema;

class Home extends Sistema
{
    public static $authenticated = false;

    public function index()
    {
        //self::myPrivilege();

        self::header('Home');
        require_once(self::$htmlPath."home/index.phtml");
        self::footer();
    }

    public static function hasAuth()
    {
        return self::$authenticated;
    }

//    private function myPrivilege()
//    {
//        if(parent::$privilegio != 'administrator'){
//            new Error(505);
//            exit;
//        }
//    }
}