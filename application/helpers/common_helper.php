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
    function get_user_wishlist_count($user_id=''){
        $CI = get_instance();
        $cart_count = 0;
        $cart_count_data = $CI->model->selectwhereData('wishlist',array('user_id'=>$user_id),array('count(wishlist.user_id) as wishlist_count'));
        if(!empty(@$cart_count_data['wishlist_count'])){
            $cart_count=@$cart_count_data['wishlist_count'];
        }
        $cart_count = (string)$cart_count;
        return $cart_count;
    }

    function custom_number_format($number='',$limit=''){
        if(!empty($number)){
            if(empty($limit)){
                $limit = 2;
            }
            $number_1 = number_format($number,$limit);
            $number_2 = str_replace(",","",$number_1);
        } else {
            $number_2 = strval(0);
        }
        return $number_2;
    }

    function get_lat_long()
    {
        $response['client_latitude'] = 25.2730664;
        $response['client_longitude']= 51.4838876;

        return $response;
    }

    function distance($lat1, $lon1, $lat2, $lon2, $unit) {
          if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
          }
          else {
            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $unit = strtoupper($unit);

            if ($unit == "K") {
              return ($miles * 1.609344);
            } else if ($unit == "N") {
              return ($miles * 0.8684);
            } else {
              return $miles;
            }
        }
    }

    // function distance1($lat1="", $lon1="", $lat2="", $lon2="", $unit="") {
    //     $radiusOfEarth = 6371000;// Earth's radius in meters.
    //     $diffLatitude = $lat1 - $lat2;
    //     $diffLongitude = $lon1 - $lon2;
    //     $a = sin($diffLatitude / 2) * sin($diffLatitude / 2) +
    //         cos($lat1) * cos($lat2) *
    //         sin($diffLongitude / 2) * sin($diffLongitude / 2);
    //     $c = 2 * asin(sqrt($a));
    //     $distance = $radiusOfEarth * $c;
    //     return $distance;
    // }
    function distance1($latitude1="", $longitude1="", $latitude2="", $longitude2="", $unit="") {
          $theta = $longitude1 - $longitude2; 
          $distance = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta))); 
          $distance = acos($distance); 
          $distance = rad2deg($distance); 
          $distance = $distance * 60 * 1.1515; 

          switch($unit) { 
            case 'miles': 
              break; 
            case 'kilometers' : 
              $distance = $distance * 1.609344; 
        } 
        return (round($distance)); 
    }
// function send_email($to,$subject,$message,$attach=''){
//     // error_reporting(0);
//     $from = "info@circuitstore.qa";
//     $CI = get_instance();
//     $CI->load->library('email');
//     // $email_data = $CI->load->view('new_email_templete', $message, true);
//     $CI->email->set_mailtype("html");
//     $CI->email->from($from);
//     $CI->email->to($to);
//     $CI->email->subject($subject);
//      $CI->email->message($email_data);
//     $CI->email->attach($attach);
//     $CI->email->send();
// }

function send_email($to="",$subject="",$message="",$attach=''){
    // error_reporting(0);
    $from = " noreply@circuitstore.qa";
    $CI = get_instance();
    $CI->load->library('email');
    // $email_data = $CI->load->view('new_email_templete', $message, true);
    $CI->email->set_mailtype("html");
    $CI->email->from($from);
    $CI->email->to($to);
    $CI->email->subject($subject);
     $CI->email->message($message);
    $CI->email->attach($attach);
    $CI->email->send();
}

function get_random_strings($tbl_name='',$column_name='')
{
    $CI = get_instance();
    $randTemp = mt_rand(100000, 999999);
    $isUnique = true;
    do {
         $result = $CI->db->get_where($tbl_name, array($column_name => $randTemp));
        if ($result->num_rows() > 0) {
            $isUnique = false;
        } else {
            $isUnique = true;
        }
    } 
    while ($isUnique == false);
    return $randTemp;
}
