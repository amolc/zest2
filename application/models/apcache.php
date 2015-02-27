<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Apcache extends CI_Model {
	function __construct()
		{
				parent::__construct();
		}
			
	function is_enable(){ return false; if(in_array('apc', get_loaded_extensions())) {return true;} else {return false;}}	
	function delete($var_name){if($this->is_enable()){apc_delete($var_name);}}
	function delete_all(){if($this->is_enable()){apc_clear_cache();}}

	function sg_webpages($id,$ttl)
		{
			$where['id'] = $id;
			if($this->is_enable()) 
				{
					$check = apc_fetch('IC_page_'.$id);	
					if($check){ return $check;} 
					else  {			
						    $check = $this->zll->gSelectWhere('webpages',$where);
							apc_add('IC_page_'.$id, $check, $ttl);		
							return $check;				 
						  }
				}
			else
				{
						    $check = $this->zll->gSelectWhere('webpages',$where);
							return $check;				 
				}
		}	
	

	function site_config($ttl)
		{
		$c_string = "IC_SiteConfig";
		if($this->is_enable()) 
			{
				$check = apc_fetch($c_string);	
				if($check){ return $check;} 
				else  {			
						$query = $this->db->query("SELECT * FROM sg_siteconfiguration");
						$check =  $query->result_array();
						apc_add($c_string, $check, $ttl);		
						return $check;				 
					  }
			}
		else
			{
						$query = $this->db->query("SELECT * FROM sg_siteconfiguration");
						$check =  $query->result_array();
						return $check;				 
			}
		}			

	

	function user_cache_data_username($username,$ttl)
		{
		$c_string = "IC_user_cache_".$username;
		if($this->is_enable()) 
			{
				$check = apc_fetch($c_string);	
				if($check){ return $check;} 
				else  {			
						$query = $this->db->query("SELECT * FROM sg_users WHERE username = '$username'");
						$check =  $query->result_array();
						apc_add($c_string, $check, $ttl);		
						return $check;				 
					  }
			}
		else
			{
						$query = $this->db->query("SELECT * FROM sg_users WHERE username = '$username'");
						$check =  $query->result_array();
						return $check;				 
			}
		}			



	function getFounderArray($ttl)
		{
			$c_string = "IC_getFounderArray";
			if($this->is_enable()) 
				{
					$check = apc_fetch($c_string);	
					if($check){ return $check;} 
					else  {			
							$query = $this->db->query("SELECT userID,username FROM sg_users WHERE isfounder = 1");
							$check =  $query->result_array();
							apc_add($c_string, $check, $ttl);		
							return $check;				 
						  }
				}
			else
				{
							$query = $this->db->query("SELECT userID,username FROM sg_users WHERE isfounder = 1");
							$check =  $query->result_array();
							return $check;				 
				}
		}

	function getUserRefArray($userID,$ttl)
		{
		$c_string = "IC_getUserRefArray_".$userID;
		if($this->is_enable()) 
			{
				$check = apc_fetch($c_string);	
				if($check){ return $check;} 
				else  {			
						$query = $this->db->query("SELECT userID,username,isfounder FROM sg_users WHERE refID = '$userID'");
						$check =  $query->result_array();
						apc_add($c_string, $check, $ttl);		
						return $check;				 
					  }
			}
		else
			{
						$query = $this->db->query("SELECT userID,username,isfounder FROM sg_users WHERE refID = '$userID'");
						$check =  $query->result_array();
						return $check;				 
			}
		}			


	function user_cache_data($userID,$ttl)
		{
		$c_string = "IC_user_cache_".$userID;
		if($this->is_enable()) 
			{
				$check = apc_fetch($c_string);	
				if($check){ return $check;} 
				else  {			
						$query = $this->db->query("SELECT * FROM sg_users WHERE userID = '$userID'");
						$check =  $query->result_array();
						apc_add($c_string, $check, $ttl);		
						return $check;				 
					  }
			}
		else
			{
						$query = $this->db->query("SELECT * FROM sg_users WHERE userID = '$userID'");
						$check =  $query->result_array();
						return $check;				 
			}
		}			


	function getSuspendedUsers($refUsername,$ttl)
		{
		$c_string = "IC_suspended_users";
		if($this->is_enable()) 
			{
				$check = apc_fetch($c_string);	
				if($check){ return $check;} 
				else  {			
						$query = $this->db->query("SELECT * FROM sg_suspenduser");
						$check =  $query->result_array();
						apc_add($c_string, $check, $ttl);		
						return $check;				 
					  }
			}
		else
			{
						$query = $this->db->query("SELECT * FROM sg_suspenduser");
						$check =  $query->result_array();
						return $check;				 
			}
		}			

	function latest_news($ttl)
		{
		$c_string = "IC_latest_news";
		if($this->is_enable()) 
			{
				$check = apc_fetch($c_string);	
				if($check){ return $check;} 
				else  {			
						$query = $this->db->query("select * from sg_news limit 5");
						$check =  $query->result_array();
						apc_add($c_string, $check, $ttl);		
						return $check;				 
					  }
			}
		else
			{
						$query = $this->db->query("select * from sg_news limit 5");
						$check =  $query->result_array();
						return $check;				 
			}
		}			


	function get_active_testi($ttl)
		{
		$c_string = "IC_user_cache_".$userID;
		if($this->is_enable()) 
			{
				$check = apc_fetch($c_string);	
				if($check){ return $check;} 
				else  {			
						$query = $this->db->query("SELECT * FROM sg_testimonials WHERE tStatus = '1'");
						$check =  $query->result_array();
						apc_add($c_string, $check, $ttl);		
						return $check;				 
					  }
			}
		else
			{
						$query = $this->db->query("SELECT * FROM sg_testimonials WHERE tStatus = '1'");
						$check =  $query->result_array();
						return $check;				 
			}
		}			


	function getActiveMatrix($userID,$matrix,$ttl)
		{
		$c_string = "IC_User_Active_Matrix_".$userID."_".$matrix;
		if($this->is_enable()) 
			{
				$check = apc_fetch($c_string);	
				if($check){ return $check;} 
				else  {			
						$query = $this->db->query("SELECT * FROM sg_tree100 WHERE userID = '$userID' and mtype = '$matrix' and paid = '0' order by treeID desc limit 1");
						$check =  $query->result_array();
						apc_add($c_string, $check, $ttl);		
						return $check;				 
					  }
			}
		else
			{
						$query = $this->db->query("SELECT * FROM sg_tree100 WHERE userID = '$userID' and mtype = '$matrix' and paid = '0' order by treeID desc limit 1");
						$check =  $query->result_array();
						return $check;				 
			}
		}			





	function getMainCategory($ttl)
		{
		if($this->is_enable()) 
			{
				$check = apc_fetch('IC_main_category');	
				if($check){ return $check;} 
				else  {			
						$query = $this->db->query("SELECT * FROM sg_category WHERE parentID = 0 order by corder asc ");
						$check =  $query->result_array();
						apc_add("IC_main_category", $check, $ttl);		
						return $check;				 
					  }
			}
		else
			{
						$query = $this->db->query("SELECT * FROM sg_category WHERE parentID = 0 order by corder asc ");
						$check =  $query->result_array();
						return $check;				 
			}
		}			


	function getSubCategory($catID,$ttl)
		{
		if($this->is_enable()) 
			{
				$check = apc_fetch('IC_sub_main_category_'.$catID);	
				if($check){ return $check;} 
				else  {			
						$query = $this->db->query("SELECT * FROM sg_category WHERE parentID = '$catID' order by corder asc ");
						$check =  $query->result_array();
						apc_add('IC_sub_main_category_'.$catID, $check, $ttl);		
						return $check;				 
					  }
			}
		else
			{
						$query = $this->db->query("SELECT * FROM sg_category WHERE parentID = '$catID' order by corder asc ");
						$check =  $query->result_array();
						return $check;				 
			}
		}			



	function getArticleList_1($catID,$ttl)
		{
		if($this->is_enable()) 
			{
				$check = apc_fetch("IC_left_menu_article_".$catID);	
				if($check){ return $check;} 
				else  {			
						$query = $this->db->query("SELECT * FROM sg_article WHERE category=subcategory and category = '$catID' order by aorder asc");
						$check =  $query->result_array();
						apc_add("IC_left_menu_article_".$catID, $check, $ttl);		
						return $check;				 
					  }
			}
		else
			{
						$query = $this->db->query("SELECT * FROM sg_article WHERE category=subcategory and category = '$catID' order by aorder asc");
						$check =  $query->result_array();
						return $check;				 
			}
		}			

	function getArticleList_2($catID,$ttl)
		{
		if($this->is_enable()) 
			{
				$check = apc_fetch("IC_left_submenu_article_".$catID);	
				if($check){ return $check;} 
				else  {			
						$query = $this->db->query("SELECT * FROM sg_article WHERE subcategory = '$catID' order by aorder asc");
						$check =  $query->result_array();
						apc_add("IC_left_submenu_article_".$catID, $check, $ttl);		
						return $check;				 
					  }
			}
		else
			{
						$query = $this->db->query("SELECT * FROM sg_article WHERE subcategory = '$catID' order by aorder asc");
						$check =  $query->result_array();
						return $check;				 
			}
		}			


	function sg_users($id,$ttl)
		{
			$where['userID'] = $id;
			if($this->is_enable()) 
				{
					$check = apc_fetch('IC_users_'.$id);	
					if($check){ return $check;} 
					else  {			
						    $check = $this->zll->gSelectWhere('users',$where);
							apc_add('IC_users_'.$id, $check, $ttl);		
							return $check;				 
						  }
				}
			else
				{
						    $check = $this->zll->gSelectWhere('users',$where);
							return $check;				 
				}
		}		


	function getUsername($id,$ttl)
		{
			$where['userID'] = $id;
			if($this->is_enable()) 
				{
					$check = apc_fetch('IC_USERNAME_'.$id);	
					if($check){ return $check;} 
					else  {			
						    $check = $this->zll->getUsername($id);
							apc_add('IC_USERNAME_'.$id, $check, $ttl);		
							return $check;				 
						  }
				}
			else
				{
						    $check = $this->zll->getUsername($id);
							return $check;				 
				}
		}		




	function countReferredUser($id,$ttl)
		{
			$where['userID'] = $id;
			if($this->is_enable()) 
				{
					$check = apc_fetch('IC_referred_users_'.$id);	
					if($check){ return $check;} 
					else  {			
						    $check = $this->zll->countRefUsers($id);
							apc_add('IC_referred_users_'.$id, $check, $ttl);		
							return $check;				 
						  }
				}
			else
				{
						    $check = $this->zll->countRefUsers($id);
							return $check;				 
				}
		}		

	function countSubscribers($id,$ttl)
		{
			$where['userID'] = $id;
			if($this->is_enable()) 
				{
					$check = apc_fetch('IC_total_subscribers_'.$id);	
					if($check){ return $check;} 
					else  {			
						    $check = $this->zll->countRefUsers($id);
							apc_add('IC_referred_users_'.$id, $check, $ttl);		
							return $check;				 
						  }
				}
			else
				{
						    $check = $this->zll->countRefUsers($id);
							return $check;				 
				}
		}		


	function countAutoForms($id,$ttl)
		{
			$wh['userID'] = $id;
			if($this->is_enable()) 
				{
					$check = apc_fetch('IC_count_all_forms_'.$id);	
					if($check){ return $check;} 
					else  {			
						    $check = $this->zll->gCountAll('auto_forms',$wh);
							apc_add('IC_count_all_forms_'.$id, $check, $ttl);		
							return $check;				 
						  }
				}
			else
				{
						    $check = $this->zll->gCountAll('auto_forms',$wh);
							return $check;				 
				}
		}		


	function countAutoCampaign($id,$ttl)
		{
			$wh['userID'] = $id;
			if($this->is_enable()) 
				{
					$check = apc_fetch('IC_count_all_campaign_'.$id);	
					if($check){ return $check;} 
					else  {			
						    $check = $this->zll->gCountAll('auto_campain',$wh);
							apc_add('IC_count_all_campaign_'.$id, $check, $ttl);		
							return $check;				 
						  }
				}
			else
				{
						    $check = $this->zll->gCountAll('auto_campain',$wh);
							return $check;				 
				}
		}		

	function countTotalSubscriber($id,$ttl)
		{
			$wh['userID'] = $id;
			if($this->is_enable()) 
				{
					$check = apc_fetch('IC_count_all_campaign_'.$id);	
					if($check){ return $check;} 
					else  {			
						    $check = $this->zll->gCountAll('auto_subscrribers',$wh);
							apc_add('IC_count_all_campaign_'.$id, $check, $ttl);		
							return $check;				 
						  }
				}
			else
				{
						    $check = $this->zll->gCountAll('auto_subscrribers',$wh);
							return $check;				 
				}
		}		


	function getCampaign($id,$ttl)
		{
			if($this->is_enable()) 
				{
					$check = apc_fetch('IC_all_campaign_names_'.$id);	
					if($check){ return $check;} 
					else  {			
						    $check = $this->zll->getCampaignNames($id);
							apc_add('IC_all_campaign_names_'.$id, $check, $ttl);		
							return $check;				 
						  }
				}
			else
				{
						    $check = $this->zll->getCampaignNames($id);
							return $check;				 
				}
		}		

	function getForms($id,$ttl)
		{
			if($this->is_enable()) 
				{
					$check = apc_fetch('IC_all_form_names_'.$id);	
					if($check){ return $check;} 
					else  {			
						    $check = $this->zll->getFormNames($id);
							apc_add('IC_all_form_names_'.$id, $check, $ttl);		
							return $check;				 
						  }
				}
			else
				{
						    $check = $this->zll->getFormNames($id);
							return $check;				 
				}
		}		


	function ajaxGraph($xd,$exd,$pageID,$ccvar,$ttl=600)
		{
		$userID = $this->session->userdata('userID');
		$start = mktime(23,59,59,date('m',strtotime("$xd day")),date('d',strtotime("$xd day")),date('Y',strtotime("$xd day")));
		$end = mktime(0,0,0,date('m',strtotime("$exd day")),date('d',strtotime("$exd day")),date('Y',strtotime("$exd day")));
		$extra_parameter = '';
		if($pageID)
			{	
				$extra_parameter = " and  pageID = '$pageID'";
			}
		$userID = $this->session->userdata('userID');
		if($this->is_enable()) 
			{
				$check = apc_fetch($ccvar);	
				if($check){ return $check;} 
				else  {			
						$query = $this->db->query("select view,optin,insertTime from sg_page_report as PR where insertTime between '$end' and '$start' and PR.userID = '$userID' $extra_parameter ");
						$check =  $query->result_array();
						apc_add($ccvar, $check, $ttl);		
						return $check;				 
					  }
			}
		else
			{
						$query = $this->db->query("select view,optin,insertTime from sg_page_report as PR where insertTime between '$end' and '$start' and PR.userID = '$userID' $extra_parameter  ");
						$check =  $query->result_array();
						return $check;				 
			}
		}		

	function countVerifiedSubscribers($formID,$ttl)
		{
			$userID = $this->session->userdata('userID');
			if($this->is_enable()) 
				{
					$check = apc_fetch('IC_varified_subscribers_'.$formID."_".$userID);	
					if($check){ return $check;} 
					else  {			
						    $check = $this->zll->countVerifiedSubscribers($formID);
							apc_add('IC_varified_subscribers_'.$formID."_".$userID, $check, $ttl);		
							return $check;				 
						  }
				}
			else
				{
						    $check = $this->zll->countVerifiedSubscribers($formID);
							return $check;				 
				}
		}		

	function countUnSubscribers($formID,$ttl)
		{
			$userID = $this->session->userdata('userID');
			if($this->is_enable()) 
				{
					$check = apc_fetch('IC_un_subscribers_'.$formID."_".$userID);	
					if($check){ return $check;} 
					else  {			
						    $check = $this->zll->countUnSubscribers($formID);
							apc_add('IC_varified_subscribers_'.$formID."_".$userID, $check, $ttl);		
							return $check;				 
						  }
				}
			else
				{
						    $check = $this->zll->countUnSubscribers($formID);
							return $check;				 
				}
		}		

	function countAllSub($formID,$ttl)
		{
			$userID = $this->session->userdata('userID');
			if($this->is_enable()) 
				{
					$check = apc_fetch('IC_all_subscribers_'.$formID."_".$userID);	
					if($check){ return $check;} 
					else  {			
						    $check = $this->zll->countAllSub($formID);
							apc_add('IC_all_subscribers_'.$formID."_".$userID, $check, $ttl);		
							return $check;				 
						  }
				}
			else
				{
						    $check = $this->zll->countAllSub($formID);
							return $check;				 
				}
		}		

	function countFaceBookSub($formID,$ttl)
		{
			$userID = $this->session->userdata('userID');
			if($this->is_enable()) 
				{
					$check = apc_fetch('IC_facebook_subscribers_'.$formID."_".$userID);	
					if($check){ return $check;} 
					else  {			
						    $check = $this->zll->countFaceBookSub($formID);
							apc_add('IC_facebook_subscribers_'.$formID."_".$userID, $check, $ttl);		
							return $check;				 
						  }
				}
			else
				{
						    $check = $this->zll->countFaceBookSub($formID);
							return $check;				 
				}
		}		


	function countBounces($formID,$ttl)
		{
			$userID = $this->session->userdata('userID');
			if($this->is_enable()) 
				{
					$check = apc_fetch('IC_bounce_subscribers_'.$formID."_".$userID);	
					if($check){ return $check;} 
					else  {			
						    $check = $this->zll->countBounces($formID);
							apc_add('IC_bounce_subscribers_'.$formID."_".$userID, $check, $ttl);		
							return $check;				 
						  }
				}
			else
				{
						    $check = $this->zll->countBounces($formID);
							return $check;				 
				}
		}		


	function getSingleFormData($formID,$ttl)
		{
			$whx['formID'] = $formID;
			$userID = $this->session->userdata('userID');
			if($this->is_enable()) 
				{
					$check = apc_fetch('IC_single_form_details_'.$formID);	
					if($check){ return $check;} 
					else  {			
						    $check = $this->zll->gSelectWhere('auto_forms',$whx);
							apc_add('IC_single_form_details_'.$formID, $check, $ttl);		
							return $check;				 
						  }
				}
			else
				{
						    $check = $this->zll->gSelectWhere('auto_forms',$whx);
							return $check;				 
				}
		}		




	function getLatest10Broadcast($ttl,$lang='spa')
		{
			$c_string = "IC_Latest10Broadcast_".$lang;
			if($this->is_enable()) 
				{
					$check = apc_fetch($c_string);	
					if($check){ return $check;} 
					else  {			
							$query = $this->db->query("SELECT * FROM `sg_newsletters` WHERE sendLanguage = '$lang' ORDER BY `ID` DESC
LIMIT 40");
							$check =  $query->result_array();
							apc_add($c_string, $check, $ttl);		
							return $check;				 
						  }
				}
			else
				{
							$query = $this->db->query("SELECT * FROM `sg_newsletters` WHERE sendLanguage = '$lang'  ORDER BY `ID` DESC
LIMIT 40");
							$check =  $query->result_array();
							return $check;				 
				}
		}


	function sg_leader_board($ttl)
		{
			$c_string = "IC_user_leader_cache";
			if($this->is_enable()) 
				{
					$check = apc_fetch($c_string);	
					if($check){ return $check;} 
					else  {			
						   	$query = $this->db->query("SELECT * FROM sg_users ORDER BY `userID` DESC LIMIT 5");
							$check =  $query->result_array();
							apc_add($c_string, $check, $ttl);		
							return $check;				 
						  }
				}
			else
				{
						$query = $this->db->query("SELECT * FROM sg_users ORDER BY `userID` DESC LIMIT 5");
						$check =  $query->result_array();
						return $check;				 
				}
		}




}	
?>