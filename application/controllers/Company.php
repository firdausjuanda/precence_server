<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Company extends CI_Controller {
    public function create_company(){
        $post_data = $this->input->post();

        if(
            !$this->input->post('uid') || 
            !$this->input->post('companyName') || 
            !$this->input->post('address') || 
            !$this->input->post('companyType') || 
            !$this->input->post('companyCode') || 
            !$this->input->post('unit') 
        ){
            $response['status'] = 'invalid-input';
            return print_r(json_encode($response));
        } elseif($this->db->get_where('companies', ['companyCode' => $post_data['companyCode']])->row_array()) {
            $response['status'] = 'duplicate-company-code';
            return print_r(json_encode($response));
        } else {
            try{
                $data_input['createdBy'] = $post_data['uid'];
                $data_input['companyName'] = $post_data['companyName'];
                $data_input['address'] = $post_data['address'];
                $data_input['companyType'] = $post_data['companyType'];
                $data_input['companyCode'] = $post_data['companyCode'];
                $data_input['unit'] = $post_data['unit'];
                $this->db->insert('companies', $data_input);
                $inserted_id = $this->db->insert_id();
                try {
                    $data_input_settings['companyId'] = $inserted_id;
                    $this->db->insert('companySettings', $data_input_settings);
                    try {
                        $data_input_admin['companyId'] = $inserted_id;
                        $data_input_admin['uid'] = $post_data['uid'];
                        $this->db->insert('companyAdmins', $data_input_admin); 
                        $response['data'] = $this->Company_model->getCompanyById($inserted_id);
                        $response['status'] = 'success';
                        return print_r(json_encode($response));
                    } catch (Exception $e) {
                        return print_r(json_encode($e));
                    }
                } catch (Exception $e) {
                    return print_r(json_encode($e));
                }
            } catch (Exception $e) {
                return print_r(json_encode($e));
            }
        }
    }

    public function set_admin()
    {
        $post_data = $this->input->post();
        if(
            !$this->input->post('uid') || 
            !$this->input->post('companyId')
        ){
            $response['status'] = 'invalid-input';
            return print_r(json_encode($response));
        } elseif($this->db->get_where('companyAdmins', ['uid' => $post_data['uid'], 'companyId' => $post_data['companyId']])->row_array()) {
            $response['status'] = 'duplicate-admin';
            return print_r(json_encode($response));
        } elseif(!$this->db->get_where('companies', ['id' => $post_data['companyId']])->row_array()) {
            $response['status'] = 'company-not-found';
            return print_r(json_encode($response));
        } elseif(!$this->db->get_where('users', ['id' => $post_data['uid']])->row_array()) {
            $response['status'] = 'user-not-found';
            return print_r(json_encode($response));
        } else {
            try{
                $this->db->insert('companyAdmins', $post_data);
                $response['status'] = 'success';
                return print_r(json_encode($response));
            } catch (Exception $e){
                return print_r(json_encode($e));
            }
        }
    }

    public function set_user_company()
    {
        $post_data = $this->input->post();
        if(
            !$this->input->post('uid') || 
            !$this->input->post('companyId')
        ){
            $response['status'] = 'invalid-input';
            return print_r(json_encode($response));
        } elseif(!$this->db->get_where('companies', ['id' => $post_data['companyId']])->row_array()) {
            $response['status'] = 'company-not-found';
            return print_r(json_encode($response));
        } elseif(!$this->db->get_where('users', ['id' => $post_data['uid']])->row_array()) {
            $response['status'] = 'user-not-found';
            return print_r(json_encode($response));
        } else {
            try{
                $this->db->where('id', $post_data['uid']);
                $this->db->update('users', ['companyId' => $post_data['companyId']]);
                $response['status'] = 'success';
                return print_r(json_encode($response));
            } catch (Exception $e){
                return print_r(json_encode($e));
            }
        }
    }

    public function set_check_point()
    {
        $post_data = $this->input->post();
        if(
            !$this->input->post('lat') || 
            !$this->input->post('long') ||
            !$this->input->post('companyId')
        ){
            $response['status'] = 'invalid-input';
            return print_r(json_encode($response));
        } elseif(!$this->db->get_where('companies', ['id' => $post_data['companyId']])->row_array()) {
            $response['status'] = 'company-not-found';
            return print_r(json_encode($response));
        } else {
            try{
                $data_input['lat'] = $post_data['lat'];
                $data_input['long'] = $post_data['long'];
                $this->db->where('companyId', $post_data['companyId']);
                $this->db->update('companySettings', $data_input);
                $response['data'] = $post_data;
                $response['status'] = 'success';
                return print_r(json_encode($response));
            } catch (Exception $e){
                return print_r(json_encode($e));
            }
        }
    }
    public function set_radius()
    {
        $post_data = $this->input->post();
        if(
            !$this->input->post('radius') ||
            !$this->input->post('companyId')
        ){
            $response['status'] = 'invalid-input';
            return print_r(json_encode($response));
        } elseif(!$this->db->get_where('companies', ['id' => $post_data['companyId']])->row_array()) {
            $response['status'] = 'company-not-found';
            return print_r(json_encode($response));
        } else {
            try{
                $data_input['radius'] = $post_data['radius'];
                $this->db->where('companyId', $post_data['companyId']);
                $this->db->update('companySettings', $data_input);
                $response['data'] = $post_data;
                $response['status'] = 'success';
                return print_r(json_encode($response));
            } catch (Exception $e){
                return print_r(json_encode($e));
            }
        }
    }
    public function get_radius()
    {
        $get_data = $this->input->get();
        if(!$this->input->get('companyId')){
            $response['status'] = 'invalid-input';
            return print_r(json_encode($response));
        } elseif(!$this->db->get_where('companies', ['id' => $get_data['companyId']])->row_array()) {
            $response['status'] = 'company-not-found';
            return print_r(json_encode($response));
        } else {
            try{
                $get_data = $this->Company_model->getCompanyRadius($get_data['companyId']);
                $response['data'] = $get_data;
                $response['status'] = 'success';
                return print_r(json_encode($response));
            } catch (Exception $e){
                return print_r(json_encode($e));
            }
        }
    }
    public function get_check_point()
    {
        $get_data = $this->input->get();
        if(!$this->input->get('companyId')){
            $response['status'] = 'invalid-input';
            return print_r(json_encode($response));
        } elseif(!$this->db->get_where('companies', ['id' => $get_data['companyId']])->row_array()) {
            $response['status'] = 'company-not-found';
            return print_r(json_encode($response));
        } else {
            try{
                $get_data = $this->Company_model->getCompanyCheckPoint($get_data['companyId']);
                $response['data'] = $get_data;
                $response['status'] = 'success';
                return print_r(json_encode($response));
            } catch (Exception $e){
                return print_r(json_encode($e));
            }
        }
    }

    public function check_user_admin()
    {
        $post_data = $this->input->get();
        if(
            !$this->input->get('uid') ||
            !$this->input->get('companyId')
        ){
            $response['status'] = 'invalid-input';
            return print_r(json_encode($response));
        } elseif(!$this->db->get_where('companies', ['id' => $post_data['companyId']])->row_array()) {
            $response['status'] = 'company-not-found';
            return print_r(json_encode($response));
        } elseif(!$this->db->get_where('users', ['id' => $post_data['uid']])->row_array()) {
            $response['status'] = 'user-not-found';
            return print_r(json_encode($response));
        } else {
            try{
                $response_data = $this->Company_model->checkCompanyAdmin($post_data['uid'], $post_data['companyId']);
                if($response_data){
                    $response['data'] = true;
                } else {
                    $response['data'] = false;
                }
                $response['status'] = 'success';
                return print_r(json_encode($response));
            } catch (Exception $e){
                return print_r(json_encode($e));
            }
        }
    }
}