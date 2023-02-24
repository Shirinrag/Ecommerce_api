<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once FCPATH.'vendor/autoload.php';

class Smsglobal {

    public function sms_send($contact_number="",$message="")
    {
            // get your REST API keys from MXT https://mxt.smsglobal.com/integrations
            \SMSGlobal\Credentials::set('b7ed804ba7caa5fd25827e1b225865ce', '6960b7e36a025717d118c3e3c854c26f');
                $sms = new \SMSGlobal\Resource\Sms();
            try {
                $response = $sms->sendToOne($contact_number, $message,"Circuit App");
                // print_r($response['messages'][0]);
            } catch (\Exception $e) {
                // echo $e->getMessage();
            }
            
            return @$response;
    }
     
}