<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Email_model extends CI_Model {

 public function order_placed_email($email_id='', $name='', $order_no='', $date="", $amount='')
    {
        if (!empty($email_id)) {
            $email_template_info = $this->model->selectWhereData('email_template', array('subject'=>'Order Placed','status'=>'1'), array('subject','body'));
            $dynamic_data = array("{order_id}","{date}","{amount}");
            $dynamic_value = array($order_no,$date,$amount);

            $email_data_post['email_txt']=str_replace($dynamic_data, $dynamic_value, $email_template_info['body']);
            $email_data_post['message']=$email_template_info['subject'];
            $email_data_post['name']=$name;
            $subject = $email_template_info['subject']." ".'#'.$order_no;
            send_email($email_id,$subject , $email_data_post);
            return true;
        } else {
            return true;
        }
    }
}