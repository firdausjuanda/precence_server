<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Presence extends CI_Controller {

    public function check_in()
    {
        $post_data = $this->input->post();
        if(!$this->input->post('uid') || !$this->input->post('date')){
            $response['status'] = 'invalid-input';
            return print_r(json_encode($response));
        }

        $data['uid'] = $post_data['uid'];
        $data['type'] = 'check_in';
        if($this->Presence_model->checkUserCheckedIn($data['uid'], $post_data['date'])) {
            $response['status'] = 'checked-in';
            return print_r(json_encode($response));
        } else {
            try{
                $this->db->insert('presences', $data);
                $inserted_id = $this->db->insert_id();
                $inserted_data = $this->Presence_model->getPresenceById($inserted_id);
                $response['data'] = $inserted_data;
                $response['status'] = 'success';
                return print_r(json_encode($response));
            } catch (Exception $e) {
                return print_r(json_encode($e));
            }
        }
    }
    public function check_out()
    {
        $post_data = $this->input->post();
        if(!$this->input->post('uid') || !$this->input->post('date')){
            $response['status'] = 'invalid-input';
            return print_r(json_encode($response));
        }
        $override = 0;
        if($this->input->post('override')){
            $override = $this->input->post('override');
        }

        $data['uid'] = $post_data['uid'];
        $data['type'] = 'check_out';
        if($override == 0){
            if($this->Presence_model->checkUserCheckedOut($data['uid'], $post_data['date'])) {
                try{
                    $response['status'] = 'checked-out';
                    return print_r(json_encode($response));
                } catch (Exception $e) {
                    return print_r(json_encode($e));
                }
            } else {
                try{
                    $this->db->insert('presences', $data);
                    $inserted_id = $this->db->insert_id();
                    $inserted_data = $this->Presence_model->getPresenceById($inserted_id);
                    $response['data'] = $inserted_data;
                    $response['status'] = 'success';
                    return print_r(json_encode($response));
                } catch (Exception $e) {
                    return print_r(json_encode($e));
                }
            }
        } else {
            try{
                $checked_out_list = $this->Presence_model->checkUserCheckedOut($data['uid'], $post_data['date']);
                $ids = [];
                foreach($checked_out_list as $cl){
                    $ids[] = $cl['id'];
                }
                $response = $ids;
                try{
                    $this->db->where_in('id', $ids);
                    $this->db->update('presences', ['isDeleted' => 1]);
                } catch (Exception $e){
                    return print_r(json_encode($e));
                }
                $this->db->insert('presences', $data);
                $inserted_id = $this->db->insert_id();
                $inserted_data = $this->Presence_model->getPresenceById($inserted_id);
                $response['data'] = $inserted_data;
                $response['clean_ids'] = $ids;
                $response['status'] = 'success';
                return print_r(json_encode($response));
            } catch (Exception $e) {
                return print_r(json_encode($e));
            }
        }
    }
    public function get_presences()
    {
        $get_data = $this->input->get();
        if(!$this->input->get('date_from') || !$this->input->get('date_until')){
            return print_r(json_encode('invalid-input'));
        }

        try{
            $response = $this->Presence_model->getPresencesBetweenDate($get_data['date_from'], $get_data['date_until']);
            return print_r(json_encode($response));
        } catch (Exception $e) {
            return print_r(json_encode($e));
        }
    }
    public function get_user_presences()
    {
        $get_data = $this->input->get();
        if(!$this->input->get('uid')){
            http_response_code(500);
            $response['statusCode'] = 500;
            $response['data'] = 'invalid-input';
            return print_r(json_encode($response));
        }
        if(!$this->input->get('limit')){
            $limit = 10;
        } else {
            $limit = $this->input->get('limit');
        }

        try{
            $response = $this->Presence_model->getPresencesByUid($get_data['uid'], $limit);
            return print_r(json_encode($response));
        } catch (Exception $e) {
            return print_r(json_encode($e));
        }
    }
}