<?php 

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Activities extends REST_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Activities_model', 'activities');
	}

	public function index_get()
	{
		/** parameter
		 */ $username = $this->get('username');
		$password = $this->get('password');

		if (is_admin($username, $password)) {
			$activities = $this->activities->getActivities();
			if ($activities) {
				$this->load->helper('date');
				$i=0;
				foreach ($activities as $key) {
					$post_date = $key['created'];
					$now = time();
					$units = 2;
					$activities[$i]['created'] = timespan($post_date, $now, $units);
					$i++;
				}
				$this->response([
			    	'status' 		=> true,
			    	'activities' 	=> $activities
				], REST_Controller::HTTP_OK);
			} else {
				$this->response([
			    	'status' 		=> true,
			    	'message' 	=> 'activities not found'
				], REST_Controller::HTTP_OK);
			}
		} else {
			$this->response([
            	'status' => false,
            	'message' => 'Something went wrong.'
        	], REST_Controller::HTTP_BAD_REQUEST);
		}
	}

}