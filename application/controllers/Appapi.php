<?php
defined('BASEPATH') or exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET,POST, OPTIONS");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Appapi extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->library('upload');
    }

    public function _returnSingle($err)
    {
        foreach ($err as $key => $value) {
            return $err[$key];
        }
    }

    //registration
    public function userRegistration()
    {
        $this->form_validation->set_rules('number', 'Contact no.', 'required');
        $this->form_validation->set_rules('username', 'Name', 'required');
        $this->form_validation->set_rules('password', 'password', 'required');
        // exit();
        if ($this->form_validation->run()) {
            $number =
                $this->security->xss_clean(
                    $this->input->post('number')
                );
            $username =
                $this->security->xss_clean(
                    $this->input->post('username')
                );
            $emailId = $this->security->xss_clean(
                $this->input->post('emailId')
            );
            $device_id = $this->security->xss_clean(
                $this->input->post('device_id')
            );
            $device_type = $this->security->xss_clean(
                $this->input->post('device_type')
            );
            $terms_cond = $this->security->xss_clean(
                $this->input->post('terms_cond')
            );
            $app_version = $this->security->xss_clean(
                $this->input->post('app_version')
            );
            $app_build_no = $this->security->xss_clean(
                $this->input->post('app_build_no')
            ); //password
            $password = $this->security->xss_clean(
                $this->input->post('password')
            ); //password


            //check user is already registered or not
            $getuser = $this->db->select('*')->from('op_user')->where('contact_no', $number)->where('status', '1')->get()->result();
            $getuseremail = $emailId == '' ? [] : $this->db->select('*')->from('op_user')->where('email', $emailId)
                ->where('status', '1')->get()->result();



            //check user number is already registered or not
            if (count($getuser) <= 0) {
                //check user email is already registered or not
                if (count($getuseremail) <= 0) {

                    $getTermsConditionId = $this->db->select('*')->from('tbl_about_us')->where('module', '1')
                        ->where('type', '1')
                        ->where('is_deleted', '1')->order_by('id desc')
                        ->get()->result_array();
                    $termsCondtnId = (string)count($getTermsConditionId) > 0 ? $getTermsConditionId[0]['id'] : 0;
                    $hashpassword = password_hash($password, PASSWORD_DEFAULT);
                    $this->db->insert('op_user', array(
                        'contact_no' => $number,
                        'email' => $emailId,
                        'role_id' => '2',
                        'device_id' => $device_id,
                        'device_type' => $device_type,
                        'notifn_topic' => $number . 'ecom',
                        'user_name' => $username,
                        'terms_condition' => $terms_cond != '' ? $terms_cond : 1,
                        'terms_conditn_id' => $terms_cond != '' ? $termsCondtnId : 0,
                        'app_version' => $app_version,
                        'app_build_no' => $app_build_no,
                        'password' => md5($password)
                    ));
                    $user_id = $this->db->insert_id();



                    $msg = array('status' => true, 'message' => 'Info successfully inserted.');
                    echo json_encode($msg);
                } else {
                    $msg = array('status' => false, 'message' => 'Email Id alredy registered');
                    echo json_encode($msg);
                }
            } else {
                $msg = array('status' => false, 'message' => 'User alredy registered');
                echo json_encode($msg);
            }
        } else {
            $errorMsg = strip_tags(validation_errors());
            $msg = array('status' => false, 'message' => $errorMsg);
            echo json_encode($msg);
        }
    }

    //login
    public function userLogin()
    {
        $this->form_validation->set_rules('number', 'Contact no.', 'required');
        $this->form_validation->set_rules('password', 'Name', 'required');
        // exit();
        if ($this->form_validation->run()) {
            $number = $this->security->xss_clean($this->input->post('number'));
            $password = $this->security->xss_clean($this->input->post('password'));

            $checkUser = $this->db->Select('*')->from('op_user')->where('contact_no', $number)
                ->where('status', '1')->order_by('op_user_id desc')->get()->result_array();

            if (count($checkUser)) {
                // print($checkUser[0]['password']);
                $verifyUser = md5($password);
                // print($verifyUser==$checkUser[0]['password']);
                if ($verifyUser == $checkUser[0]['password']) {
                    $msg = array(
                        'status' => true,
                        'message' => 'Succesfully login.',
                        'userDetails' => $checkUser[0]
                    );
                    echo json_encode($msg);
                } else {
                    $msg = array(
                        'status' => false,
                        'message' => 'Kindly enter correct credentials.',
                        'userDetails' => ''
                    );
                    echo json_encode($msg);
                }
            } else {
                $msg = array('status' => false, 'message' => 'No user found kindly register.', 'userDetails' => '');
                echo json_encode($msg);
            }
        } else {
            $errorMsg = strip_tags(validation_errors());
            $msg = array('status' => false, 'message' => $errorMsg, 'userDetails' => '');
            echo json_encode($msg);
        }
    }

    //profile
    public function userProfileDetails()
    {
        $this->form_validation->set_rules('number', 'Contact no.', 'required');
        $this->form_validation->set_rules('userId', 'UserId', 'required');

        $userData = array(
            "op_user_id" => "",
            "user_name" => "",
            "email" => "",
            "contact_no" => "",
            "address" => "",
            "role_id" => "",
            "profile_photo" => "",
            "status" => "",
            "notifn_topic" => ""
        );

        if ($this->form_validation->run()) {
            $number = $this->security->xss_clean($this->input->post('number'));
            $userId = $this->security->xss_clean($this->input->post('userId'));
            $getUserDetails = $this->db->Select('op_user_id,user_name,email,contact_no,
            address,role_id,profile_photo,status,notifn_topic')->from('op_user')->where('op_user_id', $userId)
                ->where('contact_no', $number)->order_by('op_user_id desc')->where('status', '1')->get()->result_array();
            if (count($getUserDetails)) {
                $msg = array(
                    'status' => true, 'message' => 'User Data.',
                    'userData' => array(
                        "op_user_id" => $getUserDetails[0]['op_user_id'],
                        "user_name" => $getUserDetails[0]['user_name'],
                        "email" => $getUserDetails[0]['email'],
                        "contact_no" => $getUserDetails[0]['contact_no'],
                        "address" => $getUserDetails[0]['address'],
                        "role_id" => $getUserDetails[0]['role_id'],
                        "profile_photo" => 'https://bhoomifile.com/dev/uploads/userimg/' . $getUserDetails[0]['profile_photo'],
                        "status" => $getUserDetails[0]['status'],
                        "notifn_topic" => $getUserDetails[0]['notifn_topic']
                    )
                );
                echo json_encode($msg);
            } else {
                $msg = array(
                    'status' => false, 'message' => 'No user found kindly register.',
                    'userData' => $userData
                );
                echo json_encode($msg);
            }
        } else {
            $errorMsg = strip_tags(validation_errors());
            $msg = array(
                'status' => false, 'message' => $errorMsg,
                'userData' => $userData
            );
            echo json_encode($msg);
        }
    }

    public function userProfileUpdate()
    {
        $this->form_validation->set_rules('userId', 'userId', 'required');
        $this->form_validation->set_rules('user_name', 'user_name', 'required');
        $this->form_validation->set_rules('email', 'email', 'required');
        $this->form_validation->set_rules('address', 'address', 'required');
        // $this->form_validation->set_rules('profile_photo', 'Photo', 'required');



        if ($this->form_validation->run()) {
            $userId = $this->security->xss_clean($this->input->post('userId'));
            $user_name = $this->security->xss_clean($this->input->post('user_name'));
            $email = $this->security->xss_clean($this->input->post('email'));
            $address = $this->security->xss_clean($this->input->post('address'));
            $profile_photo = $this->security->xss_clean($this->input->post('profile_photo'));

            $path = '';
            $userdata;
            if ($profile_photo != '') {
                $image = base64_decode($profile_photo);

                $imagename = md5(uniqid(rand(), true));
                $filename = $imagename . '.' . 'png';
                $path = base_url() . "uploads/userimg/" . $filename;
                $pathtosave = "./uploads/userimg/" . $filename;
                file_put_contents($pathtosave, $image);
                $userdata = array(
                    'user_name' => $user_name,
                    'email' => $email,
                    'address' => $address, //firstname
                    'profile_photo' => $filename
                );
            } else {
                $userdata = array(
                    'user_name' => $user_name,
                    'email' => $email,
                    'address' => $address
                );
            }

            $checkUser = $this->db->select('*')->from('op_user')->where('op_user_id', $userId)->where('status', '1')->get()->result_array();
            if (count($checkUser) > 0) {
                $updateUser = $this->db->where('op_user_id', $userId)->update('op_user', $userdata);
                if ($updateUser) {
                    $msg = array(
                        'status' => true, 'message' => 'Successfully updated profile.'
                    );
                    echo json_encode($msg);
                } else {
                    $msg = array(
                        'status' => false, 'message' => 'Failed  to updated profile.'
                    );
                    echo json_encode($msg);
                }
            } else {
                $msg = array(
                    'status' => false, 'message' => 'No user found. Kindly register yourself first.'
                );
                echo json_encode($msg);
            }
        } else {
            $errorMsg = strip_tags(validation_errors());
            $msg = array(
                'status' => false, 'message' => $errorMsg
            );
            echo json_encode($msg);
        }
    }

    //dashboard
    public function dashboardApi()
    {
        $this->form_validation->set_rules('userId', 'userId', 'required');
        // $this->form_validation->set_rules('profile_photo', 'Photo', 'required');



        if ($this->form_validation->run()) {
            $userId = $this->security->xss_clean($this->input->post('userId'));
            $categoryList = [];
            $bannerList = [];
            $checkUser = $this->db->Select('*')->from('op_user')->where('op_user_id', $userId)
                ->where('status', '1')->get()->result_array();
            if (count($checkUser) > 0) {
                $getBanners = $this->db->Select('*')
                    ->from('tbl_banners')->where('status', '1')->get()->result_array();
                foreach ($getBanners as $banner) {
                    // https://stzsoft.in/stz/uploads/userimg/banner.png
                    $banner['img_url'] = 'https://stzsoft.in/stz/uploads/Appbanners/' . $banner['img_url'];
                    array_push($bannerList, $banner);
                }

                $getCategories = $this->db->Select('category_id,category_name,image_path,category_name_ar,status')
                    ->from('category')->where('status', '1')->get()->result_array();
                foreach ($getCategories as $category) {
                    $getSubCategry = $this->db->Select('sub_category_id,sub_category_name,sub_category_name_ar,status')
                        ->from('subcategory')->where('category_id', $category['category_id'])
                        ->where('status', '1')->get()->result_array();
                    $category['ssubCategory'] = $getSubCategry;
                    // $categoryList.add($category);
                    array_push($categoryList, $category);
                }

                $productMasterList = [];
                $productTypeMaster = $this->db->Select('*')
                    ->from('master_product_type')->where('status', '1')->get()->result_array();
                foreach ($productTypeMaster as $product_type) {
                    $productList = [];
                    $productTypeData = $this->db->select('*')->from('product_type')
                        ->where('product_type_id', $product_type['id'])
                        ->order_by('product_type_id desc')
                        ->where('status', '1')->limit(10)->get()->result_array();
                    foreach ($productTypeData as $product) {
                        $productDetails = $this->db->select('*')->from('product')
                            ->where('product_id', $product['product_id'])
                            ->where('status', '1')->order_by('product_id desc')
                            ->get()->result_array();

                        if (count($productDetails) > 0) {
                            array_push($productList, $productDetails[0]);
                        }

                        # code...
                    }

                    $product_type['productList'] = $productList;
                    array_push($productMasterList, $product_type);
                }
                $msg = array(
                    'status' => true,
                    'message' => 'Dashboard details are as follows.',
                    'bannerList' => $bannerList,
                    'categoriesData' => $categoryList,
                    'productDetails' => $productMasterList
                );
                echo json_encode($msg);
            } else {
                $msg = array(
                    'status' => false,
                    'message' => 'No user found. Kindly register yourself first.',
                    'bannerList' => [],
                    'categoriesData' => [],
                    'productDetails' => []
                );
                echo json_encode($msg);
            }
        } else {
            $errorMsg = strip_tags(validation_errors());
            $msg = array(
                'status' => false, 'message' => $errorMsg,
                'bannerList' => [],
                'categoriesData' => [],
                'productDetails' => []
            );
            echo json_encode($msg);
        }
    }

    //product
    public function productListFilterWise()
    {
        $this->form_validation->set_rules('userId', 'userId', 'required');
        $this->form_validation->set_rules('subCategoryId', 'subCategoryId', 'required');
        $this->form_validation->set_rules('childCategoryId', 'childCategoryId', 'required'); //0=all


        if ($this->form_validation->run()) {

            $userId = $this->security->xss_clean($this->input->post('userId'));
            $subCategoryId = $this->security->xss_clean($this->input->post('subCategoryId'));
            $childCategoryId = $this->security->xss_clean($this->input->post('childCategoryId'));

            $productList = [];
            $categoryList = explode(",", $childCategoryId);

            if (count($categoryList) > 1) {
                foreach ($categoryList as $catId) {
                    $this->db->select('*')->from('product');
                    $this->db->where('sub_category_id', $subCategoryId);
                    $this->db->where('child_category_id', $catId);
                    $this->db->where('status', '1');
                    $products = $this->db->get()->result_array();
                    foreach ($products as $product) {
                        array_push($productList, $product);
                    }
                }
            } else {
                $this->db->select('*')->from('product');
                $this->db->where('sub_category_id', $subCategoryId);
                $childCategoryId != '0' ? $this->db->where('child_category_id', $childCategoryId) : '';
                $this->db->where('status', '1');
                $productList = $this->db->get()->result_array();
            }

            $childCategoryList = $this->db->Select('*')->from('childcategory')->where('sub_category_id', $subCategoryId)
                ->where('status', '1')->get()->result_array();

            if (count($productList) > 0) {
                $msg = array(
                    'status' => true, 'message' => 'List of products.',
                    'productList' => $productList,
                    'childCategoryList' => $childCategoryList
                );
                echo json_encode($msg);
            } else {
                $msg = array(
                    'status' => false, 'message' => 'No product found.',
                    'productList' => $productList,
                    'childCategoryList' => $childCategoryList
                );
                echo json_encode($msg);
            }
        } else {
            $errorMsg = strip_tags(validation_errors());
            $msg = array(
                'status' => false, 'message' => $errorMsg,
                'productList' => [],
                'childCategoryList' => []
            );
            echo json_encode($msg);
        }
    }

    public function productDetails()
    {
        $this->form_validation->set_rules('product_id', 'childCategoryId', 'required');

        $productDetails = array(
            "product_id" => "",
            "category_id" => "",
            "sub_category_id" => "",
            "child_category_id" => "",
            "unit_id" => "",
            "product_name" => "",
            "product_name_ar" => '',
            "productdesc_en" => '',
            "productdesc_ar" => '',
            "quantity" => '',
            "product_code" => '',
            "barcode" => '',
            "product_price" => '',
            "offer_price" => '',
            "purchase_price" => '',
            "mini_stock" => "",
            "max_stock" => "",
            "admin_rating" => "",
            "product_status" => "",
            "status" => "",
            "created_at" => "",
            "updated_at" => ""

        );

        if ($this->form_validation->run()) {

            $product_id = $this->security->xss_clean($this->input->post('product_id'));

            $productDetails = $this->db->select('*')->from('product')->where('product_id', $product_id)
                ->where('status', '1')->get()->result_array();
            $responseRelatedProductList = [];
            if (count($productDetails) > 0) {
                $relatedProductList = $this->db->select('*')->from('product_relative')
                    ->where('product_id', $product_id)->where('status', '1')->get()->result_array();
                if (count($relatedProductList) > 0) {
                    foreach ($relatedProductList as $relProduct) {
                        $productDataList = $this->db->select('*')->from('product')
                            ->where('product_id', $product_id)->where('status', '1')->order_by('product_id desc')->get()->result_array();
                        if (count($productDataList) > 0) {
                            array_push($responseRelatedProductList, $productDataList[0]);
                        }
                    }
                }
                $producetComments =  $this->db->select('product_comment.comment,product_comment.adminaction,op_user.user_name')
                    ->from('product_comment')
                    ->join('op_user', 'product_comment.user_id=op_user.op_user_id')
                    ->where('product_comment.product_id', $product_id)
                    ->where('product_comment.adminaction', '1')->where('product_comment.status', '1')->get()->result_array();
                $msg = array(
                    'status' => true, 'message' => 'Product details.',
                    'productDetails' => $productDetails[0],
                    'relProductList' => $responseRelatedProductList,
                    'productComment' => $producetComments
                );
                echo json_encode($msg);
            } else {
                $msg = array(
                    'status' => false, 'message' => 'No product found.',
                    'productDetails' => $productDetails,
                    'relProductList' => $responseRelatedProductList,
                    'productComment' => []
                );
                echo json_encode($msg);
            }
        } else {
            $errorMsg = strip_tags(validation_errors());
            $msg = array(
                'status' => false, 'message' => $errorMsg,
                'productDetails' => $productDetails,
                'relProductList' => [],
                'productComment' => []
            );
            echo json_encode($msg);
        }
    }

    public function searchProduct()
    {
        $this->form_validation->set_rules('searchKeyword', 'childCategoryId', 'required');


        if ($this->form_validation->run()) {
            $searchKeyword = $this->security->xss_clean($this->input->post('searchKeyword'));
            $getProductsList = $this->db->select('product_id,category_id,sub_category_id,child_category_id,
            unit_id,product_name,product_name_ar,productdesc_en,productdesc_ar')->from('product')->where('status', '1')
                // ->group_start()
                ->like('product_name', "$searchKeyword")
                ->or_like('product_name_ar', "$searchKeyword")
                ->or_like('productdesc_en', "$searchKeyword")
                ->or_like('productdesc_ar', "$searchKeyword")
                // ->group_end()
                ->get()->result_array();
            // print_r($getProductsList);
            if (count($getProductsList) > 0) {
                $msg = array(
                    'status' => true, 'message' => 'List of products.',
                    'productList' => $getProductsList
                );
                echo json_encode($msg);
            } else {
                $msg = array(
                    'status' => false, 'message' => 'No product found.',
                    'productList' => [],
                );
                echo json_encode($msg);
            }
        } else {
            $errorMsg = strip_tags(validation_errors());
            $msg = array(
                'status' => false, 'message' => $errorMsg,
                'productList' => [],
                'childCategoryList' => []
            );
            echo json_encode($msg);
        }
    }


    //cart
    public function cartList()
    {
        $this->form_validation->set_rules('userId', 'UserId', 'required');


        if ($this->form_validation->run()) {

            $userId = $this->security->xss_clean($this->input->post('userId'));

            $cartList = $this->db->select('*')->from('cart')->where('user_id', $userId)
                ->where('status', '1')->get()->result_array();
            $cartListData = [];
            if (count($cartList) > 0) {
                foreach ($cartList as $cartData) {
                    $productDetails = $this->db->select('*')->from('product')
                        ->where('product_id', $cartData['product_id'])
                        ->where('status', '1')->order_by('product_id desc')->get()->result_array();
                    if (count($productDetails) > 0) {
                        $cartCost = $cartData['qty'] * $productDetails[0]['offer_price'];
                        $productDetails[0]['cartPrice'] = $cartCost;
                        $productDetails[0]['cartQuantity'] = $cartData['qty'];
                        array_push($cartListData, $productDetails[0]);
                    }
                }
                $msg = array(
                    'status' => true, 'message' => 'cart list.',
                    'cartList' => $cartListData
                );
                echo json_encode($msg);
            } else {
                $msg = array(
                    'status' => false, 'message' => 'No product found in cart.',
                    'cartList' => []
                );
                echo json_encode($msg);
            }
        } else {
            $errorMsg = strip_tags(validation_errors());
            $msg = array(
                'status' => false, 'message' => $errorMsg,
                'cartList' => []
            );
            echo json_encode($msg);
        }
    }

    public function addToCart()
    {

        $this->form_validation->set_rules('userId', 'UserId', 'required');
        $this->form_validation->set_rules('productId', 'product_id', 'required');
        $this->form_validation->set_rules('qty', 'qty', 'required');



        if ($this->form_validation->run()) {

            $userId = $this->security->xss_clean($this->input->post('userId'));
            $product_id = $this->security->xss_clean($this->input->post('productId'));
            $qty = $this->security->xss_clean($this->input->post('qty'));

            $checkCart = $this->db->select('*')->from('cart')->where('user_id', $userId)->where('product_id', $product_id)
                ->where('status', '1')->get()->result_array();
            if (count($checkCart) > 0) {
                $quantity = $checkCart[0]['qty'] + $qty;
                $updateData = $this->db->where('user_id', $userId)->where('product_id', $product_id)->update('cart', array('qty' => $quantity));
            } else {
                $insertdata = $this->db->insert('cart', array('user_id' => $userId, 'product_id' => $product_id, 'qty' => $qty));
            }
            $msg = array(
                'status' => true, 'message' => 'Successfully added to cart'
            );
            echo json_encode($msg);
        } else {
            $errorMsg = strip_tags(validation_errors());
            $msg = array(
                'status' => false, 'message' => $errorMsg
            );
            echo json_encode($msg);
        }
    }

    public function subtractToCart()
    {

        $this->form_validation->set_rules('userId', 'UserId', 'required');
        $this->form_validation->set_rules('productId', 'product_id', 'required');
        $this->form_validation->set_rules('qty', 'qty', 'required');



        if ($this->form_validation->run()) {

            $userId = $this->security->xss_clean($this->input->post('userId'));
            $product_id = $this->security->xss_clean($this->input->post('productId'));
            $qty = $this->security->xss_clean($this->input->post('qty'));

            $checkCart = $this->db->select('*')->from('cart')->where('user_id', $userId)->where('product_id', $product_id)
                ->where('status', '1')->get()->result_array();
            if (count($checkCart) > 0) {
                if ($checkCart[0]['qty'] > $qty) {
                    $quantity = $checkCart[0]['qty'] - $qty;
                    $updateData = $this->db->where('user_id', $userId)->where('product_id', $product_id)->update('cart', array('qty' => $quantity));
                    $msg = array(
                        'status' => true, 'message' => 'Successfully updated in cart.'
                    );
                } else {
                    $msg = array(
                        'status' => false, 'message' => 'Quantity grater or equal present in cart.'
                    );
                }
            } else {
                $msg = array(
                    'status' => false, 'message' => 'Product not present in cart.'
                );
            }

            echo json_encode($msg);
        } else {
            $errorMsg = strip_tags(validation_errors());
            $msg = array(
                'status' => false, 'message' => $errorMsg
            );
            echo json_encode($msg);
        }
    }

    public function deleteInCart()
    {
        $this->form_validation->set_rules('userId', 'UserId', 'required');
        $this->form_validation->set_rules('productId', 'product_id', 'required');

        if ($this->form_validation->run()) {

            $userId = $this->security->xss_clean($this->input->post('userId'));
            $product_id = $this->security->xss_clean($this->input->post('productId'));
            $checkCart = $this->db->select('*')->from('cart')->where('user_id', $userId)->where('product_id', $product_id)
                ->where('status', '1')->get()->result_array();
            if (count($checkCart) > 0) {
                $deleteCartProduct = $this->db
                    ->where('user_id', $userId)->where('product_id', $product_id)
                    ->update('cart', array('status' => '0'));
                $msg = array(
                    'status' => true, 'message' => 'Successfully deleted product from cart.'
                );
                echo json_encode($msg);
            } else {
                $msg = array(
                    'status' => false, 'message' => 'Product not present in cart.'
                );
            }
        } else {
            $errorMsg = strip_tags(validation_errors());
            $msg = array(
                'status' => false, 'message' => $errorMsg
            );
            echo json_encode($msg);
        }
    }


    //wishlist
    public function wishList()
    {
        $this->form_validation->set_rules('userId', 'UserId', 'required');

        if ($this->form_validation->run()) {

            $userId = $this->security->xss_clean($this->input->post('userId'));
            $wishList = $this->db->select('*')->from('wishlist')->where('user_id', $userId)
                ->where('status', '1')->get()->result_array();
            // print_r($wishList);
            $wishListData = [];
            if (count($wishList) > 0) {
                foreach ($wishList as $wishData) {
                    $productDetails = $this->db->select('*')->from('product')
                        ->where('product_id', $wishData['product_id'])
                        ->where('status', '1')->order_by('product_id desc')->get()->result_array();
                    if (count($productDetails) > 0) {
                        array_push($wishListData, $productDetails[0]);
                    }
                }
                if (count($wishListData) > 0) {
                    $msg = array(
                        'status' => true, 'message' => 'List of wishlist.',
                        'productList' => $wishListData
                    );
                    echo json_encode($msg);
                } else {
                    $msg = array(
                        'status' => false, 'message' => 'No product found on wishlist.',
                        'productList' => []
                    );
                    echo json_encode($msg);
                }
            } else {
            }
        } else {
            $errorMsg = strip_tags(validation_errors());
            $msg = array(
                'status' => false, 'message' => $errorMsg,
                'productList' => []
            );
            echo json_encode($msg);
        }
    }

    public function addToWishList()
    {
        $this->form_validation->set_rules('userId', 'UserId', 'required');
        $this->form_validation->set_rules('productId', 'product_id', 'required');



        if ($this->form_validation->run()) {

            $userId = $this->security->xss_clean($this->input->post('userId'));
            $productId = $this->security->xss_clean($this->input->post('productId'));

            $checkWishList = $this->db->select('*')->from('wishlist')->where('user_id', $userId)
                ->where('product_id', $productId)->where('status', '1')->get()->result_array();
            if (count($checkWishList) > 0) {
                $msg = array(
                    'status' => false, 'message' => 'Already added to wishlist.'
                );
                echo json_encode($msg);
            } else {

                $inserttoData = $this->db->insert('wishlist', array(
                    'user_id' => $userId,
                    'product_id' => $productId,
                    'status' => '1'
                ));
                if ($inserttoData) {
                    $msg = array(
                        'status' => true, 'message' => 'Added to wishlist.'
                    );
                    echo json_encode($msg);
                } else {
                    $msg = array(
                        'status' => false, 'message' => 'Something went wrong.'
                    );
                    echo json_encode($msg);
                }
            }
        } else {
            $errorMsg = strip_tags(validation_errors());
            $msg = array(
                'status' => false, 'message' => $errorMsg
            );
            echo json_encode($msg);
        }
    }

    public function deleteFromWishList()
    {
        $this->form_validation->set_rules('userId', 'UserId', 'required');
        $this->form_validation->set_rules('productId', 'product_id', 'required');



        if ($this->form_validation->run()) {

            $userId = $this->security->xss_clean($this->input->post('userId'));
            $productId = $this->security->xss_clean($this->input->post('productId'));

            $checkWishList = $this->db->select('*')->from('wishlist')->where('user_id', $userId)
                ->where('product_id', $productId)->where('status', '1')->get()->result_array();
            if (count($checkWishList) > 0) {
                $delete = $this->db->where('user_id', $userId)
                    ->where('product_id', $productId)->where('status', '1')->update('wishlist', array('status' => '0', 'updated_at' => date('Y-m-d H:i:s')));
                $msg = array(
                    'status' => false, 'message' => 'Successfully removed from wishlist.'
                );
                echo json_encode($msg);
            } else {


                $msg = array(
                    'status' => false, 'message' => 'Not present in wishlist.'
                );
                echo json_encode($msg);
            }
        } else {
            $errorMsg = strip_tags(validation_errors());
            $msg = array(
                'status' => false, 'message' => $errorMsg
            );
            echo json_encode($msg);
        }
    }

    //user comment
    public function addUserComment()
    {
        $this->form_validation->set_rules('userId', 'UserId', 'required');
        $this->form_validation->set_rules('productId', 'product_id', 'required');
        $this->form_validation->set_rules('comment', 'product_id', 'required');



        if ($this->form_validation->run()) {

            $userId = $this->security->xss_clean($this->input->post('userId'));
            $productId = $this->security->xss_clean($this->input->post('productId'));
            $comment = $this->security->xss_clean($this->input->post('comment'));

            $insertComment = $this->db->insert('product_comment', array(
                'product_id' => $productId,
                'user_id' => $userId,
                'comment' => $comment
            ));
            if ($insertComment) {
                $msg = array(
                    'status' => true, 'message' => 'Submitted your comment'
                );
                echo json_encode($msg);
            } else {
                $msg = array(
                    'status' => false, 'message' => 'Not able to submit your comment. Kindly apply after sometime.'
                );
                echo json_encode($msg);
            }
        } else {
            $errorMsg = strip_tags(validation_errors());
            $msg = array(
                'status' => false, 'message' => $errorMsg
            );
            echo json_encode($msg);
        }
    }

    //address
    public function addressList()
    {
        $this->form_validation->set_rules('userId', 'product_id', 'required');



        if ($this->form_validation->run()) {

            $user_id = $this->security->xss_clean($this->input->post('userId'));
            $addressList = $this->db->select('*')->from('user_delivery_address')
                ->where('user_id', $user_id)->where('status', '1')->get()->result_array();
            if (count($addressList) > 0) {
                $msg = array(
                    'status' => true, 'message' => 'Address list found.',
                    'addressList' => $addressList
                );
                echo json_encode($msg);
            } else {
                $msg = array(
                    'status' => false, 'message' => 'No address list found.',
                    'addressList' => $addressList
                );
                echo json_encode($msg);
            }
        } else {
            $errorMsg = strip_tags(validation_errors());
            $msg = array(
                'status' => false, 'message' => $errorMsg,
                'addressList' => []
            );
            echo json_encode($msg);
        }
    }

    public function addressAddition()
    {
        $this->form_validation->set_rules('userId', 'UserId', 'required');
        $this->form_validation->set_rules('roomno', 'product_id', 'required');
        $this->form_validation->set_rules('building', 'product_id', 'required');
        $this->form_validation->set_rules('street', 'product_id', 'required');
        $this->form_validation->set_rules('zone', 'product_id', 'required');
        $this->form_validation->set_rules('latitude', 'product_id', 'required');
        $this->form_validation->set_rules('longitude', 'product_id', 'required');
        $this->form_validation->set_rules('address_type', 'product_id', 'required');



        if ($this->form_validation->run()) {

            $user_id = $this->security->xss_clean($this->input->post('userId'));
            $roomno = $this->security->xss_clean($this->input->post('roomno'));
            $building = $this->security->xss_clean($this->input->post('building'));
            $street = $this->security->xss_clean($this->input->post('street'));
            $zone = $this->security->xss_clean($this->input->post('zone'));
            $latitude = $this->security->xss_clean($this->input->post('latitude'));
            $longitude = $this->security->xss_clean($this->input->post('longitude'));
            $address_type = $this->security->xss_clean($this->input->post('address_type'));

            $inserData = array(
                'user_id' => $user_id,
                'roomno' => $roomno,
                'building' => $building,
                'street' => $street,
                'zone' => $zone,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'address_type' => $address_type
            );
            $insert = $this->db->insert('user_delivery_address', $inserData);
            if ($insert) {
                $msg = array(
                    'status' => true, 'message' => 'Successfully inserted addresss.'
                );
            } else {
                $msg = array(
                    'status' => false, 'message' => 'Failed to register.'
                );
            }
        } else {
            $errorMsg = strip_tags(validation_errors());
            $msg = array(
                'status' => false, 'message' => $errorMsg
            );
            echo json_encode($msg);
        }
    }

    public function adrressRemoval()
    {
        $user_id = $this->security->xss_clean($this->input->post('userId'));
        $addressId = $this->security->xss_clean($this->input->post('addressId'));
        if ($this->form_validation->run()) {

            $user_id = $this->security->xss_clean($this->input->post('userId'));
            $addressId = $this->security->xss_clean($this->input->post('addressId'));
            $checkAddress = $this->db->select('*')->from('user_delivery_address')
                ->where('user_id', $user_id)->where('id', $addressId)->where('status', '1')->get()->result_array();
            if (count($checkAddress) > 0) {
                $deleteAddress = $this->db->where('id', $addressId)->update('user_delivery_address', array('status' => '0'));
                if ($deleteAddress) {
                    $msg = array(
                        'status' => true, 'message' => 'Successfully deleted address.'
                    );
                } else {
                    $msg = array(
                        'status' => false, 'message' => 'Failed to delete kindly apply after sometime.'
                    );
                }
            } else {
                $msg = array(
                    'status' => false, 'message' => 'No such address found.'
                );
            }
        } else {
            $errorMsg = strip_tags(validation_errors());
            $msg = array(
                'status' => false, 'message' => $errorMsg
            );
            echo json_encode($msg);
        }
    }

     public function invoice()
    {
        error_reporting(0);
        
        ini_set('memory_limit', '256M');
                                
                                $pdfFilePath = FCPATH . "uploads/qr_code" . '.pdf';
                                
                                $html = $this->load->view('invoice', array(), true);
                                $this->load->library('m_pdf');
                                $mpdf = new mPDF('utf-8');
                                $mpdf->SetDisplayMode('fullpage');
                                // $mpdf->AddPage('P', 'A4');
                               
                                $mpdf->WriteHTML($html);
                                $mpdf->Output($pdfFilePath, "I");
                                $response['path'] = $pdfFilePaths;
        
    }
}
