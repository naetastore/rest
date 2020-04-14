<?php

class Superuser_model extends CI_Model
{

    public function get()
    {
        $keys = $this->db->get('keys')->result_array();
        $users = $this->db->query("SELECT `users`.`username`, `id` FROM `users`")->result_array();
        
        $_users=$users;
        $i=0;
        foreach($keys as $k)
        {
            $keys[$i]['date_created'] = date('d F y', $k['date_created']);
            foreach($users as $u) {
                if ($u['id'] == $k['user_id'])
                {
                    $keys[$i]['user'] = $this->generate_user($u);
                }
            }
            
            $i++;
        }
        return [ 'keys' => $keys, 'users' => $_users ];
    }

    public function generate_user($user)
    {
        return [
            'username' => $user['username'],
            'url' => base_url('user/profile?uid=' . $user['id'] . '&')
        ];
    }

}
