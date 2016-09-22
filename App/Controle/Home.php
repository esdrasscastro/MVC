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
    public function index()
    {
        self::header('Acheimed');
        require_once(self::$htmlPath."home/index.phtml");
        self::footer();
    }

    public static function hasAuth()
    {
        return false;
    }
}