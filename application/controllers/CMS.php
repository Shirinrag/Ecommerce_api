<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class CMS extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /* 200 = OK
       201 = Bad Request (Required param is missing)
       202 = No Valid Auth key
       204 = No post data
       203 = Generic Error
       205 = Form Validation failed
       206 = Queury Failed
       207 = Already Logged-In Error
       208 = Curl Failed
       209 = Curl UNAUTHORIZED
    */


    public function index()
    {
        $response = array('status' => false, 'msg' => 'Oops! Please try again later.', 'code' => 200);
        echo json_encode($response);
    }
    public function get_language_get()
    {
         $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) { 
            $language = $this->model->selectWhereData('tbl_language', array(),array('id','lang_name'),false);

            $response['code'] = REST_Controller::HTTP_OK;
            $response['status'] = true;
            $response['message'] = 'success';
            $response['language'] = $language;

        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function get_language_wise_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {            
                $fk_lang_id = $this->input->post('fk_lang_id');
                if(empty($fk_lang_id)){
                    $response['message'] = 'Language is required';
                    $response['code'] =201;
                }else{
                    $this->load->model('superadmin_model');
                   
                    $menu = $this->model->selectWhereData('tbl_menu', array('fk_lang_id'=>$fk_lang_id),array('id','menu'),false);
                    $sub_menu = $this->model->selectWhereData('tbl_sub_menu', array('fk_lang_id'=>$fk_lang_id),array('id','sub_menu_name'),false);
                    $child_menu = $this->model->selectWhereData('tbl_child_menu', array('fk_lang_id'=>$fk_lang_id),array('id','child_menu_name'),false);
                    $content = $this->model->selectWhereData('tbl_content', array('fk_language_id'=>$fk_lang_id));
                    $gallery = $this->superadmin_model->get_gallery_info($fk_lang_id);
                   
                    $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
                    $response['message'] = 'success';
                    $response['menu'] = $menu;
                    $response['sub_menu'] = $sub_menu;
                    $response['child_menu'] = $child_menu;
                    $response['content'] = $content;
                    $response['gallery'] = $gallery;
                }
               
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }

    public function get_dynamic_menu_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) { 
            $fk_lang_id = $this->input->post('fk_lang_id');
            if(empty($fk_lang_id)){
                $response['message'] = 'Language is required';
                $response['code'] =201;
            }else{
                    $this->load->model('superadmin_model');
                    $cat_data = $this->superadmin_model->get_dynamic_cat($fk_lang_id);
                    foreach ($cat_data as $cat_data_key => $val) {
                        $sub_menu_id = explode(",", $val['sub_menu_id']);
                        $subcat_name = explode(",", $val['sub_menu_name']);
                        $sub_cat_status = explode(",", $val['sub_cat_status']);
                        $sub_menu_function_name = explode(",", $val['sub_menu_function_name']);
                        $cat_data[$cat_data_key]['sub_menu_id'] = $sub_menu_id;
                        $cat_data[$cat_data_key]['sub_menu_name'] = $subcat_name;
                        $cat_data[$cat_data_key]['sub_cat_status'] = $sub_cat_status;
                        $cat_data[$cat_data_key]['sub_menu_function_name'] = $sub_menu_function_name;
                        foreach ($sub_menu_id as $key1 => $val1) {
                            $child_cat_name = $this->superadmin_model->get_dynamic_childcat($val1,$fk_lang_id);
                            $custom_key_name = $subcat_name[$key1] . '_' . $val1 . '_' . $sub_cat_status[$key1];
                            $cat_data[$cat_data_key]['child_name'][$custom_key_name] = $child_cat_name;
                        }
                    }

                    $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
                    $response['message'] = 'success';
                    $response['cat_data'] = $cat_data;
            }
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }

    public function get_home_page_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {  
            $fk_lang_id = $this->input->post('fk_lang_id');
            if(empty($fk_lang_id)){
                $response['message'] = 'Language is required';
                $response['code'] =201;
            }else{          
                    $this->load->model('superadmin_model');
                   
                    $slider = $this->model->selectWhereData('tbl_slider', array(),array('image'),false);

                    $about_us = $this->model->selectWhereData('tbl_about_us', array('fk_lang_id'=>$fk_lang_id,'del_status'=>'Active'),array('fk_menu_id','heading','image','about_us'));
                    $director_message = $this->model->selectWhereData('tbl_director_message', array('fk_lang_id'=>$fk_lang_id,'del_status'=>'Active'),array('id','heading','image','content'));                   
                    $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
                    $response['message'] = 'success';
                    $response['slider'] = $slider;
                    $response['about_us'] = $about_us;
                    $response['director_message'] = $director_message;
            }
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }

    public function get_about_page_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {  
            $fk_lang_id = $this->input->post('fk_lang_id');
            if(empty($fk_lang_id)){
                $response['message'] = 'Language is required';
                $response['code'] =201;
            }else{            
                    $director_message = $this->model->selectWhereData('tbl_director_message', array('fk_lang_id'=>$fk_lang_id,'del_status'=>'Active'),array('id','heading','image','content'));                   
                    $team = $this->model->selectWhereData('tbl_team', array('fk_lang_id'=>$fk_lang_id,'del_status'=>'Active'),array('*'),false);                   
                    $testimonial = $this->model->selectWhereData('tbl_testimonial', array('fk_lang_id'=>$fk_lang_id,'del_status'=>'Active'),array('*'),false);                   
                    $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
                    $response['message'] = 'success';
                    $response['director_message'] = $director_message;
                    $response['team'] = $team;
                    $response['testimonial'] = $testimonial;
            }
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }

    public function get_contact_page_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {  
            $fk_lang_id = $this->input->post('fk_lang_id');
            if(empty($fk_lang_id)){
                $response['message'] = 'Language is required';
                $response['code'] =201;
            }else{            
                    $contact_details = $this->model->selectWhereData('tbl_contact_info', array('fk_lang_id'=>$fk_lang_id,'del_status'=>'Active'),array('id','address_type','address','email','contact_no'));                                   
                    $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
                    $response['message'] = 'success';
                    $response['contact_details'] = $contact_details;
            }
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }

    public function get_gallery_page_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {  
            $fk_lang_id = $this->input->post('fk_lang_id');
            if(empty($fk_lang_id)){
                $response['message'] = 'Language is required';
                $response['code'] =201;
            }else{            
                $gallery_details = $this->model->selectWhereData('tbl_gallery', array('fk_lang_id'=>$fk_lang_id,'del_status'=>'Active'),array('id','event'),false);                               
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
                $response['gallery_details'] = $gallery_details;
            }
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }

    public function get_gallery_details_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {  
            $fk_lang_id = $this->input->post('fk_lang_id');
            $id = $this->input->post('id');
            $year = $this->input->post('year');
            if(empty($fk_lang_id)){
                $response['message'] = 'Language is required';
                $response['code'] =201;
            }else if(empty($id)){
                $response['message'] = 'Id is required';
                $response['code'] =201;
            }else{    
                if(empty($year)){
                    $year1 = date("Y"); 
                }else{
                    $year1 = $year; 
                }
                $this->load->model('superadmin_model');
                $gallery_details = $this->superadmin_model->get_year_wise_gallery($id,$year1);
                $year = $this->model->selectWhereData('tbl_gallery_details', array('fk_gallery_id'=>$id,),array('fk_gallery_id','year','id'),false);                                   

                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
                $response['gallery_details'] = $gallery_details;
                $response['year'] = $year;
            }
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }

    public function add_contact_details_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {  
            $fk_lang_id = $this->input->post('fk_lang_id');
            $name = $this->input->post('name');
            $email = $this->input->post('email');
            $phone = $this->input->post('phone');
            $note = $this->input->post('note');
            if(empty($fk_lang_id)){
                $response['message'] = 'Language is required';
                $response['code'] =201;
            }else if(empty($name)){
                $response['message'] = 'Name is required';
                $response['code'] =201;
            }else if(empty($email)){
                $response['message'] = 'Email is required';
                $response['code'] =201;
            }else if(empty($phone)){
                $response['message'] = 'Contact is required';
                $response['code'] =201;
            }else if(empty($note)){
                $response['message'] = 'Note is required';
                $response['code'] =201;
            }else{            
                $curl_data = array(
                    'name'=>$name,
                    'email'=>$email,
                    'phone'=>$phone,
                    'note'=>$note,
                    'fk_lang_id'=>$fk_lang_id,
                );
                $this->model->insertData('tbl_user_contact_details',$curl_data);
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
            }
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }

    public function get_carrer_page_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {  
            $fk_lang_id = $this->input->post('fk_lang_id');
            if(empty($fk_lang_id)){
                $response['message'] = 'Language is required';
                $response['code'] =201;
            }else{            
                    $carrer_details = $this->model->selectWhereData('tbl_carrer', array('fk_lang_id'=>$fk_lang_id,'del_status'=>'Active'),array('id','title','description'),false);                                   
                    $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
                    $response['message'] = 'success';
                    $response['carrer_details'] = $carrer_details;
            }
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
}