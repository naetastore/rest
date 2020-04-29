<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Users extends REST_Controller {

    function __construct()
    {
        parent::__construct();

        $this->load->helper('client');
    }

    public function index_get()
    {
        if ($this->get('delete')) {
            $this->_delete();
            return;
        }

        $id = $this->get('id');

        if ($id === NULL) {
            $user = basic_auth();

            if ($user['role_id'] == 1 && $this->get('all')) {
                $this->db->where('id !=', 1);
            	$user = $this->db->get('users')->result_array();
            }
        }else{
            $id = (int) $id;

            if ($id <= 0)
            {
                $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST);
            }

            $user = $this->db->get_where('users', ['id' => $id])->row_array();
        }

        if ($user)
        {
            if (isset($user['username'])) {
                if ($user['avatar'] !== NULL) {
                    $user['avatar'] = base_url('src/img/avatar/' . $user['avatar']);
                }else{
                    unset($user['avatar']);
                }
                
                $user['password'] = TRUE;
                $user['created'] = date('d F y', $user['created']);
            }else{
                $i=0;
                foreach ($user as $key) {
                    if ($user[$i]['avatar'] !== NULL) {
                        $user[$i]['avatar'] = base_url('src/img/avatar/' . $key['avatar']);
                    }else{
                        unset($user['avatar']);
                    }

                    $user[$i]['password'] = TRUE;
                    $user[$i]['created'] = date('d F y', $key['created']);
                    $i++;
                }
            }

            $this->set_response($user, REST_Controller::HTTP_OK);
        }
        else
        {
            $this->set_response([
                'status' => FALSE,
                'message' => 'User could not be found'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    private function _delete()
    {
        $user = basic_auth();

        if ($user['role_id'] == 2) {
            $this->response([
                'status' => FALSE,
                'message' => 'Something went wrong'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $id = (int) $this->get('id');

        if ($id <= 0)
        {
            $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST);
        }

        $user = $this->db->get_where('users', ['id' => $id])->row_array();

        if ($user['avatar'] !== NULL) {
            unlink(FCPATH . 'src/img/avatar/' . $user['avatar']);
        }

        $this->db->delete('users', ['id' => $id]);

        $message = [
            'id' => $id,
            'message' => 'Deleted the resource'
        ];

        $this->set_response($message, REST_Controller::HTTP_OK);
    }

    public function index_post()
    {
        if ($this->post('update')) {
            $this->_update();
            return;
        }

        $username = htmlspecialchars($this->post('username', TRUE));
        $password = $this->post('password', TRUE);
        
        if ($username === NULL)
        {
            $this->response([
                'status' => FALSE,
                'message' => 'Provide an username'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        if (strlen($username) < 5) {
            $this->response([
                'status' => FALSE,
                'message' => 'Username too short'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $user = $this->db->get_where('users', ['username' => $username]);

        if (isset($username) && !isset($password) | strlen($username) > 0 && strlen($password) < 1) {
            if ($user->num_rows() > 0) {
                $this->response([
                    'status' => FALSE,
                    'message' => 'This username has already taken'
                ], REST_Controller::HTTP_BAD_REQUEST);
            }else{
                $this->response([
                    'status' => TRUE,
                    'message' => 'Ready to insert'
                ], REST_Controller::HTTP_OK);
            }
        }

        if ($user->num_rows() > 0) {
            $this->response([
                'status' => FALSE,
                'message' => 'This username has already taken'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        if ($password === NULL)
        {
            $this->response([
                'status' => FALSE,
                'message' => 'Provide an password'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        if (strlen($password) < 5) {
            $this->response([
                'status' => FALSE,
                'message' => 'Password too short'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $this->load->helper('user');

        $email = $this->post('email');
        if (isset($email)) {
            $this->db->set('email', $email);
        }

        $this->db->insert('users', [
            'username'  => $username,
            'password'  => password_hash($password, PASSWORD_DEFAULT),
            'role_id'   => 2,
            'entry'     => create_entry(),
            'created'   => time()
        ]);

        $user = $this->db->get_where('users', ['username' => $username])->row_array();

        if ($user['avatar'] !== NULL) {
            $user['avatar'] = base_url('src/img/avatar/' . $user['avatar']);
        }else{
            unset($user['avatar']);
        }

        $user['created'] = date('d F y', $user['created']);

        $message = [
            'user' => $user,
            'message' => 'Added a resource'
        ];

        $this->set_response($message, REST_Controller::HTTP_CREATED);
    }

    private function _update()
    {
        $user = basic_auth();

        $username = $this->post('username');
        $password = $this->post('password');
        $id = $this->post('id');

        $data=[];

        $name = $this->post('name');
        if (isset($name)) {
            $data['name'] = $name;
        }

        $address = $this->post('address');
        if (isset($address)) {
            $data['address'] = $address;
        }
        
        $phone = $this->post('phonenumber');
        if (isset($phone)) {
            $data['phonenumber'] = $phone;
        }

        $repassword = $this->post('repassword');
        if (isset($repassword)) {
            if (strlen($repassword) < 5)
            {
                $this->response([
                    'status' => FALSE,
                    'message' => 'Password too short'
                ], REST_Controller::HTTP_BAD_REQUEST);
            }

            $data['password'] = password_hash($repassword, PASSWORD_DEFAULT);
        }

        $avatar = $this->post('avatar');
        if (isset($avatar)) {
            $config['upload_path']          = FCPATH . 'src/img/avatar/';
            $config['allowed_types']        = 'jpg|jpeg|png|svg';
            $config['encrypt_name']        	= TRUE;
            
            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('image'))
            {
                return TRUE;
            }
            else
            {
                $image_name = $this->upload->data('file_name');
                
                if ($user['avatar'] !== NULL) {
                    unlink(FCPATH . 'src/img/avatar/' . $user['avatar']);
                }

                $data['avatar'] = $image_name;
            }
        }

        if (!$data) {
            $this->response([
                'status' => FALSE,
                'message' => 'No data to update!'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        if ($user['role_id'] == 1) {
            $rules = ['id' => $id];
        }
        if ($user['role_id'] == 2) {
            $rules = ['username' => $username];
        }

        $this->db->update('users', $data, $rules);

        $user = $this->db->get_where('users', $rules)->row_array();

        if ($user['avatar'] === NULL) {
            unset($user['avatar']);
        }else{
            $user['avatar'] = base_url('src/img/avatar/' . $user['avatar']);
        }

        $user['password'] = TRUE;

        $this->response($user, REST_Controller::HTTP_OK);
    }

    public function notifications_get()
    {
        $user = basic_auth();

        if ($this->get('delete')) {
            $this->_delete_notifications();
            return;
        }

        $rules = ['role_id' => $user['role_id'], 'user_id' => $user['id']];

        $notifications = $this->db->order_by('created', 'DESC')->get_where('notifications', $rules)->result_array();

        if ($notifications) {
            $this->load->helper('andi');

            $i=0;
            foreach ($notifications as $key) {
                $notifications[$i]['created'] = postdate($key['created']);
                $i++;
            }

            $this->response($notifications, REST_Controller::HTTP_OK);
        }else{
            $this->response([
                'status' => FALSE,
                'message' => 'No notifications were found'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function notifications_post()
    {
        $user = basic_auth();

        if ($this->post('update'))
        {
            $this->_update_notifications();
        }
        
    }

    private function _update_notifications()
    {
        $id = $this->post('id');

        $id = (int) $id;

        if ($id <= 0) {
            $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST);
        }

        $this->db->update('notifications', ['readed' => 1], ['id' => $id]);

        if ($this->db->affected_rows() > 0) {
            $this->response([
                'id' => $id,
                'message' => 'Updated a resource'
            ], REST_Controller::HTTP_OK);
        }else{
            $this->response([
                'status' => FALSE,
                'message' => 'Failed to updated'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    private function _delete_notifications()
    {
        $id = $this->get('id');

        $id = (int) $id;

        if ($id <= 0) {
            $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST);
        }

        $this->db->delete('notifications', ['id' => $id]);
        
        if ($this->db->affected_rows() > 0) {
            $this->response([
                'id' => $id,
                'message' => 'Deleted a resource'
            ], REST_Controller::HTTP_OK);
        }else{
            $this->response([
                'status' => FALSE,
                'message' => 'Failed to deleted'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

}
