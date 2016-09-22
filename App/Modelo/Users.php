<?php
/**
 * Created by Grupo B+M
 * User: Esdras Castro
 * Date: 21/09/2016
 */

namespace Modelo;


class Users extends DbModelo
{
    /** @var  integer */
    private $users_id;
    /** @var  string */
    private $users_username;
    /** @var  string */
    private $users_name;
    /** @var  string */
    private $users_password;
    /** @var  string */
    private $users_hash;
    /** @var  integer */
    private $users_attempts;
    /** @var  string */
    private $users_privilege;
    /** @var  string */
    private $users_last_login;
    /** @var  boolean */
    private $users_publico;

    function __construct(){
        $this->tableName = "users";
        $this->tableView = "users";
        $this->prefix = $this->semCaracteresEspeciais($this->tableName);
        $this->classname = get_class();
    }

    public function adicionar()
    {
        $dados = array();
        $dados['users_username'] = $this->getUsersUsername();
        $dados['users_name'] = $this->getUsersName();
        $dados['users_password'] = $this->getUsersPassword();
        $dados['users_hash'] = $this->getUsersHash();
        $dados['users_last_login'] = $this->getUsersLastLogin();
        $dados = array_filter($dados);

        return parent::inserir($dados);
    }

    public function editar()
    {
        $dados = array();
        $dados['users_id'] = $this->getUsersId();
        $dados['users_username'] = $this->getUsersUsername();
        $dados['users_name'] = $this->getUsersName();
        $dados['users_password'] = $this->getUsersPassword();
        $dados['users_hash'] = $this->getUsersHash();
        $dados['users_attempts'] = $this->getUsersAttempts();
        $dados['users_privilege'] = $this->getUsersPrivilege();
        $dados['users_last_login'] = $this->getUsersLastLogin();
        $dados['users_publico'] = $this->getUsersPublico();
        $dados = array_filter($dados);

        return parent::alterar($dados, 'users_id=:uid', array(':uid'=>$this->getUsersId()));
    }

    public function deletar()
    {
        return parent::apagar('users_id=:uid', array(':uid'=>$this->getUsersId()));
    }

    public function comparar()
    {
        // TODO: Implement comparar() method.
    }

    public function despublicar()
    {
        return parent::alterar(array($this->prefix."_publico"=>false), $this->prefix."_id=:id", array(':id'=>$this->getUsersId()));
    }

    public function publicar()
    {
        return parent::alterar(array($this->prefix."_publico"=>true), $this->prefix."_id=:id", array(':id'=>$this->getUsersId()));
    }

    /**
     * @return int
     */
    public function getUsersId()
    {
        return $this->users_id;
    }

    /**
     * @param int $users_id
     * @return Users
     */
    public function setUsersId($users_id)
    {
        $this->users_id = filter_var($users_id, FILTER_SANITIZE_NUMBER_INT);
        return $this;
    }

    /**
     * @return string
     */
    public function getUsersUsername()
    {
        return $this->users_username;
    }

    /**
     * @param string $users_username
     * @return Users
     */
    public function setUsersUsername($users_username)
    {
        $this->users_username = filter_var($users_username, FILTER_SANITIZE_STRING);
        return $this;
    }

    /**
     * @return string
     */
    public function getUsersName()
    {
        return $this->users_name;
    }

    /**
     * @param string $users_name
     * @return Users
     */
    public function setUsersName($users_name)
    {
        $this->users_name = filter_var($users_name, FILTER_SANITIZE_STRING);
        return $this;
    }

    /**
     * @return string
     */
    public function getUsersPassword()
    {
        return $this->users_password;
    }

    /**
     * @param string $users_password
     * @return Users
     */
    public function setUsersPassword($users_password)
    {
        $this->users_password = filter_var($users_password, FILTER_SANITIZE_STRING);
        return $this;
    }

    /**
     * @return string
     */
    public function getUsersHash()
    {
        return $this->users_hash;
    }

    /**
     * @param string $users_hash
     * @return Users
     */
    public function setUsersHash($users_hash)
    {
        $this->users_hash = filter_var($users_hash, FILTER_SANITIZE_STRING);
        return $this;
    }

    /**
     * @return int
     */
    public function getUsersAttempts()
    {
        return $this->users_attempts;
    }

    /**
     * @param int $users_attempts
     * @return Users
     */
    public function setUsersAttempts($users_attempts)
    {
        $this->users_attempts = filter_var($users_attempts, FILTER_SANITIZE_NUMBER_INT);
        return $this;
    }

    /**
     * @return string
     */
    public function getUsersPrivilege()
    {
        return $this->users_privilege;
    }

    /**
     * @param string $users_privilege
     * @return Users
     */
    public function setUsersPrivilege($users_privilege)
    {
        $this->users_privilege = filter_var($users_privilege, FILTER_SANITIZE_STRING);
        return $this;
    }

    /**
     * @return string
     */
    public function getUsersLastLogin()
    {
        return $this->users_last_login;
    }

    /**
     * @param string $users_last_login
     * @return Users
     */
    public function setUsersLastLogin($users_last_login)
    {
        $this->users_last_login = filter_var($users_last_login, FILTER_SANITIZE_STRING);
        return $this;
    }

    /**
     * @return boolean
     */
    public function getUsersPublico()
    {
        return $this->users_publico;
    }

    /**
     * @param boolean $users_publico
     * @return Users
     */
    public function setUsersPublico($users_publico)
    {
        $this->users_publico = $users_publico;
        return $this;
    }


}