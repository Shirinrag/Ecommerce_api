<?php
	
	class Hl {
		public function read_hl_file($filename='')
        {
        	include_once  'vendor/autoload.php';
			$fopen = fopen($filename, r);
			$fread = fread($fopen,filesize($filename));
			fclose($fopen);
        	$adt_a01 = new HL7\Message($fread); // Either \n or \r can be used as segment endings
			$patient_info = $adt_a01->getSegmentByIndex(1);
			$patient_sample_id = $patient_info->getField(3);
		  	$patient_name_info = $patient_info->getField(5);
		  	$patient_residencial_info = $patient_info->getField(11);
		  	$report_info['patient_name'] = implode(" ",$patient_name_info);
		  	$report_info['patient_sample_id'] = $patient_sample_id;
		  	$report_info['address_one'] = @$patient_residencial_info[0];
		  	$report_info['address_two'] = @$patient_residencial_info[1];
		  	$report_info['city'] = @$patient_residencial_info[2];
		  	$report_info['state'] = @$patient_residencial_info[3];
		  	$report_info['zipcode'] = @$patient_residencial_info[4];
		  	$report_info['country'] = @$patient_residencial_info[5];
		  	$provider_info = $adt_a01->getSegmentByIndex(2);
		  	$provider_name_info = $provider_info->getField(7);
		  	unset($provider_name_info[0]);
		  	$report_info['provider_name'] = implode(" ",$provider_name_info);
		  	$test_result_info = $adt_a01->getSegmentByIndex(5);
		  	$report_test_result = $test_result_info->getField(5);
		  	if (strpos($report_test_result, 'Not Detected') !== false) {
    			$test_result='Negative';
			} else {
				$test_result='Positive';
			}
			$report_info['report_test_result'] = $report_test_result;
			$report_info['test_result'] = $test_result;
		   	return $report_info;
        }
    }