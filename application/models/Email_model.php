<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Email_model extends CI_Model {

	public function register_email($email_id='',$password='',$name='')
	{
        if (!empty($email_id)) {
            $email_template_info = $this->model->selectwhereData('email_template',array('template_name'=>'Registration','del_status'=>'Active'),array('subject','email_body'));
            $dynamic_data = array("{emailid}","{password}"); 
            $dynamic_value = array($email_id,$password);
            $email_data_post['email_txt']=str_replace($dynamic_data,$dynamic_value,$email_template_info['email_body']); 
            $email_data_post['message']=$email_template_info['subject']; 
            $email_data_post['name']=$name; 
            email($email_id,$email_template_info['subject'],$email_data_post);
            return true;
        } else {
            return true;
        }
	}

    public function kit_order_email($email_id='',$name='',$order_no='',$kit_type='',$order_date='')
    {
         if (!empty($email_id)) {
            $email_template_info = $this->model->selectwhereData('email_template',array('template_name'=>'Kit Order','del_status'=>'Active'),array('subject','email_body'));
            $dynamic_data = array("{orderno}","{kitType}","{date}"); 
            $dynamic_value = array($order_no,$kit_type,$order_date);
            $email_data_post['email_txt']=str_replace($dynamic_data,$dynamic_value,$email_template_info['email_body']); 
            $email_data_post['message']=$email_template_info['subject']; 
            $email_data_post['name']=$name;
            email($email_id,$email_template_info['subject'],$email_data_post);
            return true;
        } else {
            return true;
        }
    }

    public function video_call_appointment_email($email_id='',$name='',$navigator_name='',$appointment_date='',$appointment_time='',$appointment_number='',$video_appointment_link='')
    {
         if (!empty($email_id)) {
            $email_template_info = $this->model->selectwhereData('email_template',array('template_name'=>'Video Call Appointment','del_status'=>'Active'),array('subject','email_body'));
            $dynamic_data = array("{NavigatorName}","{Date}","{Time}","{Appno}","{clickhere}"); 
            $dynamic_value = array($navigator_name,$appointment_date,$appointment_time,$appointment_number,'<a href="'.$video_appointment_link.'"><button type="button" class="btn btn-primary">Connect Now</button></a>');
            $email_data_post['email_txt']=str_replace($dynamic_data,$dynamic_value,$email_template_info['email_body']); 
            $email_data_post['message']=$email_template_info['subject']; 
            $email_data_post['name']=$name; 
            email($email_id,$email_template_info['subject'],$email_data_post);
            return true;
        } else {
            return true;
        }
    }

    public function face_to_face_appointment_email($email_id='',$name='',$navigator_name='',$navigator_address='',$navigator_contact_no='',$appointment_date='',$appointment_time='',$appointment_number='')
    {
         if (!empty($email_id)) {
            $email_template_info = $this->model->selectwhereData('email_template',array('template_name'=>'Face to Face Appointment','del_status'=>'Active'),array('subject','email_body'));
            $dynamic_data = array("{NavigatorName}","{NavigatorAddress}","{NavigatorContactNo}","{Date}","{Time}","{Appno}"); 
            $dynamic_value = array($navigator_name,$navigator_address,$navigator_contact_no,$appointment_date,$appointment_time,$appointment_number);
            $email_data_post['email_txt']=str_replace($dynamic_data,$dynamic_value,$email_template_info['email_body']); 
            $email_data_post['message']=$email_template_info['subject']; 
            $email_data_post['name']=$name;
            email($email_id,$email_template_info['subject'],$email_data_post);
            return true;
        } else {
            return true;
        }
    }

    public function phone_call_appointment_email($email_id='',$name='',$navigator_name='',$navigator_contact_no='',$appointment_date='',$appointment_time='',$appointment_number='')
    {
         if (!empty($email_id)) {
            $email_template_info = $this->model->selectwhereData('email_template',array('template_name'=>'Phone Call Appointment','del_status'=>'Active'),array('subject','email_body'));
            $dynamic_data = array("{NavigatorName}","{NavigatorContactNo}","{Date}","{Time}","{Appno}"); 
            $dynamic_value = array($navigator_name,$navigator_contact_no,$appointment_date,$appointment_time,$appointment_number);
            $email_data_post['email_txt']=str_replace($dynamic_data,$dynamic_value,$email_template_info['email_body']); 
            $email_data_post['message']=$email_template_info['subject']; 
            $email_data_post['name']=$name; 
            email($email_id,$email_template_info['subject'],$email_data_post);
            return true;
        } else {
            return true;
        }
    }

    public function video_call_appointment_rescheduled_email($email_id='',$name='',$navigator_name='',$appointment_date='',$appointment_time='',$appointment_number='',$video_appointment_link='',$reson='')
    {
         if (!empty($email_id)) {
            $email_template_info = $this->model->selectwhereData('email_template',array('template_name'=>'Rescheduled Video Appointment','del_status'=>'Active'),array('subject','email_body'));
            $dynamic_data = array("{NavigatorName}","{Date}","{Time}","{Appno}","{reason}","{clickhere}"); 
            $dynamic_value = array($navigator_name,$appointment_date,$appointment_time,$appointment_number,$reason,'<a href="'.$video_appointment_link.'"><button type="button" class="btn btn-primary">Connect Now</button></a>');
            $email_data_post['email_txt']=str_replace($dynamic_data,$dynamic_value,$email_template_info['email_body']); 
            $email_data_post['message']=$email_template_info['subject']; 
            $email_data_post['name']=$name; 
            email($email_id,$email_template_info['subject'],$email_data_post);
            return true;
        } else {
            return true;
        }
    }

    public function face_to_face_appointment_rescheduled_email($email_id='',$name='',$navigator_name='',$navigator_address='',$navigator_contact_no='',$appointment_date='',$appointment_time='',$appointment_number='',$reson='')
    {
         if (!empty($email_id)) {
            $email_template_info = $this->model->selectwhereData('email_template',array('template_name'=>'Rescheduled Face To Face Appointment','del_status'=>'Active'),array('subject','email_body'));
            $dynamic_data = array("{NavigatorName}","{NavigatorAddress}","{NavigatorContactNo}","{Date}","{Time}","{reason}","{Appno}"); 
            $dynamic_value = array($navigator_name,$navigator_address,$navigator_contact_no,$appointment_date,$appointment_time,$reason,$appointment_number);
            $email_data_post['email_txt']=str_replace($dynamic_data,$dynamic_value,$email_template_info['email_body']); 
            $email_data_post['message']=$email_template_info['subject']; 
            $email_data_post['name']=$name;
            email($email_id,$email_template_info['subject'],$email_data_post);
            return true;
        } else {
            return true;
        }
    }

    public function phone_call_appointment_rescheduled_email($email_id='',$name='',$navigator_name='',$navigator_contact_no='',$appointment_date='',$appointment_time='',$appointment_number='',$reson='')
    {
         if (!empty($email_id)) {
            $email_template_info = $this->model->selectwhereData('email_template',array('template_name'=>'Rescheduled Phone Call Appointment','del_status'=>'Active'),array('subject','email_body'));
            $dynamic_data = array("{NavigatorName}","{contact_no}","{Date}","{Time}","{reason}","{Appno}"); 
            $dynamic_value = array($navigator_name,$appointment_date,$appointment_time,$reason,$appointment_number);
            $email_data_post['email_txt']=str_replace($dynamic_data,$dynamic_value,$email_template_info['email_body']); 
            $email_data_post['message']=$email_template_info['subject']; 
            $email_data_post['name']=$name; 
            email($email_id,$email_template_info['subject'],$email_data_post);
            return true;
        } else {
            return true;
        }
    }

    public function sample_submitted_email($email_id='',$name='',$Sample_type='')
    {
         if (!empty($email_id)) {
            $email_template_info = $this->model->selectwhereData('email_template',array('template_name'=>'Sample Submitted','del_status'=>'Active'),array('subject','email_body'));
            $dynamic_data = array("{sampleType}"); 
            $dynamic_value = array($Sample_type);
            $email_data_post['email_txt']=str_replace($dynamic_data,$dynamic_value,$email_template_info['email_body']); 
            $email_data_post['message']=$email_template_info['subject']; 
            $email_data_post['name']=$name; 
            email($email_id,$email_template_info['subject'],$email_data_post);
            return true;
        } else {
            return true;
        }
    }

    public function forget_password_email($email_id='',$name='',$password='')
    {
         if (!empty($email_id)) {

            $email_template_info = $this->model->selectwhereData('email_template',array('template_name'=>'Forget password','del_status'=>'Active'),array('subject','email_body'));
             $dynamic_data = array("{password}"); 
            $dynamic_value = array($password);
            $email_data_post['email_txt']=str_replace($dynamic_data,$dynamic_value,$email_template_info['email_body']);
            $email_data_post['message']=$email_template_info['subject']; 
            $email_data_post['name']=$name; 
            email($email_id,$email_template_info['subject'],$email_data_post);


            return true;
        } else {
            return true;
        }
    }

    public function change_password_email($email_id='',$name='',$password='')
    {
         if (!empty($email_id)) {
            $email_template_info = $this->model->selectwhereData('email_template',array('template_name'=>'Change password','del_status'=>'Active'),array('subject','email_body'));
            $email_data_post['email_txt']=$email_template_info['email_body'];
            $email_data_post['message']=$email_template_info['subject']; 
            $email_data_post['name']=$name; 
            email($email_id,$email_template_info['subject'],$email_data_post);
            return true;
        } else {
            return true;
        }
    }

    public function member_result_email($email_id='',$name='')
    {
         if (!empty($email_id)) {
            $email_template_info = $this->model->selectwhereData('email_template',array('template_name'=>'Member Result','del_status'=>'Active'),array('subject','email_body'));
            $email_data_post['email_txt']=$email_template_info['email_body'];
            $email_data_post['message']=$email_template_info['subject']; 
            $email_data_post['name']=$name; 
            email($email_id,$email_template_info['subject'],$email_data_post);
            return true;
        } else {
            return true;
        }
    }

    public function org_admin_result_email($email_id='',$name='')
    {
         if (!empty($email_id)) {
            $email_template_info = $this->model->selectwhereData('email_template',array('template_name'=>'Org Admin Result','del_status'=>'Active'),array('subject','email_body'));
            $email_data_post['email_txt']=$email_template_info['email_body'];
            $email_data_post['message']=$email_template_info['subject']; 
            $email_data_post['name']=$name; 
            email($email_id,$email_template_info['subject'],$email_data_post);
            return true;
        } else {
            return true;
        }
    }

    public function local_result_email($email_id='',$name='')
    {
         if (!empty($email_id)) {
            $email_template_info = $this->model->selectwhereData('email_template',array('template_name'=>'Local member Result','del_status'=>'Active'),array('subject','email_body'));
            $email_data_post['email_txt']=$email_template_info['email_body'];
            $email_data_post['message']=$email_template_info['subject']; 
            $email_data_post['name']=$name; 
            email($email_id,$email_template_info['subject'],$email_data_post);
            return true;
        } else {
            return true;
        }
    }
}