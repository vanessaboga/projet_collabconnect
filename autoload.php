<?php

date_default_timezone_set('GMT');

$date = date('Y-m-d H:i:s');
include('config/service.inc.php');
include('config/config.php');

/*
function __autoload($className) {
	$classFile = dirname(__FILE__).'/class/class.'.$className.'.php';
	if (!file_exists($classFile)) {
		die('LOAD_CLASS_ERROR_'.$className);
	}
	include_once $classFile;
}
*/


spl_autoload_register(function ($className) {
	//include 'config/' . $class . '.php';
	$classFile = dirname(__FILE__) . '/class/class.' . $className . '.php';
	if (!file_exists($classFile)) {
		die('LOAD_CLASS_ERROR_' . $className);
	}
	include_once $classFile;
});

function SendSms($to, $from, $text, $smsc)
{
	$to = substr($to, -8);
	$url = "http://localhost:14013/cgi-bin/sendsms?username=digital&password=digital&smsc=$smsc&to=" . $to . "&text=" . urlencode(str_replace('{CR}', PHP_EOL, utf8_decode($text))) . "&from=" . urlencode($from);
	$response = trim(@file_get_contents($url));
	$to_log = 'envoi du SMS, to=' . $to . ', from=' . $from . ', text=' . $text . ', smsc=' . $smsc . ',retour= ' . $response . ' , url=' . $url . ': ' . $response;
	logger($to_log);
	return $response;
}

function allowedToSend($smscId, $msisdnToInvite)
{
	if (strlen($msisdnToInvite) != 8)
		return FALSE;
	return TRUE;
}

function logger($to_log)
{
}

function debug($a_afficher, $affiche = true)
{

	if ($affiche) {
		if ($affiche && is_array($a_afficher)) {
			print_r($a_afficher);
		} else
			print $a_afficher . PHP_EOL;
	}



}