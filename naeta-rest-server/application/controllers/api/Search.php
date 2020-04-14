<?php 

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Search extends REST_Controller
{
	
    public function __construct()
	{
		parent::__construct();
		$this->load->model('Search_model', 'search');
	}

	public function index_get()
	{
		$keyword = $this->get('q');

		if (strlen($keyword) < 1)
		{
			$this->response([
            	'status' => false,
            	'message' => 'Provide an keyword'
        	], REST_Controller::HTTP_BAD_REQUEST);
		}

		$search = $this->search->getSearch($keyword);

		if (!$search) {
			$this->response([
            	'status' => false,
            	'message' => 'Product not found'
        	], REST_Controller::HTTP_NOT_FOUND);
		}
		
		$this->response([
			'status' => true,
			'search' => $search,
		], REST_Controller::HTTP_OK);
	}

}