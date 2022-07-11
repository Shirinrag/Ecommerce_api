<?php

function generatePassword() {
    $length = 8;
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $password = substr(str_shuffle($chars), 0, $length);
    // $pass = encrypt($password);
    return $password;
}
function generateOTP() {
    return mt_rand(1000,9999);
}

function generateid() {
    return mt_rand(1000,9999);
}

function dec_enc($action, $string)
{
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $secret_key = 'circuitstore key';
    $secret_iv = 'circuitstore iv';
    $key = hash('sha256', $secret_key);
    $iv = substr(hash('sha256', $secret_iv), 0, 16);
    if ($action == 'encrypt') {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    } elseif ($action == 'decrypt') {
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }
    return $output;
}

function add_user_log($user_log)
{
    $CI = get_instance();
    $data = array(
    "fk_user_id" => $user_log['user_id'],
    "change_by" => $user_log['add/change'],
    "type" => $user_log['type'],
    "user_type" => $user_log['user_type'],
    "sql_query" =>$user_log['sql_query']
    );
    $response = $CI->model->insertData('tbl_user_log', $data);
}

    function clean($string) {
        $string = preg_replace('/\s/', '', $string); // Removes all whitespaces.
        $result= preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
        return $result;
    }

    function get_ip()
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    function get_date($format='')
    {
        
        $timezone_identifier = date_default_timezone_get();
        date_default_timezone_set($timezone_identifier);
         if (empty($format)) {
            $format = 'Y-m-d H:i:s';
        }
        $date = date($format);
        return $date;
    }
    function token_get(){
        $tokenData = array();
        $tokenData['id'] = mt_rand(10000,99999); //TODO: Replace with data for token
        $output['token'] = AUTHORIZATION::generateToken($tokenData);
        return $output['token'];
    }
  function get_user_cart_count($user_id=''){
        $CI = get_instance();
        $cart_count = 0;
        $cart_count_data = $CI->model->selectwhereData('cart',array('user_id'=>$user_id),array('SUM(cart.qty) as cart_count'));
        if(!empty(@$cart_count_data['cart_count'])){
            $cart_count=@$cart_count_data['cart_count'];
        }
        $cart_count = (string)$cart_count;
        return $cart_count;
    }