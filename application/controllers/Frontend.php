<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// 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
require APPPATH . '/libraries/REST_Controller.php';

class Frontend extends REST_Controller {

    public function __construct() {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Headers: Content-Type');
        header('Content-Type: text/html; charset=utf-8');
        header('Content-Type: application/json; charset=utf-8'); 
    }

   /*200 = OK
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

    public function index() {
        $response = array('status' => false, 'msg' => 'Oops! Please try again later.', 'code' => 200);
        echo json_encode($response);
    }
    public function get_language_get()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
              $lang_name = $this->model->selectWhereData('tbl_language', array(),array('id','lang_name'),false);
              $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
                    $response['message'] = 'success';
                    $response['lang_name'] = $lang_name;
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    // Home Page API
    public function get_home_page_data_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
              $fk_lang_id = $this->input->post('fk_lang_id');
              if(empty($fk_lang_id)){
                    $response['message'] ="Language Name is required";
                    $response['code'] = 201;
              }else{
                    $slider = $this->model->selectWhereData('top_banner', array('status'=>1),array('bottom_id','img_url'),false);
                        $product_data = $this->model->selectWhereData('product', array('status'=>1,'fk_lang_id'=>$fk_lang_id),array('*'),false);
                    $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
                    $response['message'] = 'success';
                    $response['slider'] = $slider;
                    $response['product_data'] = $product_data;
              }
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    // Register API
    public function register_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
            $user_name = $this->input->post('user_name');
            $email = $this->input->post('email');
            $contact_no = $this->input->post('contact_no');
            $device_type = $this->input->post('device_type');
            $device_id = $this->input->post('device_id');
            $terms_cond = $this->input->post('terms_cond');
            $app_version = $this->input->post('app_version');
            $app_build_no = $this->input->post('app_build_no');
            $password = $this->input->post('password');

            if(empty($user_name)){
                $response['message']= "User Name is required";
                $response['code']= 201;
            }else if (empty($contact_no)) {
                $response['message'] = 'Contact Number should be 10 number digits.';
                $response['code'] = 201;
            } else if(empty($password)){
                $response['message']= "Pasword is required";
                $response['code']= 201;
            }else{
               
                $check_contact_no_count = $this->model->CountWhereRecord('op_user',array('contact_no'=>$contact_no,'status'=>'1'));
                // echo '<pre>'; print_r($check_contact_no_count); exit;
                if ($check_contact_no_count > 0) {
                    $response['message'] = 'Contact No is already exist.';
                    $response['code'] = 201;
                    $response['error_status']="contact";
                } else {
                        
                        $getTermsConditionId = $this->model->selectWhereData('tbl_about_us',array('module'=>"1",'type'=>'1','is_deleted'=> '1'),array('*'),false,array('id' => 'desc'));
                       
                        $termsCondtnId = (string)count($getTermsConditionId) > 0 ? $getTermsConditionId[0]['id'] : 0;

                        $curl_data = array(
                            'user_name' =>$user_name,
                            'email' =>$email,
                            'password'=>dec_enc('encrypt',$password),
                            'contact_no'=>$contact_no,
                            'role_id' => '2',
                            'device_id' => $device_id,
                            'device_type' => $device_type,
                            'notifn_topic' => $contact_no . 'ecom',
                            'terms_condition' => $terms_cond != '' ? $terms_cond : 1,
                            'terms_conditn_id' => $terms_cond != '' ? $termsCondtnId : 0,
                            'app_version' => $app_version,
                            'app_build_no' => $app_build_no,
                        );
                        $inserted_id = $this->model->insertData('op_user',$curl_data);

                        $response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
                        $response['message'] = 'success';
                }    
            }   
            echo json_encode($response);
    }
    //Login API
    public function login_post()
    {
            $response = array('code' => - 1, 'status' => false, 'message' => '');
            $contact_no = $this->input->post('contact_no');           
            $password = $this->input->post('password');
            if (empty($contact_no)) {
                $response['message'] = 'contact no is required.';
                $response['code'] = 201;
            } else if (empty($password)) {
                $response['message'] = 'Password is required.';
                $response['code'] = 201;
            } else {
                $check_username_count = $this->model->CountWhereRecord('op_user',array('contact_no'=>$contact_no));
                if($check_username_count > 0) {       
                    $login_credentials_data = array(
                      "contact_no" => $contact_no,
                      "password" => dec_enc('encrypt',$password)
                    );
                    // echo '<pre>'; print_r($login_credentials_data); exit;
                    $login_info = $this->model->selectWhereData('op_user',$login_credentials_data,'*');
                    if(!empty($login_info)){
                            $response['code'] = REST_Controller::HTTP_OK;;
                            $response['status'] = true;
                            $response['message'] = 'success';
                            $response['data'] = $login_info;
                            $response['session_token'] = token_get();
                    } else {
                        $response['code'] = 201;
                        $response['status'] = "wrong_password";
                        $response['message'] = 'Incorrect Password';
                    }      
                }  else {
                    $response['code'] = 201;
                    $response['message'] = 'Incorrect Username';
                    $response['status'] = "wrong_username";
                }          
            } 
        echo json_encode($response);
    }
    // User Update API
    public function update_user_profile_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
            $op_user_id= $this->input->post('op_user_id');
            $first_name= $this->input->post('first_name');
            $last_name= $this->input->post('last_name');
            $email= $this->input->post('email');
            $contact_no= $this->input->post('contact_no');
           
           if(empty($first_name)){
                $response['message'] = "First Name is required";
                $response['code'] =201;
           }else if(empty($last_name)){
                $response['message'] = "Last Name is required";
                $response['code'] =201;
           }else if(empty($email)){
                $response['message']= "Email is required";
                $response['code']= 201;
            }else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $response['message'] = 'Provide valid email address.';
                $response['code'] = 201;
            }else if (!empty($contact_no) && !preg_match('/^[0-9]{10}+$/', $contact_no)) {
                $response['message'] = 'Contact Number should be 10 number digits.';
                $response['code'] = 201;
            }else{
                  $check_user_count = $this->model->CountWhereRecord('op_user', array('email'=>$email,'op_user_id!='=>$op_user_id,'status'=>'1'));
                   
                if($check_user_count > 0){
                    $response['message'] = 'Email Already Exist.....!';
                    $response['code'] = 201;
                }else {
                    $curl_data = array(
                        'user_name' =>$first_name." ".$last_name,
                        'email' =>$email,
                        'contact_no'=>$contact_no,
                    );
                    $this->model->updateData('op_user', $curl_data, array('op_user_id'=>$op_user_id));

                    $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
                    $response['message'] = 'success';
                }
            }
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    // Add New Address API
    public function save_new_address_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
            $user_id = $this->input->post('user_id');
            $roomno = $this->input->post('roomno');
            $building = $this->input->post('building');
            $street = $this->input->post('street');
            $zone = $this->input->post('zone');
            $latitude = $this->input->post('latitude');
            $longitude = $this->input->post('longitude');
            $address_type = $this->input->post('address_type');
            if(empty($user_id)){
                $response['message']= "User Id is required";
                $response['code']= 201;
            }else if(empty($roomno)){
                $response['message']= "Room No is required";
                $response['code']= 201;
            }else if(empty($building)){
                $response['message']= "Building is required";
                $response['code']= 201;
            }else if(empty($street)){
                $response['message']= "Street is required";
                $response['code']= 201;
            }else if(empty($zone)){
                $response['message']= "Zone is required";
                $response['code']= 201;
            }else if(empty($latitude)){
                $response['message']= "Latitude is required";
                $response['code']= 201;
            }else if(empty($longitude)){
                $response['message']= "Longitude is required";
                $response['code']= 201;
            }else if(empty($address_type)){
                $response['message']= "Address Type is required";
                $response['code']= 201;
            }else{
                $curl_data = array(
                    'user_id' =>$user_id,
                    'roomno' =>$roomno,
                    'building' =>$building,
                    'street' =>$street,
                    'zone' =>$zone,
                    'latitude' =>$latitude,
                    'longitude' =>$longitude,
                    'address_type' =>$address_type,
                );
                $inserted_id = $this->model->insertData('user_delivery_address',$curl_data);

                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'Address Added Successfully'; 
            }   
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    // Update New Address API
    public function update_address_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
            $id = $this->input->post('id');
            $roomno = $this->input->post('roomno');
            $building = $this->input->post('building');
            $street = $this->input->post('street');
            $zone = $this->input->post('zone');
            $latitude = $this->input->post('latitude');
            $longitude = $this->input->post('longitude');
            $address_type = $this->input->post('address_type');
            if(empty($id)){
                $response['message']= "Id is required";
                $response['code']= 201;
            }else if(empty($roomno)){
                $response['message']= "Room No is required";
                $response['code']= 201;
            }else if(empty($building)){
                $response['message']= "Building is required";
                $response['code']= 201;
            }else if(empty($street)){
                $response['message']= "Street is required";
                $response['code']= 201;
            }else if(empty($zone)){
                $response['message']= "Zone is required";
                $response['code']= 201;
            }else if(empty($latitude)){
                $response['message']= "Latitude is required";
                $response['code']= 201;
            }else if(empty($longitude)){
                $response['message']= "Longitude is required";
                $response['code']= 201;
            }else if(empty($address_type)){
                $response['message']= "Address Type is required";
                $response['code']= 201;
            }else{
                $curl_data = array(
                    'roomno' =>$roomno,
                    'building' =>$building,
                    'street' =>$street,
                    'zone' =>$zone,
                    'latitude' =>$latitude,
                    'longitude' =>$longitude,
                    'address_type' =>$address_type,
                );
                 $this->model->updateData('user_delivery_address', $curl_data, array('id'=>$id));
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'Address Updated Successfully'; 
            }   
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    // Change Password API
    public function update_change_password_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){     
            $user_id = $this->input->post('user_id');
            $password = $this->input->post('password');
            if (empty($user_id)) {
                $response['message'] = 'User Id is required.';
                $response['code'] = 201;
            } else if (empty($password)) {
                $response['message'] = 'Password is required.';
                $response['code'] = 201;
            } else {
                $encryptedpassword = dec_enc('encrypt',$password);
                $this->model->updateData('op_user',array('password'=>$encryptedpassword),array('op_user_id'=>trim($user_id)));
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
    // Forgot Password API
    public function forget_password_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){     
             $contact_no = $this->input->post('contact_no');
            if (empty($contact_no)) {
                $response['message'] = 'contact No is required.';
                $response['code'] = 201;
            } else {
                $check_username_count = $this->model->CountWhereRecord('tbl_users',array('email'=>trim($username)));
                if ($check_username_count > 0) {
                 
                    $password = generatePassword();
                    $encryptedpassword = dec_enc('encrypt',$password);
                    $this->model->updateData('op_user',array('password'=>$encryptedpassword),array('contact_no'=>trim($contact_no)));

                    $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
                    $response['message'] = 'success';
                } else {
                    $response['code'] = 201;
                    $response['message'] = 'Wrong Email Provided';
                }
            }
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    // Add Wishlist API
    public function add_wishlist_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){  
            $user_id = $this->input->post('user_id');
            $product_id = $this->input->post('product_id');

            if(empty($user_id)){
                $response['message'] = 'User Id is required.';
                $response['code'] = 201;
            }else if(empty($product_id)){
                $response['message'] = 'Product Id is required.';
                $response['code'] = 201;
            }else{
                $curl_data=array(
                    'user_id'=>$user_id,
                    'product_id'=>$product_id,
                );
                $this->model->insertData('wishlist',$curl_data);
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
    // Product Details on Id API

    public function product_details_on_id_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){  
            $product_id = $this->input->post('product_id');
            $fk_lang_id = $this->input->post('fk_lang_id');
            if(empty($product_id)){
                $response['message'] = 'Product Id is required.';
                $response['code'] = 201;
            }else{
                 $product_details = $this->model->selectWhereData('product', array('status'=>1,'product_id'=>$product_id,'fk_lang_id'=>$fk_lang_id),array('*'),false);
                 $related_product_details = $this->model->selectWhereData('product_relative', array('status'=>1,'product_id'=>$product_id,),array('*'),false);

                    $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
                    $response['message'] = 'success';
                    $response['product_details'] =$product_details;
                    $response['related_product_details'] =$related_product_details;

            }           
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response); 
    }

    public function get_product_on_search_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){ 
            $search_keyword = $this->input->post('search_keyword');
            $fk_lang_id = $this->input->post('fk_lang_id');
            if(empty($search_keyword)){
                $response['message'] = 'Search is required.';
                $response['code'] = 201;
            }else if(empty($fk_lang_id)){
                $response['message'] = 'Language is required.';
                $response['code'] = 201;
            }else{

                $product_details = $this->superadmin_model->get_product_on_search($search_keyword,$fk_lang_id);
                    $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
                    $response['message'] = 'success';
                    $response['product_details'] =$product_details;
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
                        $sub_category_id = explode(",", $val['sub_category_id']);
                        $sub_category_name = explode(",", $val['sub_category_name']);    
                        $cat_data[$cat_data_key]['sub_category_id'] = $sub_category_id;
                        $cat_data[$cat_data_key]['sub_category_name'] = $sub_category_name;
                        foreach ($sub_category_id as $key1 => $val1) {
                            $child_cat_name = $this->superadmin_model->get_dynamic_childcat($val1,$fk_lang_id);
                            $custom_key_name = $sub_category_name[$key1] . '_' . $val1 . '_' . $sub_cat_status[$key1];
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
    public function send_otp_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) { 
            $contact_no = $this->input->post('contact_no');
            if(empty($contact_no)){
                $response['message'] = 'Contact No is required';
                $response['code'] =201;
            }else{
                    $this->load->library('Smsglobal');

                    $otp = generateOTP();

                    $message = "Your OTP is ".$otp;

                    $curl_data = $this->smsglobal->sms_send($contact_no,$message);
                    echo '<pre>'; print_r($curl_data); exit;
                    $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
                    $response['message'] = 'success';
                    // $response['cat_data'] = $cat_data;
            }
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }

    public function get_user_profile_data_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) { 
            $user_id = $this->input->post('user_id');
            if(empty($user_id)){
                $response['message'] = 'User Id is required';
                $response['code'] =201;
            }else{
                $user_profile_data = $this->model->selectWhereData('op_user', array('status'=>1,'op_user_id'=>$user_id),array('*'));

                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
                $response['user_profile'] = $user_profile_data;
            }
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }

}