<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {
    public function getUserByEmail($email){
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where('email', $email);
        return $this->db->get()->row_array();
    }
    public function getUserByUsername($username){
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where('username', $username);
        return $this->db->get()->row_array();
    }
    public function getUserById($uid){
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where('id', $uid);
        return $this->db->get()->row_array();
    }
    public function getUserPerPage($until){
        $this->db->select('*');
        $this->db->from('users');
        $this->db->limit($until);
        return $this->db->get()->result_array();
    }
}