<?php
/**
 * Created by Movementes.com
 * User: Esdras Castro
 * Date: 18/03/2016
 * Time: 11:29
 * Project: dhire
 * File: Cookie.php
 */

namespace Lib\Tools;

class Cookie {
    private static $expire = NULL;
    private static $path = NULL;
    private static $domain = NULL;
    private static $secure = NULL;
    private static $httponly = NULL;

    public static function setExpire($expire=0){
        if(is_numeric($expire) and $expire!=self::$expire)
            self::$expire = $expire;
    }

    public static function setPath($path='/'){
        if(!empty($path) and $path!=self::$path)
            self::$path = $path;
    }

    public static function setDomain($domain=NULL){
        if(!is_null($domain) and $domain!=self::$domain)
            self::$domain = $domain;
    }

    public static function setSecure($secure=NULL){
        if(!is_null($secure) and $secure!=self::$secure)
            self::$secure = $secure;
    }

    public static function setHttponly($httponly=NULL){
        if(!is_null($httponly) and $httponly!=self::$httponly)
            self::$httponly = $httponly;
    }

    /**
     * Cria um novo cookie
     *
     * @param string $name
     * @param string $value
     */
    public static function create($name='',$value=''){
        if(!is_null(self::$expire))
            setcookie($name,$value,time()+self::$expire);
        elseif(!is_null(self::$path))
            setcookie($name,$value,time()+self::$expire,self::$path);
        elseif(!is_null(self::$domain))
            setcookie($name,$value,time()+self::$expire,self::$path,self::$domain);
        elseif(!is_null(self::$secure))
            setcookie($name,$value,time()+self::$expire,self::$path,self::$domain,self::$secure);
        elseif(!is_null(self::$httponly))
            setcookie($name,$value,time()+self::$expire,self::$path,self::$domain,self::$secure,self::$httponly);
        else
            setcookie($name,$value);

    }

    /**
     * Apaga um cookie
     * @param string $name
     */
    public static function delete($name=''){
        setcookie($name,'',time()-self::$expire,self::$path,self::$domain,self::$secure,self::$httponly);
    }

    /**
     * Recupera um cookie
     *
     * @param string $name
     * @return bool
     */
    public static function get($name=''){
        if(!empty($name)){
            if( isset( $_COOKIE[ $name ] ) ) return $_COOKIE[ $name ];
        }

        return false;
    }
}