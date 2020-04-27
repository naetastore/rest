<?php 

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Category extends REST_Controller
{
	
    public function __construct()
	{
		parent::__construct();
		$this->load->model('Category_model', 'category');
	}

	public function index_get()
	{
		if ($this->get('delete')) {
            $this->_delete();
            return;
        }

		$id = $this->get('id');

		if ($id === null) {
			$category = $this->category->getCategory();
		} else {
			$category = $this->category->getCategory($id);
		}

		if ($category) {
			$this->response([
            	'status' => true,
            	'category' => $category,
        	], REST_Controller::HTTP_OK);
		} else {
			$this->response([
            	'status' => false,
            	'message' => 'id not found!'
        	], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	private function _delete()
    {
        $id = (int) $this->get('id');

        if ($id <= 0)
        {
            $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST);
        }

        $this->load->helper('client');

        $user = basic_auth();

        if ($user['role_id'] == 2) {
            $this->response([
                'status' => false,
                'message' => 'Something went wrong'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $this->db->delete('categories', ['id' => $id]);

        $message = [
            'id' => $id,
            'message' => 'Deleted the resource'
        ];

        $this->set_response($message, REST_Controller::HTTP_OK);
    }

	public function index_post()
	{
		$this->load->helper('client');

        $user = basic_auth();

        if ($user['role_id'] == 2) {
            $this->response([
                'status' => false,
                'message' => 'Something went wrong'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $id = $this->post('id');

        $data = [
            'name' => $this->post('name'),
			'description' => $this->post('description'),
			'global_id' => $this->post('global_id')
		];

		if (isset($id)) {
            $this->db->update('categories', $data, ['id' => $id]);
        }else{
            $this->db->insert('categories', $data);
        }

        if ($this->db->affected_rows() > 0) {
            $category = $this->db->get_where('categories', ['name' => $this->post('name')])->row_array();

            if (isset($id)) {
                $message = [
                    'category' => $category,
                    'message' => 'Update a resource'
                ];
                $code = 200;
            } else {
                $message = [
                    'category' => $category,
                    'message' => 'Added a resource'
                ];
                $code = 201;
            }

            $this->set_response($message, $code);
        }else{
            $this->response([
                'status' => false,
                'message' => 'Category failed to uploaded'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
	}

}