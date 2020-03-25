<?php 

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Banner extends REST_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Banner_model', 'banner');
	}

	public function index_get()
	{
		/* parameter
		*/ $id = $this->get('id');

		if ($id === null) {
			$banner = $this->banner->getBanner();
		} else {
			$banner = $this->banner->getBanner($id);
		}

		if ($banner) {
			$this->response([
            	'status' => true,
            	'banner' => $banner,
        	], REST_Controller::HTTP_OK);
		} else {
			$this->response([
            	'status' => false,
            	'message' => 'id not found!'
        	], REST_Controller::HTTP_NOT_FOUND);
		}
	}
	
}