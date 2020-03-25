<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Globalcategory extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		// is_logedin();
		$this->load->library('session');
	}
		
	public function index()
	{
		$data['title'] = 'Global Category';
		$data['globalcategory'] = $this->db->get('globals')->result_array();
		$this->load->view('templates/header', $data);
		$this->load->view('templates/sidebar', $data);
    	$this->load->view('admin/global');
    	$this->load->view('templates/footer');
	}

	public function insert()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('name', 'Nama kategori', 'required|trim');
		$this->form_validation->set_rules('description', 'Deskripsi', 'required|trim|max_length[128]');

		if ($this->form_validation->run() === FALSE) {
			$this->session->set_flashdata('message', '<div class="alert alert-danger">Lengkapi data!</div>');
			redirect('admin/globalcategory');
		} else {
			
			if ($_FILES['image']) {
				$image = $_FILES['image']['name'];

				$config['upload_path']          = FCPATH.'assets/img/global/';
                $config['allowed_types']        = 'gif|jpg|png';
                $config['max_size']             = 1024;

                $this->load->library('upload', $config);

                if (!$this->upload->do_upload('image'))
                {
                    $error = $this->upload->display_errors();
                    var_dump($error);
                    die;
                }
                else
                {
                	$image_name = $this->upload->data('file_name');
                	$this->db->set('image', $image_name);
                }
			}

			$data = [
				'name' => $_POST['name'],
				'description' => $_POST['description']
			];
			$this->db->insert('globals', $data);
			activities('create', 'globals');

			$this->session->set_flashdata('message', '<div class="alert alert-success">New data has been created</div>');
			redirect('admin/globalcategory');
		}
	}

	public function update($id)
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('name', 'Nama kategori', 'required|trim');
		$this->form_validation->set_rules('description', 'Deskripsi', 'required|trim|max_length[128]');

		if ($this->form_validation->run() === FALSE) {
			return;
		} else {

			if ($_FILES['image']['name']) {
				$image = $_FILES['image']['name'];

				$config['upload_path']          = FCPATH.'assets/img/global/';
                $config['allowed_types']        = 'gif|jpg|png';
                $config['max_size']             = 1024;

                $prevImage = $this->db->get_where('globals', ['id' => $id])->row_array()['image'];
                if ($prevImage !== 'default_image.jpg') {
                	unlink(FCPATH.'assets/img/global/'.$prevImage);
                }

                $this->load->library('upload', $config);

                if (!$this->upload->do_upload('image'))
                {
                    $error = $this->upload->display_errors();
                    var_dump($error);
                    die;
                }
                else
                {
                	$image_name = $this->upload->data('file_name');
                	$this->db->set('image', $image_name);
                }
			}

			$data = [
				'name' => $_POST['name'],
				'description' => $_POST['description']
			];
			$this->db->update('globals', $data, ['id' => $id]);
			activities('update', 'globals');
		}

		$this->session->set_flashdata('message', '<div class="alert alert-success">Updated successfuly</div>');
		redirect('admin/globalcategory');
	}

	public function show($id) {
		echo json_encode($this->db->get_where('globals', ['id' => $id])->row_array());
	}

	public function remove($id)
	{
		$prevImage = $this->db->get_where('globals', ['id' => $id])->row_array()['image'];
        if ($prevImage !== 'default_image.jpg') {
        	unlink(FCPATH.'assets/img/global/'.$prevImage);
        }

		// delete global category
		$this->db->delete('globals', ['id' => $id ]);
		activities('delete', 'globals');

		// // ==============================================================================================================
		// 	$global_id = $id;
		// // ==============================================================================================================

		// // query id sub category
		// $category = $this->db->get_where('categories', ['global_id' => $global_id])->result_array();
		// foreach ($category as $key) {
		// 	// query product
		// 	$product = $this->db->get_where('products', ['category_id' => $key['id']])->result_array();
		// 	foreach ($product as $key) {
		// 		// delete image
		// 		if ($key['image'] !== 'default_image.jpg') {
		// 			unlink(FCPATH.'assets/img/product/'.$image);
		// 		}
		// 	}
		// }

		// // delete sub category
		// $this->db->delete('categories', ['global_id' => $global_id]);

		// // delete product
		// $this->db->delete('products', ['category_id' => $category['id']]);
	}
}