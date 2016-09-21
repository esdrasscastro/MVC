<?php
	
/**
 * Created by Movementes.com
 * User: Esdras Castro
 * Date: 18/03/2016
 * Time: 11:51
 * Project: dhire
 * File: Session.php
 */
namespace Lib\Tools;

class Session
{
    /**
     * Cria uma nova session
     *
     * @param string $index
     * @param string $value
     */
    public static function create($index='', $value=''){
        if(is_array($index))
            foreach($index as $i=>$v)
                $_SESSION[ $i ] = $v;
        else
            if(!empty($index) and !empty($value)) $_SESSION[ $index ] = $value;
    }

    /**
     * Verifica se a chave pai existe
     *
     * @param string $index
     * @return bool
     */
    public static function hasFather($index=''){
        if(!empty($index))
            return isset($_SESSION[$index]);

        return false;
    }

    /**
     * Retorna um array com os valores da session e limpa caso o $clear seja true
     *
     * @param string $index
     * @param bool $clear
     * @return string
     */
    public static function get($index='', $clear=false){
        if(!empty($index)){
            if(isset($_SESSION[$index])){
                $session = $_SESSION[$index];
                if($clear)unset( $_SESSION[$index]);
                return $session;
            }
        }
        return '';
    }
}