<?php
	
	class Pdf_parser {
		public function read_pdf_file($filename='')
        {
        	include 'pdf_parser_vendor/autoload.php';
			$parser = new \Smalot\PdfParser\Parser();
			$pdf    = $parser->parseFile($filename);
			$details  = $pdf->getDetails();
			$text = $pdf->getText();
			$remove = "\n";

		    $split = explode($remove, $text);

		    $array[] = null;
		    $tab = "\t";

		    foreach ($split as $string)
		    {
		        $row = explode($tab, $string);
		        array_push($array,$row);
		    }
		    $patient_residencial_info = @$array[163];
		    $patient_residencial_info = explode(" ",$patient_residencial_info[0]);
		    $report_info['patient_name'] = @$array[122][0];
		  	$report_info['patient_sample_id'] = @$array[147][0];
		  	$report_info['address'] = @$array[161][0];
		  	$report_info['city'] = @$patient_residencial_info[0];
		  	$report_info['state'] = @$patient_residencial_info[1];
		  	$report_info['zipcode'] = @$patient_residencial_info[2];
		  	$report_info['patient_dob'] =@$array[124][0];
		  	$report_info['patient_age'] =@$array[126][0];
		  	$report_info['patient_id'] =@$array[128][0];
		  	$report_info['gender'] =@$array[131][0];
		  	$report_info['provider_name'] =@$array[155][0];
		  	$report_info['report_test_result']=@$array[26][0];
		  	$report_info['report_date']=@$array[144][0];
			return $report_info;
        }
    }