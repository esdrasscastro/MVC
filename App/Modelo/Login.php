<?php
/**
 * Created by Grupo B+M
 * User: Esdras Castro
 * Date: 18/08/2016
 */

namespace Modelo;

use Lib\Connection;

class Login
{
    /** @var  int */
    private $userid;
    /** @var  string */
    private $username;
    /** @var  string */
    private $password;
    /** @var  string */
    private $hash;
    /** @var  int */
    private $attempts;
    /** @var  string */
    private $privilege;
    /** @var  string */
    private $last_login;
    /** @var  boolean */
    private $published;

    public $tablename = '';
    private $results = null;
    private $rowcount = 0;

    /**
     * @return int
     */
    public function getUserid()
    {
        return $this->userid;
    }

    /**
     * @param int $userid
     */
    public function setUserid($userid)
    {
        $this->userid = $userid;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * @return int
     */
    public function getAttempts()
    {
        return $this->attempts;
    }

    /**
     * @param int $attempts
     */
    public function setAttempts($attempts)
    {
        $this->attempts = $attempts;
    }

    /**
     * @return string
     */
    public function getPrivilege()
    {
        return $this->privilege;
    }

    /**
     * @param string $privilege
     */
    public function setPrivilege($privilege)
    {
        $this->privilege = $privilege;
    }

    /**
     * @return string
     */
    public function getLastLogin()
    {
        return $this->last_login;
    }

    /**
     * @param string $last_login
     */
    public function setLastLogin($last_login)
    {
        $this->last_login = $last_login;
    }

    /**
     * @return boolean
     */
    public function isPublished()
    {
        return $this->published;
    }

    /**
     * @param boolean $published
     */
    public function setPublished($published)
    {
        $this->published = $published;
    }

    public function resetAttempts($username)
    {
        return Connection::update($this->tablename, array('attempts'=>5), 'username=:uname', array(':uname'=>$username));
    }

    public function removeAttempts($username, $amount)
    {
        $rs = self::getUser($username);
        if($rs) {
            if($rs->rowCount() > 0) {
                $user = $rs->results();

                $newamount = $user->getAttempts() - $amount;
                return Connection::update($this->tablename, array('attempts' => $newamount), 'username=:uname', array(':uname' => $username));
            }
        }

        return false;
    }

    public function getUser($username)
    {
        $rs = Connection::select($this->tablename, 'username=:uname', array(':uname'=>$username),'','*', '\\Modelo\\Login');
        if($rs){
            $this->results = $rs->results;
            $this->rowcount = $rs->rowCount;
        }

        return $this;
    }

    public function results()
    {
        return $this->results;
    }

    public function rowCount()
    {
        return $this->rowcount;
    }
}