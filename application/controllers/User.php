<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller
{
    
    private $requiredparams;
    private $on_session;

    public function __construct()
	{
		parent::__construct();
        in_session();
        $this->on_session = $this->db->get_where('users', [ 'username' => $_GET['username'] ])->row_array();
		$this->requiredparams = '?session=' . $_GET['session'] . '&username=' . $_GET['username'];
    }

    public function index()
    {
        $this->profile();
    }

    public function profile()
    {
        $data['title'] = 'Profile';
        $data['subtitle'] = '';
        $data['metadescription'] = '';
        $data['metakeyword'] = '';

        $data['user'] = $this->_parse_user($this->on_session);
        if (isset($_GET['uid'])) {
            $data['user'] = $this->_parse_user($this->db->get_where('users', [ 'id' => $_GET['uid'] ])->row_array());
        }
        // var_dump($data['user']);die;
        $data['contacts'] = $this->db->get_where('contacts', [ 'user_id' => $data['user']['id'] ])->result_array();
        $this->load->view('user/profile', $data);
    }

    private function _parse_user($user)
    {
        if (!isset($user['name'])) $user['name'] = $user['username'];
        if (!isset($user['address'])) $user['address'] = 'unset';
        if (!isset($user['phone'])) $user['phone'] = 'unset';
        if (strlen($user['avatar']) < 1) {
            $user['avatar'] = base_url('src/img/avatar/default.svg');
        }else{
            $user['avatar'] = base_url('src/img/avatar/' . $user['avatar']);
        }
        return $user;
    }

}