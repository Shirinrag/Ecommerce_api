<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'third_party/mpdf_vendor/vendor/autoload.php';
class Pdf {
	function generate_pdf(){
		$CI =& get_instance();
		$html = $CI->load->view('invoice',[],true);;
		$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4','0','',0,0,0,0,'orientation' => 'P']);
		// $mpdf->setAutoTopMargin = 'stretch';
		// $mpdf->setAutoBottomMargin = 'stretch';
		$mpdf->WriteHTML($html);
		$mpdf->Output();
	}

}