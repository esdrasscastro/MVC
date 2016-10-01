<?php
if ((function_exists('session_status') && (session_status() !== PHP_SESSION_ACTIVE)) || !session_id()) session_start();
date_default_timezone_set("America/Sao_Paulo");
/**
 * Created by Grupo B+M
 * User: Esdras Castro
 * Date: 09/08/2016
 */
require "vendor/autoload.php";

if($_SERVER['HTTP_HOST'] == 'localhost') {
    $basepath = ((!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != "on") ? "http" : "https") . '://' . $_SERVER['HTTP_HOST'] . '/orcagrafica/';
}else {
    $basepath = ((!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != "on") ? "http" : "https") . '://' . $_SERVER['HTTP_HOST'] . '/';
}

$sistema = new \Lib\Sistema(__DIR__, $basepath);
