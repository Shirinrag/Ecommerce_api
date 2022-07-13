<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once FCPATH.'vendor/autoload.php';

class Smsglobal {

    public function sms_send($contact_number="",$message="")
    {
            // get your REST API keys from MXT https://mxt.smsglobal.com/integrations
            \SMSGlobal\Credentials::set('08bd63f34181761be697b6c58cbf2b8b', '1e51c5175f36ee2ccfaecf4d8367fb01');
                $sms = new \SMSGlobal\Resource\Sms();
            try {
                $response = $sms->sendToOne($contact_number, $message);
                print_r($response['messages'][0]);
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
            return @$response;
    }
     
}

