<?php 

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Tweet extends REST_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Tweet_model', 'tweet');
	}

	public function index_post()
	{
		$data = [
			'description'   => $this->post('description'),
			'smile'   		=> $this->post('smile'),
			'created'		=> time()
		];

		if ($this->tweet->createTweet($data) > 0) {
			
			if ($data['smile'] == 1) {
				$reaction = 'smile';
			} else {
				$reaction = 'sad';
			}
			activities('create', 'tweets', 'Someone create a tweets with ' . $reaction . ' reaction.');
			
			$this->response([
            	'status' => true,
            	'message' => 'new data has been created.'
        	], REST_Controller::HTTP_CREATED);
		} else {
			$this->response([
            	'status' => false,
            	'message' => 'failed to created new data!'
        	], REST_Controller::HTTP_BAD_REQUEST);
		}
	}

}