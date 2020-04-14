<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Example extends CI_Controller
{
    
    private $on_session;
    private $requiredparams;

    public function __construct()
	{
		parent::__construct();
        in_session();
        $this->on_session = $this->db->get_where('users', [ 'username' => $_GET['username'] ])->row_array();
        $this->requiredparams = '?session=' . $_GET['session'] . '&username=' . $_GET['username'];
    }

    public function index()
    {
        $data['title'] = 'Blank Page';
        $data['subtitle'] = '';
        $data['metadescription'] = '';
        $data['metakeyword'] = '';

        $data['user'] = $this->on_session;
        $this->load->view('blankpage', $data);
    }

}