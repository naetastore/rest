<?php 

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class User extends REST_Controller
{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('User_model', 'user');
	}

	public function index_get()
	{
		$username = $this->get('username');
		$password = $this->get('password');

		$user = $this->user->getUser($username);

		if (!$user) $this->_failedAPIResponse('This user is not registered!');
		if (!password_verify($password, $user['password'])) $this->_failedAPIResponse('Wrong password! please try again.');

		$this->_successAPIResponse($msg = null, $data = rewrapp($user));
	}

	public function index_post()
	{
		$username = $this->post('username');
		$password = $this->post('password');

		if (isset($username) && !isset($password) | strlen($username) > 0 && strlen($password) < 1)
		{
			if ($this->_duplicatecheck($username)) $this->_successAPIResponse('username is ready to insert.', $data = null);
		}

		if (isset($username) && isset($password) | strlen($username) > 0 && strlen($password) > 0)
		{
			if ($this->_passwordcheck($password)) $this->_register();
		}
	}

	private function _duplicatecheck($username)
	{
		$user = $this->db->get_where('users', [ 'username' => $username ]);
		if ($user->num_rows() > 0)
		{
			$this->_failedAPIResponse('This username has already registered!');
		}else{
			return TRUE;
		}
	}

	private function _passwordcheck($pass)
	{
		if (strlen($pass) < 5)
		{
			$this->_failedAPIResponse('Password too short!');
		}else{
			return TRUE;
		}
	}

	private function _register()
	{
		$username = $this->post('username');
		$password = $this->post('password');

		$this->_duplicatecheck($username);
		$this->_passwordcheck($password);

		$default_role = get_api_setting('default_role');
		$data = [
			'username' 	=> htmlspecialchars($username, TRUE),
			'password' 	=> password_hash($password, PASSWORD_DEFAULT),
			'role_id' 	=> $default_role,
			'entry'		=> create_entry(),
			'created' 	=> time()
		];

		if ($this->user->createUser($data) < 1) $this->_failedAPIResponse('Failed to created new data!');

		activities('create', 'users');
		$user = $this->db->get_where('users', [ 'username' => $username ])->row_array();

		$data = [
			'username' 			=> $user['username'],
			'role'	 			=> $user['role_id'],
			'created'	 		=> date('d F Y', $user['created'])
		];

		$this->_createdAPIResponse('Congratulation! you account has been created.', $data);
	}

	private function _createdAPIResponse($msg, $data)
	{
		$response = [ 'status' => true ];

		if (isset($msg)) $response['message'] = $msg;
		if (isset($data)) $response['user'] = $data;

		$this->response($response, REST_Controller::HTTP_CREATED);
	}

	private function _successAPIResponse($msg, $data)
	{
		$response = [ 'status' => true ];

		if (isset($msg)) $response['message'] = $msg;
		if (isset($data)) $response['user'] = $data;

		$this->response($response, REST_Controller::HTTP_OK);
	}

	private function _failedAPIResponse($msg)
	{
		$this->response([
        	'status' => false,
        	'message' => $msg
    	], REST_Controller::HTTP_BAD_REQUEST);
	}
	
}