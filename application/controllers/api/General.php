<?php 

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class General extends REST_Controller
{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Global_model', 'global');
	}

	public function index_get()
	{
		if ($this->get('delete')) {
            $this->_delete();
            return;
        }

		$id = $this->get('id');

		if ($id === null) {
			$general = $this->global->getGlobal();
		}
		else
		{
			$general = $this->global->getGlobal($id);
		}

		if (isset($general['name'])) {
			$general['curs'] = 'Rp.';
			$general['start_price'] = number_format($general['start_price']);
			$general['high_price'] = number_format($general['high_price']);
			$general['image'] = base_url('src/img/global/' . $general['image']);
		}else{
			$i = 0;
			foreach ($general as $key) {
				$general[$i]['curs'] = 'Rp.';
				$general[$i]['start_price'] = number_format($key['start_price']);
				$general[$i]['high_price'] = number_format($key['high_price']);
				$general[$i]['image'] = base_url('src/img/global/' . $key['image']);
				$i++;
			}
		}

		if ($general) {
			$this->response([
            	'status' => true,
            	'general' => $general,
        	], REST_Controller::HTTP_OK);
		} else {
			$this->response([
            	'status' => false,
            	'message' => 'id not found',
        	], REST_Controller::HTTP_BAD_REQUEST);
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

        $queryGlobal = "SELECT `globals`.`image` FROM `globals` WHERE `globals`.`id` = $id";
        $global = $this->db->query($queryGlobal)->row_array();

        unlink(FCPATH . 'src/img/global/' . $global['image']);

        $this->db->delete('globals', ['id' => $id]);

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

        $uploadImage = TRUE;
        if (isset($id)) {
            $queryGlobal = "SELECT `globals`.`image` FROM `globals` WHERE `globals`.`id` = $id";
            $global = $this->db->query($queryGlobal)->row_array();

            $image = trim($this->post('image'), base_url());
            if ('img/global/' . $global['image'] === $image) {
                $uploadImage = FALSE;
            }
        }
        if ($uploadImage) {
            $config['upload_path']          = FCPATH . 'src/img/global/';
            $config['allowed_types']        = 'jpg|jpeg|png|svg';
            $config['encrypt_name']         = TRUE;
            
            $this->load->library('upload', $config);
            
            if (!$this->upload->do_upload('image'))
            {
                $this->response([
                    'status' => false,
                    'message' => 'Image failed to uploaded'
                ], REST_Controller::HTTP_BAD_REQUEST);
            }
            else
            {
                if (isset($id)) {
                    unlink(FCPATH . 'src/img/global/' . $global['image']);
                }

                $image_name = $this->upload->data('file_name');
                $this->db->set('image', $image_name);
            }
        }

        $data = [
            'name' => $this->post('name'),
			'description' => $this->post('description'),
		];

		if (isset($id)) {
            $this->db->update('globals', $data, ['id' => $id]);
        }else{
            $this->db->insert('globals', $data);
        }

        if ($this->db->affected_rows() > 0) {
            $global = $this->db->get_where('globals', ['name' => $this->post('name')])->row_array();

            if (isset($id)) {
                $message = [
                    'general' => $global,
                    'message' => 'Update a resource'
                ];
                $code = 200;
            } else {
                $message = [
                    'general' => $global,
                    'message' => 'Added a resource'
                ];
                $code = 201;
            }

            $this->set_response($message, $code);
        }else{
            $this->response([
                'status' => false,
                'message' => 'General failed to uploaded'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
	}

}