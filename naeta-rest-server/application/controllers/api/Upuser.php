<?php 

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Upuser extends REST_Controller
{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('User_model', 'user');
	}

	public function index_post()
	{
		// parameter
		$username = $this->post('username');
		$password = $this->post('password');
		
		$name = $this->post('name');
		$address = $this->post('address');
		$phonenumber = $this->post('phonenumber');
		$repassword = $this->post('repassword');

		$on_session = get_user($username, $password);
		if (!$on_session) $this->_failedAPIResponse('Something went wrong!');

		$data=[];
		if (isset($name) && $this->_namevalidity($name)) $data['name'] = $name;
		if (isset($address)) $data['address'] = $address;
		if (isset($phonenumber) && $this->_phonevalidity($phonenumber)) $data['phonenumber'] = $phonenumber;
		if (isset($repassword) && $this->_passwordvalidity($repassword))
		{
			$data['password'] = password_hash($repassword, PASSWORD_DEFAULT);
		}

		if (!is_array($data) OR count($data) === 0) $this->_failedAPIResponse('Opps.. Sorry no data to update...');

		$affected_rows = $this->user->updateUser($data, $on_session);
		if ($affected_rows < 1) $this->_successAPIResponse('Everything is up to date.', $data = rewrapp($on_session));
		
		$user = $this->db->get_where('users', [ 'username' => $username ])->row_array();
		$this->_successAPIResponse('User data has been changed!', $data = rewrapp($user));
	}

	private function _namevalidity($name)
	{
		if (isset(explode(' ', $name)[1]) && strlen(explode(' ', $name)[1]) > 0)
		{
			return TRUE;
		}else{
			$this->_failedAPIResponse('Naeta Store memeriksa agar mereka menggunakan nama lengkap.');
		}
	}

	private function _phonevalidity($phone)
	{
		if ((int) $phone > 0 && strlen($phone) < 11)
		{
			$this->_failedAPIResponse('Kesalahan: Nomor telepon Anda setidaknya 12 karakter atau lebih.');
		}else{
			return TRUE;
		}
	}

	private function _passwordvalidity($pass)
	{
		if ( strlen($pass) < 5)
		{
			$this->_failedAPIResponse('Kata sandi terlalu pendek.');
		}else{
			return TRUE;
		}
	}

	private function _successAPIResponse($msg, $data)
	{
		$this->response([
        	'status' => true,
        	'message' => $msg,
        	'user' => $data
    	], REST_Controller::HTTP_OK);
	}

	private function _failedAPIResponse($msg)
	{
		$this->response([
        	'status' => false,
        	'message' => $msg
    	], REST_Controller::HTTP_BAD_REQUEST);
	}
	
}