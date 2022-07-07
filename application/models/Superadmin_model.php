<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// error_reporting(0);
class Superadmin_model extends CI_Model {
	 function __construct() {
        parent::__construct();
     }
    
    public function get_product_on_search($search="",$language_name="")
    {
        $this->db->select('product_id,category_id,sub_category_id,child_category_id,
            unit_id,product_name,product_name_ar,productdesc_en,productdesc_ar');
        $this->db->from('product');
        $this->db->join('tbl_gallery','tbl_gallery_details.fk_gallery_id=tbl_gallery.id','left');
        if($language_name=="Arabic"){
                $this->db->or_like('product_name_ar',$search);
                $this->db->or_like('productdesc_ar',$search);
        }else{
            $this->db->or_like('product_name',$search);    
            $this->db->or_like('productdesc_en',$search);
        }
      
         $query=$this->db->get();
        return $query->row_array();
    }
}
