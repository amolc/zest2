<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Cl extends CI_Model {
	function __construct()
		{
				parent::__construct();
		}
	
	function getSlot1($day,$rs,$extra_time,$is_dl)
	{
				$d = explode("/",$day);
				if($is_dl)
					$bt = mktime(0,0,0,$d[0],$d[1],$d[2]) + 3600;
				else
					$bt = mktime(0,0,0,$d[0],$d[1],$d[2]);

				$HTZ = $rs['hangout_timezone'];
				
				for($i=1;$i<=12;$i++)
					{
						$dbf = "web_sessions_".$i;
						$web_session_array[$i] = $rs[$dbf];
					}
				sort($web_session_array);
				
				foreach($web_session_array as $w_row)
					{
						if($w_row) 
							{ 
								$web_session = $w_row;
								$time_from_mid_night = $bt + ($web_session * 3600);
								$time_from_mid_niht_in_gmpt = $time_from_mid_night - $HTZ;
							
								
								$currtime = time();
								
								$time_from_mid_niht_in_gmpt = $time_from_mid_niht_in_gmpt +  $extra_time; // allow them to register until last 15 minute

								if($time_from_mid_niht_in_gmpt > $currtime)
									{
										return $time_from_mid_night;
									}
							 }
					}// all time slot
			$next_day =	date('m/d/Y', strtotime($day .' +1 day'));	
			return $this->getSlot1($next_day,$rs,$extra_time,$is_dl);	
	}
	
	function getSlot2($day,$rs,$extra_time,$is_dl)
	{
				$d = explode("/",$day);
				
				if($is_dl)
					$bt = mktime(0,0,0,$d[0],$d[1],$d[2]) + 3600;
				else
					$bt = mktime(0,0,0,$d[0],$d[1],$d[2]);
					
				
				$HTZ = $rs['hangout_timezone'];
				for($i=1;$i<=12;$i++)
					{
						$dbf = "web_sessions_".$i;
						if($rs[$dbf]) 
							{ 
								$web_session = $rs[$dbf];
								$time_from_mid_night = $bt + ($web_session * 3600);
								$time_from_mid_niht_in_gmpt = $time_from_mid_night - $HTZ;
								$currtime = time();
								$time_from_mid_niht_in_gmpt = $time_from_mid_niht_in_gmpt +  $extra_time;
								
								if($time_from_mid_niht_in_gmpt > $currtime)
									{
										return $time_from_mid_night;
									}
							 }
					}// all time slot
					
			return 0;
	}

	function fullday($d)
	{
		if($d == 'sun') return "Sunday";
		if($d == 'mon') return "Monday";
		if($d == 'tue') return "Tuesday";
		if($d == 'wed') return "Wednesday";
		if($d == 'thu') return "Thrusday";
		if($d == 'fri') return "Friday";
		if($d == 'sat') return "Saturday";
	}

	function getSlot3($rs,$recordSchedule,$addDays,$limit=0,$extra_time,$is_dl)
	{
			if($recordSchedule==2)
					{
						$dd = $rs['record_play_opt_day'];
						$days[] = fullday($dd);
					}
				else
					{
						$dd = $rs['record_play_opt_days'];
						$exp = explode("###",$dd);
						foreach($exp as $E) if($E)$days[] = fullday($E);
					}
				
						$today =  date("l",strtotime("+".$addDays." days"));
						$HTZ = $rs['hangout_timezone'];
						if(in_array($today,$days))
							{
							
								$ds = date("m/d/Y",strtotime("+".$addDays." day"));
								$d = explode("/",$ds);
								if($is_dl)
									$bt = mktime(0,0,0,$d[0],$d[1],$d[2]) + 3600;
								else
									$bt = mktime(0,0,0,$d[0],$d[1],$d[2]);
								
								for($i=1;$i<=12;$i++)
									{
									
										$dbf = "web_sessions_".$i;
										//print_r($rs);
										if($rs[$dbf]) 
											{ 
												
												$web_session = $rs[$dbf];
												$time_from_mid_night = $bt + ($web_session * 3600);
												$time_from_mid_niht_in_gmpt = $time_from_mid_night - $HTZ;
												$currtime = time();
												$time_from_mid_niht_in_gmpt = $time_from_mid_niht_in_gmpt +  $extra_time;
												
												if($time_from_mid_niht_in_gmpt > $currtime)
													{
												
														return $time_from_mid_night;
													}
											 }
									}// all time slot
							}
					
			$newda = 	$addDays + 1;	
			$limit = 	$limit + 1;	
			if($limit < 10) return $this->getSlot3($rs,$recordSchedule,$newda,$limit,$extra_time,$is_dl);
	}

	
	function getClTime($wID)
	{	
		$is_dl = 0;
		$sql = mysql_query("select * from sg_webinar where wID = ".$wID) or die(mysql_error());
		$i = 0;
		while($row = mysql_fetch_array($sql))
			{
				$rs = $row;
			}
	
		$extra_time = $rs['recordYtDuration'] - 900;
		if($extra_time < 0) $extra_time = 0;
		
		if($rs['recordSchedule']==0) // if every day
			{
				$base_time = date("m/d/Y");
				$slot = $this->getSlot1($base_time,$rs,$extra_time,$is_dl);
			}

		if($rs['recordSchedule']==1) // any single day
			{
				$base_time = date("m/d/Y",$rs['record_play_opt_date']);
				$slot = $this->getSlot2($base_time,$rs,$extra_time,$is_dl);
			}


		if($rs['recordSchedule']==2 || $rs['recordSchedule']==3) // any single day
			{
				$base_time = date("m/d/Y");
				$slot = $this->getSlot3($rs,$rs['recordSchedule'],0,0,$extra_time,$is_dl);
			}

		$web_time = $slot;
		
		if($is_dl)
			$web_time_to_show = $slot-3600;  // deduct time to show original time .. if we have added due to daylight saving 
		else
			$web_time_to_show = $slot;
			
		$web_tday = date('l',$slot);
		$web_day = date('d',$slot);
		$web_mon = date('F',$slot);
		$web_t = date('h:i',$slot);
		$rTime = date("m/d/Y h:i:s a",$web_time);
		
		$CUSTOME_FIELDS['rTT'] = $slot;
		$CUSTOME_FIELDS['rTime'] = $rTime;
		return $CUSTOME_FIELDS;
	}


}	
?>