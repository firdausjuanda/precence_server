<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Company_model extends CI_Model {
    public function getCompanyById($id)
    {
        $this->db->select('*');
        $this->db->from('companies');
        $this->db->where('id', $id);
        return $this->db->get()->row_array();
    }
    public function checkCompanyAdmin($uid, $companyId)
    {
        $this->db->select('*');
        $this->db->from('companyAdmins');
        $this->db->where('uid', $uid);
        $this->db->where('companyId', $companyId);
        return $this->db->get()->row_array();
    }
}