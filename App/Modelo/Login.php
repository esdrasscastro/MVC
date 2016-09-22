<?php
/**
 * Created by Grupo B+M
 * User: Esdras Castro
 * Date: 18/08/2016
 */

namespace Modelo;

class Login extends Users
{
    public function resetAttempts($username)
    {
        $Users = parent::pegar('users_username=:uname', array(':uname'=>$username),'','*',1);
        if($Users->rowCount()){
            return $Users->results()->setUsersAttempts(5)->editar();
        }

        return false;
    }

    public function subtractAttempts($username)
    {
        $Users = parent::pegar('users_username=:uname', array(':uname'=>$username),'','*',1);
        if($Users->rowCount()){
            $Users = $Users->results();
            return $Users->setUsersAttempts($Users->getUsersAttempts()-1)->editar();
        }

        return false;
    }
}