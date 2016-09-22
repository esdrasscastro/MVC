<?php
/**
 * Created by Grupo B+M
 * User: Esdras Castro
 * Date: 18/08/2016
 */

namespace Modelo;

class Login extends Users
{
    public function __construct($username)
    {
        parent::__construct();
        parent::pegar('users_username=:uname', array(':uname'=>$username));
    }

    public function resetAttempts()
    {
        if(parent::rowCount())
            return parent::results()->setUsersAttempts(5)->editar();

        return false;
    }

    public function subtractAttempts()
    {
        if(parent::rowCount())
            if(parent::results()->getUsersAttempts() > 1)
                return parent::results()->setUsersAttempts(parent::results()->getUsersAttempts()-1)->atualizarTentativas();
            else if(parent::results()->getUsersAttempts() == 1)
                return parent::results()->setUsersAttempts(0)->atualizarTentativas();

        return false;
    }
}