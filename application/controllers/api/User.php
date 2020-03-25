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

		if (strlen($username) < 1) {
			$this->response([
            	'status' => false,
            	'message' => 'Provide an username'
        	], REST_Controller::HTTP_BAD_REQUEST);
		}

		if (strlen($password) < 1) {
			$this->response([
            	'status' => false,
            	'message' => 'Provide an password'
        	], REST_Controller::HTTP_BAD_REQUEST);
		}

		$user = $this->user->getUser($username);

		if ($user) {
			if (password_verify($password, $user['password'])) {
				$secondaryPhone = $this->db->get_where('contacts', ['user_id' => $user['id']])->result_array();
				$userdata = [
					'name' 				=> $user['name'],
	            	'userName' 			=> $user['username'],
	            	'roleId' 			=> $user['role_id'],
	            	'primaryPhone' 		=> $user['phonenumber'],
	            	'secondaryPhone' 	=> $secondaryPhone,
	            	'address' 			=> $user['address'],
	            	'avatar'			=> $user['avatar'],
	            	'dateCreated' 		=> date('d F Y', $user['created'])
				];
				$this->response([
	            	'status' => true,
	            	'user' => true,
	            	'data' => $userdata
	        	], REST_Controller::HTTP_OK);
			} else {
				$this->response([
	            	'status' => false,
	            	'message' => 'Wrong password!'
        		], REST_Controller::HTTP_BAD_REQUEST);
			}
		} else {
			$this->response([
            	'status' => false,
            	'message' => 'Username is not registered!'
        	], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	public function index_post()
	{
		$user = $this->db->get_where('users', ['username' => $this->post('username')])->num_rows();
		
		if ($user > 0) {
			$this->response([
            	'status' => false,
            	'message' => 'This username has already registered!'
        	], REST_Controller::HTTP_BAD_REQUEST);
		}
		else
		{
			if (strlen($this->post('password')) < 5) {
				$this->response([
	            	'status' => false,
	            	'message' => 'Password too short!'
	        	], REST_Controller::HTTP_BAD_REQUEST);
			} else {
				$data = [
					'username' 	=> htmlspecialchars($this->post('username'), TRUE),
					'password' 	=> password_hash($this->post('password'), PASSWORD_DEFAULT),
					'avatar'	=> 'default_avatar.svg',
					'role_id' 	=> 2,
					'entry'		=> create_entry(),
					'created' 	=> time()
				];

				if ($this->user->createUser($data) > 0) {
					activities('create', 'users');
					$this->response([
		            	'status' => true,
		            	'message' => 'Congratulation! your account has been created.'
		        	], REST_Controller::HTTP_CREATED);
				} else {
					$this->response([
		            	'status' => false,
		            	'message' => 'Failed to created new data!'
		        	], REST_Controller::HTTP_BAD_REQUEST);
				}
			}
		}
	}

	public function index_put()
	{
		$userdata = [
			'name' => $this->put('name'),
			'address' => $this->put('address'),
			'phonenumber' => $this->put('phonenumber')
		];

		$affected = $this->user->updateUser($userdata, $this->put('username'));
		
		if ($affected){
			// activities('update', 'users');
			$this->response([
            	'status' => true,
            	'message' => 'User data has been changed!'
        	], REST_Controller::HTTP_OK);
		} else {
			$this->response([
            	'status' => true,
            	'message' => 'Everything is up to date.'
        	], REST_Controller::HTTP_OK);
		}
	}
	
}