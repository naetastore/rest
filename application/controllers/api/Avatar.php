<?php 

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Avatar extends REST_Controller
{
	public function index_post()
	{
		// parameter
		$username = $this->post('username');
		$password = $this->post('password');
		$image = $_FILES['image'];

		$on_session = get_user($username, $password);
		if ($on_session) {
			if (!$image) {
				$this->_failedAPIResponse('wrong!');
			}

			$image = $_FILES['image']['name'];
			$config['upload_path']          = FCPATH . 'assets/img/avatar/';
            $config['allowed_types']        = 'jpg|jpeg|png|svg|mp4|mp3';
            $config['encrypt_name']        	= TRUE;

            $this->load->library('upload', $config);
            if (!$this->upload->do_upload('image'))
            {
                $this->_failedAPIResponse('failed to upload image!');
            }
            else
            {
            	$image_name = $this->upload->data('file_name');
            	$image_ext  = $this->upload->data('file_ext');
            	$image_type = $this->upload->data('file_type');
            	$image_size = $this->upload->data('file_size');
            	$image_path = base_url('assets/img/avatar/' . $on_session['avatar']);
            	
            	$ext = ['.mp4', '.mp3'];
            	if (!in_array($image_ext, $ext)) {
	            	if ($on_session['avatar'] !== 'default_avatar.svg') {
						unlink(FCPATH . 'assets/img/avatar/' . $on_session['avatar']);
					}

	            	$this->db->update('users', ['avatar' => $image_name], [ 'id' => $on_session['id'] ]);
	            	$image_path = base_url('assets/img/avatar/' . $image_name);
            	}

            	$img_properties = [
            		'name' 	=> $image_name,
            		'type'	=> $image_type,
            		'path'	=> $image_path,
            		'size'	=> $image_size
            	];
            	$this->_successAPIResponse($img_properties);
            }

		} else {
			$this->_failedAPIResponse('something went wrong!');
		}
	}

	private function _successAPIResponse($avatar)
	{
		$this->response([
        	'status' => true,
        	'avatar' => $avatar
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