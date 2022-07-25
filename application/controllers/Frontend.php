<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// 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
ini_set("memory_limit", "-1");
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
                  $slider = $this->model->selectWhereData('top_banner', array('status'=>1),array('*'),false);
                  foreach ($slider as $slider_key => $slider_row) {
                    $slider[$slider_key]['img_url'] = APPURL.$slider_row['img_url'];
                  }
                  $product_data = $this->superadmin_model->get_all_product_data($fk_lang_id);

                   foreach ($product_data as $product_data_key => $product_data_row) {
                    $product_data[$product_data_key]['image_name'] = APPURL.$product_data_row['image_name'];
                     if($fk_lang_id==1){
                            $product_data[$product_data_key]['product_name'] = $product_data_row['product_name'];
                            $product_data[$product_data_key]['currency_in_english'] = $product_data_row['currency_in_english'];

                     }else{
                            $product_data[$product_data_key]['product_name'] = $product_data_row['product_name_ar'];
                            $product_data[$product_data_key]['currency_in_english'] = $product_data_row['currency_in_arabic'];
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
                             $popular[$popular_key]['currency_in_english'] = $popular_row['currency_in_english'];
                         }else{
                                $popular[$popular_key]['product_name'] = $popular_row['product_name_ar'];
                                 $popular[$popular_key]['currency_in_english'] = $popular_row['currency_in_arabic'];
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
                                $featured[$featured_key]['currency_in_english'] = $featured_row['currency_in_english'];
                             }else{
                                $featured[$featured_key]['product_name'] = $featured_row['product_name_ar'];
                                 $featured[$featured_key]['currency_in_english'] = $featured_row['currency_in_arabic'];
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
                             $best_selling[$best_selling_key]['currency_in_english'] = $best_selling_row['currency_in_english'];
                         }else{
                            $best_selling[$best_selling_key]['product_name'] = $best_selling_row['product_name_ar'];
                             $best_selling[$best_selling_key]['currency_in_english'] = $best_selling_row['currency_in_arabic'];
                         }
                      }
                  }
                  $category = $this->model->selectWhereData('category', array('status'=>1,),array('*'),false);
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
               if ($check_contact_no_count > 0) {
                    $response['message'] = 'Contact No is already exist.';
                    $response['code'] = 201;
                    $response['error_status']="contact";
                } else {
                     if(!empty($email)){
                         $check_contact_no_count = $this->model->CountWhereRecord('op_user',array('email'=>$email,'status'=>'1'));
                         if ($check_contact_no_count > 0) {
                            $response['message'] = 'Enail is already exist.';
                            $response['code'] = 201;
                            $response['error_status']="email";
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
                 $product_details = $this->superadmin_model->product_details_on_id($product_id);               
                    $product_details['image_name'] = APPURL.$product_details['image_name'];
                    if($fk_lang_id==1){
                            $product_details['product_name'] = $product_details['product_name'];
                            $product_details['description'] = $product_details['description'];
                             $product_details['currency_in_english'] = $product_details['currency_in_english'];
                             $product_details['currency_in_english'] = $product_details['currency_in_english'];
                         }else{
                            $product_details['product_name'] = $product_details['product_name_ar'];
                            $product_details['description'] = $product_details['description_ar'];
                             $product_details['currency_in_english'] = $product_details['currency_in_arabic'];
                         }
                    $img_url = explode(',',$product_details['img_url']);
                    foreach ($img_url as $img_url_key => $img_url_row) {
                            $img_url1[] = APPURL.$img_url_row;   
                      }
                      $product_details['img_url']= implode(',',$img_url1);

                      $related_product_details = $this->superadmin_model->related_product_details_on_id($product_id,$fk_lang_id);
                        foreach ($related_product_details as $related_product_details_key => $related_product_details_row) {
                       $related_product_details[$related_product_details_key]['image_name'] = APPURL.$related_product_details_row['image_name'];

                         if($fk_lang_id==1){
                            $related_product_details[$related_product_details_key]['product_name'] = $related_product_details_row['product_name'];
                            $related_product_details[$related_product_details_key]['description'] = $related_product_details_row['description'];
                         }else{
                            $related_product_details[$related_product_details_key]['product_name'] = $related_product_details_row['product_name_ar'];
                            $related_product_details[$related_product_details_key]['currency_in_english'] = $related_product_details_row['currency_in_english'];
                            $related_product_details[$related_product_details_key]['description'] = $related_product_details_row['description_ar'];
                             $related_product_details[$related_product_details_key]['currency_in_english'] = $related_product_details_row['currency_in_arabic'];
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
            // echo '<pre>'; print_r($search_keyword); exit;
            $fk_lang_id = $this->input->post('fk_lang_id');
            if(empty($search_keyword)){
                $response['message'] = 'Search is required.';
                $response['code'] = 201;
            }else{
                $this->load->model('superadmin_model');
                $this->db->where("product_name like '%".$search_keyword."%' ");
                $records = $this->db->get('product')->result_array();
                foreach($records as $row ){
                     $data[] = array("product_name"=>$row['product_name']);
                }                      
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
                $response['product_name'] =$data;
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
                    foreach ($sub_category_id as $key1 => $val1) {
                        $child_cat_name = $this->superadmin_model->get_dynamic_childcat($val1,$fk_lang_id);
                            foreach ($child_cat_name as $child_cat_name_key => $child_cat_name_row) {
                                if($fk_lang_id==1){
                                    $child_cat_name[$child_cat_name_key]['child_category_name'] = $child_cat_name_row['child_category_name'];
                                }else{
                                      $child_cat_name[$child_cat_name_key]['child_category_name'] = $child_cat_name_row['child_category_name_ar'];
                                }
                            }
                        if($fk_lang_id==1){
                                $custom_key_name = $sub_category_name[$key1] . '_' . $val1 ;
                                $cat_data[$cat_data_key]['child_name'][$custom_key_name] = $child_cat_name;
                        }else{
                             $custom_key_name = $sub_category_name_ar[$key1] . '_' . $val1 ;
                                $cat_data[$cat_data_key]['child_name'][$custom_key_name] = $child_cat_name;
                        }
                    }
                }
                if(!empty($user_id)){
                    $this->load->model('superadmin_model');
                    $cart_data = $this->superadmin_model->get_cart_data($user_id,$fk_lang_id);
                    $sub_total= [];
                    foreach ($cart_data as $cart_data_key => $cart_data_row) {
                        $cart_data[$cart_data_key]['cartPrice'] = $cart_data_row['product_offer_price'] * $cart_data_row['cart_qty'];
                        $cart_data[$cart_data_key]['cartQuantity'] = $cart_data_row['cart_qty'];
                        $cart_data[$cart_data_key]['image_name'] = APPURL.$cart_data_row['image_name'];
                        @$sub_total[]= $cart_data[$cart_data_key]['cartPrice'];                
                    }
                    $sub_total_sum = array_sum($sub_total); 
                    $cart_count = get_user_cart_count($user_id);
                    $wishlist_count = get_user_wishlist_count($user_id);
                } else {
                    $cart_count = '';
                    $wishlist_count = '';
                    $cart_data = [];
                    $sub_total_sum = '';
                }
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
                $response['cat_data'] = $cat_data;
                $response['cart_data'] = $cart_data;
                $response['sub_total'] = $sub_total_sum;
                $response['cart_count'] = $cart_count;
                $response['wishlist_count'] = $wishlist_count;
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

                        if($fk_lang_id==1){
                                $cart_data[$cart_data_key]['product_name'] = $cart_data_row['product_name'];
                                $cart_data[$cart_data_key]['currency_in_english'] = $cart_data_row['currency_in_english'];

                             }else{
                                    $cart_data[$cart_data_key]['product_name'] = $cart_data_row['product_name_ar'];
                                    $cart_data[$cart_data_key]['currency_in_english'] = $cart_data_row['currency_in_arabic'];
                             }               
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
                $inventory_quantity = $this->model->selectwhereData('inventory',array('product_id'=>$product_id,'status'=>'1'),array('qty'));
                //echo '<pre>'; print_r($inventory_quantity); exit;
                if($quantity > $inventory_quantity['qty']){
                     $response['code'] = 201;
                     $message = "Out of Stock";
                     $response['message'] = $message;
                }else{
                    $previous_quantity = $cart_data['qty'];
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
                            $subtotal = $order_summary_info_row['cart_qty']*$order_summary_info_row['product_offer_price'];
                           
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

    public function check_out_api_post()
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
                $total = 0;
                foreach ($cart_data as $cart_data_key => $cart_data_row) {
                        $cart_data[$cart_data_key]['cartPrice'] = $cart_data_row['product_offer_price'] * $cart_data_row['cart_qty'];
                        $cart_data[$cart_data_key]['cartQuantity'] = $cart_data_row['cart_qty'];
                        $cart_data[$cart_data_key]['image_name'] = APPURL.$cart_data_row['image_name'];
                        $sub_total= $cart_data[$cart_data_key]['cartPrice'];
                        $total = $total + $sub_total;                       
                }
                              
                $user_address = $this->model->selectWhereData('user_delivery_address',array('user_id'=>$user_id,'status'=>'1'),array('*'),false);
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;  
                $response['message'] = 'success';
                $response['cart_product_details'] =$cart_data;
                $response['total'] = custom_number_format($total,2);
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
                $product_data = $this->model->selectWhereData('product',array('category_id'=>$category_id),array('*'),false);
                if(!empty($product_data)){
                        foreach ($product_data as $product_data_key => $product_data_row) {
                           $product_data[$product_data_key]['image_name'] = APPURL.$product_data_row['image_name'];
                           if($fk_lang_id==1){
                                    $product_data[$product_data_key]['product_name'] = $product_data_row['product_name'];
                                    $product_data[$product_data_key]['currency_in_english'] = $product_data_row['currency_in_english'];

                             }else{
                                    $product_data[$product_data_key]['product_name'] = $product_data_row['product_name_ar'];
                                    $product_data[$product_data_key]['currency_in_english'] = $product_data_row['currency_in_arabic'];
                             }
                        }
                }
                $sub_category_data = $this->model->selectWhereData('subcategory',array('fk_lang_id'=>$fk_lang_id,'category_id'=>$category_id),array('*'),false);

                if(empty($product_data)){
                    $product_data=[];
                }else{
                    $product_data = $product_data;
                }

                if(empty($sub_category_data)){
                    $sub_category_data=[];
                }else{
                    $sub_category_data = $sub_category_data;
                }


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
    public function get_product_on_sub_category_post()
    {
       $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
              $fk_lang_id = $this->input->post('fk_lang_id'); 
              $sub_category_id = $this->input->post('sub_category_id'); 

             if (empty($fk_lang_id)) {
                $response['message'] = 'Language Id is required.';
                $response['code'] = 201;
            } else  if (empty($sub_category_id)) {
                $response['message'] = 'Sub Category Id is required.';
                $response['code'] = 201;
            } else {
                $product_data = $this->model->selectWhereData('product',array('sub_category_id'=>$sub_category_id),array('*'),false);
                if(!empty($product_data)){
                        foreach ($product_data as $product_data_key => $product_data_row) {
                            $product_data[$product_data_key]['image_name'] = APPURL.$product_data_row['image_name'];
                            if($fk_lang_id==1){
                                $product_data[$product_data_key]['product_name'] = $product_data_row['product_name'];
                                $product_data[$product_data_key]['currency_in_english'] = $product_data_row['currency_in_english'];

                             }else{
                                    $product_data[$product_data_key]['product_name'] = $product_data_row['product_name_ar'];
                                    $product_data[$product_data_key]['currency_in_english'] = $product_data_row['currency_in_arabic'];
                             }
                        }
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
    public function get_product_on_child_category_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
              $fk_lang_id = $this->input->post('fk_lang_id'); 
              $child_category_id = $this->input->post('child_category_id'); 

             if (empty($fk_lang_id)) {
                $response['message'] = 'Language Id is required.';
                $response['code'] = 201;
            } else  if (empty($child_category_id)) {
                $response['message'] = 'Sub Category Id is required.';
                $response['code'] = 201;
            } else {
                $product_data = $this->model->selectWhereData('product',array('child_category_id'=>$child_category_id),array('*'),false);
                foreach ($product_data as $product_data_key => $product_data_row) {
                   $product_data[$product_data_key]['image_name'] = APPURL.$product_data_row['image_name'];
                            if($fk_lang_id==1){
                                $product_data[$product_data_key]['product_name'] = $product_data_row['product_name'];
                                $product_data[$product_data_key]['currency_in_english'] = $product_data_row['currency_in_english'];

                             }else{
                                    $product_data[$product_data_key]['product_name'] = $product_data_row['product_name_ar'];
                                    $product_data[$product_data_key]['currency_in_english'] = $product_data_row['currency_in_arabic'];
                             }
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

    public function add_payment_data_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
              $user_id = $this->input->post('user_id'); 
              $fk_product_id = json_decode($this->input->post('fk_product_id'),true); 
              $order_id = $this->input->post('order_id'); 
              $fk_address_id = $this->input->post('fk_address_id'); 
              $quantity = json_decode($this->input->post('quantity'),true); 
              $unit_price = json_decode($this->input->post('unit_price'),true); 
              $total = json_decode($this->input->post('total'),true); 
              $sub_total = $this->input->post('sub_total'); 
              $tax = $this->input->post('tax'); 
              $grand_total = $this->input->post('grand_total'); 
                  
            if (empty($user_id)) {
                $response['message'] = 'User Id is required.';
                $response['code'] = 201;
            } else if(empty($fk_product_id)) {
                $response['message'] = 'Product Id is required.';
                $response['code'] = 201;
            }else if(empty($order_id)) {
                $response['message'] = 'Order id is required.';
                $response['code'] = 201;
            }else if(empty($fk_address_id)) {
                $response['message'] = 'Address id is required.';
                $response['code'] = 201;
            }else if(empty($quantity)) {
                $response['message'] = 'Quantity is required.';
                $response['code'] = 201;
            }else if(empty($unit_price)) {
                $response['message'] = 'Price is required.';
                $response['code'] = 201;
            }else if(empty($total)) {
                $response['message'] = 'Total is required.';
                $response['code'] = 201;
            }else if(empty($sub_total)) {
                $response['message'] = 'Sub Total is required.';
                $response['code'] = 201;
            }else if(empty($grand_total)) {
                $response['message'] = 'Grand Total is required.';
                $response['code'] = 201;
            }else {
                foreach ($fk_product_id as $fk_product_id_key => $fk_product_id_row) {
                    $curl_data = array(
                        'fk_user_id'=>$user_id,
                        'order_id'=>$order_id,
                        'order_no'=>mt_rand(100000,999999),
                        'fk_address_id'=>$fk_address_id,
                        'fk_product_id'=>$fk_product_id_row,
                        'quantity'=>$quantity[$fk_product_id_key],
                        'unit_price'=>$unit_price[$fk_product_id_key],
                        'total'=>$total[$fk_product_id_key],
                        'sub_total'=>$sub_total,
                        'tax'=>$tax,                        
                        'grand_total'=>$grand_total,
                        'date'=>date('Y-m-d'),
                    );
                    $inserted_id = $this->model->insertData('tbl_payment',$curl_data);
                }
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

    public function get_confirm_order_details_post()
    {
         $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
                $order_id = $this->input->post('order_id');

                if(empty($order_id)){
                    $response['message'] = "Order Id is required";
                    $response['code'] = 201;
                }else{
                    $order_details = $this->model->selectWhereData('tbl_payment', array('order_id'=>$order_id),array('*'),false);
                    $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
                    $response['message'] = 'success';
                    $response['order_details'] = $order_details;
                }
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }

    public function place_order_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
              $user_id = $this->input->post('user_id'); 
              $fk_product_id = json_decode($this->input->post('fk_product_id'),true); 
              $order_id = $this->input->post('order_id'); 
              $id = $this->input->post('id'); 
              $fk_address_id = $this->input->post('fk_address_id'); 
              $quantity = json_decode($this->input->post('quantity'),true); 
              $unit_price = json_decode($this->input->post('unit_price'),true); 
              $total = json_decode($this->input->post('total'),true); 
              $sub_total = $this->input->post('sub_total'); 
              $tax = $this->input->post('tax'); 
              $grand_total = $this->input->post('grand_total'); 
              $payment_type = $this->input->post('payment_type'); 
              $order_no = json_decode($this->input->post('order_no'),true); 
            if (empty($user_id)) {
                $response['message'] = 'User Id is required.';
                $response['code'] = 201;
            } else if(empty($fk_product_id)) {
                $response['message'] = 'Product Id is required.';
                $response['code'] = 201;
            }else if(empty($order_no)) {
                $response['message'] = 'Order No is required.';
                $response['code'] = 201;
            }else if(empty($fk_address_id)) {
                $response['message'] = 'Address id is required.';
                $response['code'] = 201;
            }else if(empty($quantity)) {
                $response['message'] = 'Quantity is required.';
                $response['code'] = 201;
            }else if(empty($unit_price)) {
                $response['message'] = 'Price is required.';
                $response['code'] = 201;
            }else if(empty($sub_total)) {
                $response['message'] = 'Sub Total is required.';
                $response['code'] = 201;
            }else if(empty($grand_total)) {
                $response['message'] = 'Grand Total is required.';
                $response['code'] = 201;
            }else {
                foreach ($fk_product_id as $fk_product_id_key => $fk_product_id_row) {
                    $curl_data = array(
                        'fk_user_id'=>$user_id,
                        'fk_product_id'=>$fk_product_id_row,
                        'order_number'=>$order_no[$fk_product_id_key],
                        'order_id'=>$order_id,
                        'fk_address_id'=>$fk_address_id,
                        'quantity'=>$quantity[$fk_product_id_key],
                        'unit_price'=>$unit_price[$fk_product_id_key],
                        'total'=>$total[$fk_product_id_key],
                        'sub_total'=>$sub_total,
                        'tax'=>$tax,
                        'grand_total'=>$grand_total,
                        'date'=>date('Y-m-d'),
                    );
                    $inserted_id = $this->model->insertData('order_data',$curl_data);

                    $status_data = array(
                        'fk_order_id'=>$inserted_id,
                        'status'=>1,
                    );
                    $this->model->insertData('tbl_order_status',$status_data);

                    $last_total_quantity = $this->model->selectWhereData('inventory', array('product_id'=>$fk_product_id_row,'used_status'=>1),array('qty'),);

                    if(!empty($last_total_quantity)){

                         $inventory_data = array('used_status' => 0,);
                         $this->db->where('product_id', $fk_product_id_row);
                         $this->db->update('inventory', $inventory_data);

                         $inventory_data = array('product_id' => $fk_product_id_row, 'qty' => $last_total_quantity['qty'] - $quantity[$fk_product_id_key],'deduct_qty'=>$quantity[$fk_product_id_key],'date' => date('m/d/Y'),'used_status'=>1);
                         $this->model->insertData('inventory', $inventory_data);
                    }
                     $this->db->where('user_id', $user_id);
                     $this->db->where('product_id', $fk_product_id_row);
                     $this->db->delete('cart');

                      $update_data = array('payment_type'=>$payment_type);
                      $this->db->where('order_id', $order_id);
                      $this->db->update('tbl_payment', $update_data);

                       $order_data = $this->superadmin_model->order_data($order_id);

                       error_reporting(0);
        
                        ini_set('memory_limit', '256M');
                                                
                        $pdfFilePath = FCPATH . "uploads/'".$user_id."'_invoice_english_.pdf";
                        $this->load->library('m_pdf');
                        $data = $order_data;
                        $html = $this->load->view('invoice_english', array('data'=>$data), true);
                        $mpdf = new mPDF();
                        $mpdf->SetDisplayMode('fullpage');
                        $mpdf->AddPage('P', 'A4');
                       
                        $mpdf->WriteHTML($html);
                        ob_end_clean();
                        $mpdf->Output($pdfFilePath, "F");

                         $pdfFilePath = FCPATH . "uploads/'".$user_id."'_invoice_arabic_.pdf";
                        $this->load->library('m_pdf');
                        $data = $order_data;
                        $html = $this->load->view('invoice_arabic', array('data'=>$data), true);
                        $mpdf = new mPDF();
                        $mpdf->SetDisplayMode('fullpage');
                        $mpdf->AddPage('P', 'A4');
                       
                        $mpdf->WriteHTML($html);
                        ob_end_clean();
                        $mpdf->Output($pdfFilePath, "F");
                }
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
    public function order_history_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
                $user_id = $this->input->post('user_id');

                if(empty($user_id)){
                    $response['message'] = "User Id is required";
                    $response['code'] = 201;
                }else{
                    $this->load->model('superadmin_model');
                    $order_history = $this->superadmin_model->order_history($user_id);
                    $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
                    $response['message'] = 'success';
                    $response['order_history'] = $order_history;
                }
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function order_history_on_order_id_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
                $id = $this->input->post('id');
                if(empty($id)){
                    $response['message'] = "Id is required";
                    $response['code'] = 201;
                }else{
                    $this->load->model('superadmin_model');
                    $order_history = $this->superadmin_model->order_history_on_order_id($id);
                    $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
                    $response['message'] = 'success';
                    $response['order_history'] = $order_history;
                }
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }

    public function get_all_address_on_user_id_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
                $user_id = $this->input->post('user_id');

                if(empty($user_id)){
                    $response['message'] = "User Id is required";
                    $response['code'] = 201;
                }else{
                    $address_data = $this->model->selectWhereData('user_delivery_address',array('user_id'=>$user_id),array('*'),false);
                    $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
                    $response['message'] = 'success';
                    $response['address_data'] = $address_data;
                }
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }

    public function get_address_on_id_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
                $address_id = $this->input->post('id');

                if(empty($address_id)){
                    $response['message'] = "Address Id is required";
                    $response['code'] = 201;
                }else{
                    $address_data = $this->model->selectWhereData('user_delivery_address',array('id'=>$address_id),array('*'));
                    $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
                    $response['message'] = 'success';
                    $response['address_data'] = $address_data;
                }
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }

    public function get_product_name_data_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
                $product_name = $this->input->post('product_name');

                if(empty($product_name)){
                    $response['message'] = " Product Name is required";
                    $response['code'] = 201;
                }else{
                    $product_details = $this->model->selectWhereData('product',array('product_name'=>$product_name),array('*'));
                    $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
                    $response['message'] = 'success';
                    $response['product_details'] = $product_details;
                }
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }

    public function get_delivery_charges_on_address_id_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
                $address_id = $this->input->post('address_id');
                $user_id = $this->input->post('user_id');
                $fk_lang_id = $this->input->post('fk_lang_id');
                if(empty($address_id)){
                    $response['message']="Address id is required";
                    $response['code'] = 201;
                }else{
                    $this->load->model('superadmin_model');
                    $user_data = $this->model->selectWhereData('user_delivery_address',array('id'=>$address_id),array('latitude','longitude'));
                    $client_lat_long= get_lat_long();
                    $distance_calculation = distance1($client_lat_long['client_latitude'],$client_lat_long['client_longitude'],$user_data['latitude'],$user_data['longitude'],"K");
                   $distance_calculation = round($distance_calculation);
              
                    if($distance_calculation > 50)
                    {
                         $rate = $this->superadmin_model->get_rate(50);
                    }else{
                         $rate = $this->superadmin_model->get_rate(round($distance_calculation));
                    }

                     $cart_data = $this->superadmin_model->get_cart_data($user_id,$fk_lang_id);

                $total = 0;
                foreach ($cart_data as $cart_data_key => $cart_data_row) {
                        $cart_data[$cart_data_key]['cartPrice'] = $cart_data_row['product_offer_price'] * $cart_data_row['cart_qty'];
                        $cart_data[$cart_data_key]['cartQuantity'] = $cart_data_row['cart_qty'];
                        $sub_total= $cart_data[$cart_data_key]['cartPrice'];
                        $total = $total + $sub_total + $rate['rate'];                       
                }
                    $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
                    $response['message'] = 'success';
                    $response['rate'] = $rate['rate'];
                    $response['total'] = custom_number_format($total,2);
            }
                
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }




   
}