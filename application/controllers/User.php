<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

    public function index(){
        echo "Index";
    }
    public function getUserById(){
        $get = $this->input->get();
        $user_data = $this->User_model->getUserById($get['uid']);
        return print_r(json_encode($user_data));
    }
    public function loginUser(){
        if(!$this->input->post('password') || !$this->input->post('email')){
            return print_r(json_encode('missing-requirement'));
        }
        $input_data = $this->input->post();
        $userData = $this->User_model->getUserByEmail($input_data['email']);
        if($userData){
            if($userData['password'] == $input_data['password']){
                return print_r(json_encode($userData));
            } else {
                return print_r(json_encode('password-not-matched'));
            }
        } else {
            return print_r(json_encode('user-not-found'));
        }
    }
    public function createUser(){
        $post_body = html_escape($this->input->post(), true);
        if(!$post_body['username'] || !$post_body['password'] || !$post_body['email']){
            return print_r(json_encode('Data is missing'));
        }
        $checkUsername = $this->User_model->getUserByUsername($post_body['username']);
        $checkEmail = $this->User_model->getUserByUsername($post_body['email']);
        if($checkUsername){
            $response['status'] = 'duplicate-username';
            return print_r(json_encode($response));
        }
        if($checkEmail){
            $response['status'] = 'duplicate-email';
            return print_r(json_encode($response));
        }

        try{
            $this->db->insert('users', $post_body);
            $inserted_id = $this->db->insert_id();
            $response['data'] = $this->User_model->getUserById($inserted_id);
            $response['status'] = 'success';
            return print_r(json_encode($response));
        } catch (Exception $e) {
            return print_r($e);
        }
    }

    public function getUserInRange()
    {
        $get_data = $this->input->get();
        $users = $this->User_model->getUserPerPage($get_data['limit']);
        return print_r(json_encode($users));
    }
    
}
