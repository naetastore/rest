<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Subcategory extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		// is_logedin();
		$this->load->library('session');
	}

	public function index()
	{
		$data['title'] = 'Sub Category';
		$data['globals'] = $this->db->get('globals')->result_array();
		$data['categories'] = $this->db->get('categories')->result_array();
		$this->load->view('templates/header', $data);
		$this->load->view('templates/sidebar', $data);
    	$this->load->view('admin/subcategory');
    	$this->load->view('templates/footer');
	}

	public function insert()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('name', 'Nama kategori', 'required|trim');
		$this->form_validation->set_rules('description', 'Deskripsi', 'required|trim|max_length[128]');

		if ($this->form_validation->run() === FALSE){
			$this->session->set_flashdata('message', '<div class="alert alert-danger">Something went wrong!</div>');
			redirect('admin/subcategory');
		} else {
			$data = [
				'name' => $_POST['name'],
				'description' => $_POST['description'],
				'global_id' => $_POST['global_id']
			];
			$this->db->insert('categories', $data);
			activities('create', 'categories');

			$this->session->set_flashdata('message', '<div class="alert alert-success">Created successfuly</div>');
			redirect('admin/subcategory');
		}
	}

	public function show($id)
	{
		echo json_encode($this->db->get_where('categories', ['id' => $id])->row_array());
	}

	public function update($id)
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('name', 'Nama kategori', 'required|trim');
		$this->form_validation->set_rules('description', 'Deskripsi', 'required|trim|max_length[128]');

		if ($this->form_validation->run() === FALSE){
			$this->session->set_flashdata('message', '<div class="alert alert-danger">Something went wrong!</div>');
			redirect('admin/subcategory');
		} else {
			$data = [
				'name' => $_POST['name'],
				'description' => $_POST['description'],
				'global_id' => $_POST['global_id']
			];
			$this->db->update('categories', $data, ['id' => $id]);
			activities('update', 'categories');

			$this->session->set_flashdata('message', '<div class="alert alert-success">Updated successfuly</div>');
			redirect('admin/subcategory');
		}
	}

	public function remove($id)
	{
		$this->db->delete('categories', ['id' => $id ]);
		activities('delete', 'categories');

		$result = $this->db->get_where('products', ['category_id' => $id])->result_array();
		foreach ($result as $key) {
			if ($key['image'] !== 'default_image.jpg') {
				unlink(FCPATH.'assets/img/product/'.$key['image']);
			}
			$this->db->delete('products', ['category_id' => $key['category_id']]);
			activities('delete', 'products');
		}
	}

}