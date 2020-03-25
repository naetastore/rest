<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Base extends CI_Controller
{

	// public function __construct()
	// {
	// 	parent::__construct();
	// 	is_logedin();
	// }

	public function index()
	{
		$data['title'] = 'Dashboard';
		$this->load->view('templates/header', $data);
		$this->load->view('templates/sidebar', $data);
    	$this->load->view('admin/base');
    	$this->load->view('templates/footer');
	}

}