<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Administrator extends CI_Controller
{
    private $on_session;
    private $requiredparams;

    public function __construct()
	{
		parent::__construct();
        in_session();
        $this->on_session = $this->db->get_where('users', [ 'username' => $_GET['username'] ])->row_array();
        $this->requiredparams = '?session=' . $_GET['session'] . '&username=' . $_GET['username'];
        $this->load->model('Administrator_model', 'admin');
    }

    public function index()
    {
        $data['title'] = 'Dashboard';
        $data['subtitle'] = '';
        $data['metadescription'] = '';
        $data['metakeyword'] = '';

        $data['user'] = $this->on_session;

        $data['statistic'] = $this->admin->dashboard_statistics();
        $data['activity'] = $this->admin->activities();
        $this->load->view('administrator/base', $data);
    }

    public function visitors()
    {
        $visitors = $this->admin->visitors();
        echo json_encode($visitors);
    }

    public function order()
    {
        $data['title'] = 'Order';
        $data['subtitle'] = '';
        $data['metadescription'] = '';
        $data['metakeyword'] = '';

        $data['user'] = $this->on_session;
        $data['order'] = $this->admin->order();
        $this->load->view('administrator/order', $data);
    }

    public function showorder()
    {
        $id = $this->input->get('id');

        $data['title'] = 'Order #' . $id;
        $data['subtitle'] = '';
        $data['metadescription'] = '';
        $data['metakeyword'] = '';
    
        $data['user'] = $this->on_session;
        $data['data'] = $this->admin->showorder($id);
        $this->load->view('administrator/orderview', $data);
    }

    public function showproduct()
    {
		$id = $this->input->get('id');
        $product = $this->db->get_where('products', ['id' => $id])->row_array();
		echo json_encode($product);
    }
}
