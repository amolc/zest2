<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class U extends CI_Controller {


	public function u()
	{
		parent::__construct();
		error_reporting(E_ALL);
		
		
	}
	public function index()
	{
		
		$this->load->view('index');
	}
	
	
	

	
}