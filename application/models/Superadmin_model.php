<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// error_reporting(0);
class Superadmin_model extends CI_Model {
	 function __construct() {
        parent::__construct();
     }
    
    public function get_product_on_search($search="",$fk_lang_id="")
    {
        $this->db->select('*');
        $this->db->from('product');
        $this->db->join('product_gallery','product_gallery.product_id=product.product_id','left');
        $this->db->or_like('product.product_name',$search);    
        // $this->db->or_like('product.productdesc_en',$search);
      
         $query = $this->db->get();
            $result = $query->row_array();
            return $result;
    }

    public function get_dynamic_cat($fk_lang_id=""){
            $this->db->select('category.category_id,category.category_name,
            GROUP_CONCAT(subcategory.sub_category_name) as sub_category_name,
            GROUP_CONCAT(subcategory.sub_category_id) as sub_category_id');
            $this->db->from('category');
            $this->db->join('subcategory','subcategory.category_id=category.category_id','left');
            $this->db->where('category.status',1);
            $this->db->where('category.fk_lang_id',$fk_lang_id);
            $this->db->group_by('category.category_id');         
       
            $query = $this->db->get();
            $result = $query->result_array();
            return $result;
    }

     public function get_dynamic_childcat($id="",$fk_lang_id=""){
        $this->db->select('child_category_name,child_category_id');
        $this->db->from('childcategory');     
        $this->db->where('childcategory.sub_category_id',$id);
        $this->db->where('childcategory.fk_lang_id',$fk_lang_id);
        
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

}
