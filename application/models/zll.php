<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Zll extends CI_Model {
	function __construct()
		{
				parent::__construct();
				$lic_status = $this->session->userdata('licOK');
				
				
		}
	
	
	function is_mobile()
		{
			require_once getcwd().'/mobiledetect/Mobile_Detect.php';
			$detect = new Mobile_Detect;
			$deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');
			$scriptVersion = $detect->getScriptVersion();
			return $deviceType;
		}
	function get_timezone_offset($remote_tz, $origin_tz = null) {
		if($origin_tz === null) {
			if(!is_string($origin_tz = date_default_timezone_get())) {
				return false; // A UTC timestamp was returned -- bail out!
			}
		}
		$origin_dtz = new DateTimeZone($origin_tz);
		$remote_dtz = new DateTimeZone($remote_tz);
		$origin_dt = new DateTime("now", $origin_dtz);
		$remote_dt = new DateTime("now", $remote_dtz);
		$offset = $origin_dtz->getOffset($origin_dt) - $remote_dtz->getOffset($remote_dt);
		return $offset;
	}
	
	function addconstant($lID,$ue,$un)
	{
		require_once 'ConstantContact/src/Ctct/autoload.php';
		require_once 'ConstantContact/src/Ctct/ConstantContact.php';
		require_once 'ConstantContact/src/Ctct/Components/Contacts/Contact.php';
		require_once 'ConstantContact/src/Ctct/Components/Contacts/ContactList.php';
		require_once 'ConstantContact/src/Ctct/Components/Contacts/EmailAddress.php';
		require_once 'ConstantContact/src/Ctct/Exceptions/CtctException.php';
		
		define("APIKEY", "4zg6pf8y56u4attyevkgyec6");
        define("ACCESS_TOKEN", "13d7df38-b2c0-46ee-9615-9b435fcc9070");
		
		$cc = new ConstantContact(APIKEY);
		
		try 
		{
			$lists = $cc->getLists(ACCESS_TOKEN);
		} 
		catch (CtctException $ex) 
		{
			foreach ($ex->getErrors() as $error) 
			{
				print_r($error);
				exit();
			}
		}
		
		try 
		{
			// check to see if a contact with the email addess already exists in the account
			$response = $cc->getContactByEmail(ACCESS_TOKEN,$ue);
			// create a new contact if one does not exist
			if (empty($response->results)) 
			{
				$action = "Creating Contact";
				$contact = new Contact();
				$contact->addEmail($ue);
				$contact->addList($lID);
				$contact->first_name = $un;
				$contact->last_name = $un;
				/*
				* The third parameter of addContact defaults to false, but if this were set to true it would tell Constant
				* Contact that this action is being performed by the contact themselves, and gives the ability to
				* opt contacts back in and trigger Welcome/Change-of-interest emails.
				*
				* See: http://developer.constantcontact.com/docs/contacts-api/contacts-index.html#opt_in
				*/
				$returnContact = $cc->addContact(ACCESS_TOKEN, $contact, false);
				// update the existing contact if address already existed
			} 
			else 
			{
				$action = "Updating Contact";
				$contact = $response->results[0];
				$contact->addList($lID);
				$contact->first_name = $un;
				$contact->last_name = $un;
				/*
				* The third parameter of updateContact defaults to false, but if this were set to true it would tell
				* Constant Contact that this action is being performed by the contact themselves, and gives the ability to
				* opt contacts back in and trigger Welcome/Change-of-interest emails.
				*
				* See: http://developer.constantcontact.com/docs/contacts-api/contacts-index.html#opt_in
				*/
				$returnContact = $cc->updateContact(ACCESS_TOKEN, $contact, false);
			}
		// catch any exceptions thrown during the process and print the errors to screen
		} 
		catch (CtctException $ex) 
		{
			
			return $ex->getErrors();
			
		}
	}
	
	function addToEmailNotification($wID,$wuID,$rTT,$rTime,$hTZ,$wrs,$un,$ue,$web_link,$web_title,$hangout_time_name='GMT')
		{
			
			date_default_timezone_set('GMT');

			if($wID == 2370)
				{
					
					//$offset = $this->get_timezone_offset('GMT',$hangout_time_name);
					//if($offset != 0) $hrs = ($offset / 3600); else $hrs = 0;
					//echo " <br>Ofset : $offset ";
					//exit();
				}
				
			$curr_time = time();
			//$rTT = $rTT - $offset;
		
			
			$web_hoster = '';
			for($i=1;$i<=8;$i++) 
			{
				$admin_name = $wrs[0]['admin_name_'.$i];
				$admin_email = $wrs[0]['admin_email_'.$i];
				if($admin_name)
					{
						$web_hoster .= "$admin_name ($admin_email)";
					}
			}
			
			$query=$this->db->query("select * from `sg_emailnotification` where wID = '$wID' ");			
			$rs = $query->result_array();		
			foreach($rs as $row)
				{
					$notification_subject = $row['notification_subject'];
					$duration = $row['notification_date_time'];
					$d1 = substr($duration, -1); // day or hour
					$d2 = substr($duration, 0, -1); // time
					//echo " $d1  - $d2 -> $duration ";
					$X_time = 0;
					if($d1=='d') $X_time = 60*60*24*$d2;
					else if($d1=='h') $X_time = 60*60*$d2;
					else $X_time = 60*$d2;
					
					if($d2 > 0)// before webinar
						{
							//$send_time = $rTT - $X_time;
							$send_time = $rTT - $X_time;

							if($wID == 2370)
							{

//								echo " date = ".date("m/d/Y h:i:s a");
//								echo " <br><br>". " date rTT = ".date("m/d/Y h:i:s a",$rTT) ;
//								echo " <br><br>". " date send_time = ".date("m/d/Y h:i:s a",$send_time) ;
//								echo "<br> --- <br>";
							
							
							
								//echo " $send_time = $rTT - $X_time; <br> -- $send_time - $hTZ; <br><br> ";
							}
							if($send_time > $curr_time)
								{
									$p = NULL;
									$r = NULL;
									$p[0] = '/{NAME}/';
									$p[1] = '/{EMAIL}/';
									$p[2] = '/{WEBINAR_LINK}/';
									$p[3] = '/{WEBINAR_HOST}/';
									$p[4] = '/{WEBINAR_TIME}/';
									$p[5] = '/{DATE}/';
									$p[6] = '/{LINK}/';
									$p[7] = '/{HOST}/';
									$p[8] = '/{TITLE}/';

									$r[0] = $un;
									$r[1] = $ue;
									$r[2] = $web_link;
									$r[3] = $web_hoster;
									$r[4] = $rTime;
									$r[5] = $rTime;
									$r[6] = $web_link;
									$r[7] = $web_hoster;
									$r[8] = $web_title;
									
									$bSubject = addslashes(preg_replace($p,$r,$row['notification_subject']));
									$bDetails = addslashes(preg_replace($p,$r,$row['notification_data']));
									
									

									
									$sendData = NULL;
									$sendData['bSubject'] = $bSubject;
									$sendData['bDetails'] = $bDetails;
									$sendData['bEmail'] = $ue;
									$sendData['bName'] = $un;
									
									//$sendData['sendTime'] = $send_time - $hTZ;
									$sendData['sendTime'] = $send_time;
									$this->zll->gInsert('broadcast',$sendData);
									
									//print_r($sendData);
									
								}
							else
								{
									//echo "$duration DO NOT SEND - ".date("m/d/Y h:i:s a",$send_time);
								}
						
						}
					else // after webinar
						{
									
									$send_time = $rTT + $X_time;
									
									//echo "$duration After webinar .. ".date("m/d/Y :h:i:s ",$send_time);
									$p = NULL;
									$r = NULL;
									$p[0] = '/{NAME}/';
									$p[1] = '/{EMAIL}/';
									$p[2] = '/{WEBINAR_LINK}/';
									$p[3] = '/{WEBINAR_HOST}/';
									$p[4] = '/{WEBINAR_TIME}/';
									$p[5] = '/{DATE}/';
									$p[6] = '/{LINK}/';
									$p[7] = '/{HOST}/';
									$p[8] = '/{TITLE}/';

									$r[0] = $un;
									$r[1] = $ue;
									$r[2] = $web_link;
									$r[3] = $web_hoster;
									$r[4] = $rTime;
									$r[5] = $rTime;
									$r[6] = $web_link;
									$r[7] = $web_hoster;
									$r[8] = $web_title;
									
									$bSubject = addslashes(preg_replace($p,$r,$row['notification_subject']));
									$bDetails = addslashes(preg_replace($p,$r,$row['notification_data']));
									

									
									$sendData = NULL;
									$sendData['bSubject'] = $bSubject;
									$sendData['bDetails'] = $bDetails;
									$sendData['sendTime'] = $send_time;
									$sendData['bEmail'] = $ue;
									$sendData['bName'] = $un;
									$this->zll->gInsert('broadcast',$sendData);
									
						}
					
				}
		
		}
	
	function setTime($t)
		{
			$lt = $this->session->userdata('local_timezone');
			return date('m/d/Y h:i:s a',$t+$lt);
		}

	function check_sesday($str,$value)
		{
			$exp = explode('###',$str);
			foreach($exp as $row)
			{
				if($row==$value) return 1;
			}
		}
		
	function getPopupName($id)
		{
			$query=$this->db->query("select offer_name from `sg_webinar_offer` where woID = '$id' ");			
			$my_array = $query->result_array();		
			$cnt = count($my_array);
			if($cnt)		
				return $my_array[0]['offer_name'];
			else	
				return 0;
		}
		
	function getPollName($id)
		{
			$query=$this->db->query("select pollQuestion from `sg_webinar_polls` where wpID = '$id' ");			
			$my_array = $query->result_array();		
			$cnt = count($my_array);
			if($cnt)		
				return $my_array[0]['pollQuestion'];
			else	
				return 0;
		}

	function gInsertAutoCall($callType,$userID,$time)
		{
			//return true;
		
			$twilio = NULL;
			$twilio['acUserID'] = $userID;
			$twilio['acCallTime'] = time();
			$twilio['acCallMethod'] = $callType;
			$twilio['acStatus'] = 0;
			$this->zll->gInsert('auto_call',$twilio);
		}
	function getUserIDFromPage($wID)
		{
			$sql_ref = $this->db->query("SELECT userID FROM sg_webinar  WHERE wID = $wID");
			$x = $sql_ref->result_array();
			foreach($x as $row) return $row['userID'];
		}
	function page_report($pageID)
		{
			$sql_ref = $this->db->query("SELECT view,optin,insertTime FROM sg_page_report WHERE pageID = $pageID");
			return $sql_ref->result_array();
		}
	function update_total_earnings($userID,$amt)	
		{
			//$query=$this->db->query_update("UPDATE sg_users SET ");			
		}
		function Signup($username,$email,$password,$number)
  {

   $data = array(
               'username' => $username,
               'email' =>  $email,
               'password' => md5($password),
			   'number' => $number,
			   'status' => '1'            
			   );

  $this->db->insert('users',$data); 
    return true;

  }
  function Signup1($username,$membertype,$email,$password,$number,$fullname,$affid,$sCode)
  {
   $data = array(
               'username' => $username,
               'membertype' => $membertype,
               'email' =>  $email,
               'password' => md5($password),
			   'number' => $number,
			   'number_verify' => $sCode,
			   'fullname' => $fullname,
			   'status' => '1',
               'aff_by' => $affid,
			   'joinDate'=>time(),
			   );
			   

  $id=$this->db->insert('users',$data); 
    return $id;

  }
  function showAllwebpages($offset,$per_page)
	{
		$query=$this->db->query("Select * from webpages limit $offset , $per_page");
		return $query->result_array();
	}
  function countRows8()
	{
		$query=$this->db->query("Select * from webpages");
		return $query->num_rows();
	}
  
 function AdminLogin($email,$password)
 {
	 $query = $this->db->get_where('admin', array('username' => $email,'password' => md5($password)));
	 
	 if($query->num_rows()>0)
	 {
	 
		 $data = array( 
		 'id' => $query->row('id'),
		 'username' => $query->row('username'),
		 'admin_logged_in' => TRUE
	 );
	 
		 $this->session->set_userdata($data);
		 
		 
		 return true;
	 
	 }
	 else
	 {
	 
		 return false;
	 
	 
	 }
 
 }
 
  function canCreateGroup()
	{
		$userID = $this->session->userdata('userID');
		
		$sqlConf = mysql_query("SELECT * FROM usertype_config WHERE id IN (SELECT membertype FROM users WHERE id='$userID')") or die(mysql_error());
		$aConf=mysql_fetch_array($sqlConf,MYSQL_ASSOC);
		
		$sqlGroupCount = mysql_query("SELECT count(*) as cnt FROM groups WHERE owner_uid='$userID'") or die(mysql_error());
		$aCount=mysql_fetch_array($sqlGroupCount,MYSQL_ASSOC);
		
		//print_r($aCount);exit();
		
		if($aConf['can_create_count_groups']>$aCount['cnt'])
		{
		   return true;
		}
		else 
		{
			return false;
    	}
	} 
	
	function Create($name,$id,$desc,$type,$owner_id,$sImage)
  {
  
     $data = array(
               'name' => $name,
               'gr_id' => $id,
               'desc' => $desc,
               'type' =>  $type,
			   'pending' =>  $type,
               'owner_uid' => $owner_id,
               'logo' => $sImage,
			   'date_time' => date('Y-m-d h:i:s')      
			   );
			   
			//   print_r($data);
			//   exit;

 			$query = $this->db->insert('groups', $data); 
  
 			$query1= $this->db->get_where('groups',array('date_time'=>$data['date_time'],'owner_uid'=> $owner_id,'pending' => $type,'name'=>$name));
  
     		 $join = array(
               'group_id' => $query1->row('id'),
               'member_uid' =>  $owner_id,
			   'pending1' => '0'
			   );
   			$this->db->insert('members',$join); 
   
  			 return true;
  
  }
  function getDetails($id)
  {
 
  $query =  $this->db->get_where('groups', array('id' => $id));
    if($query->num_rows() > 0 )
   {
   return true;
   }
   else
   {
   return false;
   }
  
  }
  function CheckJoin($member_uid,$group_id)
  {
  
  
	   $query =  $this->db->get_where('members', array('member_uid' => $member_uid,'group_id'=> $group_id,'pending1'=> 0) );
	   
	   if($query->num_rows() > 0 )
	   {
	
		return true;
	   }
	   else
	   {
	   
		return false;
	   }
  
  
  }
   function CheckJoin1($member_uid,$group_id)
  {
  
  
	   $query =  $this->db->get_where('members', array('member_uid' => $member_uid,'group_id'=> $group_id,'pending1'=> 1) );
	   
	   if($query->num_rows() > 0 )
	   {
	
		return true;
	   }
	   else
	   {
	   
		return false;
	   }
  
  
  }
  function get_all_groups($name)
{


$this->db->like('name',$name);


$query = $this->db->get('groups');

return $query;

}
  
   function CheckIfGroupIsPublic($id,$email)
  {
 
  $query = $this->db->get_where('groups',array('id' =>$id ,'type'=>'1','pending'=>'0') );
   
   if($query->num_rows() > 0 )
   {
    
	return true;
   }
   else
   {
 	 $query = $this->db->get_where('invites',array('group_id' => $id ,'email'=> $email));
     
     if($query->num_rows() > 0 )
     {
	 	return true;
     }
     else
     {
 
        return false;
   
      }	
  
  }
  
  }
  function GroupInfo($id)
  {  
  
  $query =  $this->db->get_where('groups', array('id' => $id));
  
  if($query->num_rows() > 0 )
   {
    
	$group_name = $query->row('name');
    
	return $group_name;
   
   }
   else
   {
   return false;
   }
  
  } 
  function Join($group_id,$email,$pending1)
  {
  
  
 $query = $this->db->get_where('groups',array('id' => $group_id ,'type'=>'1') );

 if($query->num_rows() > 0 )
   {
   		 $query = $this->db->get_where('users',array('email' => $email));
	     $ABT = $this->input->post('about1234');       
		 $data = array(
               'group_id' => $group_id,
               'member_uid' =>  $query->row('id'),
			   'pending1' => $pending1,
			   'about' => $ABT
			   );

        $this->db->insert('members', $data); 
		return true;
	 
	 
	 
   }
   else
   {
   

	  $query = $this->db->get_where('users',array('email' => $email));
	   $ABT = $this->input->post('about1234');       
		 $data = array(
               'group_id' => $group_id,
               'member_uid' =>  $query->row('id'),
			   'pending1' => '1',
			   'about' => $ABT
			   );

  			$this->db->insert('members', $data); 
  
      return true;
	 
   }

  
  }

		
	function get_earned_comission($userID)		
		{
			$amt = 0;
			$rs = $this->query("SELECT sum(amount) as SUM FROM sg_purches_history WHERE userID = '$userID'  AND purTime between 1356998400 and 1388534399 AND (`details` like '%Matrix Cycle Comission%'  or `details` LIKE '%contest%'  or `details` LIKE '%car bonus%' or `details` LIKE '%monthly subscription commission for%' or `details` LIKE '%Fast Start commission for user%'  or `details` LIKE '%Venta por Mensualidad en tu nivel%' or `details` LIKE '%de Bono de inicio%'  or `details` LIKE '%Unilevel commission for level%' or `details` LIKE '%Fast start bonus for level%') ");
			foreach($rs as $r)
				{
						$amt = $r['SUM'];
				
				}
			return $amt;
		}
		
	function makeHitEntry($userID,$hitType)
		{
			$twilio['userID'] = $userID;
			$twilio['hitTime'] = time();
			$twilio['hitType'] = $hitType;
			$this->zll->gInsert('hitsummary',$twilio);
		}
	function get_earned_comission_tax($userID)		
		{
			$amt = 0;
			$start_tt = mktime(0,0,0,1,1,2013);
			$end_tt = mktime(23,59,59,12,31,2013);
			$rs = $this->query("SELECT sum(amount) as SUM FROM sg_purches_history WHERE userID = '$userID' AND (`details` like '%Matrix Cycle Comission%'  or `details` LIKE '%contest%'  or `details` LIKE '%car bonus%' or `details` LIKE '%monthly subscription commission for%' or `details` LIKE '%Fast Start commission%'  or `details` LIKE '%Venta por Mensualidad en tu nivel%' or `details` LIKE '%de Bono de inicio%'  or `details` LIKE '%Unilevel commission for level%' or `details` LIKE '%Fast start bonus for level%' or  `details` LIKE '%$50 level matrix unilevel%' or  `details` LIKE '%Bono de Emprendedor en tu nivel%') ");
			foreach($rs as $r)
				{
						$amt = $r['SUM'];
				
				}
			return $amt;
		}
	function get_user_ticket_data($ticketID)
		{
			$users = $this->db->dbprefix('users');
			$ticket = $this->db->dbprefix('ticket');
			$query=$this->db->query("select * from $users as U, $ticket as T where T.ticketID='$ticketID' and T.userID = U.userID ");			
			return $query->result_array();				
		}
		
	function charge_refunds_to_commission_earner()
		{		
			$this->db->trans_start();
			$rs = $this->query("SELECT * FROM sg_refundboards WHERE refundStatus = 1 ");
			foreach($rs as $RefundData)
				{
					$affectedUserID = $RefundData['affectedUserID'];
					$refundedUserID = $RefundData['refundedUserID'];
					$refundReason = $RefundData['refundReason'];
					$refundAmount = $RefundData['refundAmount'];
					$rbID = $RefundData['rbID'];
					$refundedUsername = $this->getUsername($refundedUserID);
					$this->zll->query_update("UPDATE sg_refundboards SET refundStatus = '2'  WHERE rbID = '$rbID' ");	
					$this->zll->query_update("UPDATE sg_users SET `pendingCycleBal` = `pendingCycleBal` - '$refundAmount' WHERE userID = '$affectedUserID' ");	
					//echo "UPDATE sg_users SET `pendingCycleBal` = `pendingCycleBal` - '$refundAmount' WHERE userID = '$affectedUserID' <br>";
					// put into history
					$his_data = NULL;
					$his_data['userID'] = $affectedUserID;
					$his_data['purTime'] = time();
					$his_data['details'] = "Refund Requested By User [<strong>Username: $refundedUsername</strong>]  [<strong>Reason By user: $refundReason</strong>]";
					$his_data['amount'] = $refundAmount;
					$his_data['type'] = 4;
					$his_data['msgType'] = 34;
					$his_data['msgVal1'] = $refundReason;
					$his_data['msgVal2'] = $refundedUsername;
					$his_data['msgVal3'] = '';
					$this->zll->gInsert('purches_history',$his_data);
				}
			$this->db->trans_complete();		
		}		

	function get_site_url($https)
		{
			$ret_url = '';
			
			if($_SERVER['SERVER_NAME']=='www.globalprompt.org')
				{
					if($https==1) $ret_url = "http://www.dgaglobal.com/ingresocybernetico/";
					else $ret_url = "http://www.dgaglobal.com/ingresocybernetico/";
				}
			else if($_SERVER['SERVER_NAME']!='192.168.1.34' && $_SERVER['SERVER_NAME']!='localhost' )
				{
					if($https==1) $ret_url = "https://www.ingresocybernetico.com/";
					else $ret_url = "https://www.ingresocybernetico.com/";
				}
			else
				{
					if($_SERVER['SERVER_NAME']=='localhost')
						$ret_url	= "http://localhost/dropbox/newic/";
					else
						$ret_url	= "http://192.168.1.34/dropbox/newic/";
				}
			return $ret_url;
		}
	
	function send_pay_notification($amt,$method,$status)		
		{
			$Subject ="IC fund credit attempt :: $amt :: $method :: $status";
			$req = '';
			$var = NULL;
			$body1 = "A new user has just tried to pay <br> Amount: $amt <br> Method: $method <br> Status: $status";
			$head = "MIME-Version: 1.0\r\n".
			"Content-type: text/html; charset=UTF-8\r\n".
			"From: IngresoCybernetico.COM <admin@ingresocybernetico.COM>\r\n".
			"Date: ".date("r")."\r\n";	
			$sent=mail('globalprompt@gmail.com', $Subject,$req.$body1,$head);	
			$sent=mail('dgolden464@yahoo.com', $Subject,$req.$body1,$head);	
			$sent=mail('privado@juancarlosolaya.com', $Subject,$req.$body1,$head);	
		
		}
		
	function generateImage($phID,$r,$id,$xid=1,$check_type=1,$lang='spa')
		{
			error_reporting(E_ALL);
			
			include("Numbers/Words.php");
			$nw = new Numbers_Words();
			
			$img_path =getcwd()."/cdn/images/check_".$xid."_".$lang.".jpg";
			
			if($lang=='spa') { $fl = 'es_AR';  $dd = 'Dolares'; $date_format  = 'd/m/Y'; $print = 'Imprima el checque';}
			else  {$fl = 'en_US';  $dd = 'Dollars'; $date_format  = 'm/d/Y';  $print = 'Print Check';}


			$query=$this->db->query("select *  from sg_users as U, sg_purches_history as H where H.userID = U.userID and H.phID = $phID ");
			$userdata = $query->result_array();
			foreach($userdata as $row) 
			{
			
				$reason = '';
				if($row['type']==1 && $lang=='spa') { $reason = 'Commisiones de Ciclos'; }
				if($row['type']==1 && $lang=='eng') { $reason = 'Matrix Cycle Commission'; }
		
				if($row['type']==4 && $lang=='spa') { $reason = 'Commisiones pagadas'; }
				if($row['type']==4 && $lang=='eng') { $reason = 'Commission Payment'; }
		
				if($row['type']==6 && $lang=='spa') { $reason = 'Car Bonus'; }
				if($row['type']==6 && $lang=='eng') { $reason = 'Car Bonus'; }
				
		
				if($row['type']==9 && $lang=='spa') { $reason = 'Ingreso Cybernetico Monthly Contest Prize Money'; }
				if($row['type']==9 && $lang=='eng') { $reason = 'Ingreso Cybernetico Monthly Contest Prize Money'; }
				
				$xvx = explode(".",number_format($row['amount'],2,'.',''));
				$deci = $xvx[1];
				$vx = ucwords(preg_replace('/-/'," ",$nw->toWords($xvx[0],$fl)));
				$final_word = $vx." ".$dd;
				
				$lenth = strlen($final_word);
				$dash = 80 - $lenth;
				$nstr = NULL;
				for($i=0;$i<=($dash/2);$i++) {
				$nstr = $nstr."--";
				}
				
				$image =   imagecreatefromjpeg($img_path);
				$white = ImageColorAllocate($image, 0,0,0);
				$font = getcwd()."/cdn/verdana.ttf";
				imagettftext($image, 12, 0, 660, 75, $white, $font, date($date_format,$row['purTime']));
				imagettftext($image, 22, 0, 655, 183, $white, $font, number_format($row['amount'],2));
				imagettftext($image, 12, 0, 130, 183, $white, $font, $userdata[0]['firstname']." ".$userdata[0]['lastname']);
				//imagettftext($image, 12, 0, 30, 232, $white, $font, $vx." ".$dd." ".$deci."/100 USD");
				imagettftext($image, 12, 0, 30, 232, $white, $font, $final_word." /100 USD ".$nstr );
				imagettftext($image, 12, 0, 70, 310, $white, $font, $reason);
				header("content-type: image/png");
				imagepng($image);
			}
		}

	function verify_sec_pin($func)
		{
			?>
			<base href="<?=base_url()?>" />
			<link rel='stylesheet' type='text/css' href='<?=base_url()?>cdn/css/style.css'/>
			<link rel='stylesheet' type='text/css' href='<?=base_url()?>cdn/css/bootstrap-combined.min.css'/>
			<link href="<?=base_url()?>cdn/css/bootstrap-combined.min.css" rel="stylesheet">
			<script src="<?=base_url()?>cdn/js/jquery.js"></script>
			<script>jQuery.noConflict();</script>
			<script src="<?=base_url()?>cdn/js/bootstrap.js"></script>
			<script src="<?=base_url()?>cdn/js/bootstrap.min.js"></script>
			<script src="<?=base_url()?>cdn/js/bootstrap-button.js"></script>
			<script src="<?=base_url()?>cdn/js/bootstrap-dropdown.js"></script>
			<script src="<?=base_url()?>cdn/js/bootstrap-tab.js"></script>
			<script src="<?=base_url()?>cdn/js/bootstrap-tooltip.js"></script>
			<script src="<?=base_url()?>cdn/js/bootstrap-popover.js"></script>
			<script src="<?=base_url()?>cdn/js/bootstrap-carousel.js"></script>
			<script src="<?=base_url()?>cdn/js/bootstrap-transition.js"></script>
			<script src="<?=base_url()?>cdn/js/bootstrap-collapse.js"></script>
			<script src="<?=base_url()?>cdn/js/bootstrap-modal.js"></script>

			<script src="<?=base_url()?>cdn/js/uc.js"></script>
			
			<div id="SG_POP" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"  data-keyboard="false" data-backdrop="static" aria-hidden="true"></div>	
			<script>aj_call('<?=base_url()?>ma/check_epin/<?=$func?>/');</script>

			<?
		}
	
	function countTicketCount($status)
		{
			$var = NULL;
			if($status=='Open') $var = " and ticketStatus = 'Open' ";
			if($status=='Closed') $var = " and ticketStatus = 'Closed' ";
			if($status=='On-Hold') $var = " and ticketStatus = 'On Hold' ";

			$ticket = $this->db->dbprefix('ticket');
			$users = $this->db->dbprefix('users');
			$query=$this->db->query("select * from $ticket as T, $users as U where T.userID = U.userID $var");
			return $query->num_rows;
		}	
	function getTickets($status,$offset,$per_page)
		{
			$var = NULL;
			if($status=='Open') $var = " and ticketStatus = 'Open' ";
			if($status=='Closed') $var = " and ticketStatus = 'Closed' ";
			if($status=='On-Hold') $var = " and ticketStatus = 'On Hold' ";
			
			$ticket = $this->db->dbprefix('ticket');
			$users = $this->db->dbprefix('users');
			$query=$this->db->query("select * from $ticket as T, $users as U where T.userID = U.userID $var order by ticketID desc limit $offset,$per_page");
			return $query->result_array();
		}	
	function getTicketInfo($ticketID)
		{
			$ticket = $this->db->dbprefix('ticket');
			$users = $this->db->dbprefix('users');
			$query=$this->db->query("select * from $ticket as T, $users as U where T.userID = U.userID and T.ticketID = '$ticketID' ");
			$ret['ticketInfo'] = $query->result_array();		
			$where['ticketID'] = $ticketID;	
			$ret['replies'] = $this->gSelectWhere('reply',$where);			
			return $ret;
		}
	
	function check_act_mat($userID,$matrix)
		{
			$sql = mysql_query("SELECT * FROM sg_tree WHERE userID = '$userID' AND mtype = '$matrix' AND isActive = '1'") or die(mysql_error());
			$row = mysql_num_rows($sql);
			return $row;
		}
	
	 function TotalRec()
		{ 	
			$sql = "SELECT * FROM sg_newsletters";
			$q = $this->db->query($sql);
			return $q->num_rows();
		}
	 function my_friends($perPage)
		{
			$offset = $this->getOffset();
			$sql = mysql_query("SELECT * FROM  sg_newsletters Order By ID Desc LIMIT ".$perPage) or die(mysql_error());
			$row = mysql_num_rows($sql);
			return $row;
		}
  	 function getOffset()
		{
			$page = $this->input->post('page');
			if(!$page):
			$offset = 0;
			else:
			$offset = $page;
			endif;
			return $offset;
    	}
	
	function addMrFree($user_email,$user_name)
		{
			$para = $user_email."##".$user_name;
			$default_post_url = 'https://www.magicresponder.com/opt_in/record/';
			
			$active_lang = $this->session->userdata('active_lang');
			if(!$active_lang) $active_lang = 'spa';
			
			if($active_lang=='spa')	$autoresponderFormID = 'lpSYcWaPanGcmmY';
			if($active_lang=='por')	$autoresponderFormID = 'lpSYcWaPanGcmmY';
			if($active_lang=='eng')	$autoresponderFormID = 'lpSYcWaPanGcmmY';
			
			$redirectURL = '';
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$default_post_url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,"formID=$autoresponderFormID&listname=$autoresponderFormID&redirect=$redirectURL&email=".$user_email."&name=".$user_name);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$server_output = curl_exec ($ch);
			curl_close ($ch);
		}
		
	function addMrPaid($user_email,$user_name)
		{
			$default_post_url = 'https://www.magicresponder.com/opt_in/record/';
			
			$active_lang = $this->session->userdata('active_lang');
			if(!$active_lang) $active_lang = 'spa';
			
			if($active_lang=='spa')	$autoresponderFormID = 'lpOZbmiPanGcmmU';
			if($active_lang=='por')	$autoresponderFormID = 'lpOZbmyPanGXmmc';
			if($active_lang=='eng')	$autoresponderFormID = 'lpOZbmqPanGXmmg';
			
			$redirectURL = '';
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$default_post_url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,"formID=$autoresponderFormID&listname=$autoresponderFormID&redirect=$redirectURL&email=".$user_email."&name=".$user_name);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$server_output = curl_exec ($ch);
			curl_close ($ch);
			
			$para = $user_email."##".$user_name;
			$default_post_url2 = 'https://www.magicresponder.com/mr_api/delete_cl_free/';
			$ch1 = curl_init();
			curl_setopt($ch1, CURLOPT_URL,$default_post_url2);
			curl_setopt($ch1, CURLOPT_POST, 1);
			curl_setopt($ch1, CURLOPT_POSTFIELDS,"para=$para");
			curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
			$server_output = curl_exec ($ch1);
			curl_close ($ch1);
		}
	
	function create_mr_account()
		{
			$userID = $this->session->userdata('userID');
			$username = strtolower($this->session->userdata('username'));
			$firstname = $this->session->userdata('firstname');
			$lastname = $this->session->userdata('lastname');
			$emailID = $this->session->userdata('emailID');
			$sub_list = 50;
			
			$para = $userID."##".$username."##".$firstname."##".$lastname."##".$emailID."##".$sub_list;
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,"http://www.magicresponder.com/mr_api/create_mr_account/");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,"para=$para");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$server_output = curl_exec ($ch);
			curl_close ($ch);
			$formID = $server_output;
			$this->zll->add_new_capture_pages($userID,$username,$formID);		
		}
	
	function create_mr_account_()
		{
//			$con = mysql_connect('localhost', 'magicres_global','global') or die(mysql_error());
//			$selected = mysql_select_db('magicres_magic',$con);
//			mysql_connect('localhost', 'root','') or die(mysql_error());
//			mysql_select_db('magicresponder') or die(mysql_error());
			$this->load->database('mr',TRUE);			
			
			$userID = $this->session->userdata('userID');
			$username = strtolower($this->session->userdata('username'));
			$firstname = $this->session->userdata('firstname');
			$lastname = $this->session->userdata('lastname');
			$emailID = $this->session->userdata('emailID');
			$sub_list = 50;
			$user_name = "ICM_".$userID;
			
			$pass = rand(11111,9999999);
			$refID = 0;
			$ins_data['username'] = $user_name;
			$ins_data['password'] = $pass;
			$ins_data['emailID'] = $emailID;
			$ins_data['firstname'] = $firstname;
			$ins_data['lastname'] = $lastname;
			$ins_data['joinDate'] = time();
			$ins_data['refID'] = $refID;
			//$ins_data['uStatus'] = 1;
			$ins_data['userPlanChose'] = 10;
			$ins_data['ownFees'] = 10;
			$ins_data['ownPlan'] = time();
			$ins_data['subLimit'] = $sub_list;
			$ins_data['broLimit'] = ($sub_list * 50);
			
			if(!$refID) $refID = 1;
			mysql_query("INSERT INTO sg_users (`username`,`password`,`emailID`,`firstname`,`lastname`,`joinDate`,`refID`,`userPlanChose`,`ownFees`,`ownPlan`,`subLimit`,`broLimit`) VALUES ('$user_name','$pass','$emailID','$firstname','$lastname','".time()."','$refID',10,10,'".time()."',50,5000)") or die(mysql_error());
			$mr_userID = mysql_insert_id();
						
			//Create Campaign
			
			$campName = 'Default_IC_Campaign'; 
			$cam_userID = $mr_userID;
			$campInsTime = time();
			$campDetails = 'Default IC Campaign'; 
			$campManagerName = $user_name; 
			$campManagerEmail = $emailID; 
			$campCompanyName = 'IC'; 
			$campCompanyUrl = 'http://www.ingresocybernetico.com'; 
			$campEmailSignature = ''; 
			$campNameOfSender = $user_name; 
			$campEmailOfSender = $emailID; 
			$campNotifyUsernameWhenSubJoin = ''; 
			$campNotifyEmailWhenSubJoin = ''; 
			$campConfirmationSubject = 'Please confirm your subscription.'; 
			$campConfirmationMessage = "<p>Thank you for subscribing to our messages. To confirm your subscription follow the link below or copy and paste the URL into your browser. <br /><br /> {CONFIRMATION_URL} <br /><br />Click the link above to get the information you requested If the link does not work, copy and paste the full URL in the bar your browser\'s address <br /><br /> If you do not want to subscribe, simply ignore this message .</p>"; 
			$campSuccessUrl = 'https://www.magicresponder.com/opt_in/de_confirm/'; 
			$campSuccessUrlType = 3; 
			$campUnsubscribeUrl = 'https://www.magicresponder.com/opt_in/de_unsubscribe/'; 
			$campUnsubscribeUrlType = 3; 
			$campTimeZone = 0; 
			$doubleOptIn = 0; 
			
			mysql_query("INSERT INTO `sg_auto_campain` (`campName`,`userID`,`campInsTime`,`campDetails`,`campManagerName`,`campManagerEmail`,`campNameOfSender`,`campEmailOfSender`,`campNotifyUsernameWhenSubJoin`,`campNotifyEmailWhenSubJoin`,`campConfirmationSubject`,`campConfirmationMessage`,`campSuccessUrl`,`campSuccessUrlType`,`campUnsubscribeUrl`,`campUnsubscribeUrlType`)	VALUES ('$campName','$cam_userID','$campInsTime','$campDetails','$campManagerName','$campManagerEmail','$campNameOfSender','$campEmailOfSender','$campNotifyUsernameWhenSubJoin',0,'$campConfirmationSubject','$campConfirmationMessage','$campSuccessUrl','$campSuccessUrlType','$campUnsubscribeUrl','$campUnsubscribeUrlType')") or die(mysql_error());
			
			$newCampID = mysql_insert_id();
			//Create Form
			
			$campID = $newCampID;
			$form_userID = $mr_userID;
			$formType = 0;
			$formName = 'Default_IC_Form';
			$formStatus = '';
			$formCreateDate = time();
			$formField1 = '1###Headline';
			$formField2 = '1###Name';
			$formField3 = '1###Email';
			$formField4 = '1###Submit';
			$formField5 = '1###We respect your privacy.';
			$formField6 = '###Address:';
			$formField7 = '###Phone:';
			$formField8 = '###City:';
			$formField9 = '###Country:';
			$formField10 = '###Gender:';
			$formField11 = '###Age:';
			$formField12 = '';
			$campSuccessUrlType = 1;
			$campSuccessUrl = 'https://www.magicresponder.com/opt_in/de_confirm/';
			$campUnsubscribeUrlType = 1;
			$campUnsubscribeUrl = 'https://www.magicresponder.com/opt_in/de_unsubscribe/';
			$tempID = 5;
			$form_bg_color = 0;
			$form_width = 300;
			$fbConnect = 1;
			$ggConnect = 1;
			$yaConnect = 1;
			$thankyouUrl = "http://www.ingresocybernetico.com/".strtolower($username);

			
			mysql_query("INSERT INTO sg_auto_forms 
(`campID`, `userID`, `formType`, `formName`, `formStatus`, `formCreateDate`, `formField1`, `formField2`, `formField3`, `formField4`, `formField5`, `formField6`, `formField7`, `formField8`, `formField9`, `formField10`, `formField11`, `formField12`, `campSuccessUrlType`, `campSuccessUrl`, `campUnsubscribeUrl`, `campUnsubscribeUrlType`, `tempID`, `form_bg_color`, `form_width`, `fbConnect`, `ggConnect`, `yaConnect`, `thankyouUrl`)	VALUES
                                                                                                                                                       ('$campID','$userID','$formType','$formName','$formStatus','$formCreateDate','$formField1','$formField2','$formField3','$formField4','$formField5','$formField6',
'$formField7','$formField8','$formField9','$formField10','$formField11','$formField12','$campSuccessUrlType','$campSuccessUrl','$campUnsubscribeUrl','$campUnsubscribeUrlType','$tempID','$form_bg_color','$form_width','$fbConnect','$ggConnect','$yaConnect','$thankyouUrl')") or die(mysql_error());
			$formID = mysql_insert_id();
			$this->load->database('default',TRUE);			

			$this->zll->add_new_capture_pages($userID,$username,$formID);			
		}
	
	function add_new_capture_pages($userID,$username,$formID)
		{
			for($i = 1; $i<=3;$i++)
				{
			$sql = mysql_query("INSERT INTO `sg_optin` (`capID`, `userID`, `templeteID`, `postURL`, `templateType`, `digital_product`, `formID`, `site`, `headerText`, `formText`, `buttonText`, `footerText`, `field1Name`, `field1Title`, `field1Validation`, `field1ValidationError`, `field2Name`, `field2Title`, `field2Validation`, `field2ValidationError`, `field3Name`, `field3Title`, `field3Validation`, `field3ValidationError`, `field4Name`, `field4Title`, `field4Validation`, `field4ValidationError`, `field5Name`, `field5Title`, `field5Validation`, `field5ValidationError`, `field6Name`, `field6Title`, `field6Validation`, `field6ValidationError`, `field7Name`, `field7Title`, `field7Validation`, `field7ValidationError`, `field8Name`, `field8Title`, `field8Validation`, `field8ValidationError`, `field9Name`, `field9Title`, `field9Validation`, `field9ValidationError`, `field10Name`, `field10Title`, `field10Validation`, `field10ValidationError`) VALUES (NULL, '".$userID."', '".$i."', 'http://www.ingresocybernetico.com/".$username."/', 'account_page', '0', '".$formID."', 'magicresponder', 'Free Video Reveals...', 'Enter your email address to get this free video.', 'Download Now!', 'We hate spam as much as you do. Your information will never be shared or sold to a 3rd party.', 'Name', 'Enter your name', '', '', 'Email', 'Enter your email address', '', '', 'Phone', 'Enter your phone number', '', '', 'Address', 'Enter your address', '', '', '0', 'Enter your mobile number', '', '', '0', 'Enter your city', '', '', '0', 'Enter your state', '', '', NULL, '', '', '', NULL, '', '', '', NULL, '', '', '');") or die(mysql_error());
			}
		}
		

	function insertIntoBzLog($userID,$pts,$logData,$bzType=0,$bzValue=0)
		{
			if(!$pts) $pts = 1;	
			$bzID = $this->zll->getActiveBonanza();
			$insData['pts'] = $pts;
			$insData['userID'] = $userID;
			$insData['logData'] = $logData;
			$insData['logTime'] = time();
			$insData['bzID'] = $bzID;
			$insData['bzType'] = $bzType;
			$insData['bzValue'] = $bzValue;
			$this->gInsert('bzlog',$insData);
		}
	
	
	function hide_string($T = '')
		{
			$pre = substr($T,0,3);
			$sh = '';
			$strlen = strlen($T);
			$star = $strlen-10;
			for($i=3;$i<=$star;$i++) $sh .= "*";
			$sub_str = substr($T,$strlen-10,$strlen);
			$final = $pre.$sh.$sub_str;
			return $final;
		
		}
	
	function auto_payment_processing($code)
		{
			error_reporting(E_ALL);
			$de = $this->decrypt($code);
			$exp = explode("!",$de);
			$userID = $exp[0];
			$username = $exp[1];
			$refID = $exp[2];
			$plan = $matrix = $exp[3];
			$is_founder = $exp[4];
			$pay_this_month = $exp[5];
			$sql = $this->db->query("SELECT * FROM sg_users WHERE userID = '$userID' ");
			$rx = $sql->result_array($sql);
			if($is_founder == 1)
				{
					$this->db->query("UPDATE sg_users set `isfounder` = '1' where `userID` = '$userID' limit 1");
					$user_got_paid = $this->zll->pay_fast_track($userID,-1,0,$username);
					$admin_will_get = 300 - $user_got_paid;									
					$this->zll->admin_revenue(16,$admin_will_get,'C',1,"Become founder member fees");
					if($user_got_paid)
						$this->zll->admin_revenue(17,$user_got_paid,'C',1,"Fast start bonus paid to users");
					
					// put into history
					$his_data['userID'] = $userID;
					$his_data['purTime'] = time();
					$his_data['details'] = "Become founder member fees [By system]";
					$his_data['amount'] = 300;
					$his_data['type'] = 4;
					$his_data['msgType'] = 11;
					$his_data['msgVal1'] = '';
					$his_data['msgVal2'] = '';
					$his_data['msgVal3'] = '';
					$this->zll->gInsert('purches_history',$his_data);
					
					// insert into system log
					$this->zll->log_entry("Automatic upgrade to founder on first payment.",$userID);

					/* CONTEST */
					$pts = NULL;
					$pts = 20;
					$this->zll->updateBonanza($userID,$pts);
					$this->zll->insertIntoBzLog($userID,$pts,"Joining of President's Circle $pts Points",0,0);
					$this->zll->updateBonanza($refID,$pts);
					$this->zll->insertIntoBzLog($refID,$pts,"Downline Has Join President's Circle $pts Points",'Founder',0);
					/* CONTEST END*/
					
					/* NEW MEMBERS CONTET */
						$joinDateExp = $rx[0]['joinDate'] + (60*60*24*30);
						$currTime = time();
						if($joinDateExp >= $currTime)
							{
								$this->zll->query_update("UPDATE `sg_users` SET `weeklyContestPts` = `weeklyContestPts` + '$pts' WHERE `userID` = '$userID' limit 1");
							}
					/* END NEW MEMBERS CONTET */
				}
			
			
			$go_pay_this_month_comission = 1;
			if($plan)
				{
					$eplan = explode("-",$plan);
					
					foreach($eplan as $curr_plan)
					{
					if($curr_plan)
						{
						if($curr_plan==50) $go_pay_this_month_comission = 0;
						
						$query = $this->db->query("SELECT * FROM sg_users WHERE userID = '$refID'");
						$frdata =  $query->result_array();
						
						/* CONTEST */
						$pts = NULL;
						if($curr_plan==50) $pts = 1;
						if($curr_plan==100) $pts = 2;
						if($curr_plan==300) $pts = 3;
						if($curr_plan==500) $pts = 5;
						if($curr_plan==1500) $pts = 15;
						if($curr_plan==3500) $pts = 25;
						if($curr_plan==7500) $pts = 50;
						
						$this->zll->updateBonanza($refID,$pts);
						$this->zll->insertIntoBzLog($refID,$pts,"Downline Has Join \$$curr_plan Level Matrix $pts Point(s)",'Matrix',$curr_plan);

						$this->zll->updateBonanza($userID,$pts);
						$this->zll->insertIntoBzLog($userID,$pts,"Joining of \$$curr_plan Level Matrix $pts Point(s)",0,0);
						// for last vegas contest only -- remove after that
						//if($curr_plan >= 100) { $this->zll->pay_last_vegas_contest_1($userID); }
						/* CONTEST END*/

					/* NEW MEMBERS CONTET */
						$joinDateExp = $rx[0]['joinDate'] + (60*60*24*30);
						$currTime = time();
						if($joinDateExp >= $currTime)
							{
								$this->zll->query_update("UPDATE `sg_users` SET `weeklyContestPts` = `weeklyContestPts` + '$pts' WHERE `userID` = '$userID' limit 1");
							}
						$joinDateExp = $frdata[0]['joinDate'] + (60*60*24*30);
						if($joinDateExp >= $currTime)
							{
							$this->zll->query_update("UPDATE `sg_users` SET `weeklyContestPts` = `weeklyContestPts` + '$pts' WHERE `userID` = '$refID' limit 1");
							}
					/* END NEW MEMBERS CONTET */

					
						foreach($rx as $row)
							{
								
							
								if(!$this->check_act_mat($userID,$curr_plan)) // check if matrix is already active
								{
								
								$field = 'matrix'.$curr_plan;							
								$field_paid = 'paid'.$curr_plan;	
								
								$ux_matrix100 = $row['matrix100'];
								$ux_matrix300 = $row['matrix300'];
								$ux_matrix500 = $row['matrix500'];
								$subscribed = $row['subscribed'];
								$username = $row['username'];
								$query = $this->db->query("SELECT * FROM sg_users WHERE userID = '$refID'");
								$frdata =  $query->result_array();
	
								$acType = 1;
								if($refID)
								{

									// put into history
									$his_data = NULL;
									$his_data['userID'] = $userID;
									$his_data['purTime'] = time();
									$his_data['details'] = "Activation of bussiness center";
									$his_data['amount'] = $curr_plan;
									$his_data['type'] = 4;
									$his_data['msgType'] = 25;
									$his_data['msgVal1'] = '';
									$his_data['msgVal2'] = '';
									$his_data['msgVal3'] = '';
									$this->zll->gInsert('purches_history',$his_data);
								
									$sp_acType = $frdata[0]['acType'];							
									$refRefID = $frdata[0]['refID'];							
									$refUserName = $frdata[0]['username'];	
									$is_matrix_active = $frdata[0]['matrix'.$curr_plan];
									$xf = 'matrix'.$curr_plan;			

//								$tempRefIDX = $this->getPaidSponsorForLevel($curr_plan,$refID);
//								$activeUAID = $this->zll->getActiveUAID($tempRefIDX,$curr_plan);
//								echo " Plan -> $curr_plan  <br> is_active ->  $is_matrix_active  <br> Temp Ref ID -> $tempRefIDX <br> Active UA ID -> $activeUAID <br><br>";
			 				
																		
									if($is_matrix_active) // if sponser's matrix is active (paid member)
										{	
											$spill_over = NULL;
											$spill_over[] = $this->zll->getActiveUAID($refID,$curr_plan);		
											$this->zll->insertIntoTree2x2($userID,$refID,$spill_over,$username,$curr_plan,0);
											$this->zll->notify_sponsor_new_user_join($refID,$username,$curr_plan);
											
											if($matrix==50) 
											{	
												$this->zll->pay_50_buks_unilevel_in_advance($userID,-1,0);
												$exp_time = time() + (60*60*24*30);
												$this->db->query("UPDATE sg_users set `subscribed` = '1', `subscribeExpire` = '$exp_time' where `userID` = '$userID' limit 1");
											}
										}
									else // if sponser is free member put user under admin acount
										{
											if($matrix==50)
												{
													$spill_over = NULL;
													$tempRefIDX = $this->getPaidSponsorForLevel($curr_plan,$refID);
													$spill_over[] = $this->zll->getActiveUAID($tempRefIDX,$curr_plan);		
													$this->zll->insertIntoTree2x2($userID,$tempRefIDX,$spill_over,$username,$curr_plan,0);
													$this->zll->notify_sponsor_new_user_join($tempRefIDX,$username,$curr_plan);
													if($matrix==50)
													{
													 $this->zll->pay_50_buks_unilevel_in_advance($userID,-1,0);
													 $exp_time = time() + (60*60*24*30);
													 $this->db->query("UPDATE sg_users set `subscribed` = '1', `subscribeExpire` = '$exp_time' where `userID` = '$userID' limit 1");
													}
												}
											else
												{
													$spill_over = NULL;
													$tempRefIDX = $this->getPaidSponsorForLevel($curr_plan,$refID);
													$spill_over[] = $this->zll->getActiveUAID($tempRefIDX,$curr_plan);		
													$this->zll->insertIntoTree2x2($userID,$tempRefIDX,$spill_over,$username,$curr_plan,0);
													$this->zll->notify_sponsor_new_user_join($tempRefIDX,$username,$curr_plan);
												}
										}
								}
								
								$this->db->query("UPDATE sg_users SET `$field` = '1',`acType` = '1' WHERE `userID` = '$userID' ");
								
								}
								
							}
						if($matrix==50) $this->zll->pay_50_buks_unilevel_in_advance($userID,-1,0);
						
						}// end of if $curr_plan
					
					}// end of foreach
				}//end of plan if

			if($pay_this_month)
				{

					$user_got_paid = $f_amount = NULL; 
					$subscribeExpire = $rx[0]['subscribeExpire'];
					$today_tt = strtotime("today");
					if($today_tt < $subscribeExpire)
						$exp_time = $subscribeExpire + (60*60*24*30*1);
					else
						$exp_time = strtotime("+1 month");
					
					$this->db->query("UPDATE sg_users set `subscribed` = '1', `subscribeExpire` = '$exp_time' where `userID` = '$userID' limit 1");

					if($go_pay_this_month_comission)
						{
							$user_got_paid = $this->pay_recurring_comission($userID,-1,0,$username,1);
							$f_amount = 15 - $user_got_paid;									
							$this->zll->admin_revenue(9,10,'C',1,"Monthly subscription");
						}
					
				
					if($user_got_paid)
						$this->zll->admin_revenue(10,$user_got_paid,'C',1,"Monthly subscription paid to user");
					if($f_amount)
						$this->zll->admin_revenue(18,$f_amount,'C',1,"Flush amount of unilevel out of 15 USD");
						
					
					// put into history
					$his_data['userID'] = $userID;
					$his_data['purTime'] = time();
					$his_data['details'] = "Automatic Monthly Subscription Fees Paid";
					$his_data['amount'] = 25;
					$his_data['type'] = 4;
					$his_data['msgType'] = 22;
					$his_data['msgVal1'] = '';
					$his_data['msgVal2'] = '';
					$his_data['msgVal3'] = '';
					$this->zll->gInsert('purches_history',$his_data);
					$this->db->trans_complete();
					
					// insert into system log
					$this->zll->log_entry("Automatic paid for monthly subscription",$userID);
				
				
				}
		}
	
	function check_cycle2x2($uaID,$mtype=100,$xuaID=0)
		{
			$sql = mysql_query("SELECT * from sg_tree where uaID = '$uaID'") or die(mysql_error());
			while($row = mysql_fetch_array($sql))
			{	
				$ref1 = $row['refID'];
				$uaID1 = $row['uaID'];
				$username = $root_username = $row['username'];
				$sql1 = mysql_query("SELECT * from sg_tree where uaID = '$ref1' and mtype = '$mtype'") or die(mysql_error());
				while($row1 = mysql_fetch_array($sql1))
				{	
					$ref2 = $row1['refID'];
					$uaID2 = $row1['uaID'];
					
					$sql2 = mysql_query("SELECT * from sg_tree where uaID = '$ref2' and mtype = '$mtype'  ") or die(mysql_error());
					while($row2 = mysql_fetch_array($sql2))
					{	
						$directRefID = $row2['directRefID'];
						$uaID_root = $row2['uaID'];
						$userID_root = $row2['userID'];
						$username_root = $row2['username'];
				
						$u1 = $u2  = $u3 = $u4 = $u5 = $u6 = $un1 = $un2 = $un3 = $un4 = $un5 = $un6 =  $ua1 = $ua2= 0;
						$rx1 = $rx2 = 0;
						$sql_c1 = mysql_query("SELECT userID,username,uaID from sg_tree where `refID` = '$uaID_root' and mtype = '$mtype' order by uaID  ") or die(mysql_error());
						$tc = 0;
						while($r1 = mysql_fetch_array($sql_c1)) 
						{ 
							$tc++;
							if($tc==1) { $u1 = $r1['userID']; $un1 = $r1['username']; $ua1 = $r1['uaID']; }
							if($tc==2) { $u2 = $r1['userID']; $un2 = $r1['username']; $ua2 = $r1['uaID'];  }							
						}
						$x1 = mysql_query("SELECT username,userID from sg_tree where `refID` = '$ua1' order by uaID  ") or die(mysql_error());
						$tc = 0;
						while($xx1 = mysql_fetch_array($x1)) { 
							$tc++; 
							if($tc==1){ $u3 = $xx1['userID'];  $un3 = $xx1['username'];  }
							if($tc==2){ $u4 = $xx1['userID'];  $un4 = $xx1['username'];  }
						 }

						$rx2 = 0;
						$x2 = mysql_query("SELECT username,userID from sg_tree where `refID` = '$ua2' order by uaID ") or die(mysql_error());
						while($xx2 = mysql_fetch_array($x2)) { 
							$rx2++; 
							if($rx2==1){ $u5 = $xx2['userID'];  $un5 = $xx2['username'];  }
							if($rx2==2){ $u6 = $xx2['userID'];  $un6 = $xx2['username'];  }
						
						}
						
						if($u1 && $u2 && $u3 && $u4 && $u5 && $u6)
							{
						
						
								if($mtype==50) $amt = 125;
								if($mtype==100) $amt = 300;
								if($mtype==300) $amt = 900;
								if($mtype==500) $amt = 1500;
								if($mtype==1500) $amt = 4500;
								if($mtype==3500) $amt = 10500;
								if($mtype==7500) $amt = 20500;
								
								$cycle1000 = 0;
								$subscribed = 0;
								$subscribeExpire = 0;
								//$orgID = 0;
								
								$xwh123 = NULL;
								$xwh123['userID'] = $userID_root;
								$rs_sub = $this->zll->gSelectWhere('users',$xwh123);
								foreach($rs_sub as $row_sub) { $subscribed = $row_sub['subscribed']; $subscribeExpire = $row_sub['subscribeExpire'];  $real_sponsor = $row_sub['refID']; }
								
								$tx = time();		
								$this->db->query("UPDATE `sg_tree` set `paid` = '1',completeDate = '$tx',isActive = '0' where uaID = '$uaID_root' "); 
								// update refund table
								$this->db->query("UPDATE `sg_refundboards` SET `refundStatus` = '1' WHERE `uaID` = '$uaID_root' AND `refundAmount` = '$mtype' AND `refundStatus` = '0'  ");
								
								if($subscribed==1)
									{
										$this->db->query("UPDATE sg_users set `pendingCycleBal` = `pendingCycleBal` + '$amt', `totalICEarning` = `totalICEarning` + '$amt' where userID = '$userID_root'");
										$ins_history['userID'] = $userID_root;
										$ins_history['purTime'] = time();
										$ins_history['details'] = 'Matrix Cycle Comission';
										$ins_history['amount'] = $amt;
										$ins_history['type'] = 1;
										$ins_history['msgType'] = 26;
										$ins_history['msgVal1'] = '';
										$ins_history['msgVal2'] = '';
										$ins_history['msgVal3'] = '';
										$this->zll->gInsert('purches_history',$ins_history);
										$this->zll->gInsertAutoCall('after_cycle',$userID_root,time());	
									}
								else
									{
										// load into admin profit
										$this->zll->admin_revenue(15,$amt,'C',1,"Matrix Cycle Comission to admin due to inactive account. [UserID: $userID_root]");
									}
									
									if($userID_root==1)	{ $this->zll->admin_revenue(14,$amt,'C',1,"Admin cycle income."); }
									else {$this->zll->admin_revenue(23,$amt,'C',1,"User cycle income.");}
							
								
								
								$inz['tcTime'] = time();
								$inz['tcAmount'] = $mtype;
								$inz['tcIDs'] = "$u1###$u2###$u3###$u4###$u5###$u6";
								$inz['tcNames'] =  "$un1###$un2###$un3###$un4###$un5###$un6";
								$inz['tcMatSize'] = 22;
								$inz['userID'] = $userID_root;
								$new_inserted_tcID = $this->zll->gInsert('treecomplete',$inz);
								//update tree table witb board ID
								$this->db->query("UPDATE `sg_tree` set `tcID` = '$new_inserted_tcID'  where uaID = '$uaID_root' "); 
								
								if($mtype==50) // pay unilevel till 3 level for $50 leval matrix when user cycle
								{ 
									$this->zll->admin_revenue(26,10,'C',1,"Bonus Pool Admin Income");
									$user_got_paid = $this->payMatrixUnilevel($userID_root,-1,0,$root_username);
									$f_amount = 15 - $user_got_paid;		
									if($user_got_paid)
										$this->zll->admin_revenue(24,$user_got_paid,'C',1,"Bonus Pool Paid To user");
									if($f_amount)
										$this->zll->admin_revenue(25,$f_amount,'C',1,"Flush amount of bonus pool out of 15 USD");
								}
								
								
								// code to check if sponsor's matrix is active or not
								$is_matrix_active = 0;
								$xwh1234 = NULL;
								$xwh1234['userID'] = $real_sponsor;
								$rs_sub4 = $this->zll->gSelectWhere('users',$xwh1234);
								foreach($rs_sub4 as $row_sub4) { $is_matrix_active = $row_sub4['matrix'.$mtype];  }
								

								$spillOverIDs = NULL;	
								if($is_matrix_active || $real_sponsor == 0 )
									{
										$spillOverIDs[] = $this->zll->getActiveUAID($real_sponsor,$mtype);	
									}
								else
									{
										$tempRefIDX = $this->getPaidSponsorForLevel($mtype,$real_sponsor);
										$spillOverIDs[] = $this->zll->getActiveUAID($tempRefIDX,$mtype);		
									}
								$ins = $this->insertIntoTree2x2($userID_root,$directRefID,$spillOverIDs,$username_root,$mtype,0);

								
							}
					}
				}
			}
		}

	function insertIntoTree2x2($userID,$ref_id,$spillOverIDs,$username,$mtype=100,$xuaID=0)	
		{
			$nextSpillOver = NULL;
			foreach($spillOverIDs as $spUAID)
			{
				$sql_count = mysql_query("SELECT count(*) as CNT from sg_tree where `refID` = '$spUAID' and `mtype` = '$mtype'") or die(mysql_error());
				while($row = mysql_fetch_array($sql_count)) $users_below = $row['CNT'];	
				if($users_below<2)
					{
						if(!$spUAID){$spUAID = 0;}					
						$leg_cnt = $users_below + 1;
						$ins_data['mtype'] = $mtype;
						$ins_data['nodeInsDate'] = time();
						$ins_data['leg'] = $leg_cnt;
						$ins_data['userID'] = $userID;
						$ins_data['username'] = $username;	
						if($userID==1)
							$ins_data['refID'] = 0;
						else
							$ins_data['refID'] = $spUAID;
							
						if($username=='Root')
							$ins_data['directRefID'] = 0;
						else
							$ins_data['directRefID'] = $ref_id;
						$xuaID = $this->gInsert('sg_tree',$ins_data);
						$this->check_cycle2x2($xuaID,$mtype,$xuaID);
						return $xuaID;
					}
				else	
					{

						$sql_get_userdata = mysql_query("SELECT uaID from sg_tree where refID = '$spUAID'  and `mtype` = '$mtype' order by leg") or die(mysql_error());
						while($row = mysql_fetch_array($sql_get_userdata))
						{	
							$nextSpillOver[] = $row['uaID'];
						}
					}
			}
			$this->insertIntoTree2x2($userID,$ref_id,$nextSpillOver,$username,$mtype);
		}
		
	function getFrozenFunds($userID)
		{
				$sql_count = mysql_query("SELECT SUM(fzAmount) as FA from sg_frozenfunds where `userID` = '$userID'") or die(mysql_error());
				while($row = mysql_fetch_array($sql_count)) return $row['FA'];
				return 0;
		}


		
	function payMatrixUnilevel($userID,$lev,$total_paid=0,$xusername)
		{
			error_reporting(0);
			$new_lev = $lev + 1;
			if($lev < 3)
				{
					if($new_lev==1)	$pay = 5;
					else if($new_lev==2)	$pay = 3;
					else if($new_lev==3)	$pay = 2;
					else $pay = 0;

					$rs = $this->apcache->user_cache_data($userID,10);
					$username = $rs[0]['username'];
					$subscribed = $rs[0]['subscribed'];
					$isfounder = $rs[0]['isfounder'];
					$refID = $rs[0]['refID'];
					if($subscribed == 1 && $pay)
					{
						//, `xbal` = `xbal` - '$pay' 
						$this->db->query("UPDATE sg_users SET `pendingUnilevelBal` = `pendingUnilevelBal` + '$pay' where `userID` = '$userID'");
						
						$his_data = NULL;
						$his_data['userID'] = $userID;
						$his_data['purTime'] = time();
						$his_data['details'] = "Entrepreneur Bonus for level $new_lev  for matrix \$50 ";
						$his_data['amount'] = $pay;
						$his_data['type'] = 5;
						$his_data['msgType'] = 27;
						$his_data['msgVal1'] = $new_lev;
						$his_data['msgVal2'] = '';
						$his_data['msgVal3'] = '';
						$this->zll->gInsert('purches_history',$his_data);
						$total_paid += $pay;
					}
					else
					{
						// $this->db->query("UPDATE sg_users SET `xbal` = `xbal` - '$pay' where `userID` = '$userID'");
					}
				
					if($refID==0) // RETURN IF ROOT LEVEL IS ARRIVED
					{
						return $total_paid;
					}
					
					return $this->payMatrixUnilevel($refID,$new_lev,$total_paid,$xusername);
				}
			else
				{
					return $total_paid;
				}
		}		







	function pay_50_buks_unilevel_in_advance($userID,$lev,$total_paid=0)
		{
			error_reporting(0);
			$new_lev = $lev + 1;
			if($lev < 3)
				{
					if($new_lev==1)	$pay = 5;
					else if($new_lev==2)	$pay = 3;
					else if($new_lev==3)	$pay = 2;
					else $pay = 0;

					$rs = $this->apcache->user_cache_data($userID,72000);
					$refID = $rs[0]['refID'];
					
					$this->db->query("UPDATE sg_users SET `xbal` = `xbal` + '$pay' where `userID` = '$userID'");
					$total_paid += $pay;
					if($refID==0) // RETURN IF ROOT LEVEL IS ARRIVED
					{
						return $total_paid;
					}
					
					return $this->pay_50_buks_unilevel_in_advance($refID,$new_lev,$total_paid);
				}
			else
				{
					return $total_paid;
				}
		}		




	function getActiveUAID($userID,$matrix=100)
		{
			$sql = mysql_query("select * from sg_tree where userID = '$userID' and isActive = '1' and  mtype = '$matrix'") or die(mysql_error());
			while($row = mysql_fetch_array($sql)) { return $row['uaID']; }
		}
		

	function getRefArray()
		{
			$userID = $this->session->userdata('userID');
			$ref_arr_db = $this->apcache->getUserRefArray($userID,300);
			foreach($ref_arr_db as $row)
				{  
					$ref_arr[] = $row['userID']; 
				}
			return $ref_arr;
		}
		
	function getFounderArray()
		{
			$userID = $this->session->userdata('userID');
			$fo_arr_db = $this->apcache->getFounderArray(1200);
			foreach($fo_arr_db as $row)
				{  
					$fo_arr[] = $row['userID']; 
				}
			return $fo_arr;
		}

	function show_cycle_matrix($tcID)
		{
			$rs = $this->db->query("SELECT * FROM sg_treecomplete WHERE tcID = '$tcID'");
			foreach($rs->result_array() as $rowx)
			{
				$tcIDs = $rowx['tcIDs'];
				$tcNames = $rowx['tcNames'];
				$tcID_Array = explode("###",$tcIDs);
				$tcName_Array = explode("###",$tcNames);
				$username = $this->getUsername($rowx['userID']);
				$ref_arr = $this->getRefArray();
				$founder_arr = $this->getFounderArray();
				$row['lev1'] = $tcID_Array[0];
				$row['lev2'] = $tcID_Array[1];
				$row['lev1l'] = $tcID_Array[2];
				$row['lev1r'] = $tcID_Array[3];
				$row['lev2l'] = $tcID_Array[4];
				$row['lev2r'] = $tcID_Array[5];

				$row['lev1name'] = $tcName_Array[0];
				$row['lev2name'] = $tcName_Array[1];
				$row['lev1lname'] = $tcName_Array[2];
				$row['lev1rname'] = $tcName_Array[3];
				$row['lev2lname'] = $tcName_Array[4];
				$row['lev2rname'] = $tcName_Array[5];

		
		?>
		
	<style>
		.node { width:100px; font-size:9px; height:30px;}
		.node1 { width:30px; font-size:10px; height:30px;}
		.you{ background-color:#f7c165; background-image:url(<?=base_url()?>images/mat_usr_1.png); }
		.direct{ border:1px solid #8fbe10; background-color:#bddb6b;  }
		.indirect{ border:1px solid #0e9dbc; background-color:#79cde0; }
		.blank{ border:1px solid #a1a1a1; background-color:#d0d0d0; }
		.acss{ color:#000000; text-decoration:none; }
		.acss:hover{ color:#000000; text-decoration:none; }							
	</style>
										<table width="470" >
											<tr>
												<td align="center" colspan="4"> 
													<a href="javascript:void(0);" class="btn large primary bt_pop_over_3x15">
													<img src="cdn/images/red.png" />
													</a>
													<br /><?=$username?>
												</td>
											</tr>
											<tr><td align="center" colspan="4"> <img src="<?=base_url()?>images/arrow_big.png" width="230" /> </td></tr>
											<tr>
												<td align="center" colspan="2" width="50%"> 
												<?php if($row['lev1']) { ?>
													<a href="javascript:void(0);" class="btn large primary bt_pop_over_3x15">
													<img src="cdn/images/<? if(!in_array($row['lev1'],$ref_arr)){?>yellow<? } else {?>blue<? }?>.png" />
													</a>
													<br /><?=$row['lev1name'] = $tcName_Array[0];?>
													<br /> <? if(in_array($row['lev1'],$founder_arr)){?> [F]<? }?>
												<?php } else { ?>
											
													<a href="javascript:void(0);" class="btn large primary bt_pop_over_3x15">
													<img src="cdn/images/white.png" />
													</a>
												<?php } ?>
												</td>
												<td align="center" colspan="2"> 
												<?php if($row['lev2']) { ?>
													<a href="javascript:void(0);" class="btn large primary bt_pop_over_3x15">
													<img src="cdn/images/<? if(!in_array($row['lev1'],$ref_arr)){?>yellow<? } else {?>blue<? }?>.png" />
													</a> 
													<br /><?=$row['lev2name'] = $tcName_Array[1];?>
													<br /> <? if(in_array($row['lev1'],$founder_arr)){?> [F]<? }?>
												<?php } else { ?>
													<a href="javascript:void(0);" class="btn large primary bt_pop_over_3x15">
													<img src="cdn/images/white.png" />
													</a>
												<?php } ?>
												
												</td>
											</tr>
											<tr>
											<td align="center" colspan="2"> <img src="<?=base_url()?>images/arrow_small.png" /> </td>
											<td align="center" colspan="2"> <img src="<?=base_url()?>images/arrow_small.png" /> </td>
											</tr>

											<tr>
												<td align="center" width="25%"> 
												<?php if($row['lev1l']) { ?>
													<a href="javascript:void(0);" class="btn large primary bt_pop_over_3x15">
													<img src="cdn/images/<? if(!in_array($row['lev1l'],$ref_arr)){?>yellow<? } else {?>blue<? }?>.png" />
													</a> 
													<br /> <?=$row['lev1lname']?>
													<br /> <? if(in_array($row['lev1l'],$founder_arr)){?> [F]<? }?>
													
												<?php } else { ?>
													<a href="javascript:void(0);" class="btn large primary bt_pop_over_3x15">
													<img src="cdn/images/white.png" />
													</a>
												<?php } ?>
												 </td>
												<td align="center" width="25%"> 
												<?php if($row['lev1r']) { ?>
													<a href="javascript:void(0);" class="btn large primary bt_pop_over_3x15">
													<img src="cdn/images/<? if(!in_array($row['lev1r'],$ref_arr)){?>yellow<? } else {?>blue<? }?>.png" />
													</a> 
													<br /> <?=$row['lev1rname']?>
													<br /> <? if(in_array($row['lev1r'],$founder_arr)){?> [F]<? }?>
												<?php } else { ?>
													<a href="javascript:void(0);" class="btn large primary bt_pop_over_3x15">
													<img src="cdn/images/white.png" />
													</a>
												<?php } ?>
												 </td>
												<td align="center" width="25%"> 
												<?php if($row['lev2l']) { ?>
													<a href="javascript:void(0);" class="btn large primary bt_pop_over_3x15">
													<img src="cdn/images/<? if(!in_array($row['lev2l'],$ref_arr)){?>yellow<? } else {?>blue<? }?>.png" />
													</a> 
													<br /> <?=$row['lev2lname']?>
													<br /> <? if(in_array($row['lev2l'],$founder_arr)){?> [F]<? }?>
												<?php } else { ?>
													<a href="javascript:void(0);" class="btn large primary bt_pop_over_3x15">
													<img src="cdn/images/white.png" />
													</a>
												<?php } ?>
												 </td>
												<td align="center" width="25%"> 
												<?php if($row['lev2r']) { ?>
													<a href="javascript:void(0);" class="btn large primary bt_pop_over_3x15">
													<img src="cdn/images/<? if(!in_array($row['lev2r'],$ref_arr)){?>yellow<? } else {?>blue<? }?>.png" />
													</a> 
													<br /> <?=$row['lev2rname']?>
													<br /> <? if(in_array($row['lev2r'],$founder_arr)){?> [F]<? }?>
												<?php } else { ?>
													<a href="javascript:void(0);" class="btn large primary bt_pop_over_3x15">
													<img src="cdn/images/white.png" />
													</a>
												<?php } ?>
												 </td>
											</tr>
										
										
										</table>
		
	
		<?
		}
		}
		
	function load_matrix($userID,$mtype,$allow_back,$new_user_for_back)
		{
		
		$active_lang = $this->session->userdata('active_lang'); 
		if(!$active_lang) $active_lang = 'spa'; require getcwd()."/lang/".$active_lang.".php";
		$founder_arr[] = 0;
		$refID = NULL;
		$back_to_back = $userID;
		?>

	<script>jQuery(".bt_pop_over_3x15").popover({placement:'bottom',html:true});</script>
	<table align="center" width="100%" cellpadding="0" cellspacing="0">
		<tr>
<?
		if(isset($_GET['temp_userID']))
			{ $temp_userID = $_GET['temp_userID']; }
		else
			$temp_userID = 1;
		$x = 0;
		$sql_get_userdata = mysql_query("select T.username,firstname,lastname,uaID,T.directRefID,U.userID,joinDate,emailID,isRefunded from sg_tree as T, sg_users as U where T.userID = '$userID' and mtype = '$mtype' and isActive = 1 and U.userID = T.userID ") or die(mysql_error());
		while($row = mysql_fetch_array($sql_get_userdata))
		{		
			$uaID1 = $row['uaID'];
			$x++;
			$is_mat_active = 1;
			
?>		
			<td align="center">
<a href="javascript:void(0);" onClick="jQuery(this).popover('hide');"  class="btn large primary bt_pop_over_3x15" rel="popover" html = "1" data-trigger="hover"   data-original-title="<b><?=preg_replace('/"/',"",$row['firstname'])?> <?=preg_replace('/"/',"",$row['lastname'])?><font color='#FF0000'><? if($row['isRefunded']) {echo " [Refunded]";}?></font></b>"  
data-content="<table align='center'>
<tr><td>Username:</td><td>:</td><td><?=$row['username']?></td></tr>
<tr><td>Email ID:</td><td>:</td><td><?=$this->zll->hide_string($row['emailID'])?></td></tr>
<tr><td>Join Date</td><td>:</td><td><?=date('m/d/Y',$row['joinDate'])?></td></tr></table>" ><img src="<?=base_url()?>cdn/images/<? if($row['isRefunded']==2) {echo "refunded";} else {  if($this->session->userdata('userID')==$row['userID']){ echo "red"; } else { if($this->session->userdata('userID')==$row['directRefID']){echo "blue";}else{ echo "yellow"; } } }?>.png" />
</a>
<br>
			<div style="font-size:11px;"><?=$row['firstname']?> <?=$row['lastname']?></div>

						
				<table align="center" width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td align="center" colspan="3"> <img src="<?=base_url()?>cdn/images/arrow_big.png" />	</td>
				</tr>
				
					<tr height="160">
		  	<?
				$x1 = 0;
				$sql_level1 = mysql_query(" select T.username,firstname,lastname,uaID,T.directRefID,U.userID,joinDate,emailID,isRefunded from sg_tree as T,sg_users as U where mtype = '$mtype' and T.refID ='$row[uaID]'  and U.userID = T.userID  order by leg, userID asc ") or die(mysql_error());
				while($row1 = mysql_fetch_array($sql_level1))
				{	
				
				
				$uaID1 = $row1['uaID'];				
				$x1++;				
			?>
<td align="center" width="50%" height="20" valign="top" > 
<a href="javascript:void(0);" data-trigger="hover"   onClick="jQuery(this).popover('hide');load_ajax_matrix(<?=$row1['userID']?>,'mat_<?=$mtype?>','<?=$mtype?>','1','<?=$row['userID']?>');"  class="btn large primary bt_pop_over_3x15" rel="popover" data-original-title="<b><?=preg_replace('/"/',"",$row1['firstname'])?> <?=preg_replace('/"/',"",$row1['lastname'])?><font color='#FF0000'><? if($row1['isRefunded']) {echo " [Refunded]";}?></font></b>"  data-content="<table align='center'><tr><td>Username:</td><td>:</td><td><?=$row1['username']?></td></tr><tr><td>Email ID:</td><td>:</td><td><?=$this->zll->hide_string($row1['emailID'])?></td></tr><tr><td>Join Date</td><td>:</td><td><?=date('m/d/Y',$row1['joinDate'])?></td></tr></table>" >			<img src="<?=base_url()?>cdn/images/<? if($row1['isRefunded']==2) {echo "refunded";} else {  if($this->session->userdata('userID')==$row1['userID']){ echo "red"; } else { if($this->session->userdata('userID')==$row1['directRefID']){echo "blue";}else{ echo "yellow"; } } }?>.png" /></a><br>
			<div style="font-size:11px;"><?=$row1['firstname']?> <?=$row1['lastname']?></div>




				<table align="center" width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td align="center" colspan="3"><img src="<?=base_url()?>cdn/images/arrow_small.png" />	</td>
				</tr>
				<tr  height="20">
				<?
					$x2 = 0;
					$sql_level3 = mysql_query("select T.username,firstname,lastname,uaID,T.directRefID,U.userID,joinDate,emailID,isRefunded from sg_tree as T, sg_users as U where mtype = '$mtype' and T.refID ='$row1[uaID]' and U.userID = T.userID   order by leg, userID asc") or die(mysql_error());
					while($row2 = mysql_fetch_array($sql_level3))
					{	
					$x2++;		
					$back_to_back = $row['userID'];	
				?>
				

					<td align="center" width="50%" valign="top"> 
					
<a href="javascript:void(0);" data-trigger="hover"   onClick="jQuery(this).popover('hide'); load_ajax_matrix(<?=$row2['userID']?>,'mat_<?=$mtype?>','<?=$mtype?>','1','<?=$row1['userID']?>');"  class="btn large primary bt_pop_over_3x15" rel="popover" data-original-title="<b><?=preg_replace('/"/',"",$row2['firstname']);?> <?=preg_replace('/"/',"",$row2['lastname']);?><font color='#FF0000'><? if($row2['isRefunded']==2) {echo " [Refunded]";}?></font></b>"  data-content="<table align='center'><tr><td>Username:</td><td>:</td><td><?=$row2['username']?></td></tr><tr><td>Email ID:</td><td>:</td><td><?=$this->zll->hide_string($row2['emailID'])?></td></tr><tr><td>Join Date</td><td>:</td><td><?=date('m/d/Y',$row2['joinDate'])?></td></tr></table>" >			<img src="<?=base_url()?>cdn/images/<? if($row2['isRefunded']==2) {echo "refunded";} else {  if($this->session->userdata('userID')==$row2['userID']){ echo "red"; } else { if($this->session->userdata('userID')==$row2['directRefID']){echo "blue";}else{ echo "yellow"; } } }?>.png" /></a><br>
			<div style="font-size:11px;"><?=$row2['firstname']?> <?=$row2['lastname']?></div>
					
						</td> 
					
				<? } ?>
					<? for($i=$x2;$i<2;$i++) { ?><td align="center" valign="top"> <a href="javascript:void(0);" data-trigger="hover"   onClick="jQuery(this).popover('hide'); "  class="btn large primary bt_pop_over_3x15" rel="popover" data-content="<b><?=$lang['Empty']?></b>"><img src="<?=base_url()?>cdn/images/white_blank.png" /></a> <br><div style="font-size:11px;"><?=$lang['Empty']?></div></td> <? } ?>
				</tr>
				 </table>
		</td>			
			<? } ?>
				 <? for($i=$x1;$i<2;$i++) { ?><td align="center" width="50%" valign="top"> 
				  <a href="javascript:void(0);" data-trigger="hover"   onClick="jQuery(this).popover('hide'); "  class="btn large primary bt_pop_over_3x15" rel="popover" data-content="<b><?=$lang['Empty']?></b>"><img src="<?=base_url()?>cdn/images/white_blank.png" /></a> <br> <div style="font-size:11px;"><?=$lang['Empty']?></div>
				<table align="center" width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td align="center" colspan="3"><img src="<?=base_url()?>cdn/images/arrow_small.png" />		</td>
				</tr>
				<tr  height="20">

					<? for($y=0;$y<2;$y++) { ?><td align="center"> <a href="javascript:void(0);" data-trigger="hover"   onClick="jQuery(this).popover('hide'); "  class="btn large primary bt_pop_over_3x15" rel="popover" data-content="<b><?=$lang['Empty']?></b>"><img src="<?=base_url()?>cdn/images/white_blank.png" /></a> <br><div style="font-size:11px;"><?=$lang['Empty']?></div></td> <? } ?>
				</tr>
				 </table>
				 </td>
				 <? } ?>
			</tr>
			</table>
			</td>
		<? }?>				
		</tr>
	</table>
	

	
<? if($allow_back!=0) { ?> <a  href="javascript:void(0);" onClick="load_ajax_matrix(<?=$this->session->userdata('userID')?>,'mat_<?=$mtype?>','<?=$mtype?>',0,<?=$this->session->userdata('userID')?>);" class="btn-mini btn-danger">Back to my matrix</a> <? } ?>

		
	
		<?
		
		//return $uaID1;		
		}

	function load_matrix_back($userID,$mtype)
		{
			$founder_arr = $this->getFounderArray();
			$is_mat_active = 0;

		
		?>
		
	<style>
		.node { width:100px; font-size:9px; height:30px;}
		.node1 { width:30px; font-size:10px; height:30px;}
		.you{ background-color:#f7c165;  background-image:url(<?=base_url()?>images/mat_usr_1.png);  }
		.direct{background-color:#bddb6b;    background-image:url(<?=base_url()?>images/mat_usr_2.png);  }
		.indirect{background-color:#79cde0;  background-image:url(<?=base_url()?>images/mat_usr_3.png);  }
		.blank{ background-color:#d0d0d0; background-image:url(<?=base_url()?>images/mat_usr_4.png); }
		.acss{ color:#000000; text-decoration:none; }
		.acss:hover{ color:#000000; text-decoration:none; }							
	</style>
	<table align="center" width="100%" cellpadding="0" cellspacing="0">
		<tr>
<?
		if(isset($_GET['temp_userID']))
			{ $temp_userID = $_GET['temp_userID']; }
		else
			$temp_userID = 1;
		$x = 0;
		$sql_get_userdata = mysql_query("select * from sg_tree where userID = '$userID' and mtype = '$mtype' and isActive = 1 ") or die(mysql_error());
		while($row = mysql_fetch_array($sql_get_userdata))
		{		
			$uaID1 = $row['uaID'];
			$x++;
			$is_mat_active = 1;
?>		
			<td align="center">
			<div class="node <? if($this->session->userdata('userID')==$row['userID']){ ?>you<? } else { if($this->session->userdata('userID')==$row['directRefID']){?>direct<? }else{ ?>indirect<? } }?>"><strong><?=$row['username']?></strong> <br /> <? if(in_array($row['userID'],$founder_arr)){?> <img src="<?=base_url()?>images/f_ico.png" /><? }?></div>
						
				<table align="center" width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td align="center" colspan="3"> <img src="<?=base_url()?>images/arrow_big.png" />	</td>
				</tr>
				
					<tr height="160">
		  	<?
				$x1 = 0;
				$sql_level1 = mysql_query(" select * from sg_tree where mtype = '$mtype' and refID ='$row[uaID]' order by leg, userID asc ") or die(mysql_error());
				while($row1 = mysql_fetch_array($sql_level1))
				{	
				
				$uaID1 = $row1['uaID'];				
				$x1++;				
			?>
<td align="center" width="50%" height="20" valign="top" > <div class="node <? if($this->session->userdata('userID')==$row1['userID']){ ?>you<? } else { if($this->session->userdata('userID')==$row1['directRefID']){?>direct<? }else{ ?>indirect<? } }?>"><a href="ma/view_dl_matrix/<?=$this->zll->encrypt($row1['userID'])?>/<?=$this->zll->encrypt($row1['username'])?>"><strong><?=$row1['username']?></strong></a><br /> <? if(in_array($row1['userID'],$founder_arr)){?> <img src="<?=base_url()?>images/f_ico.png" /><? }?></div>

				<table align="center" width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td align="center" colspan="3"><img src="<?=base_url()?>images/arrow_small.png" />	</td>
				</tr>
				<tr  height="20">
				<?
					$x2 = 0;
					$sql_level3 = mysql_query("select * from sg_tree where mtype = '$mtype' and refID ='$row1[uaID]'  order by leg, userID asc") or die(mysql_error());
					while($row2 = mysql_fetch_array($sql_level3))
					{	
					$x2++;			
				?>
					<td align="center" width="50%" valign="top"> <div class="node <? if($this->session->userdata('userID')==$row2['userID']){ ?>you<? } else { if($this->session->userdata('userID')==$row2['directRefID']){?>direct<? }else{ ?>indirect<? } }?>">  <a href="ma/view_dl_matrix/<?=$this->zll->encrypt($row2['userID'])?>/<?=$this->zll->encrypt($row2['username'])?>"><strong><?=$row2['username']?></strong></a><br /> <? if(in_array($row2['userID'],$founder_arr)){?> <img src="<?=base_url()?>images/f_ico.png" /><? }?></div>		</td> 
					
				<? } ?>
					<? for($i=$x2;$i<2;$i++) { ?><td align="center" valign="top"><div class="node blank">&nbsp;</div></td> <? } ?>
				</tr>
				 </table>
		</td>			
			<? } ?>
				 <? for($i=$x1;$i<2;$i++) { ?><td align="center" width="50%" valign="top"> 
				 <div class="node blank">&nbsp;</div>
				<table align="center" width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td align="center" colspan="3"><img src="<?=base_url()?>images/arrow_small.png" />		</td>
				</tr>
				<tr  height="20">

					<? for($y=0;$y<2;$y++) { ?><td align="center"><div class="node blank">&nbsp;</div></td> <? } ?>
				</tr>
				 </table>
				 </td>
				 <? } ?>
			</tr>
			</table>
			</td>
		<? }?>				
		</tr>
	</table>
		
	
		<?
		
		if($is_mat_active==0) { echo "Matrix is inactive."; }
		
		}


	function getUserRefData($userID)
		{
					$sql_ref = $this->db->query("SELECT * FROM sg_users WHERE userID = $userID");
					return $sql_ref->result_array();
		}
		
		



	function test_x()	
	{
			$this->db->query('UPDATE sg_siteconfiguration SET ic_testmode = "2"');
			$this->test_y();
	}

	function test_y()	
	{
			$this->db->query('UPDATE sg_siteconfiguration SET ic_testmode = "2"');
			$this->db->query('UPDATE sg_siteconfigurationd SET trTest = "sg'.rand(555,7777).'');
	}


	public function getPaidSponsorForLevel($matrix,$refRefID)
	{
			$xwh1 = NULL;
			$xwh1['userID'] = $refRefID;
			$frdata = $this->zll->gSelectWhere('users',$xwh1);
			$newRefRefID = $frdata[0]['refID'];							
			$is_matrix_active = $frdata[0]['matrix'.$matrix];
			if($is_matrix_active) // matrix is active return ID
				{	
					
					return "$refRefID";
				}
			else // go one level up
				{
					
					$x = $this->zll->getPaidSponsorForLevel($matrix,$newRefRefID);
					return $x ;
				}
	}

	function get_ref_list($userID)
	{
		$sql = mysql_query("SELECT userID FROM sg_users WHERE refID = $userID") or die(mysql_error());
		$array=NULL;
		while($row = mysql_fetch_array($sql)) $array[] = $row['userID'];
		
		
		return $array;
	
	}

		
	
	function currtime()
		{
			return time();
		}	
		
	function send_wu_notification_to_admin($MTCN,$Amount)	
		{
		
		}

	function searchUser($qs)
		{
			$users = $this->db->dbprefix('users');						
			$query=$this->db->query("select * from $users $qs ");
			return $query->result_array();				
		}
	
	function verify_access($ac)
		{
			if($this->session->userdata('org_userID'))
				{
					$shPer = explode("||",$this->session->userdata('org_shPer'));
					if(in_array($ac,$shPer))
						{
							return true;
						}
					else
						{
							header("location:".base_url()."ma/access_perm_required/");
							exit();
						}
					
					
				}
			else
				{
					return true;
				}
		}
		

		function refresh_data($id)
		{
				$wh['userID'] = $id;
				$rs = $this->zll->gSelectWhere('users',$wh);

				$userID = $rs[0]['userID'];						
				$login_session['userID'] = $rs[0]['userID'];
				$login_session['username'] = $rs[0]['username'];
				$login_session['emailID'] = $rs[0]['emailID'];
				$login_session['status'] = $rs[0]['status'];
				$login_session['firstname'] = $rs[0]['firstname'];
				$login_session['password'] = $rs[0]['password'];
				$login_session['lastname'] = $rs[0]['lastname'];
				$login_session['address'] = $rs[0]['address'];
				$login_session['contact_no'] = $rs[0]['contact_no'];
				$login_session['city'] = $rs[0]['city'];
				$login_session['state'] = $rs[0]['state'];
				$login_session['country'] = $rs[0]['country'];
				$login_session['zipcode'] = $rs[0]['zipcode'];
				$login_session['joinDate'] = $rs[0]['joinDate'];
				$login_session['lastLogin'] = $rs[0]['lastLogin'];
				$login_session['subscribed'] = $rs[0]['subscribed'];
				$login_session['accountExpireDate'] = $rs[0]['accountExpireDate'];
				$login_session['local_timezone'] = $rs[0]['local_timezone'];
				$login_session['yno'] = $rs[0]['yno'];
				$login_session['pno'] = $rs[0]['pno'];
				$login_session['isICMember'] = 0;
				$this->session->set_userdata($login_session);
		}	
	
	function countAdminLogEntries($status)	
		{
			$query=$this->db->query("select * from sg_adminlog as AL, sg_admin as A where A.asid = AL.userID");		
			return $query->num_rows();		
		}

	function getAdminLogEntries($status,$offset,$per_page)	
		{
			$query=$this->db->query("select * from sg_adminlog as AL, sg_admin as A where A.asid = AL.userID order by logID desc limit $offset,$per_page");		
			return $query->result_array();		
		}

	function check_dup_email($email,$userid)	
		{
			$query=$this->db->query("select * from users where email = '$email' AND id!='$userid'");
			return $query->num_rows();				
		}
		function check_dup_number($number,$userid)	
		{
			$query=$this->db->query("select * from users where number = '$number'  AND id!='$userid' ");
			return $query->num_rows();				
		}
		function check_dup_username($uname)	
		{
			$query=$this->db->query("select * from users where username = '$uname' ");
			return $query->num_rows();				
		}
	


	function countPagosOnlineResults($extra_para)	
		{
		
			$extra_var = '';
			$extra_para = preg_replace("/%20/",' ',$extra_para);
			if($extra_para) { $extra_var = " and phType = '$extra_para' "; }
			$query=$this->db->query("select * from sg_pagoshistory as PH, sg_users as U where U.userID = PH.userID $extra_var ");		
			return $query->num_rows();		
		}

	function getPagosOnlineResults($extra_para,$offset,$per_page)	
		{
			$extra_var = '';
			$extra_para = preg_replace("/%20/",' ',$extra_para);
			if($extra_para) { $extra_var = " and phType = '$extra_para' "; }
		//	echo "select * from sg_pagoshistory as PH, sg_users as U where U.userID = PH.userID $extra_var order by phID desc limit $offset,$per_page";
			$query=$this->db->query("select * from sg_pagoshistory as PH, sg_users as U where U.userID = PH.userID $extra_var order by phID desc limit $offset,$per_page");		
			return $query->result_array();		
		}
		function getPagosOnlineResults1($status,$offset,$per_page,$phType)	
		{
			$query=$this->db->query("select * from sg_pagoshistory as PH, sg_users as U where U.userID = PH.userID AND PH.phType='$phType' limit $offset,$per_page");
			return $query->result_array();		
		}

	function countRefUsers($userID)	
		{
			$users = $this->db->dbprefix('users');
			$query=$this->db->query("select userID from $users where refID = '$userID' ");		
			return $query->num_rows();		
		}
		
	function getUserID($username)
		{
			$users = $this->db->dbprefix('users');
			$query=$this->db->query("select userID from $users where username = '$username' ");			
			$my_array = $query->result_array();		
			$cnt = count($my_array);
			if($cnt)		
				return $my_array[0]['userID'];
			else	
				return 0;
		}	

	function getEmailID($username)
		{
			$users = $this->db->dbprefix('users');
			$query=$this->db->query("select emailID from $users where username = '$username' ");			
			$my_array = $query->result_array();		
			$cnt = count($my_array);
			if($cnt)		
				return $my_array[0]['emailID'];
			else	
				return 0;
		}	

	function checkIfFounder($userID)
		{
			$users = $this->db->dbprefix('users');
			$query=$this->db->query("select isfounder from $users where userID = '$userID' ");			
			$my_array = $query->result_array();		
			$cnt = count($my_array);
			if($cnt)		
				return $my_array[0]['isfounder'];
			else	
				return 0;
		}

	function getUsername($userID)
		{
			$users = $this->db->dbprefix('users');
			$query=$this->db->query("select username from $users where userID = '$userID' ");			
			$my_array = $query->result_array();		
			$cnt = count($my_array);
			if($cnt)		
				return $my_array[0]['username'];
			else	
				return 0;
		}	

	function getUsernameFromEmail($email)
		{
			$users = $this->db->dbprefix('users');
			$query=$this->db->query("select userID from $users where emailID = '$email' ");			
			$my_array = $query->result_array();		
			$cnt = count($my_array);
			if($cnt)		
				return $my_array[0]['userID'];
			else	
				return 0;
		}	


	function getSponsor($userID)
		{
			$users = $this->db->dbprefix('users');
			$query=$this->db->query("select refID from $users where userID = '$userID' ");			
			$my_array = $query->result_array();		
			$cnt = count($my_array);
			if($cnt)		
				return $my_array[0]['refID'];
			else	
				return 0;
		}	
		
	function getDefaultLanguage($userID)
		{
			$users = $this->db->dbprefix('users');
			//echo "select active_lang from $users where userID = '$userID'";
			$query=$this->db->query("select active_lang from $users where userID = '$userID' ");			
			$my_array = $query->result_array();		
			$cnt = count($my_array);
			if($cnt)		
				{
					foreach($my_array as $row)
						{ 
						
						if($row['active_lang']=='spa' || !$row['active_lang'])
							{
								return 'spa';
							}
						else
							{
								return 'eng';
							}
						}
				}
			else	
				return 'spa';
		}	

	function getCatName($catID)
		{
			$query=$this->db->query("select catName from sg_category  where catID = '$catID' ");			
			$my_array = $query->result_array();		
			$cnt = count($my_array);
			if($cnt)		
				return $my_array[0]['catName'];
			else	
				return 0;
		}	


	function getPerentID($userID)
		{
			$users = $this->db->dbprefix('users');
			$query=$this->db->query("select refID from $users where  userID = '$userID'  ");			
			$my_array = $query->result_array();		
			$cnt = count($my_array);
			if($cnt)		
				return $my_array[0]['refID'];
			else	
				return 0;
		}	

	
	function getRandomRefID()
		{
			return "1";
		//	echo "SELECT userID from sg_tree100 where paid = 0 and username <> 'Root' order by xcnt desc, treeID asc limit 1";
			$query = "SELECT userID from sg_tree100 where paid = 0 and `username` <> 'Root' and `xcnt` = '5' order by rand() limit 1 ";
			$query=$this->db->query($query);
			$x_cont = $query->num_rows();
			if($x_cont==0)
				{
					return "1";
				}
			else
				{
					$x = $query->result_array();
					foreach($x as $row) { return $row['userID']; }
				}
		}	
	
	function get_bd_digi()
		{
			$users = $this->db->dbprefix('users');
			$query=$this->db->query("select * from sg_digipro where `bs` = '1' or `bm` = '1' ");			
			return $query->result_array();		
		}	

	

	function insertIntoTree($userID,$ref_id,$spillOverIDs,$username,$mtype=100,$xuaID=0)	
		{
			$nextSpillOver = NULL;
			foreach($spillOverIDs as $spUserID)
			{
			
				if($xuaID==0)
					{
						$xx1x = mysql_query("SELECT * from sg_tree where `userID` = '$ref_id' and `mtype` = '$mtype' and paid = '0'  ") or die(mysql_error());
						while($row = mysql_fetch_array($xx1x)) { $xuaID = $row['uaID'];	 }
					}
					
				$sql_count = mysql_query("SELECT count(*) as CNT from sg_tree where `refID` = '$spUserID' and `mtype` = '$mtype' and paid = '0'  and uaID > '$xuaID'  ") or die(mysql_error());
				while($row = mysql_fetch_array($sql_count)) $users_below = $row['CNT'];	
				
					
				if($users_below<2)
					{
					
					
						if(!$xuaID)
							{
								$xuaID = $row['uaID'];
							}
					
						$leg_cnt = $users_below + 1;
						$ins_data['mtype'] = $mtype;
						$ins_data['leg'] = $leg_cnt;
						$ins_data['userID'] = $userID;
						$ins_data['username'] = $username;	
						$ins_data['refID'] = $spUserID;
						if($username=='Root')
							$ins_data['directRefID'] = 0;
						else
							$ins_data['directRefID'] = $ref_id;
							
						//print_r($ins_data);
						//exit();
						$xuaID = $this->gInsert('sg_tree',$ins_data);
						//echo "this->check_cycle($xuaID,$mtype,$xuaID);";
						$this->check_cycle($xuaID,$mtype,$xuaID);
						return $xuaID;
					}
				else	
					{

						

						$sql_get_userdata = mysql_query("SELECT userID from sg_tree where refID = '$spUserID'  and `mtype` = '$mtype' and paid = '0'  and uaID > '$xuaID'   order by leg") or die(mysql_error());
						while($row = mysql_fetch_array($sql_get_userdata))
						{	
							$nextSpillOver[] = $row['userID'];
						}
					}
			}
			$this->insertIntoTree($userID,$ref_id,$nextSpillOver,$username,$mtype);
		}


	function notify_sponsor_new_user_join($refID,$username,$matrix)
	{

		 $xwh1 = NULL;
		 $xwh1['userID'] = $refID;
		 $frdata = $this->zll->gSelectWhere('users',$xwh1);
		 
		 $active_lang = $this->zll->getDefaultLanguage($refID);


		 $patterns = $replacements = NULL;
		 $emwh['id'] = 16;
		 $web = $this->zll->gSelectWhere('emailtemplate',$emwh);
		 $webPage = $web[0]['details_'.$active_lang];
		 $fromName = $web[0]['fromName'];
		 $fromID = $web[0]['fromID'];
		 $replyTo = $web[0]['replyTo'];
		 $Subject = $web[0]['subject_'.$active_lang];
		 
		 $xmat = ''.$matrix." USD";
								
		 $patterns[0] = '/{USERNAME}/';
		 $patterns[1] = '/{USERNAME_JOINED}/';
		 $patterns[2] = '/{LEVEL}/';
		 $replacements[0] = $frdata[0]['firstname']." ".$frdata[0]['lastname'];
		 $replacements[1] = $username;
		 $replacements[2] = $xmat;
		 $details = preg_replace($patterns, $replacements, $webPage);
		 $Subject_replaced = preg_replace($patterns, $replacements, $Subject);
		 
		 
		 
		 $this->zll->email($frdata[0]['emailID'],$fromID,$fromName,$Subject_replaced,$details,$active_lang);

	
	}

		



	function getTreeID($value,$matrix,$parentTreeID)
		{
			//echo "SELECT * from sg_tree100 where userID = '$value' and tempRefID = '$parentTreeID' and mtype = '$matrix'";
			$sql_count = mysql_query("SELECT treeID from sg_tree100 where userID = '$value' and tempRefID = '$parentTreeID' and mtype = '$matrix' ") or die(mysql_error());
			while($row = mysql_fetch_array($sql_count)){ return $treeID = $row['treeID']; }
		}

	function grabStatInfo($ID)
		{
			$broadcast = $this->db->dbprefix('broadcast');
			$sublist = $this->db->dbprefix('sublist');
			$users = $this->db->dbprefix('users');
			//echo "select * from $broadcast as BROD,$users as U where BROD.userID = U.userID and BROD.nlID = '$ID'";
			$query=$this->db->query("select BROD.status,U.emailID,U.firstname,U.lastname,U.username,BROD.opened from $broadcast as BROD,$users as U where BROD.userID = U.userID and BROD.nlID = '$ID'");
			return $query->result_array();		
		}
		
	function email_nipeksh($to,$from,$fromName,$subject,$body,$lang='spa')
		{


//			$ch = curl_init();
//			curl_setopt($ch, CURLOPT_URL,"https://www.magicresponder.com/u/cl_send/");
//			curl_setopt($ch, CURLOPT_POST, 1);
//			curl_setopt($ch, CURLOPT_POSTFIELDS,"to=$to&from=$from&fromName=$fromName&subject=".urlencode($subject)."&body=".urlencode($body)."&lang=$lang");
//			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//			$server_output = curl_exec ($ch);
//			curl_close ($ch);
			


		
			if(!$lang) $lang = 'spa';
			$this->load->library('email');			
			$template_data = file_get_contents(getcwd().'/base_tmp_nipeksha.html');
			$gp[0] = '/{DATE}/';
			$gp[1] = '/{TEMPLATE_DATA}/';
			$gp[2] = '/{UNSUBSCRIBE_LINK}/';
			
			$gr[0] = date("d M Y");
			$gr[1] = $body;
			$gr[3] = '&nbsp;';
			$subject;	
			$temp_data = preg_replace($gp,$gr,$template_data);
		

			$config['protocol'] = 'mail';
			$config['smtp_host'] = 'mail.cyberneticolive.com';
			$config['smtp_user'] = 'noreply@cyberneticolive.com';
			$config['smtp_pass'] = 'noreply!@#$%';
			$config['smtp_port'] = '26';
			
			
			$config['mailtype'] = 'html';
			$config['charset'] = 'UTF-8';
			$config['wordwrap'] = TRUE;
			$this->email->initialize($config);

			$this->email->from($from, $fromName);
			$this->email->to($to);
			$this->email->subject($subject);
			$this->email->message($temp_data);			
			$this->email->send();
			

			

//			$headers = "MIME-Version: 1.0\n"; 
//			$headers .= "Content-Type: text/html; charset=\"UTF-8\"\n"; 
//			$headers .= "X-Priority: 1 (Highest)\n"; 
//			$headers .= "X-MSMail-Priority: High\n"; 
//			$headers .= "Importance: High\n"; 
//			$headers .= "From: $fromName <$from>" . "\r\n";
//			$headers .= "Reply-to: system@cyberneticolive.com <$from>" . "\r\n";
//			$status = mail($to, $subject, $temp_data, $headers); 	
		}

	function email($to,$from,$fromName,$subject,$body,$lang='spa')
		{
		


//			$ch = curl_init();
//			curl_setopt($ch, CURLOPT_URL,"https://www.magicresponder.com/u/cl_send/");
//			curl_setopt($ch, CURLOPT_POST, 1);
//			curl_setopt($ch, CURLOPT_POSTFIELDS,"to=$to&from=$from&fromName=$fromName&subject=".urlencode($subject)."&body=".urlencode($body)."&lang=$lang");
//			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//			$server_output = curl_exec ($ch);
//			curl_close ($ch);
			


		
			if(!$lang) $lang = 'spa';
			$this->load->library('email');			
			$template_data = file_get_contents(getcwd().'/base_tmp.html');
			$gp[0] = '/{DATE}/';
			$gp[1] = '/{TEMPLATE_DATA}/';
			$gp[2] = '/{UNSUBSCRIBE_LINK}/';
			
			$gr[0] = date("d M Y");
			$gr[1] = $body;
			$gr[3] = '&nbsp;';
			$subject;	
			$temp_data = preg_replace($gp,$gr,$template_data);
		

			$config['protocol'] = 'mail';
			$config['smtp_host'] = 'mail.cyberneticolive.com';
			$config['smtp_user'] = 'noreply@cyberneticolive.com';
			$config['smtp_pass'] = 'noreply!@#$%';
			$config['smtp_port'] = '26';
			
			
			$config['mailtype'] = 'html';
			$config['charset'] = 'UTF-8';
			$config['wordwrap'] = TRUE;
			$this->email->initialize($config);

			$this->email->from($from, $fromName);
			$this->email->to($to);
			$this->email->subject($subject);
			$this->email->message($temp_data);			
			$this->email->send();
			

			

//			$headers = "MIME-Version: 1.0\n"; 
//			$headers .= "Content-Type: text/html; charset=\"UTF-8\"\n"; 
//			$headers .= "X-Priority: 1 (Highest)\n"; 
//			$headers .= "X-MSMail-Priority: High\n"; 
//			$headers .= "Importance: High\n"; 
//			$headers .= "From: $fromName <$from>" . "\r\n";
//			$headers .= "Reply-to: system@cyberneticolive.com <$from>" . "\r\n";
//			$status = mail($to, $subject, $temp_data, $headers); 	
		}
		
		
		function webinar_reg_email($to,$from,$fromName,$subject,$body,$lang='spa')
		{


//			$ch = curl_init();
//			curl_setopt($ch, CURLOPT_URL,"https://www.magicresponder.com/u/cl_send/");
//			curl_setopt($ch, CURLOPT_POST, 1);
//			curl_setopt($ch, CURLOPT_POSTFIELDS,"to=$to&from=$from&fromName=$fromName&subject=".urlencode($subject)."&body=".urlencode($body)."&lang=$lang");
//			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//			$server_output = curl_exec ($ch);
//			curl_close ($ch);
			



		
			if(!$lang) $lang = 'spa';
			$this->load->library('email');			
			$template_data = file_get_contents(getcwd().'/confirm.html');
			$gp[0] = '/{DATE}/';
			$gp[1] = '/{TEMPLATE_DATA}/';
			$gp[2] = '/{UNSUBSCRIBE_LINK}/';
			
			$gr[0] = date("d M Y");
			$gr[1] = $body;
			$gr[3] = '&nbsp;';
			$subject;	
			$temp_data = preg_replace($gp,$gr,$template_data);
		

			$config['protocol'] = 'mail';
			$config['smtp_host'] = 'ingreso.drhinternet.net';
			$config['smtp_user'] = 'noreply@magicresponder.com';
			$config['smtp_pass'] = 'NR123456';
			$config['smtp_port'] =  587;
			
			
			$config['mailtype'] = 'html';
			$config['charset'] = 'UTF-8';
			$config['wordwrap'] = TRUE;
			$this->email->initialize($config);

			$this->email->from($from, $fromName);
			$this->email->to($to);
			$this->email->subject($subject);
			$this->email->message($temp_data);			
			$this->email->send();
			
			//echo $this->email->print_debugger();
			//exit();
			


//			$headers = "MIME-Version: 1.0\n"; 
//			$headers .= "Content-Type: text/html; charset=\"UTF-8\"\n"; 
//			$headers .= "X-Priority: 1 (Highest)\n"; 
//			$headers .= "X-MSMail-Priority: High\n"; 
//			$headers .= "Importance: High\n"; 
//			$headers .= "From: $fromName <$from>" . "\r\n";
//			$headers .= "Reply-to: system@cyberneticolive.com <$from>" . "\r\n";
//			$status = mail($to, $subject, $temp_data, $headers); 	
		}
		
		
		function post($member_uid,$group_id,$txt)
  {
		   $query = $this->db->get_where('members',array('member_uid' => $member_uid,'group_id' => $group_id));
			if($query->num_rows() >0)
			{
				   $data = array(
							   'member_uid' => $member_uid,
							   'group_id' =>  $group_id,
							   'txt' => $txt,
							   'time_stamp' => time()
							   );
				
				  $this->db->insert('messages', $data); 
				  $query1 = $this->db->get_where('members',array('group_id' => $group_id));
			 }  
		return TRUE;
  }

// View  group messages  function //
  
  function view_group_txt($group_id,$from = FALSE)
  {


  $this->db->order_by('id desc');
  $this->db->limit(5);
  
  if(is_numeric($from))
  {
  $this->db->where('id <',$from);
  }
  
  
 $query = $this->db->get_where('messages',array('group_id' => $group_id));
  

  return $query;

 
 
   }


// View user all group messages  function //
  
  function view_user_all_groups_txt($group_ids,$from = FALSE)
  {


  $this->db->order_by('id desc');
  $this->db->limit(5);
  $this->db->where_in('group_id',$group_ids);
  
  if(is_numeric($from))
  {
  $this->db->where('id <',$from);
  }
  
  
 $query = $this->db->get('messages');
  

  return $query;

 
 
   }










 
  function usernameByUID($id)
  {
  
  $query = $this->db->get_where('users',array('userID' => $id));
  
  if($query->num_rows() > 0)
  {
  
  $username = $query->row('username');
  
  return $username;
  }
  else
  {
  return false;
  }
  
  
  
  
  }
  
  function check_duplicate_number($no1,$no2)
  	{
		$sql = mysql_query("select * from users where number = '$no1'") or die(mysql_error());
		return mysql_num_rows($sql);
	}
  
  
/* Works out the time since the entry post, takes a an argument in unix time (seconds)
*/
 function Timesince($original) {
    // array of time period chunks
    $chunks = array(
	array(60 * 60 * 24 * 365 , 'year'),
	array(60 * 60 * 24 * 30 , 'month'),
	array(60 * 60 * 24 * 7, 'week'),
	array(60 * 60 * 24 , 'day'),
	array(60 * 60 , 'hour'),
	array(60 , 'min'),
	array(1 , 'sec'),
    );

    $today = time(); /* Current unix time  */
    $since = $today - $original;

    // $j saves performing the count function each time around the loop
    for ($i = 0, $j = count($chunks); $i < $j; $i++) {

	$seconds = $chunks[$i][0];
	$name = $chunks[$i][1];

	// finding the biggest chunk (if the chunk fits, break)
	if (($count = floor($since / $seconds)) != 0) {
	    break;
	}
    }

    $print = ($count == 1) ? '1 '.$name : "$count {$name}s";

    if ($i + 1 < $j) {
	// now getting the second item
	$seconds2 = $chunks[$i + 1][0];
	$name2 = $chunks[$i + 1][1];

	// add second item if its greater than 0
	if (($count2 = floor(($since - ($seconds * $count)) / $seconds2)) != 0) {
	    $print .= ($count2 == 1) ? ', 1 '.$name2 : " $count2 {$name2}s";
	}
    }
    return $print;
}

  
  
  
function getPhoto($id)
{

  $query = $this->db->get_where('users',array('id' => $id));
  
  if($query->num_rows() > 0)
  {
  
  $photo = $query->row('photo');

  return $photo;

  }
  else
  {
   return false;
  }
  
  

}






function get_all_messages($name = FALSE)
{

if($name != FALSE)
{
 $this->db->where('txt =',$name);
}


$query = $this->db->get('messages');



  return $query;
  
 } 

function MessageById($id)
{

$query = $this->db->get_where('messages',array('id'=>$id));

$name = $query->row('txt');

return $name;


}



 function ChangeMessage($id,$name)
 {
 
 $data = array(
            'txt' => $name
            );
      $this->db->where('id =', $id);
$q = $this->db->update('messages', $data); 
 
   return true;
 }
 
 
 function DestoryMessage($id)
 {
 
 $this->db->where('id =',$id);
 $this->db->delete('messages'); 
 
  return true;
 
 
 
 }
  
		
	function getEmailListDownline($userID)
		{
			if($userID) $extra = "where userID = '$userID' and downloadEmailUnsub = 0";
			else  $extra = "where refID = '".$this->session->userdata('userID')." and downloadEmailUnsub = 0'";
			$users = $this->db->dbprefix('users');
			$query=$this->db->query("select * from sg_users $extra");
			return $query->result_array();
		}
			
	function gSelectAllDirectRefflike($userID,$key)
		{
			$users = $this->db->dbprefix('users');
			$query=$this->db->query("select * from sg_users where refID = '$userID' and ( username like '%$key%' or emailID like '%$key%') ");
			return $query->result_array();
		}	
	
	function check_sponcer($username)
		{
			$users = $this->db->dbprefix('users');
			$query=$this->db->query("select * from sg_users where username = '$username' limit 1 ");
			return $query->result_array();
		}
		
	function count_withdraw($s)
		{
			$users = $this->db->dbprefix('users');
			$withdraw = $this->db->dbprefix('withdraw');
			if($s=='all') $status = '';
			else $status = " and W.status = '$s'";
			
			$query=$this->db->query("select * from $users as U, $withdraw as W WHERE U.userID = W.userID $status");
			return $query->num_rows();
		}	
	
	function getAllWithdrawal($s,$offset,$per_page)
		{
			$users = $this->db->dbprefix('users');
			$withdraw = $this->db->dbprefix('withdraw');
			if($s=='all') $status = '';
			else $status = " and W.status = '$s'";
			
			$query=$this->db->query("select * from $users as U, $withdraw as W WHERE U.userID = W.userID $status ORDER  BY wID DESC limit $offset,$per_page");
			return $query->result_array();
		}
	
	function getSingleWithdrawal($wID)
		{
			$users = $this->db->dbprefix('users');
			$withdraw = $this->db->dbprefix('withdraw');
			$query=$this->db->query("select * from $users as U, $withdraw as W WHERE U.userID = W.userID and  wID = '$wID' ");
			return $query->result_array();
		}
	
	function query($query)
		{
			$query=$this->db->query($query);
			return $query->result_array();
		}

	function query_update($query)
		{
			$query=$this->db->query($query);
		}

	function gUpdate($tableName,$where,$qData)
		{	
			foreach($where as $col=>$val)
				$this->db->where($col,$val);		
			$this->db->update($tableName,$qData);		
		}

	function gInsert($tableName,$qData)
		{
			$this->db->insert($tableName, $qData); 
			return $ID = $this->db->insert_id();	
		}
	function createUserAccount($data)
		{
				$this->db->insert('users',$data);
				$insertID = $this->db->insert_id();
				$data1['userID'] = $insertID;
				$data1['user_name'] = md5($data['username']);
				$this->db->insert('validateuser',$data1);
				return $insertID;
		}	
	function gDelete($tableName,$where)
		{
			foreach($where as $col=>$val)
				$this->db->where($col,$val);		
			$this->db->delete($tableName); 
			
		}
	function gLike($tableName,$where)
		{
			foreach($where as $col=>$val)
				$this->db->like($col,$val);		
			$this->db->insert($tableName); 
		}
	
	function gCountAll($tableName,$where=NULL)
		{
			if(is_array($where))
				foreach($where as $col=>$val)
					$this->db->where($col,$val);		
			return $this->db->count_all_results($tableName);		
		}
	
	function gTruncate($tableName)
		{
			$this->db->truncate($tableName); 
		}

	function gSelectStar($tableName,$obf='',$ob='')
		{
			if($obf)
				$this->db->order_by($obf, $ob); 
			$query = $this->db->get($tableName); 
			return $query->result_array();	
		}
	function gSelectWhere($tableName,$where,$orderby=NULL)
		{
			if(is_array($orderby))
				foreach($orderby as $col=>$val)
					$this->db->order_by($col,$val);		
					
				//	print_r($orderby);

			$query = $this->db->get_where($tableName,$where); 
			return $query->result_array();	
		}
	function gSelectWhereOr($tableName,$where,$orderby=NULL)
		{
			if(is_array($orderby))
				foreach($orderby as $col=>$val)
					$this->db->order_by($col,$val);		

			if(is_array($where))
				foreach($where as $col=>$val)
					$this->db->or_where($col,$val);		

		
			$query = $this->db->get_where($tableName); 
			return $query->result_array();	
		}
	function gSelectWhereLimit($tableName,$where,$offset,$per_page,$orderby=NULL,$groupby=NULL)
		{
			if(is_array($orderby))
				foreach($orderby as $col=>$val)
					$this->db->order_by($col,$val);		
			

			if($groupby)
					$this->db->group_by($groupby);		

			if(is_array($where))
				$query = $this->db->get_where($tableName,$where,$per_page,$offset); 
			else
				$query = $this->db->get($tableName,$per_page,$offset); 
			return $query->result_array();	
		}
		
	function log_entry($ld,$userID)
		{
			$ins_data['logData'] = $ld;
			$ins_data['userID'] = $userID;
			$ins_data['insTime'] = time();
			$this->gInsert('log',$ins_data);
		}

	function adminlog($ld,$userID=NULL)
		{
		
			$ins_data['logData'] = $ld;
			$ins_data['userID'] = $userID;
			$ins_data['insTime'] = time();
			$this->gInsert('adminlog',$ins_data);
		}

	function admin_revenue($sType,$sAmount,$sCreditDebit,$sRefID=NULL,$sNote=NULL)
		{
			$ins_data['sRefID'] = $sRefID;
			$ins_data['sCreditDebit'] = $sCreditDebit;
			$ins_data['sAmount'] = $sAmount;
			$ins_data['sType'] = $sType;
			$ins_data['sDate'] = time();
			$ins_data['sNote'] = $sNote;
			$this->gInsert('siterevenue',$ins_data);
		}

	function pagos_history($userID,$transactionID,$amount,$responce=NULL,$other=NULL,$phType=NULL)
		{
			if($userID)
			{
				$ins_data['userID'] = $userID;
				$ins_data['transactionID'] = $transactionID;
				$ins_data['amount'] = $amount;
				$ins_data['responce'] = $responce;
				$ins_data['other'] = serialize($other);
				$ins_data['phType'] = $phType;
				$ins_data['phInsID'] = time();
				$this->gInsert('pagoshistory',$ins_data);
			}
		}
	



	function pay_fast_track($userID,$lev,$total_paid=0,$xusername)
		{	
			error_reporting(0);
			$new_lev = $lev + 1;
			if($lev < 5)
				{
					if($new_lev==1)	$pay = 50;
					else if($new_lev==2)	$pay = 20;
					else if($new_lev==3)	$pay = 10;
					else if($new_lev==4)	$pay = 10;
					else if($new_lev==5)	$pay = 10;
					else $pay = 0;

					$rs = $this->apcache->user_cache_data($userID,72000);
					$username = $rs[0]['username'];
					$subscribed = $rs[0]['subscribed'];
					$isfounder = $rs[0]['isfounder'];
					$refID = $rs[0]['refID'];
					if( ($subscribed == 1) && ($isfounder == 1) ) // FOR FOUNDERS ONLY
					{
						$this->db->query("UPDATE sg_users SET `pendingCycleBal` = `pendingCycleBal` + '$pay',`totalICEarning` = `totalICEarning` + '$pay' where `userID` = '$userID'");
						
						$his_data = NULL;
						$his_data['userID'] = $userID;
						$his_data['purTime'] = time();
						$his_data['details'] = " Fast start bonus for level $new_lev for user '$xusername'";
						$his_data['amount'] = $pay;
						$his_data['type'] = 3;
						$his_data['msgType'] = 29;
						$his_data['msgVal1'] = $new_lev;
						$his_data['msgVal2'] = $xusername;
						$his_data['msgVal3'] = '';
						$this->zll->gInsert('purches_history',$his_data);
						$total_paid += $pay;
					}
				
					if($refID==0) // RETURN IF ROOT LEVEL IS ARRIVED
					{
						return $total_paid;
					}
					
					return $this->zll->pay_fast_track($refID,$new_lev,$total_paid,$xusername);
				}
			else
				{
					return $total_paid;
				}
		
		}
	
	
	function pay_last_vegas_contest_1($userID,$level=1)
		{
			if($level<=5)
				{
					if($level==1)	$pay = 5;
					else if($level==2)	$pay = 10;
					else if($level==3)	$pay = 15;
					else if($level==4)	$pay = 20;
					else if($level==5)	$pay = 25;
					else $pay = 0;
					$wh = $udata = NULL;
					$wh['userID'] = $userID;
					$udata = $this->zll->gSelectWhere('users',$wh);
					$refID = $udata[0]['userID'];
					$this->query_update("UPDATE sg_users SET `c1` = `c1` + '$pay' WHERE userID = '$userID' limit 1 ");
					$level++;
					return $this->zll->pay_last_vegas_contest_1($refID,$level);
				}
			else
				{
					return 0;
				}
		}
	
	function pay_recurring_comission($userID,$lev,$total_paid=0,$xusername,$xmonths)
		{	
			$xmonths =1;
			$new_lev = $lev + 1;
			if($lev < 10)
				{
					
					$wh['userID'] = $userID;
					$rs = $this->zll->gSelectWhere('users',$wh);
					$username = $rs[0]['username'];
					$subscribed = $rs[0]['subscribed'];
					$isfounder = $rs[0]['isfounder'];
					$refID = $rs[0]['refID'];
					$aprilCampaign = $rs[0]['aprilCampaign'];
					$activePlan = $rs[0]['activePlan'];

					$matrix50 = $rs[0]['matrix50'];
					$matrix100 = $rs[0]['matrix100'];
					$matrix300 = $rs[0]['matrix300'];
					$matrix500 = $rs[0]['matrix500'];
					$matrix1500 = $rs[0]['matrix1500'];
					
					
					if($activePlan==1)
					{
						if($aprilCampaign==1 && $isfounder==0)
						{
							if($new_lev==1)	$pay = 8*$xmonths; // 8
							else if($new_lev==2)	$pay = 2*$xmonths; // 2 
							else if($new_lev==3)	$pay = 2*$xmonths; // 2
							else if($new_lev==4)	$pay = 2*$xmonths; // 2
							else if($new_lev==5)	$pay = 1*$xmonths; // 1
							else $pay = 0;
						}
						else
						{
							if($new_lev==1)	$pay = 5*$xmonths; // 5
							else if($new_lev==2)	$pay = 2*$xmonths; // 2 
							else if($new_lev==3)	$pay = 1*$xmonths; // 2
							else if($new_lev==4)	$pay = 1*$xmonths; // 2
							else if($new_lev==5)	$pay = 1*$xmonths; // 1
							else if($new_lev==6)	$pay = 2*$xmonths;
							else if($new_lev==7)	$pay = 1*$xmonths;
							else if($new_lev==8)	$pay = 1*$xmonths;
							else if($new_lev==9)	$pay = 1*$xmonths;
							else $pay = 0;
						}
					}
					else if($activePlan==2)
					{
						if($isfounder==0)
						{
							if($new_lev==1)	$pay = 2*$xmonths; // 5
							else if($new_lev==2)	$pay = 2*$xmonths;
							else if($new_lev==3)	$pay = 1*$xmonths;
							else if($new_lev==4)	$pay = 1*$xmonths;
							else if($new_lev==5)	$pay = 1*$xmonths;
							else $pay = 0;
						
						}
						else
						{
							if($matrix50==1 || $matrix100==1)
								{
									if($new_lev==1)	$pay = 2*$xmonths;
									else if($new_lev==2)	$pay = 2*$xmonths;
									else if($new_lev==3)	$pay = 1*$xmonths;
									else if($new_lev==4)	$pay = 1*$xmonths;
									else if($new_lev==5)	$pay = 2*$xmonths; 
									else if($new_lev==6)	$pay = 3*$xmonths;
									else if($new_lev==7)	$pay = 4*$xmonths;
									else $pay = 0;
								}
							if($matrix300==1)
								{
									if($new_lev==1)	$pay = 2*$xmonths;
									else if($new_lev==2)	$pay = 2*$xmonths;
									else if($new_lev==3)	$pay = 1*$xmonths;
									else if($new_lev==4)	$pay = 1*$xmonths;
									else if($new_lev==5)	$pay = 1*$xmonths; 
									else if($new_lev==6)	$pay = 1*$xmonths;
									else if($new_lev==7)	$pay = 3*$xmonths;
									else if($new_lev==8)	$pay = 4*$xmonths;
									else $pay = 0;
								}
							if($matrix500==1)
								{
									if($new_lev==1)	$pay = 2*$xmonths;
									else if($new_lev==2)	$pay = 2*$xmonths;
									else if($new_lev==3)	$pay = 1*$xmonths;
									else if($new_lev==4)	$pay = 1*$xmonths;
									else if($new_lev==5)	$pay = 1*$xmonths; 
									else if($new_lev==6)	$pay = 1*$xmonths;
									else if($new_lev==7)	$pay = 1*$xmonths;
									else if($new_lev==8)	$pay = 2*$xmonths;
									else if($new_lev==9)	$pay = 4*$xmonths;
									else $pay = 0;
								}
							if($matrix1500==1)
								{
									if($new_lev==1)	$pay = 2*$xmonths;
									else if($new_lev==2)	$pay = 2*$xmonths;
									else if($new_lev==3)	$pay = 1*$xmonths;
									else if($new_lev==4)	$pay = 1*$xmonths;
									else if($new_lev==5)	$pay = 1*$xmonths; 
									else if($new_lev==6)	$pay = 1*$xmonths;
									else if($new_lev==7)	$pay = 1*$xmonths;
									else if($new_lev==8)	$pay = 1*$xmonths;
									else if($new_lev==9)	$pay = 2*$xmonths;
									else if($new_lev==10)	$pay = 3*$xmonths;
									else $pay = 0;
								}
						}					
					}
					else if($activePlan==3)
					{
						if($isfounder==0)
						{
							if($new_lev==1)	$pay = 5*$xmonths; 
							else if($new_lev==2)	$pay = 2*$xmonths;
							else if($new_lev==3)	$pay = 1*$xmonths;
							else if($new_lev==4)	$pay = 1*$xmonths;
							else if($new_lev==5)	$pay = 1*$xmonths;
							else $pay = 0;
						
						}
						else
						{
							if($matrix50==1 || $matrix100==1)
								{
									if($new_lev==1)	$pay = 2*$xmonths;
									else if($new_lev==2)	$pay = 2*$xmonths;
									else if($new_lev==3)	$pay = 1*$xmonths;
									else if($new_lev==4)	$pay = 1*$xmonths;
									else if($new_lev==5)	$pay = 1*$xmonths; 
									else if($new_lev==6)	$pay = 2*$xmonths;
									else if($new_lev==7)	$pay = 3*$xmonths;
									else $pay = 0;
								}
							if($matrix300==1)
								{
									if($new_lev==1)	$pay = 2*$xmonths;
									else if($new_lev==2)	$pay = 2*$xmonths;
									else if($new_lev==3)	$pay = 1*$xmonths;
									else if($new_lev==4)	$pay = 1*$xmonths;
									else if($new_lev==5)	$pay = 1*$xmonths; 
									else if($new_lev==6)	$pay = 2*$xmonths;
									else if($new_lev==7)	$pay = 3*$xmonths;
									else if($new_lev==8)	$pay = 1*$xmonths;
									else $pay = 0;
								}
							if($matrix500==1)
								{
									if($new_lev==1)	$pay = 2*$xmonths;
									else if($new_lev==2)	$pay = 2*$xmonths;
									else if($new_lev==3)	$pay = 1*$xmonths;
									else if($new_lev==4)	$pay = 1*$xmonths;
									else if($new_lev==5)	$pay = 1*$xmonths; 
									else if($new_lev==6)	$pay = 2*$xmonths;
									else if($new_lev==7)	$pay = 3*$xmonths;
									else if($new_lev==8)	$pay = 1*$xmonths;
									else if($new_lev==9)	$pay = 1*$xmonths;
									else $pay = 0;
								}
							if($matrix1500==1)
								{
									if($new_lev==1)	$pay = 2*$xmonths;
									else if($new_lev==2)	$pay = 2*$xmonths;
									else if($new_lev==3)	$pay = 1*$xmonths;
									else if($new_lev==4)	$pay = 1*$xmonths;
									else if($new_lev==5)	$pay = 1*$xmonths; 
									else if($new_lev==6)	$pay = 2*$xmonths;
									else if($new_lev==7)	$pay = 3*$xmonths;
									else if($new_lev==8)	$pay = 1*$xmonths;
									else if($new_lev==9)	$pay = 1*$xmonths;
									else if($new_lev==10)	$pay = 1*$xmonths;
									else $pay = 0;
								}
						}					
					}

					if( ($lev<5) && ($subscribed == 1) && ($new_lev)  && ($pay)) // FOR ALL USERS
					{
						$this->db->query("UPDATE sg_users SET `pendingUnilevelBal` = `pendingUnilevelBal` + '$pay',`totalICEarning` = `totalICEarning` + '$pay' where `userID` = '$userID'");
						
						$his_data = NULL;
						$his_data['userID'] = $userID;
						$his_data['purTime'] = time();
						$his_data['details'] = "Unilevel commission for level $new_lev for user '$xusername'";
						$his_data['amount'] = $pay;
						$his_data['type'] = 3;
						$his_data['msgType'] = 28;
						$his_data['msgVal1'] = $new_lev;
						$his_data['msgVal2'] = $xusername;
						$his_data['msgVal3'] = '';
						$this->zll->gInsert('purches_history',$his_data);
						//print_r($his_data);
						$total_paid += $pay;
					
					}
					if( ($lev>=5) && ($subscribed == 1) && ($isfounder == 1)  && ($pay) ) // FOR FOUNDERS ONLY
					{
					
						$this->db->query("UPDATE sg_users SET `pendingUnilevelBal` = `pendingUnilevelBal` + '$pay',`totalICEarning` = `totalICEarning` + '$pay' where `userID` = '$userID'");
						
						$his_data = NULL;
						$his_data['userID'] = $userID;
						$his_data['purTime'] = time();
						$his_data['details'] = "Unilevel commission for level $new_lev for user '$xusername'";
						$his_data['amount'] = $pay;
						$his_data['type'] = 3;
						$his_data['msgType'] = 28;
						$his_data['msgVal1'] = $new_lev;
						$his_data['msgVal2'] = $xusername;
						$his_data['msgVal3'] = '';
						$this->zll->gInsert('purches_history',$his_data);
						$total_paid += $pay;
					}
				
					if($refID==0) // RETURN IF ROOT LEVEL IS ARRIVED
					{
						return $total_paid;
					}
					
					return $this->zll->pay_recurring_comission($refID,$new_lev,$total_paid,$xusername,$xmonths);
				}
			else
				{
					return $total_paid;
				}
		
		}
	

		function encrypt($sData, $sKey='K(*@(+D34S^W(#$)@*$#$LJ%(*3OSDs098s*&(dfsdf(d#f)sof9sd0fdK)(&())LOIYS*&sdsdf23(*&)D(SFDFJDSLFDSFY*Y'){
			$sResult = '';
			for($i=0;$i<strlen($sData);$i++){
				$sChar    = substr($sData, $i, 1);
				$sKeyChar = substr($sKey, ($i % strlen($sKey)) - 1, 1);
				$sChar    = chr(ord($sChar) + ord($sKeyChar));
				$sResult .= $sChar;
			}			
			
			return $this->encode_base64($sResult);
			
		}
		//'K(*@(+D34S^W(#$)@*$#$LJ%(*3OSDs098s*&(dfsdf(d#f)sof9sd0fdK)(&())LOIYS*&sdsdf23(*&)D(SFDFJDSLFDSFY*Y'
		function decrypt($sData, $sKey='K(*@(+D34S^W(#$)@*$#$LJ%(*3OSDs098s*&(dfsdf(d#f)sof9sd0fdK)(&())LOIYS*&sdsdf23(*&)D(SFDFJDSLFDSFY*Y'){
			$sResult = '';
			$sData   = $this->decode_base64($sData);
			for($i=0;$i<strlen($sData);$i++){
				$sChar    = substr($sData, $i, 1);
				$sKeyChar = substr($sKey, ($i % strlen($sKey)) - 1, 1);
				$sChar    = chr(ord($sChar) - ord($sKeyChar));				
				$sResult .= $sChar;
			}
			return $sResult;
		}
		
		
		function encode_base64($sData){
			$sBase64 = base64_encode($sData);
			return str_replace('=', '', strtr($sBase64, '+/', '-_'));
		}
		
		function decode_base64($sData){
			$sBase64 = strtr($sData, '-_', '+/');
			return base64_decode($sBase64.'==');
		}



	function restart_service($userID)
		{
			$start_tt = mktime(0,0,0,date('m'),date('d'),date('Y'));
			$end_tt = mktime(23,59,59,date('m'),date('d'),date('Y'));
			$query = "SELECT hostingName,userID,hostingStatus,mrStatus FROM sg_users WHERE userID = '$userID'";	
			$sql = mysql_query($query) or die(mysql_error());
			while($row = mysql_fetch_array($sql))
				{

					$hostingName = $row['hostingName'];	
					$hostingStatus = $row['hostingStatus'];	
					$mrStatus = $row['mrStatus'];	
					$userID = $row['userID'];	
					
					if($mrStatus==0)
					{					
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
						curl_setopt($ch, CURLOPT_URL,"http://www.magicresponder.com/mr_api/ic_change_status/$userID/1/");
						curl_setopt($ch, CURLOPT_POST, 1);
						curl_setopt($ch, CURLOPT_POSTFIELDS,"nombre=1");
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						$server_output = curl_exec ($ch);
						curl_close ($ch);
						mysql_query("UPDATE sg_users SET mrStatus = 1 WHERE userID = '$userID'") or die(mysql_error());
					}
					
					
					if($hostingStatus==0) // suspend hosting
					{
						mysql_query("UPDATE sg_users SET hostingStatus = 1 WHERE userID = '$userID'") or die(mysql_error());
			
			
												include getcwd()."/crons/xmlapi.php";
												$root_hash = "541a3902b9ff2eac5d34964f04b9fcde
						25de6c0c543c78d69de4489c539b5575
						753a9be4a7ccde350c6a149dec196d50
						1b1bf6331cdeb1f8409640b41abce82f
						7480f0a9fdc76cae56302b4db9347d53
						76b1ec705ac98d72bd462ad6ed6c4111
						39fccbaa84d551125b34f9795e356bd6
						df493f73082140d96eed856414bd3f0a
						c2c3b3050ed0bc3eb1a53824797270f5
						1afeed28f8050e8403f180e21ffdb262
						9cbcf02fbc5d449dd1dab4e3d8a42eae
						2d9f99ccfc002eac085fcb7b081665f3
						43656cfd829f33848ff83719afd4b709
						8540557198e64d01e9295f29dc4f2987
						4f44902df7a4a91d7154fcac0aae1c62
						b6ccee6fc75dcd387a0ffdb344f86d70
						2cfc947f66c87d28ec58465adcbd5418
						4a3bea717fd092508f98b50b73f6576f
						d8cf7cd3ea049849e35dab24d1c918a2
						e379e6c8980343d3dded559207fd2d3d
						25a58659c46fd56010a0758f7f8b720d
						434373903eec1e706c041ac1ef909066
						7f7339379106165921bca7c910a63837
						8f932c21b7c29c1b7c78c22612588dcd
						e418ae8a80977fde4fc47a42d5c532ed
						50f4c8abd4d3f7bfcb43da6dd1f8ef6a
						46407e8e2b4005f191fd955fefc391ac
						750a9255cce7d97d1b31b965b4e7309e
						6b81e7d903b499b9e79039100c4b099b";
												$xmlapi = new xmlapi('198.154.251.54');
												$xmlapi->hash_auth("root",$root_hash);
												$ac = $xmlapi->unsuspendacct($hostingName);
				}
				
				
				
				
				
				
				
				}
		}

	function getActiveBonanza()
		{
			$bonanza8 = $this->db->dbprefix('bonanza8');
			$query=$this->db->query("select bzID from $bonanza8 where bzStatus = 1 order by bzID limit 1");
			$rs = $query->result_array();				
			//return $rs[0]['bzID'];
		}	
		
	function getBonanzaResults($bzID)
		{
			$bzresult = $this->db->dbprefix('bzresult');
			$users = $this->db->dbprefix('users');
			//echo "select * from $bzresult as BZR, $users U where U.userID = BZR.userID and bzID = '$bzID' order by userRank";
			//echo "select * from $bzresult as BZR, $users as U where bzID = '$bzID' and U.userID = BZR.userID  AND  (U.userID <> '601' AND U.userID <> '1') order by userRank";
			$query=$this->db->query("select userRank,totalBids,U.username,firstname,lastname,emailID,U.userID from $bzresult as BZR, $users as U where bzID = '$bzID' and U.userID = BZR.userID  AND  (U.userID <> '601' AND U.userID <> '1') order by userRank");
			return $query->result_array();				
		}	
	// (bzID = '$bzID' and (userID <> '601' or userID <> '1')) 	
	function getBonanzaUserlist($bzID)
		{
			$bzresult = $this->db->dbprefix('bzresult');
			$query=$this->db->query("select * from $bzresult where (bzID = '$bzID' and (userID <> '601' AND userID <> '1')) order by totalBids desc");
			return $query->result_array();				
		}




	function updateBonanza($userID,$pts = 1,$subtract=0)
	{
		$bzID = $this->zll->getActiveBonanza();
		
		if($bzID)
			{
				$user_check_where['userID'] = $userID;
				$user_check_where['bzID'] = $bzID;
				$rs = $this->zll->gSelectWhere('bzresult',$user_check_where);
				$res_count = count($rs);
				if($res_count) // update
					{
						foreach($rs as $row1)
							{
								if($subtract) $new_user_bids = $row1['totalBids'] - $pts;
								else $new_user_bids = $row1['totalBids'] + $pts;
								$xxwh['bzrID'] = $row1['bzrID'];
								$upd_Data['totalBids'] = $new_user_bids;
								$update = $this->zll->gUpdate('bzresult',$xxwh,$upd_Data);
							}	
					}
				else // create new entry on first bid for bonanza
					{
								if(!$pts) $pts = 1;	
								if($subtract) $ins_Data['totalBids'] = "-".$pts;
								else $ins_Data['totalBids'] = $pts;
								$ins_Data['bzID'] = $bzID;
								$ins_Data['userID'] = $userID;
								$update = $this->zll->gInsert('bzresult',$ins_Data);
					}

			}
	}	

	function checkImg($path)
		{
					if(!file_exists(getcwd()."/uploads/".$path) && !is_dir(getcwd()."/items/".$path))
						return 'default_img.png';
					else
						return $path;
		
		}		


function Sec2Time($time){
  if(is_numeric($time)){
    $value = array(
      "years" => 0, "days" => 0, "hours" => 0,
      "minutes" => 0, "seconds" => 0,
    );
    if($time >= 31556926){
      $value["years"] = floor($time/31556926);
      $time = ($time%31556926);
    }
    if($time >= 86400){
      $value["days"] = floor($time/86400);
      $time = ($time%86400);
    }
    if($time >= 3600){
      $value["hours"] = floor($time/3600);
      $time = ($time%3600);
    }
    if($time >= 60){
      $value["minutes"] = floor($time/60);
      $time = ($time%60);
    }
    $value["seconds"] = floor($time);
    return (array) $value;
  }else{
    return (bool) FALSE;
  }
}

	function gen_pdf($invID,$invHTML)
		{
			require_once(getcwd().'/html2pdf/new/tcpdf_autoconfig.php');
			require_once(getcwd().'/html2pdf/new/tcpdf.php');
			
			// create new PDF document
			$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, false, 'ISO-8859-1', false);
			
			
			// set margins
			$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
			$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
			$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
			
			// set auto page breaks
			$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
			
			// set image scale factor
			$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
			
			// set some language-dependent strings (optional)
			if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
				require_once(dirname(__FILE__).'/lang/eng.php');
				$pdf->setLanguageArray($l);
			}
			
			// set font
			$pdf->SetFont('helvetica', '', 10);
			
			// add a page
			$pdf->AddPage();
			
			// output the HTML content
			$pdf->writeHTML($invHTML, true, false, true, false, '');
			
			// reset pointer to the last page
			$pdf->lastPage();
			
			// ---------------------------------------------------------
			
			//Close and output PDF document
			$pdf->Output('IC_Invoice_'.$invID.'.pdf', 'I');


		}
		
		function generateCertificate($userID,$level,$firstname,$lastname,$act_lang)
		{
			error_reporting(E_ALL);
			if($act_lang=='eng') $L = 'eng';
			else $L = 'spa';
			
			$firstname = trim($firstname);
			$lastname = trim($lastname);
			
			
			$img_path =getcwd()."/cdn/certificates/".$L."/level".$level.".jpg";
			$image =   imagecreatefromjpeg($img_path);
			$white = ImageColorAllocate($image, 0,0,0);
			$font = getcwd()."/cdn/KUNSTLER.TTF";
			
			$image_width = imagesx($image);  
			$image_height = imagesy($image);
			
			$text_box = imagettfbbox(58,0,$font,trim($firstname)." ".trim($lastname));
			$text_width = $text_box[2]-$text_box[0];
			//$text_height = $text_box[3]-$text_box[1];
			
			// Calculate coordinates of the text
			$x = ($image_width/2) - ($text_width/2) + 20;
			$y = 320;
			
			$final_firstname = str_replace("%20"," ",$firstname);
			$final_lastname = str_replace("%20"," ",$lastname);
			
			imagettftext($image, 58, 0, $x, $y, $white, $font, trim($final_firstname)." ".trim($final_lastname));
			header("content-type: image/png");
			imagepng($image);
			
		}


	function seourl($p)
		{
		
			$active_lang = $this->session->userdata('active_lang'); if($active_lang=='') $active_lang = 'eng';
			
			$pattern[0] = '/\s/i';
			$pattern[1] = '/\'/i';
			$pattern[2] = '/"/i';
			$pattern[3] = '/\(/i';
			$pattern[4] = '/\)/i';
			$pattern[5] = '/\]/i';
			$pattern[6] = '/\[/i';
			$pattern[7] = '/\#/';
			$pattern[8] = '/\./';
			$pattern[9] = '/\&/';
			$pattern[10] = '/\//';
			$pattern[11] = '/\\\/';
			$pattern[12] = '/`/';
			$pattern[13] = '/\?/';
			$pattern[14] = '/\+/';
			$pattern[15] = '/\,/';
			$pattern[16] = '/\$/';
			$replacement[0] = '-';
			$replacement[1] = '';
			$replacement[2] = '';
			$replacement[3] = '';
			$replacement[4] = '';
			$replacement[5] = '';
			$replacement[6] = '';
			$replacement['7']='';
			$replacement['8']='';
			$replacement['9']='and';
			$replacement['10']='-';
			$replacement['11']='-';
			$replacement['12']='';
			$replacement['13']='';
			$replacement['14']='';
			$replacement['15']='';
			$replacement['16']='';
			return strtolower(trim(preg_replace($pattern, $replacement, $p),'-'))."-".$active_lang;
		}


	function getSeconds($FP)
		{
			$CMD = '/usr/bin/ffmpeg -i "http://s3.amazonaws.com/cyberneticolive/CL_LIVE_STREAM_1_540a9ee6dac14.flv" 2>&1 | grep "Duration"';
			error_reporting(E_ALL);
			exec($CMD,$OP);
			$data = $OP[0];
			$exp1 = explode(",",$data);
			$exp2 = explode(":",$exp1[0]);
			$hr =  $exp2[1];
			$min =  $exp2[2];
			$sec =  floor( $exp2[3] );
			$total_sec = ($hr * 3600) + ($min * 60) + $sec;
			return $total_sec;
		}

	function load_mr($act,$name,$emailID,$lang='')
		{
			if(!$lang)
			{
				$lang = $this->session->userdata('active_lang');
				if(!$lang) $lang = 'spa';
			}
			$default_post_url = "https://www.magicresponder.com/mr_api/ic_campaign/$lang/$act/";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$default_post_url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,"name=$name&email=$emailID");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);			
			$server_output = curl_exec ($ch);
			curl_close ($ch);
		}
		
	function load_mr_free($name,$emailID)
		{
			$default_post_url = "https://www.magicresponder.com/mr_api/cl_campaign/";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$default_post_url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,"name=$name&email=$emailID");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);			
			$server_output = curl_exec ($ch);
			curl_close ($ch);
		}
		
	function load_mr_affiliate($name,$emailID)
		{
			$default_post_url = "https://www.magicresponder.com/mr_api/cl_campaign_affiliate/";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$default_post_url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,"name=$name&email=$emailID");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);			
			$server_output = curl_exec ($ch);
			curl_close ($ch);
			return $server_output;
		}
		
	function get_timezone_list()
		{
				static $regions = array(
					DateTimeZone::AFRICA,
					DateTimeZone::AMERICA,
					DateTimeZone::ANTARCTICA,
					DateTimeZone::ASIA,
					DateTimeZone::ATLANTIC,
					DateTimeZone::AUSTRALIA,
					DateTimeZone::EUROPE,
					DateTimeZone::INDIAN,
					DateTimeZone::PACIFIC,
				);
			
				$timezones = array();
				foreach( $regions as $region )
				{
					$timezones = array_merge( $timezones, DateTimeZone::listIdentifiers( $region ) );
				}
			
				$timezone_offsets = array();
				foreach( $timezones as $timezone )
				{
					$tz = new DateTimeZone($timezone);
					$timezone_offsets[$timezone] = $tz->getOffset(new DateTime);
				}
			
				// sort timezone by offset
				asort($timezone_offsets);
				return $timezone_offsets;
				//print_r($timezone_offsets);
			
//				$timezone_list = array();
//				foreach( $timezone_offsets as $timezone => $offset )
//				{
//					$offset_prefix = $offset < 0 ? '-' : '+';
//					$offset_formatted = gmdate( 'H:i', abs($offset) );
//			
//					$pretty_offset = "GMT${offset_prefix}${offset_formatted}";
//			
//					$timezone_list[$timezone] = "(${pretty_offset}) $timezone";
//				}
//			
//				return $timezone_list;
		}		
		
	function set_tz($tz)
		{

			
			date_default_timezone_set($tz);
			$is_daylight = date('I', time());
			date_default_timezone_set('GMT');
			
			return $is_daylight;
			
		}
function cc_masking($number) {
    return substr($number, 0, 4) . str_repeat("X", strlen($number) - 8) . substr($number, -4);
}
		
	function verify_fraud($userID,$amount,$ccn,$mxGateway,$ip,$xip,$city,$state,$postal,$country,$emailID,$bin,$username,$password,$user_agent='',$binName='',$binPhone='',$custPhone='',$requested_type='',$shipAddr='',$shipCity='',$shipRegion='',$shipPostal='',$sessionID='',$accept_language='en-en')
		{
		
		

			//  check if credit card is in block list
			$sql_block = mysql_query("SELECT * FROM sg_maxmindcc WHERE mmCard = '$ccn' AND mmCardStatus = 0 LIMIT 1") or die(mysql_error());
			$cnt_block = mysql_num_rows($sql_block);
			if($cnt_block) return true;		// return its fraud
		
		
			//  check if credit card is in allowed list... if yes then overwrride  risk score
			$sql_allow = mysql_query("SELECT * FROM sg_maxmindcc WHERE mmCard = '$ccn' AND mmCardStatus = 1 LIMIT 1") or die(mysql_error());
			$cnt_allow = mysql_num_rows($sql_allow);
			if($cnt_allow) return false;	 // return its not fraud
			else
			{

				$txnID = "CL_".$userID."_".$amount."_".$mxGateway;

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, "http://162.254.144.191/~cybernet/maxmind/");
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
					'forwardedIP' => $xip,
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
				//print_r($contents);
				$FR = unserialize($contents);
				// insert data
				$ins_d['mxUserID'] = $userID;
				$ins_d['mxAmount'] = $amount;
				$ins_d['mxGateway'] = $mxGateway;
				$ins_d['mxScore'] = $FR['riskScore'];
				if($FR['riskScore'] <= 3) { $mxStatus = 0; }
				else if($FR['riskScore'] > 3 && $FR['riskScore'] <= 70){ $mxStatus = 1; }
				else if($FR['riskScore'] > 70 && $FR['riskScore'] <= 100){ $mxStatus = 2; }
				$ins_d['mxStatus'] = $mxStatus;
				$ins_d['mxRawData'] = $contents;
				$ins_d['ccn'] = $ccn;
				$ins_d['mxInsTime'] = time();
				$mxID = $this->zll->gInsert('maxmind',$ins_d);
				

				$is_fraud = 0;
				if($mxStatus == 1 || $mxStatus == 2)
					{
					
					    // insert into block list
						$sql = mysql_query("SELECT * FROM sg_maxmindcc WHERE mmCard = '$ccn' LIMIT 1") or die(mysql_error());
						$cnt = mysql_num_rows($sql);
						if($cnt == 0)
							{		
								$ins_m['mmUserID'] = $userID;
								$ins_m['mmCard'] = $ccn;
								$ins_m['mmCardStatus'] = 0;
								$ins_m['mmInsTime'] = time();
								$ins_m['mmNote'] = 'Added to blocked cards, due to high risk score of '.$FR['riskScore'];
								$ins_m['mxID'] = $mxID;
								$this->zll->gInsert('maxmindcc',$ins_m);
								$sql_auto = mysql_query("UPDATE  sg_maxmind SET mxNote = 'Added to block list, due to hight risk score' WHERE mxID = '$mxID' LIMIT 1") or die(mysql_error());
							}
						$is_fraud = 1;	

					}
				return $is_fraud;

			}		
				

		}

}