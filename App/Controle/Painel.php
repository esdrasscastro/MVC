<?php
/**
 * Created by Grupo B+M
 * User: Esdras Castro
 * Date: 22/09/2016
 */

namespace Controle;


use Lib\Sistema;

class Painel extends Sistema
{
    public static function index()
    {
        parent::setPrivilege('prestador');

        self::myPrivilege();
        parent::setJsScript("Cadastrar.init();");
        parent::setJsScript("Global.vars.basePath = '".self::$basePath."';");
        parent::header('Painel do Associado');
        require_once(self::$painelPath."index.phtml");
        parent::footer();
    }

    /**
     * Verifica se a página precisa ser autenticada
     *
     * @return bool
     */
    protected static function hasAuth()
    {
        return true;
    }

    /*
     * Privilégios de acesso a essa página
     */
    private static function myPrivilege()
    {
        if(!parent::userHasPrivilege()){
            new Error(505);
            exit;
        }
    }
}