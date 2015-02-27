<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Paysimple extends CI_Model 
{
	function __construct()
		{
				parent::__construct();				
				$this->mode = 'live';
				if($this->mode == 'test')
				{
					$this->userName = "APIUser129138";
					$this->superSecretCode = "MnfBPLoOnum1h2SzD32gZfSzcUanr4mnuF8QDQvX7GsT51WfZWndCisSr8ngEVoXz7XBUobopCNOoTtLBD2co0WrcMlyIGrJOkweUV1eEDgY6fmxI8ejw3qZohPbDQR0";
					$this->api_url = "https://sandbox-api.paysimple.com/v4/";
				}
				else
				{
					$this->userName = "APIUser33550";
					$this->superSecretCode = "BZbeTsvTZPk74liTy8BrjndNlK4AYIbBAhxOUtBSU4lZNheD5qkMWSZfDc2JkXIGsavKBaTcfUYwPN1d0urOStrT3deUS1u5O0JnyWawcBMBAo2KhLfg8kFmI5J0Hth0";
					$this->api_url = "https://api.paysimple.com/v4/";
				}
		}

	function ping_api()
		{
				$timestamp = gmdate("c");
				$hmac = hash_hmac("sha256", $timestamp, $this->superSecretCode, true); //note the raw output parameter
				$hmac = base64_encode($hmac);
				$auth = "Authorization: PSSERVER AccessId = ".$this->userName."; Timestamp = $timestamp; Signature = $hmac";		
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				curl_setopt($curl, CURLOPT_URL, $this->api_url);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
				curl_setopt($curl, CURLOPT_HTTPHEADER, array($auth, 'Content-Type: application/json', 'Content-Length: 0'));
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);                                                                    
				var_dump(curl_exec($curl));
				$responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
				curl_close($curl);
				echo "<br>response: $responseCode <br><br>";
		}

	function create_account($user_data)
		{
				$timestamp = gmdate("c");
				$hmac = hash_hmac("sha256", $timestamp, $this->superSecretCode, true); //note the raw output parameter
				$hmac = base64_encode($hmac);
				$auth = "Authorization: PSSERVER AccessId = ".$this->userName."; Timestamp = $timestamp; Signature = $hmac";	
				$data_string = json_encode($user_data);
					
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				curl_setopt($curl, CURLOPT_URL, $this->api_url."customer");
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
				curl_setopt($curl, CURLOPT_HTTPHEADER, array($auth, 'Content-Type: application/json', 'Content-Length: '. strlen($data_string)));
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);                                                                    
				curl_setopt($curl, CURLOPT_POSTFIELDS,$data_string);  
				$gateway_reply = json_decode(curl_exec($curl),true);
				//echo "<br><br>"; print_r($gateway_reply);
				if($gateway_reply['Meta']['Errors'])
					{
						$r_array['success'] = 0;
						$r_array['msg'] = $gateway_reply['Meta']['Errors']['ErrorMessages'][0]['Message'];
					}
				else
					{
						$credit_accntID = $gateway_reply['Response']['Id'];
						$r_array['success'] = 1;
						$r_array['psUserID'] = $credit_accntID;
					}
				//print_r($r_array);
				return $r_array;
		}
	function add_credit_card($card_data)
		{
				//echo "In cc";
				$timestamp = gmdate("c");
				$hmac = hash_hmac("sha256", $timestamp, $this->superSecretCode, true); //note the raw output parameter
				$hmac = base64_encode($hmac);
				$auth = "Authorization: PSSERVER AccessId = ".$this->userName."; Timestamp = $timestamp; Signature = $hmac";	
				$data_string = json_encode($card_data);
					
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				curl_setopt($curl, CURLOPT_URL, $this->api_url."account/creditcard");
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
				curl_setopt($curl, CURLOPT_HTTPHEADER, array($auth, 'Content-Type: application/json', 'Content-Length: '. strlen($data_string)));
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);                                                                    
				curl_setopt($curl, CURLOPT_POSTFIELDS,$data_string);  
				$gateway_reply = json_decode(curl_exec($curl),true);
				//echo "<br><br>"; print_r($gateway_reply);


				if($gateway_reply['Meta']['Errors'])
					{
						$r_array['success'] = 0;
						$r_array['msg'] = $gateway_reply['Meta']['Errors']['ErrorMessages'][0]['Message'];
					}
				else
					{
						$credit_accntID = $gateway_reply['Response']['Id'];
						$r_array['success'] = 1;
						$r_array['ccUserID'] = $credit_accntID;
					}
				return $r_array;

		}

	function charge_credit_card($data)
		{
				$timestamp = gmdate("c");
				$hmac = hash_hmac("sha256", $timestamp, $this->superSecretCode, true); //note the raw output parameter
				$hmac = base64_encode($hmac);
				$auth = "Authorization: PSSERVER AccessId = ".$this->userName."; Timestamp = $timestamp; Signature = $hmac";	
				$data_string = json_encode($data);
				
					
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				curl_setopt($curl, CURLOPT_URL, $this->api_url."payment");
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
				curl_setopt($curl, CURLOPT_HTTPHEADER, array($auth, 'Content-Type: application/json', 'Content-Length: '. strlen($data_string)));
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);                                                                    
				curl_setopt($curl, CURLOPT_POSTFIELDS,$data_string);  
				$gateway_reply = json_decode(curl_exec($curl),true);
				//echo "<br><br>"; print_r($gateway_reply);
				
				if($gateway_reply['Meta']['Errors'] )
					{
						$r_array['success'] = 0;
						$r_array['msg'] = $gateway_reply['Meta']['Errors']['ErrorCode'];
					}
				else
					{
						$credit_status = $gateway_reply['Response']['Status'];
						$credit_tr_id = $gateway_reply['Response']['Id'];
						$FailureData = 	$gateway_reply['Response']['FailureData'];
						if($FailureData == '' && $credit_status == 'Authorized')
							{
								$r_array['tr_id'] = $credit_tr_id;
								$r_array['success'] = 1;
								$r_array['ccUserID'] = $gateway_reply['Response']['AccountId'];
							}
						else
							{
								$r_array['success'] = 0;
								$r_array['FailureData'] = $FailureData;
							}
					}
			//	print_r($r_array);
			//	exit();
				return $r_array;

		}


	function payu_latum($amt,$buyer_fullname,$contactPhone,$address1,$address2,$city,$state,$country,$emailID,$postcode,$cc_no,$ccv,$cc_year,$cc_month,$pay_method)
		{


						
						$userID = $this->session->userdata('userID');
						error_reporting(E_ALL);
						// TEST
						//$merchantId = '500238';
						//$apiLogin = '11959c415b33d0c';
						//$apiKey = '6u39nqhq8ftd0hlvnjfs66eh8c';
						//$accountID = '500537';
						//$URL = "https://stg.api.payulatam.com/payments-api/4.0/service.cgi";
						
						// LIVE
						$merchantId = '513579';
						$apiLogin   = 'dca42c4ed85a8c3';
						$apiKey     = '7h60vq3n792q7s9ega4jfecfav';
						$accountID  = '514924';
						$URL = "https://api.payulatam.com/payments-api/4.0/service.cgi";
						$currency = 'USD';
						$refID =  time().rand(9999,332242);
						
						$signature = md5("$apiKey~$merchantId~$refID~$amt~$currency");
						$description = "IC Purchase - $userID";
						$language = "en";
						$notifyUrl = "";
						
						$data = NULL;
						$data["test"] = FALSE;
						$data["language"] = $language;
						$data["command"] = 'SUBMIT_TRANSACTION';
						$data["merchant"]["apiLogin"] = $apiLogin;
						$data["merchant"]["apiKey"] = $apiKey;
						
						$data["transaction"]["order"]["accountId"] = $accountID;
						$data["transaction"]["order"]["referenceCode"] = $refID;
						$data["transaction"]["order"]["description"] = $description;
						$data["transaction"]["order"]["language"] = $language;
						$data["transaction"]["order"]["notifyUrl"] = $notifyUrl;
						$data["transaction"]["order"]["signature"] = $signature;
						
						$data["transaction"]["order"]["buyer"]["fullName"] = $buyer_fullname;
						$data["transaction"]["order"]["buyer"]["contactPhone"] = $contactPhone;
						$data["transaction"]["order"]["buyer"]["shippingAddress"]["street1"] = $address1;
						$data["transaction"]["order"]["buyer"]["shippingAddress"]["street2"] = $address1;
						$data["transaction"]["order"]["buyer"]["shippingAddress"]["city"] = $city;
						$data["transaction"]["order"]["buyer"]["shippingAddress"]["state"] = $state;
						$data["transaction"]["order"]["buyer"]["shippingAddress"]["country"] = $country;
						$data["transaction"]["order"]["buyer"]["shippingAddress"]["phone"] = $contactPhone;
						$data["transaction"]["order"]["buyer"]["shippingAddress"]["postalCode"] = $postcode;
						$data["transaction"]["order"]["buyer"]["emailAddress"] = $emailID;
						$data["transaction"]["order"]["additionalValues"]["TX_VALUE"]["value"] = $amt;
						$data["transaction"]["order"]["additionalValues"]["TX_VALUE"]["currency"] = $currency;
						$data["transaction"]["creditCard"]["number"] = $cc_no;
						$data["transaction"]["creditCard"]["securityCode"] = $ccv;
						$data["transaction"]["creditCard"]["expirationDate"] = "20".$cc_year."/".$cc_month;
						$data["transaction"]["creditCard"]["name"] = $buyer_fullname; // "Approved"
						$data["transaction"]["type"] = "AUTHORIZATION_AND_CAPTURE";
						$data["transaction"]["paymentMethod"] = $pay_method;
						$data["transaction"]["paymentCountry"] = "CO";
						$data["transaction"]["extraParameters"]["INSTALLMENTS_NUMBER"] = "1";
						
						
						
						$data_string = json_encode($data);
						//echo $data_string;
						//exit();
//						echo "Request Sent:<br>=============================================================== <br>";
//						echo $data_string;
//						echo "<br>=============================================================== <br> Response:<br> ";
																								   
						$ch = curl_init($URL);                                                                      
						curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
						curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
						curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);                                                                    
						curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json', 'Content-Length: ' . strlen($data_string)) );
						$result = curl_exec($ch);
						//var_dump($result);

						$xml = new SimpleXMLElement($result);
//						echo "<br><br>";
//						echo $xml->error;
//						exit();
						if($xml->code == "SUCCESS")
							{
								$r_array['success'] = 1;
								$r_array['tr_id'] = $xml->transactionResponse->transactionId;
								$r_array['orderId'] = $xml->transactionResponse->orderId;
								$r_array['raw_data'] = $data_string." ----> ".$_SERVER["HTTP_REFERER"];
						}
						else
							{
							
								$r_array['success'] = 0;
								$r_array['FailureData'] = $xml->error;
								$r_array['raw_data'] = $data_string." ----> ".$_SERVER["HTTP_REFERER"];
							}
							
						return $r_array;
	


		}


	function payu_latum_recurring($amt,$buyer_fullname,$contactPhone,$address1,$address2,$city,$state,$country,$emailID,$postcode,$cc_no,$ccv,$cc_year,$cc_month,$pay_method)
		{


						
						$userID = $this->session->userdata('userID');
						error_reporting(E_ALL);
						// TEST
						//$merchantId = '500238';
						//$apiLogin = '11959c415b33d0c';
						//$apiKey = '6u39nqhq8ftd0hlvnjfs66eh8c';
						//$accountID = '500537';
						//$URL = "https://stg.api.payulatam.com/payments-api/4.0/service.cgi";
						
						// LIVE
						$merchantId = '513579';
						$apiLogin   = 'dca42c4ed85a8c3';
						$apiKey     = '7h60vq3n792q7s9ega4jfecfav';
						$accountID  = '514924';
						$URL = "https://api.payulatam.com/payments-api/4.0/service.cgi";
						$currency = 'USD';
						$refID =  time().rand(9999,332242);
						
						$signature = md5("$apiKey~$merchantId~$refID~$amt~$currency");
						$description = "IC Purchase - $userID";
						$language = "en";
						$notifyUrl = "";
						
						$data = NULL;
						$data["test"] = FALSE;
						$data["language"] = $language;
						$data["command"] = 'SUBMIT_TRANSACTION';
						$data["merchant"]["apiLogin"] = $apiLogin;
						$data["merchant"]["apiKey"] = $apiKey;
						
						$data["transaction"]["order"]["accountId"] = $accountID;
						$data["transaction"]["order"]["referenceCode"] = $refID;
						$data["transaction"]["order"]["description"] = $description;
						$data["transaction"]["order"]["language"] = $language;
						$data["transaction"]["order"]["notifyUrl"] = $notifyUrl;
						$data["transaction"]["order"]["signature"] = $signature;
						
						$data["transaction"]["order"]["buyer"]["fullName"] = $buyer_fullname;
						$data["transaction"]["order"]["buyer"]["contactPhone"] = $contactPhone;
						$data["transaction"]["order"]["buyer"]["shippingAddress"]["street1"] = $address1;
						$data["transaction"]["order"]["buyer"]["shippingAddress"]["street2"] = $address1;
						$data["transaction"]["order"]["buyer"]["shippingAddress"]["city"] = $city;
						$data["transaction"]["order"]["buyer"]["shippingAddress"]["state"] = $state;
						$data["transaction"]["order"]["buyer"]["shippingAddress"]["country"] = $country;
						$data["transaction"]["order"]["buyer"]["shippingAddress"]["phone"] = $contactPhone;
						$data["transaction"]["order"]["buyer"]["shippingAddress"]["postalCode"] = $postcode;
						$data["transaction"]["order"]["buyer"]["emailAddress"] = $emailID;
						$data["transaction"]["order"]["additionalValues"]["TX_VALUE"]["value"] = $amt;
						$data["transaction"]["order"]["additionalValues"]["TX_VALUE"]["currency"] = $currency;
						$data["transaction"]["creditCard"]["number"] = $cc_no;
						$data["transaction"]["creditCard"]["securityCode"] = $ccv;
						$data["transaction"]["creditCard"]["expirationDate"] = "20".$cc_year."/".$cc_month;
						$data["transaction"]["creditCard"]["name"] = $buyer_fullname; // "Approved"
						$data["transaction"]["type"] = "AUTHORIZATION_AND_CAPTURE";
						$data["transaction"]["paymentMethod"] = $pay_method;
						$data["transaction"]["paymentCountry"] = "CO";
						$data["transaction"]["extraParameters"]["INSTALLMENTS_NUMBER"] = "1";
						
						
						
						$data_string = json_encode($data);
						//echo $data_string;
						//exit();
//						echo "Request Sent:<br>=============================================================== <br>";
//						echo $data_string;
//						echo "<br>=============================================================== <br> Response:<br> ";
																								   
						$ch = curl_init($URL);                                                                      
						curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
						curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
						curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);                                                                    
						curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json', 'Content-Length: ' . strlen($data_string)) );
						$result = curl_exec($ch);
						//var_dump($result);

						$xml = new SimpleXMLElement($result);
//						echo "<br><br>";
//						echo $xml->error;
//						exit();
						if($xml->code == "SUCCESS")
							{
								$r_array['success'] = 1;
								$r_array['tr_id'] = $xml->transactionResponse->transactionId;
								$r_array['orderId'] = $xml->transactionResponse->orderId;
								$r_array['raw_data'] = $data_string." ----> ".$_SERVER["HTTP_REFERER"];
						}
						else
							{
							
								$r_array['success'] = 0;
								$r_array['FailureData'] = $xml->error;
								$r_array['raw_data'] = $data_string." ----> ".$_SERVER["HTTP_REFERER"];
							}
							
						return $r_array;
	


		}



}