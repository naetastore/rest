<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Product extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		// is_logedin();
		$this->load->library('session');
	}

	public function index()
	{
		$data['title'] = 'Product';
		$data['products'] = $this->db->get('products')->result_array();
		$data['categories'] = $this->db->get('categories')->result_array();
		$this->load->view('templates/header', $data);
		$this->load->view('templates/sidebar', $data);
    	$this->load->view('admin/product');
    	$this->load->view('templates/footer');
	}

	public function insert()
	{
		$this->load->library('form_validation');
		$this->load->model('Rules_model');
		$rules = $this->Rules_model->product();
		$this->form_validation->set_rules($rules);

		if ($this->form_validation->run() === FALSE){
			$this->session->set_flashdata('message', '<div class="alert alert-danger">Lengkapi data!</div>');
			redirect('admin/product');
		} else {

			if ($_FILES['image']) {
				$image = $_FILES['image']['name'];
				$config['upload_path']          = FCPATH.'assets/img/product/';
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

			$global_id = $this->db->get_where('categories', ['id' => $_POST['category_id']])->row_array()['global_id'];
			$data = [
				'name' => $_POST['name'],
				'price' => $_POST['price'],
				'qty' => $_POST['qty'],
				// nambahi global_id
				'global_id' => $global_id,
				'category_id' => $_POST['category_id'],
				'description' => $_POST['description'],
				'seo_keyword' => $_POST['seo_keyword'],
				'suggested' => $_POST['suggested'],
				// nambahi is_ready
				'is_ready' => $_POST['is_ready'],
				'created' => time()
			];
			$this->db->insert('products', $data);
			activities('create', 'products');

			$start_price = $this->db->order_by('price', 'ASC')->get_where('products', ['global_id' => $global_id])->row_array()['price'];
			$high_price = $this->db->order_by('price', 'DESC')->get_where('products', ['global_id' => $global_id])->row_array()['price'];
			$this->db->update('globals', ['start_price' => $start_price, 'high_price' => $high_price], ['id' => $global_id]);

			$this->session->set_flashdata('message', '<div class="alert alert-success">Created successfuly</div>');
			redirect('admin/product');
		}
	}

	public function show($id)
	{
		echo json_encode($this->db->get_where('products', ['id' => $id])->row_array());
	}

	public function update($id)
	{
		$this->load->library('form_validation');
		$this->load->model('Rules_model');
		$rules = $this->Rules_model->product();
		$this->form_validation->set_rules($rules);

		if ($this->form_validation->run() === FALSE){
			$this->session->set_flashdata('message', '<div class="alert alert-danger">Lengkapi data!</div>');
			redirect('admin/product');
		} else {

			if ($_FILES['image']['name']) {
				$image = $_FILES['image']['name'];
				$config['upload_path']          = FCPATH.'assets/img/product/';
                $config['allowed_types']        = 'gif|jpg|png';
                $config['max_size']             = 1024;

                $this->load->library('upload', $config);
                if (!$this->upload->do_upload('image'))
                {
                    $error = $this->upload->display_errors();
                    echo ($error);
                    die;
                }
                else
                {
                	$image_name = $this->upload->data('file_name');
                	$this->db->set('image', $image_name);
                }
			}

			$global_id = $this->db->get_where('categories', ['id' => $_POST['category_id']])->row_array()['global_id'];
			$data = [
				'name' => $_POST['name'],
				'price' => $_POST['price'],
				'qty' => $_POST['qty'],
				// nambahi global_id
				'global_id' => $global_id,
				'category_id' => $_POST['category_id'],
				'description' => $_POST['description'],
				'seo_keyword' => $_POST['seo_keyword'],
				'suggested' => $_POST['suggested'],
				// nambahi is_ready
				'is_ready' => $_POST['is_ready'],
				'created' => time()
			];
			$this->db->update('products', $data, ['id' => $id]);
			activities('update', 'products');

			$start_price = $this->db->order_by('price', 'ASC')->get_where('products', ['global_id' => $global_id])->row_array()['price'];
			$high_price = $this->db->order_by('price', 'DESC')->get_where('products', ['global_id' => $global_id])->row_array()['price'];
			$this->db->update('globals', ['start_price' => $start_price, 'high_price' => $high_price], ['id' => $global_id]);

			$this->session->set_flashdata('message', '<div class="alert alert-success">Updated successfuly</div>');
			redirect('admin/product');
		}
	}

	public function remove($id)
	{
		$product = $this->db->get_where('products', ['id' => $id])->row_array();
		
		if ($product['image'] !== 'default_image.jpg') {
			unlink(FCPATH.'assets/img/product/'.$product['image']);
		}
		$this->db->delete('products', ['id' => $id ]);
		activities('delete', 'products');

		$global_id = $this->db->get_where('categories', ['id' => $product['category_id']])->row_array()['global_id'];
		$start_price = $this->db->order_by('price', 'ASC')->get_where('products', ['global_id' => $global_id])->row_array()['price'];
		$high_price = $this->db->order_by('price', 'DESC')->get_where('products', ['global_id' => $global_id])->row_array()['price'];
		$this->db->update('globals', ['start_price' => $start_price, 'high_price' => $high_price], ['id' => $global_id]);
	}

}