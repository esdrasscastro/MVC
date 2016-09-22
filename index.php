<?php
if ((function_exists('session_status') && (session_status() !== PHP_SESSION_ACTIVE)) || !session_id()) session_start();
/**
 * Created by Grupo B+M
 * User: Esdras Castro
 * Date: 09/08/2016
 */
require "vendor/autoload.php";

if($_SERVER['HTTP_HOST'] == 'localhost:81') {
    $basepath = ((!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != "on") ? "http" : "https") . '://' . $_SERVER['HTTP_HOST'] . '/acheimed/';
}else {
    $basepath = ((!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != "on") ? "http" : "https") . '://' . $_SERVER['HTTP_HOST'] . '/';
}

$sistema = new \Lib\Sistema(__DIR__, $basepath);
