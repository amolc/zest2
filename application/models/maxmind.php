<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Maxmind extends CI_Model {
	function __construct()
		{
				parent::__construct();
		}

	function verify_fraud($ip,$xip,$city,$state,$postal,$country,$emailID,$bin,$username,$password,$binName='',$binPhone='',$custPhone='',$requested_type='standard',$shipAddr='',$shipCity='',$shipRegion='',$shipPostal='',$txnID='',$sessionID='',$accept_language='en-en',$user_agent)
		{

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, "http://maxmind.cyberneticolive.com/");
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
				$data = array(
					'ip' => $ip,
					'city' => $city,
					'region' => $state,
					'postal' => $postal,
					'country' => $country,
					'emailID' => $emailID,
					'bin' => $bin,
					'ip' => $xip,
					'usernameMD5' => $username,
					'passwordMD5' => $password,
					'binName' => $binName,
					'binPhone' => $binPhone,
					'custPhone' => $custPhone,
					'requested_type' => $requested_type,
					'shipAddr' => $shipAddr,
					'shipCity' => $shipCity,
					'shipRegion' => $shipRegion,
					'shipPostal' => $shipPostal,
					'txnID' => $txnID,
					'sessionID' => $sessionID,
					'accept_language' => $accept_language,
					'user_agent' => $user_agent
				);
				
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
				$contents = curl_exec($ch);
				curl_close($ch);
				$FR = unserialize($contents);
				return $FR;

		}
}