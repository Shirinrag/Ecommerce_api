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
            $user_id = $this->input->post('user_id');
            if(empty($fk_lang_id)){
                  $response['message'] ="Language Name is required";
                  $response['code'] = 201;
            }else{
                $this->load->model('superadmin_model');
                  $slider = $this->model->selectWhereData('top_banner', array('status'=>1),array('bottom_id','img_url'),false);
                  foreach ($slider as $slider_key => $slider_row) {
                    $slider[$slider_key]['img_url'] = APPURL.$slider_row['img_url'];
                  }
                  $product_data = $this->superadmin_model->get_all_product_data($fk_lang_id);

                   foreach ($product_data as $product_data_key => $product_data_row) {
                    $product_data[$product_data_key]['image_name'] = APPURL.$product_data_row['image_name'];
                     if($fk_lang_id==1){
                            $product_data[$product_data_key]['product_name'] = $product_data_row['product_name'];
                     }else{
                            $product_data[$product_data_key]['product_name'] = $product_data_row['product_name_ar'];

                     }
                  }

                  $popular = $this->superadmin_model->get_all_popular_product_data($fk_lang_id);
                  if(empty($popular)){
                    $popular=[];
                  }else{
                     foreach ($popular as $popular_key => $popular_row) {
                        $popular[$popular_key]['image_name'] = APPURL.$popular_row['image_name'];
                        if($fk_lang_id==1){
                            $popular[$popular_key]['product_name'] = $popular_row['product_name'];
                         }else{
                                $popular[$popular_key]['product_name'] = $popular_row['product_name_ar'];

                         }
                      }
                  }
                  $featured = $this->superadmin_model->get_all_featured_product_data($fk_lang_id);
                   if(empty($featured)){
                    $featured=[];
                  }else{
                     foreach ($featured as $featured_key => $featured_row) {
                            $featured[$featured_key]['image_name'] = APPURL.$featured_row['image_name'];
                            if($fk_lang_id==1){
                                $featured[$featured_key]['product_name'] = $featured_row['product_name'];
                             }else{
                                $featured[$featured_key]['product_name'] = $featured_row['product_name_ar'];

                             }

                      }

                  }
                  $best_selling =  $this->superadmin_model->get_all_best_selling_product_data($fk_lang_id);
                  if(empty($best_selling)){
                    $best_selling=[];
                  }else{
                     foreach ($best_selling as $best_selling_key => $best_selling_row) {
                        $best_selling[$best_selling_key]['image_name'] = APPURL.$best_selling_row['image_name'];

                         if($fk_lang_id==1){
                            $best_selling[$best_selling_key]['product_name'] = $best_selling_row['product_name'];
                         }else{
                            $best_selling[$best_selling_key]['product_name'] = $best_selling_row['product_name_ar'];
                         }
                      }
                  }
                  $category = $this->model->selectWhereData('category', array('status'=>1,'fk_lang_id'=>$fk_lang_id),array('*'),false);
                  foreach ($category as $category_key => $category_row) {
                    $category[$category_key]['image_path'] = APPURL.$category_row['image_path'];
                        if($fk_lang_id==1){
                            $best_selling[$best_selling_key]['category_name'] = $category_row['category_name'];
                         }else{
                            $best_selling[$best_selling_key]['category_name'] = $category_row['category_name_ar'];
                         }
                  }
                  $response['code'] = REST_Controller::HTTP_OK;
                  $response['status'] = true;
                  $response['message'] = 'success';
                  $response['slider'] = $slider;
                  $response['popular']=$popular;
                  $response['featured']=$featured;
                  $response['best_selling']=$best_selling;
                  $response['product_data'] = $product_data;
                  $response['category'] = $category;
                  if(!empty($user_id)){
                        $response['cart_count'] = get_user_cart_count($user_id);
                        $response['wishlist_count'] = get_user_wishlist_count($user_id);
                  }
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
                     if(!empty($email)){
                         $check_contact_no_count = $this->model->CountWhereRecord('op_user',array('email'=>$email,'status'=>'1'));
                         if ($check_contact_no_count > 0) {
                            $response['message'] = 'Contact No is already exist.';
                            $response['code'] = 201;
                            $response['error_status']="contact";
                        } else {
                                $getTermsConditionId = $this->model->selectWhereData('tbl_about_us',array('module'=>"1",'type'=>'1','is_deleted'=> '1'),array('*'),false,array('id' => 'desc'));
                       
                                $termsCondtnId = (string)count($getTermsConditionId) > 0 ? $getTermsConditionId[0]['id'] : 0;

                                 $otp = generateOTP();
                               
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
                                    'otp'=>54321
                                );
                                $inserted_id = $this->model->insertData('op_user',$curl_data);

                                // $this->load->library('Smsglobal');
                                // $message = "Your OTP is ".$otp;                       
                                // $this->smsglobal->sms_send($contact_no,$message);

                                $response['code'] = REST_Controller::HTTP_OK;
                                $response['status'] = true;
                                $response['message'] = 'success';
                                $response['contact_no'] = $contact_no;
                        }                        
                     }                        
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
                    $login_info = $this->model->selectWhereData('op_user',$login_credentials_data,'*');
                    if(!empty($login_info)){
                         if ($login_info['otp_verify_status'] == 0) {
                            $response['code'] = 205;
                            $response['status'] = "failure";
                            $response['message'] = 'Otp Not Verified';
                            $response['contact_no'] = $login_info['contact_no'];
                            $response['op_user_id'] = $login_info['op_user_id'];
                        } else {
                            $response['code'] = REST_Controller::HTTP_OK;;
                            $response['status'] = true;
                            $response['message'] = 'success';
                            $response['data'] = $login_info;
                            $response['session_token'] = token_get();
                        }
                    } else {
                        $response['code'] = 201;
                        $response['status'] = false;
                        $response['status1'] = "wrong_password";
                        $response['message'] = 'Incorrect Password';
                        $response['data'] = [];
                        $response['session_token'] = "";

                    }      
                }  else {
                    $response['code'] = 201;
                    $response['message'] = 'Incorrect Username';
                    $response['status'] = false;
                    $response['status1'] = "wrong_username";
                    $response['data'] = [];
                    $response['session_token'] = "";


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
                $check_wishlist_count = $this->model->CountWhereRecord('wishlist',array('user_id'=>$user_id,'product_id'=>$product_id));
                 if ($check_wishlist_count > 0) {
                            $response['message'] = 'Product Already exist.';
                            $response['code'] = 201;                        
                } else {
                        $curl_data=array(
                            'user_id'=>$user_id,
                            'product_id'=>$product_id,
                        );
                        $this->model->insertData('wishlist',$curl_data);
                        $response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
                        $response['message'] = 'Added to Wishlist';
                }
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
            $user_id = $this->input->post('user_id');
            if(empty($product_id)){
                $response['message'] = 'Product Id is required.';
                $response['code'] = 201;
            }else{
                $this->load->model('superadmin_model');
                 $product_details = $this->superadmin_model->product_details_on_id($product_id,$fk_lang_id);               
                    $product_details['image_name'] = APPURL.$product_details['image_name'];

                    $img_url = explode(',',$product_details['img_url']);
                    foreach ($img_url as $img_url_key => $img_url_row) {
                            $img_url1[] = APPURL.$img_url_row;   
                      }
                      $product_details['img_url']= implode(',',$img_url1);

                      $related_product_details = $this->superadmin_model->related_product_details_on_id($product_id,$fk_lang_id);
                        foreach ($related_product_details as $related_product_details_key => $related_product_details_row) {
                       $related_product_details[$related_product_details_key]['image_name'] = APPURL.$related_product_details_row['image_name'];

                         if($fk_lang_id==1){
                            $related_product_details[$related_product_details_key]['product_name'] = $category_row['product_name'];
                         }else{
                            $related_product_details[$related_product_details_key]['product_name'] = $category_row['product_name_ar'];
                         }

                      $related_product_img_url = explode(',',$related_product_details_row['img_url']);
                        foreach ($related_product_img_url as $related_product_img_url_key => $related_product_img_url_row) {
                            $related_product_img_url1[]= APPURL.$related_product_img_url_row;   
                            
                        }
                        $related_product_details[$related_product_details_key]['img_url']= implode(',',$related_product_img_url1);                     
                    }

                    $cat_data = $this->superadmin_model->get_dynamic_cat($fk_lang_id);
                    foreach ($cat_data as $cat_data_key => $val) {
                         if($fk_lang_id==1){
                            $cat_data[$cat_data_key]['category_name'] = $val['category_name'];
                         }else{
                            $cat_data[$cat_data_key]['category_name'] = $val['category_name_ar'];
                         }

                        $sub_category_id = explode(",", $val['sub_category_id']);
                        $sub_category_name = explode(",", $val['sub_category_name']);    
                        $sub_category_name_ar = explode(",", $val['sub_category_name_ar']);    
                        $cat_data[$cat_data_key]['sub_category_id'] = $sub_category_id;
                         if($fk_lang_id==1){
                                $cat_data[$cat_data_key]['sub_category_name'] = $sub_category_name;
                         }else{
                                $cat_data[$cat_data_key]['sub_category_name'] = $sub_category_name_ar;

                         }
                        // foreach ($sub_category_id as $key1 => $val1) {
                        //     $child_cat_name = $this->superadmin_model->get_dynamic_childcat($val1,$fk_lang_id);
                        //     $custom_key_name = $sub_category_name[$key1] . '_' . $val1 ;
                        //     $cat_data[$cat_data_key]['child_name'][$custom_key_name] = $child_cat_name;


                        // }
                    }
     
                    $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
                    $response['message'] = 'success';
                    $response['product_details'] =$product_details;
                    $response['related_product_details'] =$related_product_details;
                    $response['cat_data'] =$cat_data;
                       if(!empty($user_id)){
                            $response['cart_count'] = get_user_cart_count($user_id);
                            $response['wishlist_count'] = get_user_wishlist_count($user_id);
                      }

            }           
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response); 
    }

    // Product Search on name API

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
                $this->load->model('superadmin_model');
                $product_details = $this->superadmin_model->get_product_on_search($search_keyword);
                        if($fk_lang_id==1){
                            $product_details['product_name'] = $val['product_name'];
                         }else{
                            $product_details['product_name'] = $val['product_name_ar'];
                         }

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
    // Get Dynamic Menu API
     public function get_dynamic_menu_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) { 
            $fk_lang_id = $this->input->post('fk_lang_id');
            $user_id = $this->input->post('user_id');
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
                            $custom_key_name = $sub_category_name[$key1] . '_' . $val1 ;
                            $cat_data[$cat_data_key]['child_name'][$custom_key_name] = $child_cat_name;
                        }
                    }


                    $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
                    $response['message'] = 'success';
                    $response['cat_data'] = $cat_data;
                    if(!empty($user_id)){
                        $response['cart_count'] = get_user_cart_count($user_id);
                        $response['wishlist_count'] = get_user_wishlist_count($user_id);
                    }
            }
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    // Send OTP API
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
// Verify OTP API
    public function verify_otp_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) { 
            $contact_no = $this->input->post('contact_no');
            $otp = $this->input->post('otp');

            if(empty($contact_no)){
                $response['message'] = 'Contact No is required';
                $response['code'] =201;
            }else if(empty($otp)){
                $response['message'] = 'Otp is required';
                $response['code'] =201;
            }else{
                    $condition = array('contact_no' => $contact_no, 'otp' => $otp);
                    $this->load->model('superadmin_model');
                    $response = $this->superadmin_model->verify_otp('op_user', $contact_no, $otp);
                    if($response['status']==1){
                        $user_login_data =array('contact_no' => $contact_no,);
                        $user_data = $this->model->selectWhereData('op_user', $user_login_data,"*");
                        $response['status'] = true;
                        $response['code'] = 200;
                        $response['message'] = 'Otp Verified Successfullly';
                        $response['data'] = $user_data;
                }
            }
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }

    public function resend_otp_post() {
          $response = array('code' => - 1, 'status' => false, 'message' => '');
          $validate = validateToken();
            if ($validate) { 
            $contact_no = $this->input->post('contact_no');
            $id = $this->input->post('id');
                if (empty($contact_no)) {
                    $response['message'] = "Phone No is required";
                    $response['code'] = 201;
                }else if (empty($id)) {
                    $response['message'] = "Id is required";
                    $response['code'] = 201;
                } else {
                    $check_exist_contact1 = $this->model->check_exist('op_user', array('contact_no' => $contact_no, "status" => "1"));
                    if ($check_exist_contact1['status'] == 0) {
                        $response = array();
                        $response['status'] = 0;
                        $response['message'] = 'Contact No does not exist';
                        $response['code']=201;
                    }else {
                        $otp = mt_rand(100000, 999999);
                        $date = date('d-m-Y H:i:s');
                        $condition = array('op_user_id' => $id,);
                        $get_user_data = $this->model->selectWhereData('op_user', array('op_user_id' => $id,));
                        $sender_id = $get_user_data['op_user_id'];
                        $receiver_id = '';
                        $data = array("otp" => $otp,'contact_no'=>$contact_no);
                        $this->db->where('op_user_id', $sender_id);
                        $this->db->update('op_user', $data);

                        $response['message'] = "Otp Sent Successfully";
                        $response['code'] = 200;
                        $response['status'] = true;
                        // if ($response['status'] == 1) {
                        //         $find = array("+1","(",")");
                        //         $replace = array("");
                        //         $mobile_no = array("$mobile");
                        //         $replace_mobile_no = str_replace($find,$replace,$mobile_no);
                        //     $smstext = "Thank you for registering with us , Your OTP =" . $otp;
                        //     send_sms_check($replace_mobile_no[0], $smstext, 'sms');
                        //     // $sms_data = smslog($sender_id, $receiver_id, $mobile, $smstext, $date, $ip_server);
                        //     // $curl = $this->api_model->comman_insert('tbl_sms_log', $sms_data);
                        // }
                    }
            }
         } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
// User Profile API
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
                $user_profile_data = $this->model->selectWhereData('op_user', array('status'=>"1",'op_user_id'=>$user_id),array('*'));

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

    function add_cart_post(){
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
            $user_id=$this->input->post('user_id');
            $product_id = $this->input->post('product_id');                   
            if (empty($product_id )) {
                $response['message'] = 'Product Id is required.';
                $response['code'] = 201;
            }else if (empty($user_id)) {
                $response['message'] = 'user id is required.';
                $response['code'] = 201;
            }else{ 
                $cart_query = $this->model->selectWhereData('cart',array('user_id'=>$user_id,'product_id'=>$product_id),array('cart_id','qty'));

                if(empty($cart_query['cart_id'])){
                    $insert_data = array(
                        'user_id'=>$user_id,
                        'product_id'=>$product_id,
                        'qty' =>1, 
                    );
                    $cart_id = $this->model->insertData('cart', $insert_data);
                    $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
                    $response['message'] = 'Added To Cart Successfully.';
                    $response['id'] = (string)$cart_id;
                    $response['cart_count'] = get_user_cart_count($user_id);
                } else {
                    $updated_quantity = $cart_query['qty']+1;
                    $update_data = array(
                        'qty'=>$updated_quantity,
                    );
                    $this->model->updateData('cart',$update_data,array('cart_id'=>$cart_query['cart_id']));
                    $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
                    $response['message'] = 'Added To Cart Successfully.';
                    $response['id'] = $cart_query['cart_id'];
                    $response['cart_count'] = get_user_cart_count($user_id);
                } 
            }
        } else {
            $response['message'] = 'Invalid Request';
            $response['code'] = 204;
        }
        echo json_encode($response);
    }

    public function delete_cart_post() {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
            $user_id = $this->input->post('user_id'); 
            $cart_id = $this->input->post('cart_id'); 
            if (empty($user_id)) {
                $response['message'] = 'user id is required.';
                $response['code'] = 201;
            } else if (empty($cart_id)) {
                $response['message'] = 'cart id is required.';
                $response['code'] = 201;
            } else {
                $this->model->deleteData2('cart',array('cart_id' =>$cart_id , 'user_id' =>$user_id));
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;  
                $response['message'] = 'Item Removed Successfully.';
                $response['cart_count'] = get_user_cart_count($user_id);
            }       
        }else {
            $response['message'] = 'Invalid Request';
            $response['code'] = 204;
        }
        echo json_encode($response);
    }

    public function get_all_user_cart_post() {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
            $user_id = $this->input->post('user_id'); 
            $fk_lang_id = $this->input->post('fk_lang_id'); 
            if (empty($user_id)) {
                $response['message'] = 'user id is required.';
                $response['code'] = 201;
            }else if (empty($fk_lang_id)) {
                $response['message'] = 'Language Id is required.';
                $response['code'] = 201;
            }
            else{
                $this->load->model('superadmin_model');
                $cart_data = $this->superadmin_model->get_cart_data($user_id,$fk_lang_id);

                foreach ($cart_data as $cart_data_key => $cart_data_row) {
                        $cart_data[$cart_data_key]['cartPrice'] = $cart_data_row['product_offer_price'] * $cart_data_row['cart_qty'];
                        $cart_data[$cart_data_key]['cartQuantity'] = $cart_data_row['cart_qty'];
                        $cart_data[$cart_data_key]['image_name'] = APPURL.$cart_data_row['image_name'];
                        $sub_total[]= $cart_data[$cart_data_key]['cartPrice'];
                       
                }


                $response['code'] = REST_Controller::HTTP_OK;
                $response['message'] = 'success';
                $response['status'] = true;  
                $response['cart_data'] = $cart_data;  
                $response['sub_total'] = array_sum($sub_total);  
            }       
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }

    public function plus_minus_cart_count_post(){
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
            $user_id = $this->input->post('user_id'); 
            $product_id = $this->input->post('product_id');
            $cart_id = $this->input->post('cart_id'); 
            $quantity = $this->input->post('quantity');
            if (empty($user_id)) {
                $response['message'] = 'User Id is required.';
                $response['code'] = 201;
            } else if (empty($product_id)) {
                $response['message'] = 'Product Id is required.';
                $response['code'] = 201;
            }  else if (empty($cart_id)) {
                $response['message'] = 'Cart Id is required.';
                $response['code'] = 201;
            } else{              
                $cart_data = $this->model->selectwhereData('cart',array('cart_id'=>$cart_id),array('*'));
                // echo '<pre>'; print_r($cart_data); exit;
                $inventory_quantity = $this->model->selectwhereData('inventory',array('product_id'=>$product_id,'status'=>'1'),array('qty'));
                // echo '<pre>'; print_r($inventory_quantity); exit;
                if($quantity > $inventory_quantity['qty']){
                     $response['code'] = 201;
                     $message = "Out of Stock";
                     $response['message'] = $message;
                }else{
                    $previous_quantity = $cart_data['qty'];
                    // echo '<pre>'; print_r($previous_quantity); exit;
                    if(!empty($cart_data) && !empty($quantity)){
                        $update_data = array(
                            'qty'=>$quantity,     
                        );
                        $cart_data = $this->model->updateData('cart',$update_data,array('cart_id'=>$cart_id));  
                    } else {
                        $this->model->deleteData2('cart',array('cart_id' => $cart_id)); 
                    }
                    if($quantity > $previous_quantity){
                        $message = "Added To Cart Successfully.";
                    } else {
                        $message = "Removed From Cart Successfully.";
                    }
                    $this->load->model('superadmin_model');
                    $order_summary_info = $this->superadmin_model->get_order_summary_info($user_id);
                    $total = 0;
                    if(!empty($order_summary_info)){
                        foreach ($order_summary_info as $order_summary_info_key => $order_summary_info_row) {
                            $subtotal = ($order_summary_info_row['qty']*$order_summary_info_row['product_price']);
                           
                            $order_summary_info[$order_summary_info_key]['subtotal'] = custom_number_format($subtotal,2);
                          
                            $total = $total+$subtotal;
                        }                  
                        $response['code'] = REST_Controller::HTTP_OK;;
                        $response['status'] = true;
                        $response['message'] = $message;
                        $response['order_summary_info'] = $order_summary_info;
                        $response['total'] = custom_number_format($total,2);
                        $response['cart_count'] = get_user_cart_count($user_id); 
                    } else {
                        $response['message'] = 'Cart is empty.';
                        $response['code'] = 201;
                        $response['cart_count'] = get_user_cart_count($user_id);
                    }
                }
                
            }       
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }

    public function get_all_whislist_post()
    {
       $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){  
            $user_id = $this->input->post('user_id');
            // $product_id = $this->input->post('product_id');

            if(empty($user_id)){
                $response['message'] = 'User Id is required.';
                $response['code'] = 201;
            }else{
                $this->load->model('superadmin_model');
               $wishlist_data = $this->superadmin_model->get_wishlist_data($user_id);
                $wishlist_count = $this->model->CountWhereInRecord('wishlist',array('user_id'=>$user_id,'status'=>'1'));
               foreach ($wishlist_data as $wishlist_data_key => $wishlist_data_row) {
                $wishlist_data[$wishlist_data_key]['image_name'] = APPURL.$wishlist_data_row['image_name'];

                    $whislist_data_img_url = explode(',',$wishlist_data_row['img_url']);
                    foreach ($whislist_data_img_url as $whislist_data_img_url_key => $whislist_data_img_url_row) {
                            $whislist_data_img_url1[]= APPURL.$whislist_data_img_url_row;   
                            
                      }
                       $wishlist_data[$wishlist_data_key]['img_url']= implode(',',$whislist_data_img_url1);
                  
               }
                $response['code'] = REST_Controller::HTTP_OK;
                $response['message'] = 'success';
                $response['status'] = true;  
                $response['wishlist_data'] = $wishlist_data;  
                $response['wishlist_count'] = $wishlist_count;  
            }                 
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response); 
    }

    public function delete_whislist_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
            $user_id = $this->input->post('user_id'); 
            $id = $this->input->post('id'); 
            if (empty($user_id)) {
                $response['message'] = 'user id is required.';
                $response['code'] = 201;
            } else if (empty($id)) {
                $response['message'] = 'Id is required.';
                $response['code'] = 201;
            } else {
                $this->model->deleteData2('wishlist',array('id' =>$id , 'user_id' =>$user_id));
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;  
                $response['message'] = 'Item Removed Successfully.';
            }       
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }

    public function add_user_comment_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
            $user_id = $this->input->post('user_id'); 
            $product_id = $this->input->post('product_id'); 
            $comment = $this->input->post('comment'); 
            if (empty($user_id)) {
                $response['message'] = 'user id is required.';
                $response['code'] = 201;
            } else if (empty($product_id)) {
                $response['message'] = 'Product Id is required.';
                $response['code'] = 201;
            }  else if (empty($comment)) {
                $response['message'] = 'comment is required.';
                $response['code'] = 201;
            } else {
                $curl_data = array(
                    'user_id' =>$user_id,
                    'product_id' =>$product_id,
                    'comment' =>$comment,
                );
                 $inserted_id = $this->model->insertData('product_comment',$curl_data);
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

    public function get_search_product_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
              $fk_lang_id = $this->input->post('fk_lang_id'); 

             if (empty($fk_lang_id)) {
                $response['message'] = 'Language Id is required.';
                $response['code'] = 201;
            } else {
                $this->load->model('superadmin_model');
                $product_data = $this->superadmin_model->get_search_product($fk_lang_id);
                foreach ($product_data as $product_data_key => $product_data_row) {
                   $product_data[$product_data_key]['image_name'] = APPURL.$product_data_row['image_name'];
                }
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;  
                $response['message'] = 'success';
                $response['product_data'] =$product_data;
            }       
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }

    public function category_data_post()
    {
       $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
              $fk_lang_id = $this->input->post('fk_lang_id'); 

             if (empty($fk_lang_id)) {
                $response['message'] = 'Language Id is required.';
                $response['code'] = 201;
            } else {
                $category = $this->model->selectWhereData('category', array('status'=>1,'fk_lang_id'=>$fk_lang_id),array('*'),false);
                  foreach ($category as $category_key => $category_row) {
                    $category[$category_key]['image_path'] = APPURL.$category_row['image_path'];
                  }
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;  
                $response['message'] = 'success';
                $response['category'] =$category;
            }       
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }

    public function check_out_api()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
              $fk_lang_id = $this->input->post('fk_lang_id'); 
              $user_id = $this->input->post('user_id'); 

             if (empty($fk_lang_id)) {
                $response['message'] = 'Language Id is required.';
                $response['code'] = 201;
            }else if (empty($user_id)) {
                $response['message'] = 'User Id is required.';
                $response['code'] = 201;
            } else {
                $this->load->model('superadmin_model');
                $cart_data = $this->superadmin_model->get_cart_data($user_id,$fk_lang_id);
                foreach ($cart_data as $cart_data_key => $cart_data_row) {
                        $cart_data[$cart_data_key]['cartPrice'] = $cart_data_row['product_offer_price'] * $cart_data_row['qty'];
                        $cart_data[$cart_data_key]['cartQuantity'] = $cart_data_row['qty'];
                        $cart_data[$cart_data_key]['image_name'] = APPURL.$cart_data_row['image_name'];
                }


                $user_address = $this->model->selectWhereData('user_delivery_address',array('user_id'=>$user_id,'status'=>'1'),array('*'),false);
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;  
                $response['message'] = 'success';
                $response['cart_product_details'] =$cart_data;
                $response['user_address'] =$user_address;
            }       
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }

    public function get_product_on_category_post()
    {
       $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
              $fk_lang_id = $this->input->post('fk_lang_id'); 
              $category_id = $this->input->post('category_id'); 

             if (empty($fk_lang_id)) {
                $response['message'] = 'Language Id is required.';
                $response['code'] = 201;
            } else  if (empty($category_id)) {
                $response['message'] = 'Category Id is required.';
                $response['code'] = 201;
            } else {
                $product_data = $this->model->selectWhereData('product',array('fk_lang_id'=>$fk_lang_id,'category_id'=>$category_id),array('*'),false);
                foreach ($product_data as $product_data_key => $product_data_row) {
                   $product_data[$product_data_key]['image_name'] = APPURL.$product_data_row['image_name'];
                }
                $sub_category_data = $this->model->selectWhereData('subcategory',array('fk_lang_id'=>$fk_lang_id,'category_id'=>$category_id),array('*'),false);


                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;  
                $response['message'] = 'success';
                $response['product_data'] =$product_data;
                $response['sub_category_data'] =$sub_category_data;
            }       
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }

}