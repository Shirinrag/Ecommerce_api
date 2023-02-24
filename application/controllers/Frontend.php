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
             $all_product = $this->input->post('all_product');
            //  print_r($all_product);die;
            if(empty($fk_lang_id)){
                  $response['message'] ="Language Name is required";
                  $response['code'] = 201;
            }else{
                $this->load->model('superadmin_model');
                  $slider = $this->model->selectWhereData('top_banner', array('status'=>1,'active_inactive'=>'1'),array('*'),false);
                  if(!empty($slider)){
                        foreach ($slider as $slider_key => $slider_row) {
                             $slider[$slider_key]['img_url'] = APPURL.$slider_row['img_url'];
                        }
                  }else{
                      $slider = [];
                  }
                 if($all_product!=1){
                  $product_data = $this->superadmin_model->get_all_product_data($fk_lang_id,$limit,$start);
                   foreach ($product_data as $product_data_key => $product_data_row) {
                        $wishlist_data = $this->model->CountWhereRecord('wishlist',array('product_id'=>$product_data_row['product_id'],'user_id'=>@$user_id));
                        $wishlist_id = $this->model->selectwhereData('wishlist',array('product_id'=>$product_data_row['product_id'],'user_id'=>@$user_id),array('id'));
                        $cart_data = $this->model->CountWhereRecord('cart',array('product_id'=>$product_data_row['product_id'],'user_id'=>@$user_id));
                        $cart_id = $this->model->selectwhereData('cart',array('product_id'=>$product_data_row['product_id'],'user_id'=>@$user_id),array('cart_id'));
                            if($wishlist_data==1){
                                $wishlist_data = true;
                                $wishlist_id = $wishlist_id['id'];
                            }else{
                                $wishlist_data = false;
                                $wishlist_id = "";
                            }
                            
                            if($cart_data==1){
                                $cart_data = true;
                                $cart_id = $cart_id['cart_id'];
                            }else{
                                $cart_data = false;
                                $cart_id = "";
                            }
                        $product_data[$product_data_key]['wishlist_product'] = $wishlist_data;
                        $product_data[$product_data_key]['cart_product'] = $cart_data;
                        $product_data[$product_data_key]['wishlist_id'] = $wishlist_id;
                        $product_data[$product_data_key]['cart_id'] = $cart_id;
                        
                        // if(empty($product_data_row['image_name'])){
                        //     $product_data[$product_data_key]['image_name'] = APPURL.'No_Image_Available.jpg';
                        // }else{
                            $explode_image=explode(',',$product_data_row['img_url']);
                            // $product_data[$product_data_key]['image_name'] = APPURL.$explode_image[0];
                            $product_data[$product_data_key]['image_name'] = APPURL.$product_data_row['image_name'];
                        // }
                        
                         if($fk_lang_id==1){
                                $product_data[$product_data_key]['product_name'] = $product_data_row['product_name'];
                                $product_data[$product_data_key]['currency_in_english'] = $product_data_row['currency_in_english'];
                                $product_data[$product_data_key]['add_to_cart'] ='Add to Cart';
                                $product_data[$product_data_key]['label'] ='NEW';
                                $product_data[$product_data_key]['font-size'] = "";
                                $product_data[$product_data_key]['label-font-size'] = "";
                         }else{
                                $product_data[$product_data_key]['product_name'] = $product_data_row['product_name_ar'];
                                $product_data[$product_data_key]['currency_in_english'] = $product_data_row['currency_in_arabic'];
                                $product_data[$product_data_key]['add_to_cart'] ='إضافة الى السلة';
                                $product_data[$product_data_key]['font-size'] = "style='font-size:15px;'";
                                $product_data[$product_data_key]['label-font-size'] = "style='font-size:20px;'";
                                $product_data[$product_data_key]['label'] ='جديد';
                         }
                  }
                 }
                  $popular = $this->superadmin_model->get_all_popular_product_data($fk_lang_id);
                  //print_r($popular);die();
                  if(empty($popular)){
                    $popular=[];
                  }else{
                     foreach ($popular as $popular_key => $popular_row) {
                         $explode_image=explode(',',$popular_row['img_url']);
                            //  $popular[$popular_key]['image_name'] = APPURL.$explode_image[0];
                             $popular[$popular_key]['image_name'] = APPURL.$popular_row['image_name'];
                           
                             $wishlist_data = $this->model->CountWhereRecord('wishlist',array('product_id'=>$popular_row['product_id'],'user_id'=>$user_id));
                             $wishlist_id = $this->model->selectwhereData('wishlist',array('product_id'=>$popular_row['product_id'],'user_id'=>$user_id),array('id'));
                             $cart_data = $this->model->CountWhereRecord('cart',array('product_id'=>$popular_row['product_id'],'user_id'=>@$user_id));
                             $cart_id = $this->model->selectwhereData('cart',array('product_id'=>$popular_row['product_id'],'user_id'=>@$user_id),array('cart_id'));
                                if($wishlist_data==1){
                                    $wishlist_data = true;
                                    $wishlist_id = $wishlist_id['id'];
                                }else{
                                    $wishlist_data = false;
                                    $wishlist_id = "";
                                }
                                
                                if($cart_data==1){
                                    $cart_data = true;
                                    $cart_id = $cart_id['cart_id'];
                                }else{
                                    $cart_data = false;
                                    $cart_id = "";
                                }
                            $popular[$popular_key]['wishlist_id'] = $wishlist_id;
                            $popular[$popular_key]['wishlist_product'] = $wishlist_data;
                            $popular[$popular_key]['cart_id'] = $cart_id;
                            $popular[$popular_key]['cart_product'] = $cart_data;
                            if($fk_lang_id==1){
                                $popular[$popular_key]['product_name'] = $popular_row['product_name'];
                                $popular[$popular_key]['currency_in_english'] = $popular_row['currency_in_english'];
                                $popular[$popular_key]['add_to_cart'] ='Add to Cart';
                                $popular[$popular_key]['font-size'] = "";
                                $popular[$popular_key]['label'] ='NEW';
                                $popular[$popular_key]['label-font-size'] = "";
                                
                             }else{
                                    $popular[$popular_key]['product_name'] = $popular_row['product_name_ar'];
                                    $popular[$popular_key]['currency_in_english'] = $popular_row['currency_in_arabic'];
                                    $popular[$popular_key]['add_to_cart'] ='إضافة الى السلة';
                                    $popular[$popular_key]['font-size'] = "style='font-size:14px;'";
                                    $popular[$popular_key]['label-font-size'] = "style='font-size:20px;'";
                                    $popular[$popular_key]['label'] ='جديد';
                             }
                        }
                  }
                  $featured = $this->superadmin_model->get_all_featured_product_data($fk_lang_id);
                   if(empty($featured)){
                        $featured=[];
                    }else{
                         foreach ($featured as $featured_key => $featured_row) {
                            $explode_image=explode(',',$featured_row['img_url']);
                            // $featured[$featured_key]['image_name'] = APPURL.$explode_image[0];
                               $featured[$featured_key]['image_name'] = APPURL.$featured_row['image_name'];

                                
                                $wishlist_data = $this->model->CountWhereRecord('wishlist',array('product_id'=>$featured_row['product_id'],'user_id'=>$user_id));
                                $wishlist_id = $this->model->selectwhereData('wishlist',array('product_id'=>$featured_row['product_id'],'user_id'=>$user_id),array('id'));
                                $cart_data = $this->model->CountWhereRecord('cart',array('product_id'=>$featured_row['product_id'],'user_id'=>@$user_id));
                                $cart_id = $this->model->selectwhereData('cart',array('product_id'=>$featured_row['product_id'],'user_id'=>@$user_id),array('cart_id'));
                                if($wishlist_data==1){
                                    $wishlist_data = true;
                                    $wishlist_id = $wishlist_id['id'];
                                }else{
                                    $wishlist_data = false;
                                    $wishlist_id = "";
                                }
                                
                                if($cart_data==1){
                                    $cart_data = true;
                                    $cart_id = $cart_id['cart_id'];
                                }else{
                                    $cart_data = false;
                                    $cart_id = "";
                                }
                                
                                $featured[$featured_key]['wishlist_id'] = $wishlist_id;
                                $featured[$featured_key]['wishlist_product'] = $wishlist_data;
                                $featured[$featured_key]['cart_id'] = $cart_id;
                                $featured[$featured_key]['cart_product'] = $cart_data;
                                if($fk_lang_id==1){
                                    $featured[$featured_key]['product_name'] = $featured_row['product_name'];
                                    $featured[$featured_key]['currency_in_english'] = $featured_row['currency_in_english'];
                                    $featured[$featured_key]['add_to_cart'] ='Add to Cart';
                                      $featured[$featured_key]['font-size'] = "";
                                      $featured[$featured_key]['label-font-size'] = "";
                                      $featured[$featured_key]['label'] ='NEW';
                                       
                                    
                                 }else{
                                    $featured[$featured_key]['product_name'] = $featured_row['product_name_ar'];
                                    $featured[$featured_key]['currency_in_english'] = $featured_row['currency_in_arabic'];
                                    $featured[$featured_key]['add_to_cart'] ='إضافة الى السلة';
                                    $featured[$featured_key]['font-size'] = "style='font-size:14px;'";
                                    $featured[$featured_key]['label'] ='جديد';
                                    $featured[$featured_key]['label-font-size'] = "style='font-size:20px;'";
                                 }
                          }
                  }
                  $best_selling =  $this->superadmin_model->get_all_best_selling_product_data($fk_lang_id);
                  
                  if(empty($best_selling)){
                    $best_selling=[];
                  }else{
                     foreach ($best_selling as $best_selling_key => $best_selling_row) {
                        $wishlist_data = $this->model->CountWhereRecord('wishlist',array('product_id'=>$best_selling_row['product_id'],'user_id'=>$user_id));
                    //   echo "<pre>"; print_r($wishlist_data);
                        $wishlist_id = $this->model->selectwhereData('wishlist',array('product_id'=>$best_selling_row['product_id'],'user_id'=>$user_id),array('id'));
                          $cart_data = $this->model->CountWhereRecord('cart',array('product_id'=>$best_selling_row['product_id'],'user_id'=>@$user_id));
                                $cart_id = $this->model->selectwhereData('cart',array('product_id'=>$best_selling_row['product_id'],'user_id'=>@$user_id),array('cart_id'));
                                if($wishlist_data==1){
                                    $wishlist_data = true;
                                    $wishlist_id = $wishlist_id['id'];
                                }else{
                                    $wishlist_data = false;
                                    $wishlist_id = "";
                                }
                                
                                if($cart_data==1){
                                    $cart_data = true;
                                    $cart_id = $cart_id['cart_id'];
                                }else{
                                    $cart_data = false;
                                    $cart_id = "";
                                }
                                
                        $best_selling[$best_selling_key]['wishlist_id'] = $wishlist_id;
                        $best_selling[$best_selling_key]['wishlist_product'] = $wishlist_data;
                        $best_selling[$best_selling_key]['cart_id'] = $cart_id;
                        $best_selling[$best_selling_key]['cart_product'] = $cart_data;
                      
                        $explode_image=explode(',',$best_selling_row['img_url']);
                         //echo "<pre>";print_r($explode_image[0]);

                        // $best_selling[$best_selling_key]['image_name'] = APPURL.$explode_image[0];
                        $best_selling[$best_selling_key]['image_name'] = APPURL.$best_selling_row['image_name'];
                    
                         if($fk_lang_id==1){
                            $best_selling[$best_selling_key]['product_name'] = $best_selling_row['product_name'];
                            $best_selling[$best_selling_key]['currency_in_english'] = $best_selling_row['currency_in_english'];
                            $best_selling[$best_selling_key]['add_to_cart'] ='Add to Cart';
                            $best_selling[$best_selling_key]['font-size'] = "";
                            $best_selling[$best_selling_key]['label'] ='NEW';
                            $best_selling[$best_selling_key]['label-font-size'] = ""; 
                          
                         }else{
                            $best_selling[$best_selling_key]['product_name'] = $best_selling_row['product_name_ar'];
                            $best_selling[$best_selling_key]['currency_in_english'] = $best_selling_row['currency_in_arabic'];
                            $best_selling[$best_selling_key]['add_to_cart'] ='إضافة الى السلة';     
                            $best_selling[$best_selling_key]['font-size'] = "style='font-size:14px;'";
                            $best_selling[$best_selling_key]['label-font-size'] = "style='font-size:20px;'";
                            $best_selling[$best_selling_key]['label'] ='جديد';
                             
                         }
                      }
                    //   die;
                  }
                  $category = $this->model->selectWhereData('category', array('status'=>1,'active_inactive'=>'1','category_type !='=>'Gift Card'),array('*'),false,array('sort_order'));
                
                  if(!empty($category)){
                      foreach ($category as $category_key => $category_row) {
                        $category[$category_key]['image_path'] = APPURL.$category_row['image_path'];
                            if($fk_lang_id==1){
                                $category[$category_key]['category_name'] = $category_row['category_name'];
                                $category[$category_key]['font-size'] = "";
                                 
                             }else{
                                $category[$category_key]['category_name'] = $category_row['category_name_ar'];
                                $category[$category_key]['font-size'] = "style='font-size:20px;margin-top:10px'";
                             }
                      }
                  }
                  if(!empty($user_id)){
                        $cart_count = get_user_cart_count($user_id);
                        $wishlist_count = get_user_wishlist_count($user_id);
                  }else{
                        $cart_count ="";
                        $wishlist_count ="";
                  }
                  $response['code'] = REST_Controller::HTTP_OK;
                  $response['status'] = true;
                  $response['message'] = 'success';
                  $response['slider'] = $slider;
                  $response['popular']=$popular;
                  $response['featured']=$featured;
                  $response['best_selling']=$best_selling;
                  if($all_product!=1){
                        $response['product_data'] = $product_data;
                  }
                  $response['category'] = $category;
                  $response['cart_count'] = $cart_count;
                  $response['wishlist_count'] = $wishlist_count;
                 
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
            $fk_lang_id = $this->input->post('fk_lang_id');

            if(empty($user_name)){
                $response['message']= "User Name is required";
                $response['code']= 201;
            }else if (empty($contact_no)) {
                $response['message'] = 'Contact Number should be 8 number digits.';
                $response['code'] = 201;
            } else if(empty($password)){
                $response['message']= "Pasword is required";
                $response['code']= 201;
            }else{               
                $check_contact_no_count = $this->model->CountWhereRecord('op_user',array('contact_no'=>$contact_no,'status'=>'1'));
                // 9307382744
               if ($check_contact_no_count > 0) {
                    $response['message'] = 'Contact No is already exist.';
                    $response['code'] = 201;
                    $response['error_status']="contact";
                } else {
                      if(!empty($email)){
                          $check_email_count = $this->model->CountWhereRecord('op_user',array('email'=>$email,'status'=>'1'));
                        //   echo '<pre>'; print_r($check_email_count); exit;
                         if ($check_email_count > 0) {
                             $response['message'] = 'Email is already exist.';
                             $response['code'] = 201;
                             $response['error_status']="email";
                         } else {
                                $getTermsConditionId = $this->model->selectWhereData('tbl_about_us',array('module'=>"1",'type'=>'1','is_deleted'=> '1'),array('*'),false,array('id' => 'desc'));
                                $termsCondtnId = (string)count($getTermsConditionId) > 0 ? $getTermsConditionId[0]['id'] : 0;
                                 // $otp = 1234;                               
                                 $otp = generateOTP();                               
                                $curl_data = array(
                                    'user_name' =>$user_name,
                                    'email' =>$email,
                                    'password'=>dec_enc('encrypt',$password),
                                    'contact_no'=>$contact_no,
                                    'role_id' => '2',
                                    // 'device_id' => $device_id,
                                    'device_type' => $device_type,
                                    'notifn_topic' => $contact_no . 'ecom',
                                    // 'terms_condition' => $terms_cond != '' ? $terms_cond : 1,
                                    // 'terms_conditn_id' => $terms_cond != '' ? $termsCondtnId : 0,
                                    // 'app_version' => $app_version,
                                    // 'app_build_no' => $app_build_no,
                                    'otp'=>$otp,
                                    'date'=>date("d/m/Y")
                                );
                                $inserted_id = $this->model->insertData('op_user',$curl_data);

                                $this->load->library('Smsglobal');
                                $message = "Your Circuit Store Verification code is ".$otp." Happy Shopping !";                       
                                $this->smsglobal->sms_send($contact_no,$message);

                                $response['code'] = REST_Controller::HTTP_OK;
                                $response['status'] = true;
                                if($fk_lang_id==1){
                                    $response['message'] = 'Register Successfull & OTP Send Successfully';
                                }else{
                                    $response['message'] ='تسجيل بنجاح';
                                }
                                $response['contact_no'] = $contact_no;
                        }       
                     }else{
                          $getTermsConditionId = $this->model->selectWhereData('tbl_about_us',array('module'=>"1",'type'=>'1','is_deleted'=> '1'),array('*'),false,array('id' => 'desc'));
                                $termsCondtnId = (string)count($getTermsConditionId) > 0 ? $getTermsConditionId[0]['id'] : 0;
                                 // $otp = 1234;                               
                                 $otp = generateOTP();                               
                                $curl_data = array(
                                    'user_name' =>$user_name,
                                    'email' =>$email,
                                    'password'=>dec_enc('encrypt',$password),
                                    'contact_no'=>$contact_no,
                                    'role_id' => '2',
                                    // 'device_id' => $device_id,
                                    'device_type' => $device_type,
                                    'notifn_topic' => $contact_no . 'ecom',
                                    // 'terms_condition' => $terms_cond != '' ? $terms_cond : 1,
                                    // 'terms_conditn_id' => $terms_cond != '' ? $termsCondtnId : 0,
                                    // 'app_version' => $app_version,
                                    // 'app_build_no' => $app_build_no,
                                    'otp'=>$otp,
                                    'date'=>date("d/m/Y")
                                );
                                $inserted_id = $this->model->insertData('op_user',$curl_data);

                                $this->load->library('Smsglobal');
                                $message = "Your Circuit Store Verification code is ".$otp." Happy Shopping !";                       
                                $this->smsglobal->sms_send($contact_no,$message);

                                $response['code'] = REST_Controller::HTTP_OK;
                                $response['status'] = true;
                                if($fk_lang_id==1){
                                    $response['message'] = 'Register Successfull & OTP Send Successfully';
                                }else{
                                    $response['message'] ='تسجيل بنجاح';
                                }
                                $response['contact_no'] = $contact_no; 
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
            $fk_lang_id=$this->input->post('fk_lang_id');
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
                      "password" => dec_enc('encrypt',$password),
                    );
                    $login_info = $this->model->selectWhereData('op_user',$login_credentials_data,'*');
                  if(!empty($login_info)){
                         if ($login_info['otp_verify_status'] == 0) {
                            $response['code'] = 205;
                            $response['status'] = false;
                            if($fk_lang_id==1){
                                $response['message'] = 'Otp Not Verified';
                            }else{
                                $response['message'] = 'لم يتم التحقق من OTP';
                            }
                            $response['contact_no'] = $login_info['contact_no'];
                            $response['op_user_id'] = $login_info['op_user_id'];
                        }else if($login_info['active_inactive']==0){
                            $response['code'] = 201;
                            $response['status'] = false;
                            $response['status1'] = "wrong_password";
                            if($fk_lang_id==1){
                                $response['message'] = 'User Not Active';
                            }else{
                                $response['message'] = 'المستخدم غير نشط';
                            }
                            $response['session_token'] = "";
                        } else {
                            $update_data=array(
                                'login_time_date'=>date('d/m/Y h:i:s'),
                            );
                            $this->model->updateData('op_user',$update_data,array('op_user_id'=>$login_info['op_user_id']));
                            $response['code'] = REST_Controller::HTTP_OK;;
                            $response['status'] = true;
                            if($fk_lang_id==1){
                                $response['message'] = 'Logged In Successfully';
                            }else{
                                $response['message'] ='تم تسجيل الدخول بنجاح';
                            }
                            $response['data'] = $login_info;
                            $response['session_token'] = token_get();
                        }
                    }else {
                        $response['code'] = 201;
                        $response['status'] = false;
                        $response['status1'] = "wrong_password";
                        if($fk_lang_id==1){
                            $response['message'] = 'Incorrect Password';
                        }else{
                            $response['message'] = 'كلمة سر خاطئة  ';
                        }
                        $response['session_token'] = "";
                    }      
                }  else {
                    $response['code'] = 201;
                    if($fk_lang_id==1){
                            $response['message'] = 'Incorrect Contact No';
                    }else{
                            $response['message'] = 'رقم الاتصال غير صحيح';
                    }
                    $response['status'] = false;
                    $response['status1'] = "wrong_username";
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
            $user_name= $this->input->post('user_name');
            // $last_name= $this->input->post('last_name');
            $email= $this->input->post('email');
            $contact_no= $this->input->post('contact_no');
           
           if(empty($user_name)){
                $response['message'] = "User Name is required";
                $response['code'] =201;
           }else if(empty($email)){
                $response['message']= "Email is required";
                $response['code']= 201;
            }else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $response['message'] = 'Provide valid email address.';
                $response['code'] = 201;
            }else if (empty($contact_no)) {
                $response['message'] = 'Contact No is required';
                $response['code'] = 201;
            }else{
                  $check_user_count = $this->model->CountWhereRecord('op_user', array('email'=>$email,'op_user_id!='=>$op_user_id,'status'=>'1'));                   
                if($check_user_count > 0){
                    $response['message'] = 'Email Already Exist.....!';
                    $response['code'] = 201;
                }else {
                    $curl_data = array(
                        'user_name' =>$user_name,
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
                    // 'roomno' =>$roomno,
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

    public function save_ratings_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
            $user_id = $this->input->post('user_id');
            $rating = $this->input->post('ratings');
            $product_id = $this->input->post('product_id');
          
            if(empty($user_id)){
                $response['message']= "User Id is required";
                $response['code']= 201;
            }else if(empty($rating)){
                $response['message']= "Ratings are required";
                $response['code']= 201;
            }else if(empty($product_id)){
                $response['message']= "Product Id is required";
                $response['code']= 201;
            }else{
                $curl_data = array(
                    'user_id' =>$user_id,
                    'ratings' =>$rating,
                    'product_id' =>$product_id,
                    'created_at' =>date('d/m/Y H:i:s'),
                 
                );
                $inserted_id = $this->model->insertData('ratings',$curl_data);
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'Ratings Added Successfully'; 
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
            // $roomno = $this->input->post('roomno');
            $building = $this->input->post('building');
            $street = $this->input->post('street');
            $zone = $this->input->post('zone');
            $latitude = $this->input->post('latitude');
            $longitude = $this->input->post('longitude');
            $address_type = $this->input->post('address_type');
            if(empty($id)){
                $response['message']= "Id is required";
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
                    // 'roomno' =>$roomno,
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
            $fk_lang_id = $this->input->post('fk_lang_id');
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
                if($fk_lang_id==1){
                     $response['message'] = 'Password Changed Successfullly';
                }else{
                     $response['message'] = 'تم تغيير كلمة المرور بنجاح  ';

                }
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
                $check_username_count = $this->model->CountWhereRecord('op_user',array('contact_no'=>trim($contact_no)));
                if ($check_username_count > 0) {
                 
                    $password = generateOTP();
                    $encryptedpassword = dec_enc('encrypt',$password);
                    $this->model->updateData('op_user',array('password'=>$encryptedpassword),array('contact_no'=>trim($contact_no)));
                     $this->load->library('Smsglobal');
                                $message = "Your Temporary Password is ".$password."";                       
                                $this->smsglobal->sms_send($contact_no,$message);

                    $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
                    $response['message'] = 'success';
                } else {
                    $response['code'] = 201;
                    $response['message'] = 'Wrong Contact Number Provided';
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
            $fk_lang_id = $this->input->post('fk_lang_id');

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
                        if($fk_lang_id==1){
                            $response['message'] = 'Added to Wishlist';
                        }else{
                            $response['message'] = 'تم الإضافة الى قائمة المفضلة ';
                        }
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
                    $wishlist_data = $this->model->CountWhereRecord('wishlist',array('product_id'=>$product_id,'user_id'=>$user_id));
                    $wishlist_id = $this->model->selectwhereData('wishlist',array('product_id'=>$product_id,'user_id'=>$user_id),array('id'));
                    $cart_quantity = $this->model->selectWhereData('cart',array('product_id'=>$product_id,'user_id'=>$user_id),array('qty'));
                     $cart_data = $this->model->CountWhereRecord('cart',array('product_id'=>$product_id,'user_id'=>$user_id));
                     $cart_id = $this->model->selectwhereData('cart',array('product_id'=>$product_id,'user_id'=>@$user_id),array('cart_id'));
                    if(empty($cart_quantity['qty'])){
                        $cart_quantity['qty'] = 0;
                    }else{
                        $cart_quantity['qty'] = $cart_quantity['qty'];
                    }
                    
                    if(!empty($user_id)){
                        $cart_count = get_user_cart_count($user_id);
                        $wishlist_count = get_user_wishlist_count($user_id);
                  }else{
                        $cart_count ="";
                        $wishlist_count ="";
                  }
                 
                    if($wishlist_data==1){
                        $wishlist_data = true;
                        $wishlist_id = $wishlist_id['id'];
                    }else{
                        $wishlist_data = false;
                        $wishlist_id = "";
                    }
                    
                    if($cart_data==1){
                        $cart_data = true;
                        $cart_id = $cart_id['cart_id'];
                    }else{
                        $cart_data = false;
                        $cart_id = "";
                    }
                    $cart_id = $this->model->selectWhereData('cart',array('product_id'=>$product_id,'user_id'=>$user_id),array('cart_id'));
                            if(!empty($cart_id)){
                                $cart_id = $cart_id['cart_id'];
                            }else{
                                $cart_id = "";
                            }
                            
                    $product_details['wishlist_product'] = $wishlist_data;    
                    $product_details['cart_product'] = $cart_data;    
                    $product_details['wishlist_id'] = $wishlist_id;       
                    $product_details['cart_quantity'] = $cart_quantity['qty'];   
                    $product_details['cart_id']  = $cart_id;
                    $product_details['image_name'] = APPURL.$product_details['image_name'];
                         if($fk_lang_id==1){
                            $product_details['product_name'] = $product_details['product_name'];
                            $product_details['description'] = $product_details['description'];
                             $product_details['currency_in_english'] = $product_details['currency_in_english'];
                             $product_details['currency_in_english'] = $product_details['currency_in_english'];
                             $product_details['add_to_cart'] = 'Add to Cart';
                             $product_details['categories'] = 'CATEGORIES';
                            
                         }else{
                            $product_details['product_name'] = $product_details['product_name_ar'];
                            $product_details['description'] = $product_details['description_ar'];
                             $product_details['currency_in_english'] = $product_details['currency_in_arabic'];
                             $product_details['add_to_cart'] = 'إضافة الى السلة';
                             $product_details['categories'] = 'الاقسام';
                             $product_details['related_product'] = 'منتجات ذات صلة';
                         }
                    $img_url = explode(',',$product_details['img_url']);
                    $pos =0;
                    foreach ($img_url as $img_url_key => $img_url_row) {
                            $img_url1[] = APPURL.$img_url_row;   
                      }
                      $img_url1 = array_merge(array_slice($img_url1, 0, $pos), array($product_details['image_name']), array_slice($img_url1, $pos));
                      $product_details['img_url']= implode(',',$img_url1);

                      $related_product_details = $this->superadmin_model->related_product_details_on_id($product_id);
                        foreach ($related_product_details as $related_product_details_key => $related_product_details_row) {

                            $wishlist_data = $this->model->CountWhereRecord('wishlist',array('product_id'=>$related_product_details_row['product_id'],'user_id'=>$user_id));
                            $cart_data = $this->model->CountWhereRecord('cart',array('product_id'=>$related_product_details_row['product_id'],'user_id'=>$user_id));
                            $wishlist_id = $this->model->selectwhereData('wishlist',array('product_id'=>$related_product_details_row['product_id'],'user_id'=>$user_id),array('id'));
                            
                            if($wishlist_data==1){
                                $wishlist_data = true;
                                $wishlist_id = $wishlist_id['id'];
                            }else{
                                $wishlist_data = false;
                                $wishlist_id = "";
                            }
                            
                            if($cart_data==1){
                                $cart_data = true;
                            }else{
                                $cart_data = false;
                            }
                            
                            $related_product_details[$related_product_details_key]['wishlist_product'] = $wishlist_data;
                             $related_product_details[$related_product_details_key]['cart_product'] = $cart_data;
                              $related_product_details[$related_product_details_key]['wishlist_id'] = $wishlist_id;

                       $related_product_details[$related_product_details_key]['image_name'] = APPURL.$related_product_details_row['image_name'];

                         if($fk_lang_id==1){
                            $related_product_details[$related_product_details_key]['product_name'] = $related_product_details_row['product_name'];
                            $related_product_details[$related_product_details_key]['description'] = $related_product_details_row['description'];
                             $product_details[$related_product_details_key] = 'RELATED PRODUCTS';
                         }else{
                            $related_product_details[$related_product_details_key]['product_name'] = $related_product_details_row['product_name_ar'];
                            $related_product_details[$related_product_details_key]['currency_in_english'] = $related_product_details_row['currency_in_english'];
                            $related_product_details[$related_product_details_key]['description'] = $related_product_details_row['description_ar'];
                            $related_product_details[$related_product_details_key]['currency_in_english'] = $related_product_details_row['currency_in_arabic'];
                            $product_details[$related_product_details_key] = 'منتجات ذات صلة';
                         }

                    //   $related_product_img_url = explode(',',$related_product_details_row['img_url']);
                    //     foreach ($related_product_img_url as $related_product_img_url_key => $related_product_img_url_row) {
                    //         $related_product_img_url1[]= APPURL.$related_product_img_url_row;   
                            
                    //     }
                    //     $related_product_details[$related_product_details_key]['img_url']= implode(',',$related_product_img_url1);                     
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
            $fk_lang_id = $this->input->post('fk_lang_id');
            // echo '<pre>'; print_r($search_keyword); exit;
            $fk_lang_id = $this->input->post('fk_lang_id');
            if(empty($search_keyword)){
                $response['message'] = 'Search is required.';
                $response['code'] = 201;
            }else{
                if($fk_lang_id==1){
                    $this->load->model('superadmin_model');
                    $this->db->where("product_name like '%".$search_keyword."%' ");
                    $this->db->where('status',1);
                    $this->db->where('product_status','1');  
                  
                    $records = $this->db->get('product')->result_array();
                    foreach($records as $row ){
                         $data[] = array("product_name"=>$row['product_name']);
                    }    
                }else{
                    $this->load->model('superadmin_model');
                    $this->db->where("product_name_ar like '%".$search_keyword."%' ");
                    $this->db->where('status',1);
                    $this->db->where('product_status','1');  
                  
                    $records = $this->db->get('product')->result_array();
                    foreach($records as $row ){
                         $data[] = array("product_name"=>$row['product_name_ar']);
                    }    
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
    public function get_related_product_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $temp=array();
        $validate = validateToken();
        if($validate){ 
            $bottom_id = $this->input->post('bottom_id');
            $user_id = $this->input->post('user_id');
            $fk_lang_id = $this->input->post('fk_lang_id');
            if(empty($bottom_id)){
                $response['message'] = 'bottom_id is required.';
                $response['code'] = 201;
            }else{
                $this->load->model('superadmin_model');
                $user_profile_data = $this->model->selectWhereData('top_banner', array('status'=>"1",'active_inactive'=>"1",'bottom_id'=>$bottom_id),array('*'));
                    $relatable_product=explode(",",$user_profile_data['relatable_products']);
                   
                    foreach($relatable_product as $key => $val)
                    {
                        $product_data[] = $this->model->selectWhereData('product', array('status'=>1,'product_id '=>$val,'product_status'=>'1'),array('*'));
                    
                         $product_data = array_filter($product_data);
                    }
                   
                   
                    foreach($product_data as $product_data_key => $product_data_row){
                            $product_data[$product_data_key]['image_name'] = APPURL.$product_data_row['image_name'];
                        
                            if($fk_lang_id==1){
                                $product_data[$product_data_key]['product_name'] = $product_data_row['product_name'];
                                $product_data[$product_data_key]['currency_in_english'] =  $product_data_row['currency_in_english'];
                                $product_data[$product_data_key]['add_to_cart'] ='Add to Cart';
                            }else{
                                  $product_data[$product_data_key]['product_name'] = $product_data_row['product_name_ar'];
                                  $product_data[$product_data_key]['currency_in_english'] = $product_data_row['currency_in_arabic'];
                                  $product_data[$product_data_key]['add_to_cart'] ='إضافة الى السلة';
                            }
                             
                            $wishlist_data = $this->model->CountWhereRecord('wishlist',array('product_id'=>$product_data_row['product_id'],'user_id'=>$user_id));
                            $wishlist_id = $this->model->selectwhereData('wishlist',array('product_id'=>$product_data_row['product_id'],'user_id'=>$user_id),array('id'));
                            $cart_data = $this->model->CountWhereRecord('cart',array('product_id'=>$product_data_row['product_id'],'user_id'=>@$user_id));
                            $cart_id = $this->model->selectwhereData('cart',array('product_id'=>$product_data_row['product_id'],'user_id'=>@$user_id),array('cart_id'));
                            if($wishlist_data==1){
                                $wishlist_data = 1;
                                $wishlist_id = $wishlist_id['id'];
                            }else{
                                $wishlist_data = 0;
                                $wishlist_id = "";
                            }
                            
                            if($cart_data==1){
                                $cart_data = true;
                                $cart_id = $cart_id['cart_id'];
                            }else{
                                $cart_data = false;
                                $cart_id = "";
                            }
                            $product_data[$product_data_key]['wishlist_data'] = $wishlist_data;
                            $product_data[$product_data_key]['wishlist_id'] = $wishlist_id;
                            $product_data[$product_data_key]['cart_product'] = $cart_data;
                            $product_data[$product_data_key]['cart_id'] = $cart_id;
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
                $response['banner'] = $user_profile_data;
                $response['product_details'] = $product_data;
                $response['cat_data'] = $cat_data;
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
                $brand_data= $this->model->selectWhereData('brands',array('status'=>'1','active_inactive'=>'1'),array('*'),false);
                foreach ($brand_data as $brand_data_key => $val1) {
                    if($fk_lang_id==1){
                        $brand_data[$brand_data_key]['brand_name'] =  $val1['brand_name']; 
                    }else
                    {
                       $brand_data[$brand_data_key]['brand_name'] =  $val1['brand_name_ar'];  
                    }
                      
                    $brand_data[$brand_data_key]['img_url'] =  APPURL.$val1['img_url'];   
                }
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
                     $user_profile_data = $this->model->selectwhereData('op_user',array('op_user_id'=>$user_id),array('user_name')); 
                    $sub_total_sum = array_sum($sub_total); 
                    $cart_count = get_user_cart_count($user_id);
                    $wishlist_count = get_user_wishlist_count($user_id);
                    $user_profile_data = $user_profile_data['user_name'];
                } else {
                    $cart_count = '';
                    $wishlist_count = '';
                    $cart_data = [];
                    $sub_total_sum = '';
                    $user_profile_data = '';
                }
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
                $response['cat_data'] = $cat_data;
                $response['cart_data'] = $cart_data;
                $response['sub_total'] = $sub_total_sum;
                $response['cart_count'] = $cart_count;
                $response['wishlist_count'] = $wishlist_count;
                $response['user_profile_data'] = $user_profile_data;
                $response['brand_data'] = $brand_data;
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
            $fk_lang_id = $this->input->post('fk_lang_id');
            if(empty($contact_no)){
                $response['message'] = 'Contact No is required';
                $response['code'] =201;
            }else if(empty($otp)){
                $response['message'] = 'Otp is required';
                $response['code'] =201;
            }else{
                    $condition = array('contact_no' => $contact_no, 'otp' => $otp);
                    $this->load->model('superadmin_model');
                    $response = $this->superadmin_model->verify_otp('op_user', $contact_no, $otp, $fk_lang_id);
                    if($response['status']==1){
                        $user_login_data =array('contact_no' => $contact_no, "status" =>"1", "otp_verify_status" =>"1");
                        $user_data = $this->model->selectWhereData('op_user', $user_login_data,"*");
                        
                         $update_data=array(
                                'login_time_date'=>date('d/m/Y h:i:s'),
                            );
                        $this->model->updateData('op_user',$update_data,array('op_user_id'=>$user_data['op_user_id']));
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
                } else {
                    $check_exist_contact1 = $this->model->check_exist('op_user', array('contact_no' => $contact_no, "status" => "1"));
                    if ($check_exist_contact1['status'] == 0) {
                        $response = array();
                        $response['status'] = 0;
                        $response['message'] = 'Contact No does not exist';
                        $response['code']=201;
                    }else {
                        // $otp = 1234;
                        $otp = generateOTP();
                        $date = date('d/m/Y H:i:s');
                       
                        $get_user_data = $this->model->selectWhereData('op_user', array('contact_no' => $contact_no,));
                        $sender_id = $get_user_data['op_user_id'];
                        $receiver_id = '';
                        $data = array("otp" => $otp,'contact_no'=>$contact_no);
                        $this->db->where('op_user_id', $sender_id);
                        $this->db->update('op_user', $data);

                            $this->load->library('Smsglobal');
                            $message = "Your Circuit Store verifictaion code is".$otp." Happy Shopping !";                       
                            $this->smsglobal->sms_send($contact_no,$message);

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
                $response['cart_count'] = get_user_cart_count($user_id);
                $response['wishlist_count'] = get_user_wishlist_count($user_id);
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
            $fk_lang_id = $this->input->post('fk_lang_id');                   
            if (empty($product_id )) {
                $response['message'] = 'Product Id is required.';
                $response['code'] = 201;
            }else if (empty($user_id)) {
                $response['message'] = 'user id is required.';
                $response['code'] = 201;
            }else{ 
                $cart_query = $this->model->selectWhereData('cart',array('user_id'=>$user_id,'product_id'=>$product_id),array('cart_id','qty'));
                $product_type_info = $this->model->selectWhereData('product',array('product_id'=>$product_id),array('category_type'));
                $inventory_quantity = $this->model->selectWhereData('inventory',array('product_id'=>$product_id,'used_status'=>'1'),array('qty'));
                $product_type = $product_type_info['category_type'];
                if($product_type == 'Normal'){
                    $add_to_cart = 'enabled';
                } else {
                    $add_to_cart = 'disabled';
                }
                if(empty($cart_query['cart_id'])){
                    $insert_data = array(
                        'user_id'=>$user_id,
                        'product_id'=>$product_id,
                        'qty' =>1, 
                    );
                    $cart_id = $this->model->insertData('cart', $insert_data);
                    $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
                    if($fk_lang_id==1){
                        $response['message'] = 'Added To Cart Successfully.';
                    }else{
                        $response['message'] = 'تم الإضافة إلى السلة';
                    }
                    $response['id'] = (string)$cart_id;
                    $response['cart_count'] = get_user_cart_count($user_id);
                    $response['add_to_cart_status'] = $add_to_cart;
                } else {
                    $updated_quantity = $cart_query['qty']+1;
                    $update_data = array(
                        'qty'=>$updated_quantity,
                    );
                    if($add_to_cart == 'enabled'){
                        if($updated_quantity <= $inventory_quantity['qty']){
                            $this->model->updateData('cart',$update_data,array('cart_id'=>$cart_query['cart_id']));
                            if($fk_lang_id==1){
                                $message = 'Added To Cart Successfully.';
                            }else{
                                $message = 'تم الإضافة إلى السلة';
                            }
                        }else{
                            if($fk_lang_id==1){
                                $message = 'Out of Stock';
                            }
                        }
                        
                    } else {
                        if($fk_lang_id==1){
                            $message = 'Only One Quantity Of Gift Card Is Allowed.';
                        }else{
                            $message = 'مسموح بكمية واحدة فقط من بطاقات الهدايا. ';
                        }
                    }
                    
                    $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
                    $response['message'] = $message;
                    $response['id'] = $cart_query['cart_id'];
                    $response['cart_count'] = get_user_cart_count($user_id);
                    $response['add_to_cart_status'] = $add_to_cart;
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
                $sub_total = [];
                foreach ($cart_data as $cart_data_key => $cart_data_row) {
                        $cart_data[$cart_data_key]['cartPrice'] = $cart_data_row['product_offer_price'] * $cart_data_row['cart_qty'];
                        $cart_data[$cart_data_key]['cartQuantity'] = $cart_data_row['cart_qty'];
                        $cart_data[$cart_data_key]['image_name'] = APPURL.$cart_data_row['image_name'];
                         
                        if(!empty($cart_data[$cart_data_key]['cartPrice'])){
                            $sub_total[]= $cart_data[$cart_data_key]['cartPrice'];
                        }else{
                            $sub_total = 0;
                        }

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
                $inventory_quantity = $this->model->selectwhereData('inventory',array('product_id'=>$product_id,'used_status'=>'1'),array('qty'));
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

    // public function get_all_whislist_post()
    // {
    //   $response = array('code' => - 1, 'status' => false, 'message' => '');
    //     $validate = validateToken();
    //     if($validate){  
    //         $user_id = $this->input->post('user_id');
    //         $fk_lang_id = $this->input->post('fk_lang_id');
    //         if(empty($user_id)){
    //             $response['message'] = 'User Id is required.';
    //             $response['code'] = 201;
    //         }else{
    //             $this->load->model('superadmin_model');
    //           $wishlist_data = $this->superadmin_model->get_wishlist_data($user_id);
    //             $wishlist_count = $this->model->CountWhereInRecord('wishlist',array('user_id'=>$user_id,'status'=>'1'));
    //           foreach ($wishlist_data as $wishlist_data_key => $wishlist_data_row) {
    //                       $wishlist_data[$wishlist_data_key]['image_name'] = APPURL.$wishlist_data_row['image_name'];
    //                     if($fk_lang_id==1){
    //                             $wishlist_data[$wishlist_data_key]['product_name'] = $wishlist_data_row['product_name'];
    //                             $wishlist_data[$wishlist_data_key]['currency_in_english'] = $wishlist_data_row['currency_in_english'];

    //                     }else{
    //                             $wishlist_data[$wishlist_data_key]['product_name'] = $wishlist_data_row['product_name_ar'];
    //                             $wishlist_data[$wishlist_data_key]['currency_in_english'] = $wishlist_data_row['currency_in_arabic'];
    //                     } 

    //                 $whislist_data_img_url = explode(',',$wishlist_data_row['img_url']);
    //                 foreach ($whislist_data_img_url as $whislist_data_img_url_key => $whislist_data_img_url_row) {
    //                         $whislist_data_img_url1[]= APPURL.$whislist_data_img_url_row;                     
    //                   }


    //                   $wishlist_data[$wishlist_data_key]['img_url']= implode(',',$whislist_data_img_url1);                  
    //           }
    //             $response['code'] = REST_Controller::HTTP_OK;
    //             $response['message'] = 'success';
    //             $response['status'] = true;  
    //             $response['wishlist_data'] = $wishlist_data;  
    //             $response['wishlist_count'] = $wishlist_count;  
    //             $response['cart_count'] = get_user_cart_count($user_id); 
    //         }                 
    //     } else {
    //         $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
    //         $response['message'] = 'Unauthorised';
    //     }
    //     echo json_encode($response); 
    // }
     public function get_all_whislist_post()
    {
       $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){  
            $user_id = $this->input->post('user_id');
            $fk_lang_id = $this->input->post('fk_lang_id');
            if(empty($user_id)){
                $response['message'] = 'User Id is required.';
                $response['code'] = 201;
            }else{
                $this->load->model('superadmin_model');
               $wishlist_data = $this->superadmin_model->get_wishlist_data($user_id);
               foreach ($wishlist_data as $wishlist_data_key => $wishlist_data_row) {
                          $wishlist_data[$wishlist_data_key]['image_name'] = APPURL.$wishlist_data_row['image_name'];
                        if($fk_lang_id==1){
                                $wishlist_data[$wishlist_data_key]['product_name'] = $wishlist_data_row['product_name'];
                                $wishlist_data[$wishlist_data_key]['currency_in_english'] = $wishlist_data_row['currency_in_english'];

                        }else{
                                $wishlist_data[$wishlist_data_key]['product_name'] = $wishlist_data_row['product_name_ar'];
                                $wishlist_data[$wishlist_data_key]['currency_in_english'] = $wishlist_data_row['currency_in_arabic'];
                        } 


                    $whislist_data_img_url = explode(',',$wishlist_data_row['img_url']);
                    foreach ($whislist_data_img_url as $whislist_data_img_url_key => $whislist_data_img_url_row) {
                            $whislist_data_img_url1[]= APPURL.$whislist_data_img_url_row;                     
                      }
                      

                       $wishlist_data[$wishlist_data_key]['img_url']= implode(',',$whislist_data_img_url1); 
                           $cart_data = $this->model->CountWhereRecord('cart',array('product_id'=>$wishlist_data_row['product_id'],'user_id'=>@$user_id));
                            $cart_id = $this->model->selectwhereData('cart',array('product_id'=>$wishlist_data_row['product_id'],'user_id'=>@$user_id),array('cart_id'));
                           
                            
                             if($cart_data==1){
                                $wishlist_data[$wishlist_data_key]['cart_data'] = true;
                                 $wishlist_data[$wishlist_data_key]['cart_id'] = $cart_id['cart_id'];
                            }else{
                                $wishlist_data[$wishlist_data_key]['cart_data'] = false;
                                 $wishlist_data[$wishlist_data_key]['cart_id'] = "";
                            }
                            
                    }
                $response['code'] = REST_Controller::HTTP_OK;
                $response['message'] = 'success';
                $response['status'] = true;  
                $response['wishlist_data'] = $wishlist_data;  
                $response['cart_count'] = get_user_cart_count($user_id); 
               
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
              $user_id = $this->input->post('user_id'); 

             if (empty($fk_lang_id)) {
                $response['message'] = 'Language Id is required.';
                $response['code'] = 201;
            } else {
                $this->load->model('superadmin_model');
                $product_data = $this->superadmin_model->get_search_product($fk_lang_id);
                foreach ($product_data as $product_data_key => $product_data_row) {
                   $product_data[$product_data_key]['image_name'] = APPURL.$product_data_row['image_name'];
                    if($fk_lang_id==1){
                                $product_data[$product_data_key]['product_name'] = $product_data_row['product_name'];
                                // $product_data[$product_data_key]['currency_in_english'] = $product_data_row['currency_in_english'];

                        }else{
                                $product_data[$product_data_key]['product_name'] = $product_data_row['product_name_ar'];
                                // $product_data[$product_data_key]['currency_in_english'] = $product_data_row['currency_in_arabic'];
                        } 
                }
                if(!empty($user_id)){
                    $cart_count = get_user_cart_count($user_id); 
                }else{
                    $cart_count = "";
                }
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;  
                $response['message'] = 'success';
                $response['product_data'] =$product_data;
                $response['cart_count'] =$cart_count;
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
            $user_id = $this->input->post('user_id'); 
            if (empty($fk_lang_id)) {
                $response['message'] = 'Language Id is required.';
                $response['code'] = 201;
            } else {
                $category = $this->model->selectWhereData('category', array('status'=>1,'active_inactive'=>'1','category_type'=>'Normal'),array('*'),false);
                if(!empty($category)){
                  foreach ($category as $category_key => $category_row) {
                    $category[$category_key]['image_path'] = APPURL.$category_row['image_path'];
                     if($fk_lang_id==1){
                                $category[$category_key]['category_name'] = $category_row['category_name'];
                     }else{
                        $category[$category_key]['category_name'] = $category_row['category_name_ar'];
                     }
                  }
                }else{
                    $category = [];
                }

                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;  
                $response['message'] = 'success';
                $response['category'] =$category;
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
                        $inventory_data = $this->model->selectWhereData('inventory',array('product_id'=>$cart_data_row['product_id'],'used_status'=>'1'),array('qty','id'));
                          if(empty($inventory_data['qty'])){
                             $this->model->deleteData2('cart',array('product_id'=>$cart_data_row['product_id']));
                          }
                            if($fk_lang_id==1){
                                    $cart_data[$cart_data_key]['product_name'] = $cart_data_row['product_name'];
                                    $cart_data[$cart_data_key]['currency_in_english'] = $cart_data_row['currency_in_english'];
                                  

                            }else{
                                    $cart_data[$cart_data_key]['product_name'] = $cart_data_row['product_name_ar'];
                                    $cart_data[$cart_data_key]['currency_in_english'] = $cart_data_row['currency_in_arabic'];
                                    
                            }
                        $cart_data[$cart_data_key]['cartPrice'] = $cart_data_row['product_offer_price'] * $cart_data_row['cart_qty'];
                        $cart_data[$cart_data_key]['cartQuantity'] = $cart_data_row['cart_qty'];
                        $cart_data[$cart_data_key]['image_name'] = APPURL.$cart_data_row['image_name'];
                        $sub_total= $cart_data[$cart_data_key]['cartPrice'];
                        $total = $total + $sub_total;                       
                }
                              
                $user_address = $this->model->selectWhereData('user_delivery_address',array('user_id'=>$user_id,'status'=>'1'),array('*'),false);
                $default_user_address_id = $this->model->selectWhereData('op_user',array('op_user_id'=>$user_id,'status'=>'1'),array('fk_address_id'));
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;  
                $response['message'] = 'success';
                $response['cart_product_details'] =$cart_data;
                $response['total'] = custom_number_format($total,2);
                if(empty($user_address)){
                    $response['user_address'] =[];
                }else{
                    $response['user_address'] =$user_address;
                }
                
                $response['fk_address_id'] =$default_user_address_id['fk_address_id'];
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
              $user_id = $this->input->post('user_id'); 

             if (empty($fk_lang_id)) {
                $response['message'] = 'Language Id is required.';
                $response['code'] = 201;
            } else  if (empty($category_id)) {
                $response['message'] = 'Category Id is required.';
                $response['code'] = 201;
            } else {
                 $this->load->model('superadmin_model');
                 $product_data =  $this->superadmin_model->get_product_on_category($category_id);
                //  print_r($product_data);die;
                // $product_data = $this->model->selectWhereData('product',array('category_id'=>$category_id,'status'=>1,'product_status'=>'1'),array('*'),false);
                          
                if(!empty($product_data)){
                        foreach ($product_data as $product_data_key => $product_data_row) {
                             $explode_image=explode(',',$product_data_row['img_url']);
                            // $product_data[$product_data_key]['image_name'] = APPURL.$explode_image[0];
                          $product_data[$product_data_key]['image_name'] = APPURL.$product_data_row['image_name'];
                            $wishlist_data = $this->model->CountWhereRecord('wishlist',array('product_id'=>$product_data_row['product_id'],'user_id'=>$user_id));
                            $wishlist_id = $this->model->selectwhereData('wishlist',array('product_id'=>$product_data_row['product_id'],'user_id'=>$user_id),array('id'));
                            $cart_data = $this->model->CountWhereRecord('cart',array('product_id'=>$product_data_row['product_id'],'user_id'=>@$user_id));
                            $cart_id = $this->model->selectwhereData('cart',array('product_id'=>$product_data_row['product_id'],'user_id'=>@$user_id),array('cart_id'));
                            if($wishlist_data==1){
                                $wishlist_data = true;
                                $wishlist_id = $wishlist_id['id'];
                            }else{
                                $wishlist_data = false;
                                $wishlist_id = "";
                            }
                            
                             if($cart_data==1){
                                $cart_data = true;
                                $cart_id = $cart_id['cart_id'];
                            }else{
                                $cart_data = false;
                                $cart_id = "";
                            }
                            
                            $product_data[$product_data_key]['wishlist_product'] = $wishlist_data;
                            $product_data[$product_data_key]['wishlist_id'] = $wishlist_id;
                            $product_data[$product_data_key]['cart_product'] = $cart_data;
                            $product_data[$product_data_key]['cart_id'] = $cart_id;
                           if($fk_lang_id==1){
                                    $product_data[$product_data_key]['product_name'] = $product_data_row['product_name'];
                                    $product_data[$product_data_key]['currency_in_english'] = $product_data_row['currency_in_english'];
                                    $product_data[$product_data_key]['add_to_cart'] ='Add to Cart';
                                    $product_data[$product_data_key]['label'] ='NEW';
                                    $product_data[$product_data_key]['font-size'] = "";
                                    $product_data[$product_data_key]['label-font-size'] = "";

                             }else{
                                    $product_data[$product_data_key]['product_name'] = $product_data_row['product_name_ar'];
                                    $product_data[$product_data_key]['currency_in_english'] = $product_data_row['currency_in_arabic'];
                                    $product_data[$product_data_key]['add_to_cart'] ='إضافة الى السلة';
                                    $product_data[$product_data_key]['font-size'] = "style='font-size:18px;'";
                                    $product_data[$product_data_key]['label-font-size'] = "style='font-size:20px;'";
                                    $product_data[$product_data_key]['label'] ='جديد';
                             }
                        }
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
               
                $sub_category_data = $this->model->selectWhereData('subcategory',array('category_id'=>$category_id,'status'=>'1','active_inactive'=>'1'),array('*'),false);
                if(!empty($sub_category_data)){
                     foreach ($sub_category_data as $sub_category_data_key => $sub_category_data_row) {
                         if($fk_lang_id==1){
                            $sub_category_data[$sub_category_data_key]['sub_category_name'] = $sub_category_data_row['sub_category_name'];
                         }else{
                            $sub_category_data[$sub_category_data_key]['sub_category_name'] = $sub_category_data_row['sub_category_name_ar'];
                         }
                    }
                }
               

                $categorydata = $this->model->selectwhereData('category',array('category_id'=>$category_id,'status'=>'1','active_inactive'=>'1'),array('*'),false);
               
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

                if(empty($categorydata)){
                    $categorydata=[];
                }else{
                    $categorydata = $categorydata;
                }
             
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;  
                $response['message'] = 'success';
                $response['product_data'] =$product_data;
                $response['sub_category_data'] =$sub_category_data;
                $response['category_data'] =$categorydata;
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
    public function get_product_on_sub_category_post()
    {
       $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
              $fk_lang_id = $this->input->post('fk_lang_id'); 
              $sub_category_id = $this->input->post('sub_category_id'); 
              $category_id = $this->input->post('category_id'); 
              $user_id = $this->input->post('user_id'); 
             if (empty($fk_lang_id)) {
                $response['message'] = 'Language Id is required.';
                $response['code'] = 201;
            } else  if (empty($sub_category_id)) {
                $response['message'] = 'Sub Category Id is required.';
                $response['code'] = 201;
            } else  if (empty($category_id)) {
                $response['message'] = 'Category Id is required.';
                $response['code'] = 201;
            }else {
                $this->load->model('superadmin_model');
                 $product_data =  $this->superadmin_model->get_product_on_sub_category($sub_category_id);
                // $product_data = $this->model->selectWhereData('product',array('sub_category_id'=>$sub_category_id,'status'=>1,'product_status'=>'1'),array('*'),false);
                if(!empty($product_data)){
                        foreach ($product_data as $product_data_key => $product_data_row) {
                               $explode_image=explode(',',$product_data_row['img_url']);
                            $product_data[$product_data_key]['image_name'] = APPURL.$explode_image[0];
                            $product_data[$product_data_key]['image_name'] = APPURL.$product_data_row['image_name'];
                             $wishlist_data = $this->model->CountWhereRecord('wishlist',array('product_id'=>$product_data_row['product_id'],'user_id'=>$user_id));
                             $wishlist_id = $this->model->selectwhereData('wishlist',array('product_id'=>$product_data_row['product_id'],'user_id'=>$user_id),array('id'));
                            $cart_data = $this->model->CountWhereRecord('cart',array('product_id'=>$product_data_row['product_id'],'user_id'=>@$user_id));
                            $cart_id = $this->model->selectwhereData('cart',array('product_id'=>$product_data_row['product_id'],'user_id'=>@$user_id),array('cart_id'));
                            if($wishlist_data==1){
                                $wishlist_data = true;
                                $wishlist_id = $wishlist_id['id'];
                            }else{
                                $wishlist_data = false;
                                $wishlist_id = "";
                            }
                            
                            
                            if($cart_data==1){
                                $cart_data = true;
                                $cart_id = $cart_id['cart_id'];
                            }else{
                                $cart_data = false;
                                $cart_id = "";
                            }
                            
                            
                            $best_selling[$product_data_key]['wishlist_id'] = $wishlist_id;
                            $product_data[$product_data_key]['wishlist_product'] = $wishlist_data;
                            
                            $product_data[$product_data_key]['cart_product'] = $cart_data;
                            $product_data[$product_data_key]['cart_id'] = $cart_id;
                            if($fk_lang_id==1){
                                $product_data[$product_data_key]['product_name'] = $product_data_row['product_name'];
                                $product_data[$product_data_key]['currency_in_english'] = $product_data_row['currency_in_english'];
                                $product_data[$product_data_key]['add_to_cart'] ='Add to Cart';
                                $product_data[$product_data_key]['label'] ='NEW';
                                $product_data[$product_data_key]['font-size'] = "";
                                $product_data[$product_data_key]['label-font-size'] = "";  

                             }else{
                                    $product_data[$product_data_key]['product_name'] = $product_data_row['product_name_ar'];
                                    $product_data[$product_data_key]['currency_in_english'] = $product_data_row['currency_in_arabic'];
                                    $product_data[$product_data_key]['add_to_cart'] ='إضافة الى السلة';
                                    $product_data[$product_data_key]['font-size'] = "style='font-size:18px;'";
                                    $product_data[$product_data_key]['label-font-size'] = "style='font-size:20px;'";
                                    $product_data[$product_data_key]['label'] ='جديد';
                             }
                             
                        }
                }
                $sub_category_data = $this->model->selectWhereData('subcategory',array('sub_category_id'=>$sub_category_id,'active_inactive'=>'1','status'=>'1'),array('*'),false);
                $category_data = $this->model->selectWhereData('category',array('category_id'=>$category_id,'active_inactive'=>'1','status'=>'1'),array('*'),false);
               
                if(empty($sub_category_data)){
                    $sub_category_data=[];
                }else{
                    $sub_category_data = $sub_category_data;
                }

                if(empty($category_data)){
                    $category_data=[];
                }else{
                    $category_data = $category_data;
                }
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;  
                $response['message'] = 'success';
                $response['product_data'] =$product_data;
                $response['sub_category_data'] =$sub_category_data;
                $response['category_data'] =$category_data;
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
    public function get_product_on_child_category_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
              $fk_lang_id = $this->input->post('fk_lang_id'); 
              $child_category_id = $this->input->post('child_category_id'); 
              $user_id = $this->input->post('user_id'); 
            if (empty($fk_lang_id)) {
                $response['message'] = 'Language Id is required.';
                $response['code'] = 201;
            } else  if (empty($child_category_id)) {
                $response['message'] = 'Sub Category Id is required.';
                $response['code'] = 201;
            } else {
                $product_data = $this->model->selectWhereData('product',array('child_category_id'=>$child_category_id,'status'=>1,'product_status'=>'1'),array('*'),false);
                foreach ($product_data as $product_data_key => $product_data_row) {
                          $product_data[$product_data_key]['image_name'] = APPURL.$product_data_row['image_name'];
                          $wishlist_data = $this->model->CountWhereRecord('wishlist',array('product_id'=>$product_data_row['product_id'],'user_id'=>$user_id));
                          $wishlist_id = $this->model->selectwhereData('wishlist',array('product_id'=>$product_data_row['product_id'],'user_id'=>$user_id),array('id'));
                           $cart_data = $this->model->CountWhereRecord('cart',array('product_id'=>$product_data_row['product_id'],'user_id'=>@$user_id));
                        $cart_id = $this->model->selectwhereData('cart',array('product_id'=>$product_data_row['product_id'],'user_id'=>@$user_id),array('cart_id'));
                          if($wishlist_data==1){
                              $wishlist_data = true;
                              $wishlist_id = $wishlist_id['id'];
                          }else{
                              $wishlist_data = false;
                              $wishlist_id = "";
                          }
                          
                          
                          if($cart_data==1){
                                $cart_data = true;
                                $cart_id = $cart_id['cart_id'];
                            }else{
                                $cart_data = false;
                                $cart_id = "";
                            }
                            
                            $product_data[$product_data_key]['wishlist_id'] = $wishlist_id;
                            $product_data[$product_data_key]['wishlist_product'] = $wishlist_data;
                            $product_data[$product_data_key]['cart_product'] = $cart_data;
                            $product_data[$product_data_key]['cart_id'] = $cart_id;
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
              $fk_lang_id = $this->input->post('fk_lang_id'); 
                  
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
                // if(!empty($order_id)){
                //      $order_id = get_random_strings('tbl_payment','order_id');
                // }
                foreach ($fk_product_id as $fk_product_id_key => $fk_product_id_row) {
                    $curl_data = array(
                        'fk_user_id'=>$user_id,
                        'fk_lang_id'=>$fk_lang_id,
                        'order_id'=>$order_id,
                        'order_no'=>get_random_strings('tbl_payment','order_no'),
                        'fk_address_id'=>$fk_address_id,
                        'fk_product_id'=>$fk_product_id_row,
                        'quantity'=>$quantity[$fk_product_id_key],
                        'unit_price'=>$unit_price[$fk_product_id_key],
                        'total'=>$total[$fk_product_id_key],
                        'sub_total'=>$sub_total,
                        'tax'=>$tax,                        
                        'grand_total'=>$grand_total,
                        'date'=>date('d/m/Y H:i:s'),
                    );
                    $inserted_id = $this->model->insertData('tbl_payment',$curl_data);
                }
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;  
                $response['message'] = 'success';
                $response['order_id'] = $order_id;
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
                    $overall_category_type = [];
                    foreach($order_details as $order_data_key => $order_data_row){
                        $category_type = $this->model->selectWhereData('product', array('product_id'=>$order_data_row['fk_product_id'],'product_status'=>'1','status'=>1),array('category_type'));
                        $order_details[$order_data_key]['category_type'] =$category_type['category_type'];
                        $overall_category_type[]=@$category_type['category_type'];
                    }
                    $overall_category_type_1 = array_unique($overall_category_type);
                    if(in_array('Gift Card',$overall_category_type_1)){
                        $overall_category_type_2 = 'Gift Card';
                    } else {
                        $overall_category_type_2 = 'Normal';
                    }
                    $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
                    $response['message'] = 'success';
                    $response['order_details'] = $order_details;
                    $response['category_type'] = $overall_category_type_2;
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
              $fk_lang_id = $this->input->post('fk_lang_id'); 
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
              $order_source = $this->input->post('order_source'); 
              $transaction_status = $this->input->post('transaction_status'); 
              $transaction_number = $this->input->post('transaction_number'); 
              $MID = $this->input->post('MID'); 
              $RESPCODE = $this->input->post('RESPCODE'); 
              $RESPMSG = $this->input->post('RESPMSG'); 
              $STATUS = $this->input->post('STATUS'); 
              $TXNAMOUNT = $this->input->post('TXNAMOUNT'); 
              $checksumhash = $this->input->post('checksumhash'); 
              $category_type = $this->input->post('category_type');
              
            //   print_r($payment_type);die;
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
                 $pdf_english = base_url() . "uploads/invoice/".$order_id."_invoice_english.pdf";
                 $pdf_arabic = base_url() . "uploads/invoice/".$order_id."_invoice_arabic.pdf";
                foreach ($fk_product_id as $fk_product_id_key => $fk_product_id_row) {
                    $curl_data = array(
                        'fk_lang_id'=>$fk_lang_id,
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
                        'date'=>date('d/m/Y'),
                        'order_date_time'=>date('d/m/Y H:i:s'),
                        'pdf_english'=>$pdf_english,
                        'pdf_arabic'=>$pdf_arabic,
                        'order_source'=>$order_source,

                    );
                    // print_r($curl_data);
                    $inserted_id = $this->model->insertData('order_data',$curl_data);

                    $status_data = array(
                        'fk_order_id'=>@$inserted_id,
                        'status'=>1,
                        'order_id'=>$order_id,
                    );
                    $this->model->insertData('tbl_order_status',$status_data);
                    $this->model->updateData('op_user',array('fk_address_id'=>$fk_address_id),array('op_user_id'=>$user_id));
                    $last_total_quantity = $this->model->selectWhereData('inventory', array('product_id'=>$fk_product_id_row,'used_status'=>1),array('qty'));
                    

                    if(!empty($last_total_quantity)){

                         $inventory_data = array('used_status' => 0,);
                         $this->db->where('product_id', $fk_product_id_row);
                         $this->db->update('inventory', $inventory_data);

                         $inventory_data = array('product_id' => $fk_product_id_row, 'qty' => $last_total_quantity['qty'] - $quantity[$fk_product_id_key],'deduct_qty'=>$quantity[$fk_product_id_key],'date' => date('d/m/Y'),'used_status'=>1);
                         $this->model->insertData('inventory', $inventory_data);
                         
                         $this->model->updateData('product',array('qty'=> $last_total_quantity['qty'] - $quantity[$fk_product_id_key]),array('product_id'=>$fk_product_id_row));
                    }
                    if($last_total_quantity){
                        $this->model->deleteData2('cart',array('product_id'=>$fk_product_id_row));
                    }
                     $this->db->where('user_id', $user_id);
                     $this->db->where('product_id', $fk_product_id_row);
                     $this->db->delete('cart');

                      $update_data = array(
                          'payment_type'=>$payment_type,
                          'transaction_status' => $transaction_status,
                          'transaction_number' => $transaction_number,
                          'MID' => $MID,
                          'RESPCODE' => $RESPCODE,
                          'RESPMSG' => $RESPMSG,
                          'STATUS' => $STATUS,
                          'TXNAMOUNT' => $TXNAMOUNT,
                          'checksumhash' => $checksumhash,
                          );
                        //   print_r($update_data);
                        //   print_r($order_id);
                      $this->db->where('order_id', $order_id);
                      $this->db->update('tbl_payment', $update_data);
                    //   die;
                      $product_data1 = $this->model->selectWhereData('product', array('product_id'=>$fk_product_id_row),array('category_type'));
                      
                      if($product_data1['category_type'] =="Gift Card"){
                          $uniq_code = $this->model->selectWhereData('tbl_gift_card_code', array('fk_product_id'=>$fk_product_id_row,'gift_card_stock'=>'1'),array('uniq_code'));
                        //   print_r($uniq_code);die;
                          $details = array(
                            'uniq_code' =>$uniq_code['uniq_code'],
                            'order_id'=>$order_id,
                          );
                    
                            $update_data1 = array(
                                'gift_card_stock'=>'0' 
                            );
                            $this->db->where('uniq_code', $uniq_code['uniq_code']);
                            $this->db->update('tbl_gift_card_code', $update_data1);

                            $gift_code_update = array(
                                'gift_code'=>$uniq_code['uniq_code']
                            );
                            $this->db->where('fk_product_id',$fk_product_id_row);
                            $this->db->update('order_data', $gift_code_update);
                            $user_details = $this->model->selectWhereData('op_user', array('op_user_id'=>$user_id),array('contact_no','email'));
                            $subject1 = "Gift Card Details";
                            $ins =  $this->config->item('ins');
                            $api =  $this->config->item('api_key');
                                if(!empty($user_details['email'])){
                                    $uniq_code_mail = $this->load->view('gift_code_mail', array('data'=>$details),true);
                                }
                                send_email($user_details['email'],$subject1 , $uniq_code_mail);
                              $msg_html = '';  
                            $msg_html .= "Thank you for your order from Circuit Store! Please find below the Gift Card Code: ";  
                            $msg_html .= "\r\n";  
                            $msg_html .= "Code: ".$uniq_code['uniq_code'];
                            $msg_html .= "\r\n";   
                            $msg_html .= "Enjoy and see you soon ";   
                            $curl_data1 = array(
                                'number' => '974'.$user_details['contact_no'],
                                'msg' => $msg_html,
                                // 'media' => $media,
                                'instance' => $ins,
                                'apikey' => $api,
                            );

                          $curl = $this->link->whatsapp_hits($curl_data1);
                        //   print_r($curl);die;
                      }
                      
                       $this->load->model('superadmin_model');
                       $order_data = $this->superadmin_model->order_data($order_id);

                       error_reporting(0);
        
                        ini_set('memory_limit', '256M');
                                                
                        $pdfFilePath = FCPATH . "uploads/invoice/".$order_id."_invoice_english.pdf";
                        $this->load->library('m_pdf');
                        $data = $order_data;
                        $html = $this->load->view('invoice_english', array('data'=>$data), true);
                        $mpdf = new mPDF();
                        $mpdf->SetDisplayMode('fullpage');
                        $mpdf->AddPage('P', 'A4');
                       
                        $mpdf->WriteHTML($html);
                        ob_end_clean();
                        $mpdf->Output($pdfFilePath, "F");

                         $pdfFilePath = FCPATH . "uploads/invoice/".$order_id."_invoice_arabic.pdf";
                        $this->load->library('m_pdf');
                        $data = $order_data;
                        $html = $this->load->view('invoice_arabic', array('data'=>$data), true);
                        $mpdf = new mPDF();
                        $mpdf->SetDisplayMode('fullpage');
                        $mpdf->AddPage('P','A4');
                        $mpdf->autoLangToFont = true;
                        $mpdf->WriteHTML($html);
                        ob_end_clean();
                        $mpdf->Output($pdfFilePath, "F");


                }
                $user_data = $this->model->selectWhereData('op_user',array('op_user_id'=>$user_id),array('email','user_name'));
                $order_data = $this->superadmin_model->order_history_on_order_id($order_id);

                foreach ($order_data as $order_data_key => $order_data_row) {
                    if($fk_lang_id ==1){
                        $order_data[$order_data_key]['product_name']= $order_data_row['product_name'];
                    }else{
                        $order_data[$order_data_key]['product_name']= $order_data_row['product_name_ar'];

                    }
                }
                if($product_data1['category_type'] !="Gift Card"){
                        if(!empty($user_data['email'])){
                            $order_date = date('Y-m-d');
                          // $data = $this->email_model->order_placed_email($user_data['email'],$user_data['user_name'],$order_id,$order_date,$grand_total);
                                $subject = "Order Placed"." ".'#'.$order_id;
                                if($fk_lang_id ==1){
                              $html = $this->load->view('new_email_template', array('data'=>$data), true);
                            }else{
                              $html = $this->load->view('new_email_template_arabic', array('data'=>$data), true);

                            }
                          send_email($user_data['email'],$subject , $html);
                          // echo '<pre>'; print_r($data); exit;
                        }
                }
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;  
                 if($fk_lang_id ==1){
                    $response['message'] = 'Your Order Has Been Placed';
                }else{
                    $response['message'] = 'تم تقديم طلبكم بنجاح  ';
                }
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
                $fk_lang_id = $this->input->post('fk_lang_id');

                if(empty($user_id)){
                    $response['message'] = "User Id is required";
                    $response['code'] = 201;
                }else{
                    $this->load->model('superadmin_model');
                    $order_history = $this->superadmin_model->order_history($user_id);
                    foreach($order_history as $order_history_key => $order_history_row){
                      if($fk_lang_id==1){
                                $order_history[$order_history_key]['product_name'] = $order_history_row['product_name'];
                                $order_history[$order_history_key]['currency_in_english'] = $order_history_row['currency_in_english'];
                                $order_history[$order_history_key]['order_status'] = $order_history_row['order_status'];

                             }else{
                                    $order_history[$order_history_key]['product_name'] = $order_history_row['product_name_ar'];
                                    $order_history[$order_history_key]['currency_in_english'] = $order_history_row['currency_in_arabic'];
                                    $order_history[$order_history_key]['order_status'] = $order_history_row['order_status_ar'];
                             }

                    }
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
                $fk_lang_id = $this->input->post('fk_lang_id');

                if(empty($id)){
                    $response['message'] = "Id is required";
                    $response['code'] = 201;
                }else{
                    $this->load->model('superadmin_model');
                    $order_history = $this->superadmin_model->order_history_on_order_id($id);
                    foreach($order_history as $order_history_key => $order_history_row){
                         $order_history[$order_history_key]['image_name'] = APPURL.$order_history_row['image_name'];
                      if($fk_lang_id==1){
                                $order_history[$order_history_key]['product_name'] = $order_history_row['product_name'];
                                $order_history[$order_history_key]['currency_in_english'] = $order_history_row['currency_in_english'];

                             }else{
                                    $order_history[$order_history_key]['product_name'] = $order_history_row['product_name_ar'];
                                    $order_history[$order_history_key]['currency_in_english'] = $order_history_row['currency_in_arabic'];
                             }

                    }
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
                    $address_data = $this->model->selectWhereData('user_delivery_address',array('user_id'=>$user_id,'status'=>'1'),array('*'),false);
                    $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
                    $response['message'] = 'success';
                    if(empty($address_data)){
                        $response['address_data'] = [];
                    }else{
                         $response['address_data'] = $address_data;
                    }
                    
                }
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }

   public function get_gift_card_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
                $fk_lang_id = $this->input->post('fk_lang_id');
                $user_id = $this->input->post('user_id');
                if(empty($fk_lang_id)){
                    $response['message'] = "Language Id is required";
                    $response['code'] = 201;
                }else{
                    $this->load->model('superadmin_model');
                    $gift_card_data = $this->superadmin_model->get_gift_card();
                    // print_r($gift_card_sum);die();
                    foreach($gift_card_data as $gift_data_key => $gift_data_row)
                    {
                        $gift_card_sum=$this->superadmin_model->get_gift_card_sum($gift_data_row['product_id']);
                        $gift_card_data[$gift_data_key]['gift_count'] = $gift_card_sum['uniq_code'];
                        if($fk_lang_id==1){
                            $gift_card_data[$gift_data_key]['product_name'] = $gift_data_row['product_name'];
                            $gift_card_data[$gift_data_key]['currency_in_english'] = $gift_data_row['currency_in_english'];
                     }else{
                            $gift_card_data[$gift_data_key]['product_name'] = $gift_data_row['product_name_ar'];
                            $gift_card_data[$gift_data_key]['currency_in_english'] = $gift_data_row['currency_in_arabic'];
                     }
                     $wishlist_data = $this->model->CountWhereRecord('wishlist',array('product_id'=>$gift_data_row['product_id'],'user_id'=>@$user_id));
                     $wishlist_id = $this->model->selectwhereData('wishlist',array('product_id'=>$gift_data_row['product_id'],'user_id'=>$user_id),array('id'));
                     $cart_data = $this->model->CountWhereRecord('cart',array('product_id'=>$gift_data_row['product_id'],'user_id'=>@$user_id));
                        $cart_id = $this->model->selectwhereData('cart',array('product_id'=>$gift_data_row['product_id'],'user_id'=>@$user_id),array('cart_id'));
                    if($wishlist_data==1){
                         $wishlist_data = true;
                         $wishlist_id = $wishlist_id['id'];
                     }else{
                         $wishlist_data = false;
                         $wishlist_id = "";
                     }
                     
                    if($cart_data==1){
                        $cart_data = true;
                        $cart_id = $cart_id['cart_id'];
                    }else{
                        $cart_data = false;
                        $cart_id = "";
                    }
                            
                            
                     $gift_card_data[$gift_data_key]['wishlist_id'] = $wishlist_id;
                     $gift_card_data[$gift_data_key]['wishlist_data'] = $wishlist_data;
                     $gift_card_data[$gift_data_key]['cart_product'] = $cart_data;
                     $gift_card_data[$gift_data_key]['cart_id'] = $cart_id;
                     $gift_card_data[$gift_data_key]['image_name'] = APPURL.$gift_data_row['image_name'];
                    }
                    if(!empty($user_id)){
                        $cart_count = get_user_cart_count($user_id);
                        $wishlist_count = get_user_wishlist_count($user_id);
                  }else{
                        $cart_count ="";
                        $wishlist_count ="";
                  }
                    $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
                    $response['message'] = 'success';
                    $response['gift_card_data'] = $gift_card_data;
                    $response['cart_count'] = $cart_count;
                    $response['wishlist_count'] = $wishlist_count;
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
                $fk_lang_id = $this->input->post('fk_lang_id');

                if(empty($product_name)){
                    $response['message'] = " Product Name is required";
                    $response['code'] = 201;
                }else{
                    if($fk_lang_id==1){
                        $product_details = $this->model->selectWhereData('product',array('product_name'=>$product_name,'status'=>1),array('*'));
                    }else{
                           $product_details = $this->model->selectWhereData('product',array('product_name_ar'=>$product_name,'status'=>1),array('*'));
                    }
                 
                    if(!empty($product_details))
                    {
                        $response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
                        $response['message'] = 'success';
                        $response['product_details'] = $product_details;
                    }
                    else{
                        $response['code'] = 201;
                        $response['status'] = false;
                        $response['message'] = 'failure';
                    }
                 
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
                    $distance_calculation = distance1($client_lat_long['client_latitude'],$client_lat_long['client_longitude'],$user_data['latitude'],$user_data['longitude'],"kilometers");
                   
                    if($distance_calculation > 50)
                    {
                        $rate = $this->superadmin_model->get_rate(50);
                    }else{
                        $rate = $this->superadmin_model->get_rate($distance_calculation);
                    }
                    
                    $cart_data = $this->superadmin_model->get_cart_data($user_id,$fk_lang_id);
                    $gift_card_info = [];
                    foreach ($cart_data as $cart_data_key => $cart_data_row) {
                        $cart_data[$cart_data_key]['cartPrice'] = $cart_data_row['product_offer_price'] * $cart_data_row['cart_qty'];
                        $cart_data[$cart_data_key]['cartQuantity'] = $cart_data_row['cart_qty'];
                        $sub_total[]= $cart_data[$cart_data_key]['cartPrice'];
                        $gift_card_info[] = $cart_data_row['category_type'];
                    }
                    $gift_card_info_1 = array_unique($gift_card_info);
                    if(in_array('Normal',$gift_card_info_1)){
                       $delivery_rate = $rate['rate']; 
                    } else {
                        $delivery_rate = 0;
                    }
                    $sub_total = array_sum($sub_total);
                    $total = $sub_total + $delivery_rate;
                    $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
                    $response['message'] = 'success';
                    $response['rate'] = $delivery_rate;
                    $response['total'] = custom_number_format($total,2);
            }
                
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function contact_us_data_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
                $name = $this->input->post('name');
                $email = $this->input->post('email');
                $message = $this->input->post('message');

                if(empty($name)){
                    $response['message'] = "Name is required";
                    $response['code'] = 201;
                }else if(empty($email)){
                    $response['message'] = "Email is required";
                    $response['code'] = 201;
                }else if(empty($message)){
                    $response['message'] = "Message is required";
                    $response['code'] = 201;
                }else{
                    $curl_data=array(
                      'name'=>$name,
                      'email'=>$email,
                      'message'=>$message,
                    );
                    $this->model->insertData('tbl_contact_us',$curl_data);
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

    public function subscribed_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
                $email = $this->input->post('email');
               
                if(empty($email)){
                    $response['message'] = "Email is required";
                    $response['code'] = 201;
                }else{
                    $curl_data=array(
                      'email'=>$email,
                   );
                    $get_user_data = $this->model->selectWhereData('subscribed', array('email' => $email));
                
                    if(empty($get_user_data))
                    {
                        $this->model->insertData('subscribed',$curl_data);
                        $response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
                        $response['message'] = 'success';
                    }else{
                        $response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = false;
                        $response['message'] = 'failure';
                    }
                }
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }  
    public function whatsapp_api_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
          $contact_no = $this->input->post('contact_no');
          $msg = $this->input->post('msg');
          $media = $this->input->post('media');

          $ins =  $this->config->item('ins');
          $api =  $this->config->item('api_key');

          if(empty($contact_no)){
            $response['message']="Contact No is required";
            $response['code']=201;
          }else if(empty($msg)){
              $response['message']="Message is required";
              $response['code']=201;           
          }else{
               $msg_html = '';  
                            $msg_html .= "Thank you for your order from Circuit Store! Please find below the Gift Card Code: ";  
                            $msg_html .= "\r\n";  
                            $msg_html .= "Code: 87238278728";
                            $msg_html .= "\r\n";   
                            $msg_html .= "Enjoy and see you soon ";   
                           
              $curl_data = array(
                'number' => $contact_no,
                'msg' => $msg_html,
                'instance' => $ins,
                'apikey' => $api,
               );
               
               print_r($curl_data);die;
             
            $curl = $this->link->whatsapp_hits($curl_data);
           
            print_r($curl);die;
            $response['message']= $curl['message'];
            $response['code']=200;
            $response['status']= $curl['messageData']['status'];
           
          }
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    
     public function get_brand_details_post()
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
                $brand_data= $this->model->selectWhereData('brands',array('status'=>'1','active_inactive'=>'1'),array('*'),false);
               
                if(!empty($brand_data)){
                    foreach ($brand_data as $brand_data_key => $brand_data_row) {
                        $brand_data[$brand_data_key]['img_url'] =  APPURL.$brand_data_row['img_url'];   
                         if($fk_lang_id==1){
                               $brand_data[$brand_data_key]['brand_name'] =  $brand_data_row['brand_name'];   
                             }else{
                                $brand_data[$brand_data_key]['brand_name'] = $brand_data_row['brand_name_ar'];
                             }
                    }
                } else {
                   
                    $brand_data = [];
                   
                }
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
                
                $response['brand_data'] = $brand_data;
            }
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    
     public function get_product_on_brand_post()
    {
       $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
              $fk_lang_id = $this->input->post('fk_lang_id'); 
              $brand_id = $this->input->post('brand_id'); 
              $user_id = $this->input->post('user_id'); 

             if (empty($fk_lang_id)) {
                $response['message'] = 'Language Id is required.';
                $response['code'] = 201;
            } else  if (empty($brand_id)) {
                $response['message'] = 'Brand Id is required.';
                $response['code'] = 201;
            } else {
                $this->load->model('superadmin_model');
                $product_data = $this->superadmin_model->get_brand_product($brand_id);
                if(!empty($product_data)){
                        foreach ($product_data as $product_data_key => $product_data_row) {
                           $product_data[$product_data_key]['image_name'] = APPURL.$product_data_row['image_name'];
                            $wishlist_data = $this->model->CountWhereRecord('wishlist',array('product_id'=>$product_data_row['product_id'],'user_id'=>$user_id));
                            $wishlist_id = $this->model->selectwhereData('wishlist',array('product_id'=>$product_data_row['product_id'],'user_id'=>$user_id),array('id'));
                            $cart_data = $this->model->CountWhereRecord('cart',array('product_id'=>$product_data_row['product_id'],'user_id'=>@$user_id));
                            $cart_id = $this->model->selectwhereData('cart',array('product_id'=>$product_data_row['product_id'],'user_id'=>@$user_id),array('cart_id'));
                            if($wishlist_data==1){
                                $wishlist_data = true;
                                $wishlist_id = $wishlist_id['id'];
                            }else{
                                $wishlist_data = false;
                                $wishlist_id = "";
                            }
                            
                            if($cart_data==1){
                                $cart_data = true;
                                $cart_id = $cart_id['cart_id'];
                            }else{
                                $cart_data = false;
                                $cart_id = "";
                            }
                            
                        $product_data[$product_data_key]['cart_product'] = $cart_data;
                        $product_data[$product_data_key]['cart_id'] = $cart_id;
                            $product_data[$product_data_key]['wishlist_product'] = $wishlist_data;
                            $product_data[$product_data_key]['wishlist_id'] = $wishlist_id;
                           if($fk_lang_id==1){
                                $product_data[$product_data_key]['product_name'] = $product_data_row['product_name'];
                                $product_data[$product_data_key]['currency_in_english'] = $product_data_row['currency_in_english'];
                                $product_data[$product_data_key]['add_to_cart'] ='Add to Cart';
                                $product_data[$product_data_key]['label'] ='NEW';
                                $product_data[$product_data_key]['font-size'] = "";
                                $product_data[$product_data_key]['label-font-size'] = "";  

                             }else{
                                    $product_data[$product_data_key]['product_name'] = $product_data_row['product_name_ar'];
                                    $product_data[$product_data_key]['currency_in_english'] = $product_data_row['currency_in_arabic'];
                                    $product_data[$product_data_key]['add_to_cart'] ='إضافة الى السلة';
                                    $product_data[$product_data_key]['font-size'] = "style='font-size:18px;'";
                                    $product_data[$product_data_key]['label-font-size'] = "style='font-size:20px;'";
                                    $product_data[$product_data_key]['label'] ='جديد';
                             }
                             
                           
                        }
                }
                      if(empty($product_data)){
                    $product_data=[];
                }else{
                    $product_data = $product_data;
                }

             
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;  
                $response['message'] = 'success';
                $response['product_data'] =$product_data;
              
                // if(!empty($user_id)){
                //     $response['cart_count'] = get_user_cart_count($user_id);
                //     $response['wishlist_count'] = get_user_wishlist_count($user_id);
                // }
            }       
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
     public function get_all_product_data_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
              $fk_lang_id = $this->input->post('fk_lang_id'); 
              $user_id = $this->input->post('user_id'); 
               $start = $this->input->post('skip'); 
              $end = $this->input->post('step'); 
               if(empty($fk_lang_id)){
                $response['message'] = 'Language Id is required.';
                $response['code'] = 201;
            }else{
                   $this->load->model('superadmin_model');
                   $product_data = $this->superadmin_model->get_all_product_data($fk_lang_id,$end,$start);
                   foreach ($product_data as $product_data_key => $product_data_row) {
                        $wishlist_data = $this->model->CountWhereRecord('wishlist',array('product_id'=>$product_data_row['product_id'],'user_id'=>@$user_id));
                        $wishlist_id = $this->model->selectwhereData('wishlist',array('product_id'=>$product_data_row['product_id'],'user_id'=>@$user_id),array('id'));
                        $cart_data = $this->model->CountWhereRecord('cart',array('product_id'=>$product_data_row['product_id'],'user_id'=>@$user_id));
                        $cart_id = $this->model->selectwhereData('cart',array('product_id'=>$product_data_row['product_id'],'user_id'=>@$user_id),array('cart_id'));
                            if($wishlist_data==1){
                                $wishlist_data = true;
                                $wishlist_id = $wishlist_id['id'];
                            }else{
                                $wishlist_data = false;
                                $wishlist_id = "";
                            }
                            
                            if($cart_data==1){
                                $cart_data = true;
                                $cart_id = $cart_id['cart_id'];
                            }else{
                                $cart_data = false;
                                $cart_id = "";
                            }
                        $product_data[$product_data_key]['wishlist_product'] = $wishlist_data;
                        $product_data[$product_data_key]['cart_product'] = $cart_data;
                        $product_data[$product_data_key]['wishlist_id'] = $wishlist_id;
                        $product_data[$product_data_key]['cart_id'] = $cart_id;
                        $explode_image=explode(',',$product_data_row['img_url']);
                        // $product_data[$product_data_key]['image_name'] = APPURL.$explode_image[0];
                         $product_data[$product_data_key]['image_name'] = APPURL.$product_data_row['image_name'];
                         if($fk_lang_id==1){
                                $product_data[$product_data_key]['product_name'] = $product_data_row['product_name'];
                                $product_data[$product_data_key]['currency_in_english'] = $product_data_row['currency_in_english'];
                                $product_data[$product_data_key]['add_to_cart'] ='Add to Cart';
                                 $product_data[$product_data_key]['label'] ='NEW';
                                $product_data[$product_data_key]['font-size'] = "";
                                $product_data[$product_data_key]['label-font-size'] = "";
                         }else{
                                $product_data[$product_data_key]['product_name'] = $product_data_row['product_name_ar'];
                                $product_data[$product_data_key]['currency_in_english'] = $product_data_row['currency_in_arabic'];
                                $product_data[$product_data_key]['add_to_cart'] ='إضافة الى السلة';
                                 $product_data[$product_data_key]['font-size'] = "style='font-size:15px;'";
                                //  $product_data[$product_data_key]['font-size'] = "style='font-size:15px;'";
                                $product_data[$product_data_key]['label-font-size'] = "style='font-size:20px;'";
                                $product_data[$product_data_key]['label'] ='جديد';
                         }
                  }
                  if(!empty($user_id)){
                        $cart_count = get_user_cart_count($user_id);
                        $wishlist_count = get_user_wishlist_count($user_id);
                  }else{
                        $cart_count ="";
                        $wishlist_count ="";
                  }
                  $response['code'] = REST_Controller::HTTP_OK;
                  $response['status'] = true;
                  $response['message'] = 'success';
                  $response['product_data'] = $product_data;
                  $response['cart_count'] = $cart_count;
                  $response['wishlist_count'] = $wishlist_count;
            }
               
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);             
    }
    public function get_all_featured_product_post()
    {
         $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
              $fk_lang_id = $this->input->post('fk_lang_id'); 
              $user_id = $this->input->post('user_id'); 
               if(empty($fk_lang_id)){
                $response['message'] = 'Language Id is required.';
                $response['code'] = 201;
            }else{
                   $this->load->model('superadmin_model');
                   $product_data = $this->superadmin_model->get_all_featured_product_data();
                   
                   foreach ($product_data as $product_data_key => $product_data_row) {
                        $wishlist_data = $this->model->CountWhereRecord('wishlist',array('product_id'=>$product_data_row['product_id'],'user_id'=>@$user_id));
                        $wishlist_id = $this->model->selectwhereData('wishlist',array('product_id'=>$product_data_row['product_id'],'user_id'=>@$user_id),array('id'));
                        $cart_data = $this->model->CountWhereRecord('cart',array('product_id'=>$product_data_row['product_id'],'user_id'=>@$user_id));
                        $cart_id = $this->model->selectwhereData('cart',array('product_id'=>$product_data_row['product_id'],'user_id'=>@$user_id),array('cart_id'));
                            if($wishlist_data==1){
                                $wishlist_data = true;
                                $wishlist_id = $wishlist_id['id'];
                            }else{
                                $wishlist_data = false;
                                $wishlist_id = "";
                            }
                            
                            if($cart_data==1){
                                $cart_data = true;
                                $cart_id = $cart_id['cart_id'];
                            }else{
                                $cart_data = false;
                                $cart_id = "";
                            }
                        $product_data[$product_data_key]['wishlist_product'] = $wishlist_data;
                        $product_data[$product_data_key]['cart_product'] = $cart_data;
                        $product_data[$product_data_key]['wishlist_id'] = $wishlist_id;
                        $product_data[$product_data_key]['cart_id'] = $cart_id;
                        $product_data[$product_data_key]['image_name'] = APPURL.$product_data_row['image_name'];
                         if($fk_lang_id==1){
                                $product_data[$product_data_key]['product_name'] = $product_data_row['product_name'];
                                $product_data[$product_data_key]['currency_in_english'] = $product_data_row['currency_in_english'];
                                $product_data[$product_data_key]['add_to_cart'] ='Add to Cart';
                                 $product_data[$product_data_key]['label'] ='NEW';
                                $product_data[$product_data_key]['font-size'] = "";
                                $product_data[$product_data_key]['label-font-size'] = "";
                         }else{
                                $product_data[$product_data_key]['product_name'] = $product_data_row['product_name_ar'];
                                $product_data[$product_data_key]['currency_in_english'] = $product_data_row['currency_in_arabic'];
                                $product_data[$product_data_key]['add_to_cart'] ='إضافة الى السلة';
                                 $product_data[$product_data_key]['font-size'] = "style='font-size:15px;'";
                                //  $product_data[$product_data_key]['font-size'] = "style='font-size:15px;'";
                                $product_data[$product_data_key]['label-font-size'] = "style='font-size:20px;'";
                                $product_data[$product_data_key]['label'] ='جديد';
                         }
                  }
                  $response['code'] = REST_Controller::HTTP_OK;
                  $response['status'] = true;
                  $response['message'] = 'success';
                  $response['data'] = $product_data;
            }
               
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);  
    }

    public function get_all_latest_product_post()
    {
         $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
              $fk_lang_id = $this->input->post('fk_lang_id'); 
              $user_id = $this->input->post('user_id'); 
               if(empty($fk_lang_id)){
                $response['message'] = 'Language Id is required.';
                $response['code'] = 201;
            }else{
                   $this->load->model('superadmin_model');
                   $product_data = $this->superadmin_model->get_all_popular_product_data();
                   
                   foreach ($product_data as $product_data_key => $product_data_row) {
                        $wishlist_data = $this->model->CountWhereRecord('wishlist',array('product_id'=>$product_data_row['product_id'],'user_id'=>@$user_id));
                        $wishlist_id = $this->model->selectwhereData('wishlist',array('product_id'=>$product_data_row['product_id'],'user_id'=>@$user_id),array('id'));
                        $cart_data = $this->model->CountWhereRecord('cart',array('product_id'=>$product_data_row['product_id'],'user_id'=>@$user_id));
                        $cart_id = $this->model->selectwhereData('cart',array('product_id'=>$product_data_row['product_id'],'user_id'=>@$user_id),array('cart_id'));
                            if($wishlist_data==1){
                                $wishlist_data = true;
                                $wishlist_id = $wishlist_id['id'];
                            }else{
                                $wishlist_data = false;
                                $wishlist_id = "";
                            }
                            
                            if($cart_data==1){
                                $cart_data = true;
                                $cart_id = $cart_id['cart_id'];
                            }else{
                                $cart_data = false;
                                $cart_id = "";
                            }
                        $product_data[$product_data_key]['wishlist_product'] = $wishlist_data;
                        $product_data[$product_data_key]['cart_product'] = $cart_data;
                        $product_data[$product_data_key]['wishlist_id'] = $wishlist_id;
                        $product_data[$product_data_key]['cart_id'] = $cart_id;
                        $product_data[$product_data_key]['image_name'] = APPURL.$product_data_row['image_name'];
                          if($fk_lang_id==1){
                                $product_data[$product_data_key]['product_name'] = $product_data_row['product_name'];
                                $product_data[$product_data_key]['currency_in_english'] = $product_data_row['currency_in_english'];
                                $product_data[$product_data_key]['add_to_cart'] ='Add to Cart';
                                 $product_data[$product_data_key]['label'] ='NEW';
                                $product_data[$product_data_key]['font-size'] = "";
                                $product_data[$product_data_key]['label-font-size'] = "";
                         }else{
                                $product_data[$product_data_key]['product_name'] = $product_data_row['product_name_ar'];
                                $product_data[$product_data_key]['currency_in_english'] = $product_data_row['currency_in_arabic'];
                                $product_data[$product_data_key]['add_to_cart'] ='إضافة الى السلة';
                                 $product_data[$product_data_key]['font-size'] = "style='font-size:15px;'";
                                //  $product_data[$product_data_key]['font-size'] = "style='font-size:15px;'";
                                $product_data[$product_data_key]['label-font-size'] = "style='font-size:20px;'";
                                $product_data[$product_data_key]['label'] ='جديد';
                         }
                  }
                  $response['code'] = REST_Controller::HTTP_OK;
                  $response['status'] = true;
                  $response['message'] = 'success';
                  $response['data'] = $product_data;
            }
               
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);  
    }
    public function get_all_best_selling_product_post()
    {
         $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
              $fk_lang_id = $this->input->post('fk_lang_id'); 
              $user_id = $this->input->post('user_id'); 
               if(empty($fk_lang_id)){
                $response['message'] = 'Language Id is required.';
                $response['code'] = 201;
            }else{
                   $this->load->model('superadmin_model');
                   $product_data = $this->superadmin_model->get_all_best_selling_product_data();
                   
                   foreach ($product_data as $product_data_key => $product_data_row) {
                        $wishlist_data = $this->model->CountWhereRecord('wishlist',array('product_id'=>$product_data_row['product_id'],'user_id'=>@$user_id));
                        $wishlist_id = $this->model->selectwhereData('wishlist',array('product_id'=>$product_data_row['product_id'],'user_id'=>@$user_id),array('id'));
                        $cart_data = $this->model->CountWhereRecord('cart',array('product_id'=>$product_data_row['product_id'],'user_id'=>@$user_id));
                        $cart_id = $this->model->selectwhereData('cart',array('product_id'=>$product_data_row['product_id'],'user_id'=>@$user_id),array('cart_id'));
                            if($wishlist_data==1){
                                $wishlist_data = true;
                                $wishlist_id = $wishlist_id['id'];
                            }else{
                                $wishlist_data = false;
                                $wishlist_id = "";
                            }
                            
                            if($cart_data==1){
                                $cart_data = true;
                                $cart_id = $cart_id['cart_id'];
                            }else{
                                $cart_data = false;
                                $cart_id = "";
                            }
                        $product_data[$product_data_key]['wishlist_product'] = $wishlist_data;
                        $product_data[$product_data_key]['cart_product'] = $cart_data;
                        $product_data[$product_data_key]['wishlist_id'] = $wishlist_id;
                        $product_data[$product_data_key]['cart_id'] = $cart_id;
                        $product_data[$product_data_key]['image_name'] = APPURL.$product_data_row['image_name'];
                         if($fk_lang_id==1){
                                $product_data[$product_data_key]['product_name'] = $product_data_row['product_name'];
                                $product_data[$product_data_key]['currency_in_english'] = $product_data_row['currency_in_english'];
                                $product_data[$product_data_key]['add_to_cart'] ='Add to Cart';
                                 $product_data[$product_data_key]['label'] ='NEW';
                                $product_data[$product_data_key]['font-size'] = "";
                                $product_data[$product_data_key]['label-font-size'] = "";
                         }else{
                                $product_data[$product_data_key]['product_name'] = $product_data_row['product_name_ar'];
                                $product_data[$product_data_key]['currency_in_english'] = $product_data_row['currency_in_arabic'];
                                $product_data[$product_data_key]['add_to_cart'] ='إضافة الى السلة';
                                 $product_data[$product_data_key]['font-size'] = "style='font-size:15px;'";
                                //  $product_data[$product_data_key]['font-size'] = "style='font-size:15px;'";
                                $product_data[$product_data_key]['label-font-size'] = "style='font-size:20px;'";
                                $product_data[$product_data_key]['label'] ='جديد';
                         }
                  }
                  $response['code'] = REST_Controller::HTTP_OK;
                  $response['status'] = true;
                  $response['message'] = 'success';
                  $response['data'] = $product_data;
            }
               
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);  
    }
     public function get_brand_on_search_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){ 
            $search_keyword = $this->input->post('search_keyword');
            $fk_lang_id = $this->input->post('fk_lang_id');
            if(empty($search_keyword)){
                $response['message'] = 'Search is required.';
                $response['code'] = 201;
            }else{
                if($search_keyword=="All"){
                    $product_data= $this->model->selectWhereData('brands',array('status'=>'1','active_inactive'=>'1'),array('*'),false);
                        foreach ($product_data as $product_data_key => $val1) {
                            if($fk_lang_id==1){
                                $product_data[$product_data_key]['brand_name'] =  $val1['brand_name']; 
                            }else
                            {
                               $product_data[$product_data_key]['brand_name'] =  $val1['brand_name_ar'];  
                            }
                              
                            $product_data[$product_data_key]['img_url'] =  $val1['img_url'];   
                        }
                }else{
                    $this->db->where("brand_name like '".$search_keyword."%' ");
                    // $this->db->where("brand_name_ar like '".$search_keyword."%' ");
                    $this->db->where('status',1);
                    $this->db->where('active_inactive','1');  
                  
                    $product_data = $this->db->get('brands')->result_array();
                    foreach ($product_data as $product_data_key => $val1) {
                            if($fk_lang_id==1){
                                $product_data[$product_data_key]['brand_name'] =  $val1['brand_name']; 
                            }else
                            {
                               $product_data[$product_data_key]['brand_name'] =  $val1['brand_name_ar'];  
                            }
                              
                            $product_data[$product_data_key]['img_url'] =  $val1['img_url'];   
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

    public function delete_address_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $id = $this->input->post('id');
                $user_id = $this->input->post('user_id');
                if (empty($id)) {
                    $response['code'] = 201;
                    $response['message'] = 'Id is required';
                } else {
                    $update_data = array('status' => '0');
                   $this->model->updateData('user_delivery_address',$update_data,array('id'=>$id));
                    
                    $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
                    $response['message'] = 'success';
                }
            } else {
                $response['message'] = 'No direct script is allowed.';
                $response['code'] = 204;
            }
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function user_delete_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
              $user_id = $this->input->post('user_id');
              $password = $this->input->post('password');
              if(empty($user_id)){
                    $response['code'] = 201;
                    $response['message'] = 'User Id is required';
              }else if(empty($password)){
                    $response['code'] = 201;
                    $response['message'] = 'Password is required';
              }else{
                    $curl_data = array('status'=>'0');
                     $this->model->updateData('op_user',$curl_data,array('op_user_id'=>$user_id,'password'=>dec_enc('encrypt',$password)));
                    
                    // $lang_name = $this->model->selectWhereData('op_user', array('op_user_id'=>$user_id,  "password" => dec_enc('encrypt',$password)),array('id','lang_name'),false);
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
    
    
    public function share_myapp_get(){
        
        $os =  $this->getOS();
         if($os=='Android'){
	            
            header("Location: https://play.google.com/store/apps/details?id=com.circuit_store.circuit_store");
            exit();
            }else if($os=='iPhone'){
                header("Location: https://apps.apple.com/in/app/circuit-store/id6443840848");
                exit();
            }
    }
    
    	public  function getOS() { 
	       $user_agent = $_SERVER['HTTP_USER_AGENT'];    
          $os_platform  = "Unknown OS Platform";
          $os_array     = array(
                    '/windows nt 10/i'      =>  'Windows 10',
                    '/windows nt 6.3/i'     =>  'Windows 8.1',
                    '/windows nt 6.2/i'     =>  'Windows 8',
                    '/windows nt 6.1/i'     =>  'Windows 7',
                    '/windows nt 6.0/i'     =>  'Windows Vista',
                    '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
                    '/windows nt 5.1/i'     =>  'Windows XP',
                    '/windows xp/i'         =>  'Windows XP',
                    '/windows nt 5.0/i'     =>  'Windows 2000',
                    '/windows me/i'         =>  'Windows ME',
                    '/win98/i'              =>  'Windows 98',
                    '/win95/i'              =>  'Windows 95',
                    '/win16/i'              =>  'Windows 3.11',
                    '/macintosh|mac os x/i' =>  'Mac OS X',
                    '/mac_powerpc/i'        =>  'Mac OS 9',
                    '/linux/i'              =>  'Linux',
                    '/ubuntu/i'             =>  'Ubuntu',
                    '/iphone/i'             =>  'iPhone',
                    '/ipod/i'               =>  'iPod',
                    '/ipad/i'               =>  'iPad',
                    '/android/i'            =>  'Android',
                    '/blackberry/i'         =>  'BlackBerry',
                    '/webos/i'              =>  'Mobile'
                    );
    foreach ($os_array as $regex => $value)
        if (preg_match($regex, $user_agent))
            $os_platform = $value;

    return $os_platform;
}
}