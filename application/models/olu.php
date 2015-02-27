<?php
class Olu extends CI_Model {

	var $timeout = 600;
	var $count = 0;
	var $error;
	var $i = 0;
	function __construct()
		{
				parent::__construct();
		}
	function usersOnline ($username=NULL) {
		$this->timestamp = time();
		$this->ip = $this->ipCheck();
		$this->new_user($username);
		$this->delete_user();
		return $this->count_users();
		
	}
	
	function ipCheck() {
	/*
	This function will try to find out if user is coming behind proxy server. Why is this important?
	If you have high traffic web site, it might happen that you receive lot of traffic
	from the same proxy server (like AOL). In that case, the script would count them all as 1 user.
	This function tryes to get real IP address.
	Note that getenv() function doesn't work when PHP is running as ISAPI module
	*/
		if (getenv('HTTP_CLIENT_IP')) {
			$ip = getenv('HTTP_CLIENT_IP');
		}
		elseif (getenv('HTTP_X_FORWARDED_FOR')) {
			$ip = getenv('HTTP_X_FORWARDED_FOR');
		}
		elseif (getenv('HTTP_X_FORWARDED')) {
			$ip = getenv('HTTP_X_FORWARDED');
		}
		elseif (getenv('HTTP_FORWARDED_FOR')) {
			$ip = getenv('HTTP_FORWARDED_FOR');
		}
		elseif (getenv('HTTP_FORWARDED')) {
			$ip = getenv('HTTP_FORWARDED');
		}
		else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
	
	function new_user($username=NULL) 
	{
			$useronline = $this->db->dbprefix('useronline');
			$insert = mysql_query ("INSERT INTO $useronline(timestamp, ip, username) VALUES ('$this->timestamp', '$this->ip', '$username')");
	}
	
	function delete_user() 
	{
			$useronline = $this->db->dbprefix('useronline');
			$delete = mysql_query ("DELETE FROM $useronline WHERE timestamp < ($this->timestamp - $this->timeout)");
			
	}
	
	function show_users() 
	{
			$useronline = $this->db->dbprefix('useronline');
			$query=$this->db->query("SELECT DISTINCT ip,timestamp,username FROM $useronline group by IP order by timestamp desc  ");
			return $query->result_array();				
	}

	function count_users() 
	{
			$useronline = $this->db->dbprefix('useronline');
			$count = mysql_num_rows( mysql_query("SELECT DISTINCT ip FROM $useronline "));
			return $count;
	}

}

?>