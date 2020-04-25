<?php

function basic_auth() {
    $ci = get_instance();

    if ($_SERVER['REQUEST_METHOD'] === "POST")
    {
        $username = $ci->post('username');
        $password = $ci->post('password');
    }
    
    if ($_SERVER['REQUEST_METHOD'] === "GET")
    {
        $username = $ci->get('username');
        $password = $ci->get('password');
    }

    if ($username === NULL)
    {
        $ci->response([
            'status' => FALSE,
            'message' => 'Provide an username'
        ], REST_Controller::HTTP_BAD_REQUEST);
    }
    if ($password === NULL)
    {
        $ci->response([
            'status' => FALSE,
            'message' => 'Provide an password'
        ], REST_Controller::HTTP_BAD_REQUEST);
    }

    $user = $ci->db->get_where('users', ['username' => $username])->row_array();
    
    $password_verify = password_verify($password, $user['password']);

    if ($password_verify === FALSE) {
        $ci->response([
            'status' => FALSE,
            'message' => 'Not authorization'
        ], REST_Controller::HTTP_BAD_REQUEST);
    }else{
        $user['password'] = $password_verify;
        return $user;
    }

    if (!$user) {
        $ci->response([
            'status' => FALSE,
            'message' => 'Not authorization'
        ], REST_Controller::HTTP_BAD_REQUEST);
    }
}