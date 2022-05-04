<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Presence_model extends CI_Model {

    public function getPresenceById($id)
    {
        $this->db->select('*');
        $this->db->from('presences');
        $this->db->where('id', $id);
        $this->db->where('isDeleted', 0);
        return $this->db->get()->row_array();
    }
    public function checkUserCheckedIn($uid, $date)
    {
        $this->db->select('*');
        $this->db->from('presences');
        $this->db->where('uid', $uid);
        $this->db->where('type', 'check_in');
        $this->db->where('DATE_FORMAT(time, "%Y-%c-%e") =', $date);
        $this->db->where('isDeleted', 0);
        if($this->db->get()->row_array()){
            return true;
        } else {
            return false;
        }
    }
    public function checkUserCheckedOut($uid, $date)
    {
        $this->db->select('*');
        $this->db->from('presences');
        $this->db->where('uid', $uid);
        $this->db->where('type', 'check_out');
        $this->db->where('DATE_FORMAT(time, "%Y-%c-%e") =', $date);
        $this->db->where('isDeleted', 0);
        return $this->db->get()->result_array();
        
    }
    public function getPresencesByUid($uid, $limit)
    {
        $this->db->select('*');
        $this->db->from('presences');
        $this->db->where('uid', $uid);
        $this->db->limit($limit);
        $this->db->order_by('id', 'desc');
        $this->db->where('isDeleted', 0);
        return $this->db->get()->result_array();
    }
    public function getPresencesBetweenDate($date_from, $date_until)
    {
        $this->db->select('*');
        $this->db->from('presences');
        $this->db->where('time >=', $date_from);
        $this->db->where('time <=', $date_until);
        $this->db->where('isDeleted', 0);
        return $this->db->get()->result_array();
    }
}
