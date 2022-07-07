<?php
    include_once(APPPATH . '/third_party/phpseclib/Net/SFTP.php');
    include_once(APPPATH . '/third_party/phpseclib/Crypt/RC4.php');
    include_once(APPPATH . '/third_party/phpseclib/Crypt/Rijndael.php');
    include_once(APPPATH . '/third_party/phpseclib/Crypt/Twofish.php');
    include_once(APPPATH . '/third_party/phpseclib/Crypt/Blowfish.php');
    include_once(APPPATH . '/third_party/phpseclib/Crypt/TripleDES.php');
    include_once(APPPATH . '/third_party/phpseclib/Crypt/Random.php');
    include_once(APPPATH . '/third_party/phpseclib/Math/BigInteger.php');
    include_once(APPPATH . '/third_party/phpseclib/Crypt/Hash.php');

	class Phpseclib {
		public function read_sftp_pdf_file($prefix='',$username='',$password='',$prefix_sample_type='',$path='')
        {
			$sftp = new Net_SFTP("helixds.exavault.com");
            if (!$sftp->login($username, $password))
            {
                $filename = '';
            } else {
                if (empty($path)) {
                    $path = "/Results";
                }
            	$list = $sftp->nlist($path);
            	if ($list === false)
	            {

	                $filename = '';
	            } else {
                    $matches = array();
	            	$sample_type_info = array();
	            	foreach ($prefix as $prefix_key => $prefix_row) {
                        $match = preg_grep("/^$prefix_row.*/i", $list);
                        // $rty=$this->array_search_partial($list,$prefix_row);
                        // $match = $list[$rty];
                        array_push($matches,$match);
	            		array_push($sample_type_info,$prefix_sample_type[$prefix_key]);
	            	}
                    // echo '<pre>'; print_r($matches);
                      // exit;
		            if (count($matches) == 0)
		            {
		               $filename = '';
		            } else {
		                $matches = array_values($matches);
                        $filename = array_flatten($matches);

                        $sample_type_info = array_values($sample_type_info);
                        $sample_type_info_1 = array_flatten($sample_type_info);
                        foreach ($filename as $filename_key => $filename_row) {
                            if($filename_row)
                            {   
                                if ($sample_type_info_1[$filename_key]==2) {
                                    $local_folder = 'test_swab_upload';
                                } else {
                                    $local_folder = 'test_saliva_upload';
                                }
                                $local_file_path=FCPATH.$local_folder.'/'.$filename_row;
                                $sftp->get($path.'/'.$filename_row, $local_file_path);
                            }
                        }
		            }
	            }
            }
            unset($sftp);
            return $filename;
        }

        public function delete_file($filename='',$credentials='')
        {
            $sftp = new Net_SFTP("helixds.exavault.com");
            if (!$sftp->login($credentials['username'],$credentials['password']))
            {
                $filename = '';
            } else {
                $sftp->delete($filename.'remote');
            }
            unset($sftp);
            return true;
        }

        function array_search_partial($arr, $keyword) {
            foreach($arr as $index => $string) {
                if (strpos($string, $keyword) !== FALSE)
                    return $index;
            }
        }
    }