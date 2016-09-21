<?php
/**
 * Created by PhpStorm.
 * User: Esdras Castro
 * Date: 04/02/2016
 * Time: 17:17
 * From: Movementes
 * Project: dhire
 * File: Route.php
 */
namespace Lib\Tools;

class Route {

    /**
     * @param string $type
     *
     * @return object
     */
    public static function get($type='get'){
        $return = array();
        $array = array();
        switch($type){
            case 'get' : $array = $_GET; break;
            case 'post' : $array = $_POST; break;
            case 'request' : $array = $_REQUEST; break;
            case 'session' : $array = $_SESSION; break;
            case 'cookie' : $array = $_COOKIE; break;
        }

        if(!empty($array)){
            foreach($array AS $index=>$value){
                if(is_array($value)){
                    $value = (object)$value;
                }
                $return[$index] = $value;
            }
        }else{
            $return['page'] = 'home';
        }

        return (object)$return;
    }

    /**
     * @param array  $values
     * @param string $type
     * @param string $cookielife
     * @param string $cookiepath
     *
     * @return bool
     */
    public static function set(array $values, $type='get', $cookielife=3600, $cookiepath='/'){
        if(is_array($values)){
            foreach($values AS $index=>$value){
                switch($type){
                    case 'get' : $_GET[$index] = $value; break;
                    case 'post' : $_POST[$index] = $value; break;
                    case 'request' : $_REQUEST[$index] = $value; break;
                    case 'session' : $_SESSION[$index] = $value; break;
                    case 'cookie' : setcookie($index, $value, time() + $cookielife, $cookiepath); break;
                }
            }
            return true;
        }
        return false;
    }

    /**
     * @param array  $values
     * @param string $type
     * @param string $cookielife
     * @param string $cookiepath
     */
    public static function clear(array $values, $type='get', $cookielife='2592000', $cookiepath='/'){
        if(is_array($values)){
            foreach($values AS $index=>$value){
                switch($type){
                    case 'get' : unset($_GET[$index]); break;
                    case 'post' : unset($_POST[$index]); break;
                    case 'request' : unset($_REQUEST[$index]); break;
                    case 'session' : unset($_SESSION[$index]); break;
                    case 'cookie' : unset($_COOKIE[$index]); setcookie($index, '', time() - $cookielife, $cookiepath); break;
                }
            }
        }
    }
	
	/**
	 * @return bool
	 */
	public static function isSiteRequest(){
		$dominio= $_SERVER['HTTP_HOST'];
		$referer = $_SERVER['HTTP_REFERER'];
		$isDominio = reset(explode('/',str_replace(array('http://','https://'),'',$dominio)));
		$isReferer = reset(explode('/',str_replace(array('http://','https://'),'',$referer)));
		return ($isDominio==$isReferer);
	}

	/**
	 * @param string $data
	 * @param string $format
	 *
	 * @return string
	 */
	public static function dataTransform($data='', $format='US'){
		if(!empty($data)){
			switch($format){
				case 'US' :
				case 'us' :
					if(count($data = explode('/',$data))==3)
						return $data[2].'-'.$data[1].'-'.$data[0];
					else
						die('Data informada é inválida. Favor, verifique o formato de data. Entrada: dd/mm/aaaa | Saída: aaaa-mm-dd');
				break;
				case 'BR' :
				case 'br' :
				if(count($data = explode('-',$data))==3)
					return $data[2].'/'.$data[1].'/'.$data[0];
				else
					die('Data informada é inválida. Favor, verifique o formato de data. Entrada: aaaa/mm/dd | Saída: dd-mm-aaaa');
				break;
				default : die ("Data informada é inválida");
			}
		}else{
			die("Informe a data a ser traduzida");
		}
	}

	public static function getFiles($folder, $exception=array(), $ext=array()){
		$diretorio = dir($folder);
		$files = array();
		while($arquivo = $diretorio->read()){
			if(!in_array($arquivo, $exception))
				if(!empty($ext)){
					if( in_array( strtolower( end( explode( '.', $arquivo ) ) ), $ext ) )
						 $files[]=$folder.$arquivo;
				}
				else{
					$files[]=$folder.$arquivo;
				}
		}
		$diretorio->close();
		return $files;
	}
}