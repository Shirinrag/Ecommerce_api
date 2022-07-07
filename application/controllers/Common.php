<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Common extends REST_Controller {

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
    function get_token_get(){
		$token = token_get();
		echo $token;
        // eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6ODMzOTB9.zqJz-OnL-qY1ESWedXPCHF2kEsyrwKsMn5xuIh8-nJk
	}
    public function login_post() {
       
        $response = array('code' => - 1, 'status' => false, 'message' => '');
            $username = $this->input->post('username');           
            $password = $this->input->post('password');
            if (empty($username)) {
                $response['message'] = 'Username is required.';
                $response['code'] = 201;
            } else if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
                $response['message'] = 'Provide valid email address.';
                $response['code'] = 201;
            } else if (empty($password)) {
                $response['message'] = 'Password is required.';
                $response['code'] = 201;
            } else {
                $encryptedpassword = dec_enc('encrypt',$password);
                $check_username_count = $this->model->CountWhereRecord('tbl_users',array('email'=>$username));
                if($check_username_count > 0) {       
                    $login_credentials_data = array(
                      "email" => $username,
                      "password" => $encryptedpassword
                    );
                    $login_info = $this->model->selectWhereData('tbl_users',$login_credentials_data,'*');
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

    public function forget_password_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){     
             $username = $this->input->post('username');
              if (empty($username)) {
                $response['message'] = 'Username is required.';
                $response['code'] = 201;
            } else if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
                $response['message'] = 'Provide valid email address.';
                $response['code'] = 201;
            }  else {
                $check_username_count = $this->model->CountWhereRecord('tbl_users',array('email'=>trim($username)));
                if ($check_username_count > 0) {
                    $this->load->library('email');
                    $password = getPassword(6);
                    $encryptedpassword = encrypt($password);
                    $this->model->updateData('tbl_users',array('password'=>$encryptedpassword),array('email'=>trim($username)));
                    $login_info = $this->model->selectWhereData('tbl_users',array('email'=>$username),array('first_name','middle_name','last_name','contact_email'));

                    $this->email_model->forget_password_email($login_info['contact_email'],$login_info['first_name'].' '.$login_info['last_name'],$password);

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
                $this->load->library('email');
                $encryptedpassword = encrypt($password);
                $this->model->updateData('tbl_users',array('password'=>$encryptedpassword),array('id'=>trim($user_id)));
                $login_info = $this->model->selectWhereData('tbl_users',array('id'=>$user_id),array('first_name','middle_name','last_name','contact_email','email'));
                if (empty($login_info['contact_email'])) {
                    $email = $login_info['email'];
                } else {
                    $email = $login_info['contact_email'];
                }
                $this->email_model->change_password_email($email,$login_info['first_name'].' '.$login_info['last_name'],$password);

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
    public function register_user_post()
    {
            $response = array('code' => - 1, 'status' => false, 'message' => '');
            $name = $this->input->post('name');   
            $contact_number = $this->input->post('contact_number');
            $email = $this->input->post('email');          
            $password = $this->input->post('password');
            if (empty($name)) {
                $response['message'] = 'Name is required.';
                $response['code'] = 201;
            } else if (!empty($contact_number) && !preg_match('/^[0-9]{10}+$/', $contact_number)) {
                $response['message'] = 'Contact Number should be 10 number digits.';
                $response['code'] = 201;
            } else if (empty($email)) {
                    $response['message'] = 'Email is required.';
                    $response['code'] = 201;
            } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $response['message'] = 'Provide valid email address.';
                $response['code'] = 201;
            } else if (empty($password)) {
                $response['message'] = 'Password is required.';
                $response['code'] = 201;
            } 
            else {
                $check_username_count = $this->model->CountWhereRecord('tbl_users',array('email'=>$email,'del_status'=>'Active'));
                if ($check_username_count > 0) {
                    $response['message'] = 'Email is already exist.';
                    $response['code'] = 201;
                    $response['error_status']="email";
                } else {
                    $user_insert_array = array(
                        'name'=>$name,
                        'email'=>trim($email),
                        'password'=>dec_enc('encrypt',$password),                        
                        'contact'=>$contact_number,                       
                        
                    );
                    $insert_id = $this->model->insertData('tbl_users',$user_insert_array);
                    $query = $this->db->last_query();
                    $data1 = array("user_id" => $insert_id, "add/change" => $insert_id, "type" => 'Add', "user_type" => 'User', "sql_query" => $query);
                    $response_data = add_user_log($data1);    
                    $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
                    $response['message'] = 'success';
                }
            }
        echo json_encode($response);
    }

    public function get_common_data_get()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
                // $organization = $this->model->selectWhereData('tbl_organization',array('del_status'=>"Active"),array('id','org_name'),false);
                $language = $this->model->selectWhereData('tbl_language',array('del_status'=>"Active"),array('id','lang_name'),false);
                $menu = $this->model->selectWhereData('tbl_menu',array('del_status'=>"Active"),array('id','menu'),false);
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';               
                $response['language']=$language;
                $response['menu']=$menu;
            } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
}
