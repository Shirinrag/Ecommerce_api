<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// error_reporting(0);
ini_set("memory_limit", "-1");
class Superadmin_model extends CI_Model {
	 function __construct() {
        parent::__construct();
     }
    
    public function get_product_on_search($search="",$fk_lang_id="")
    {
        $this->db->select('product_name');
        $this->db->from('product');
        $this->db->like('product.product_name',$search);    
        // $this->db->or_like('product.productdesc_en',$search);
      
         $query = $this->db->get();
            $result = $query->result_array();
            return $result;
    }

    public function get_dynamic_cat($fk_lang_id=""){
            $this->db->select('category.category_id,category.category_name,category.category_name_ar,
            GROUP_CONCAT(subcategory.sub_category_name) as sub_category_name,
            GROUP_CONCAT(subcategory.sub_category_name_ar) as sub_category_name_ar,
            GROUP_CONCAT(subcategory.sub_category_id) as sub_category_id');
            $this->db->from('category');
            $this->db->join('subcategory','subcategory.category_id=category.category_id','left');
            $this->db->where('category.status',1);
            // $this->db->where('category.fk_lang_id',$fk_lang_id);
            $this->db->group_by('category.category_id');         
       
            $query = $this->db->get();
            $result = $query->result_array();
            return $result;
    }

     public function get_dynamic_childcat($id="",$fk_lang_id=""){
        $this->db->select('child_category_name,child_category_id,child_category_name_ar');
        $this->db->from('childcategory');     
        $this->db->where('childcategory.sub_category_id',$id);
        // $this->db->where('childcategory.fk_lang_id',$fk_lang_id);
        
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

      public function verify_otp($table, $mobile_no,$otp) {
        $response = array();
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where('contact_no', $mobile_no);
        $query = $this->db->get();
        $result = $query->row_array();   
        @$db_otp = $result['otp'];
        if($db_otp == $otp)
        {
            $created_at = $result['added_on'];
            $timestamp = strtotime($created_at);
            $t = date('Y-m-d H:i:s');
            $latest_time = strtotime($t);
            $interval = abs($latest_time - $timestamp);
            $minutes = round($interval / 60);
            // if ($minutes >= 15) {
            //     $response['message'] = "15 minutes exceeded";
            //     $response['status'] = "0";
            // } else {
                $this->db->set('otp_verify_status', '1'); //value that used to update column
                $this->db->where('contact_no', $mobile_no); //which row want to upgrade  
                $this->db->update($table);
                $response['status'] = "1";
                $response['message'] = "Otp Verified Successfully";
                $response['code'] = 200;
                $response['op_user_id'] = $result['op_user_id'];
                $response['data']=$result;
            // }
        } else {
            $response['message'] = "otp mismatch";
            $response['status'] = "0";
            $response['code']=201;
        }
        return $response;
    }

    public function get_cart_data($user_id="",$fk_lang_id="")
    {
       $this->db->select('cart.*,cart.qty as cart_qty,product.*');
       $this->db->from('cart');
       $this->db->join('product','cart.product_id=product.product_id','left');
       $this->db->where('cart.user_id',$user_id);
       // $this->db->where('product.fk_lang_id',$fk_lang_id);
        $this->db->where('cart.status','1');
       $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

    public function get_order_summary_info($user_id=''){
        $this->db->select('cart.cart_id,cart.product_id as cart_product_id,cart.qty as cart_qty,
        product.*');
        $this->db->from('cart');      
        $this->db->join('product','product.product_id=cart.product_id', 'left');
       $this->db->join('inventory','inventory.product_id=product.product_id','left');
        $this->db->where('cart.user_id',$user_id);
        $this->db->where('cart.status','1');
        $this->db->where('product.status','1');
        $this->db->where('inventory.status','1');
        $this->db->order_by('cart.cart_id','DESC'); 
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

    public function product_details_on_id($product_id='')
    {
        $this->db->select('product.*,GROUP_CONCAT(product_gallery.img_url) as img_url,category.category_name,subcategory.sub_category_name,childcategory.child_category_name');
        $this->db->from('product');      
        $this->db->join('product_gallery','product_gallery.product_id=product.product_id','left');
        $this->db->join('category','product.category_id=category.category_id','left');
        $this->db->join('subcategory','product.sub_category_id=subcategory.sub_category_id','left');
        $this->db->join('childcategory','childcategory.child_category_id=product.child_category_id','left');
        $this->db->where('product.product_id',$product_id);
        // $this->db->where('product.fk_lang_id',$fk_lang_id);
        $this->db->where('product.status','1');
        $this->db->group_by('product_gallery.product_id');
        $query = $this->db->get();
        $result = $query->row_array();
        return $result;
    }

    public function related_product_details_on_id($product_id='',$fk_lang_id="")
    {
         $this->db->select('product_relative.rel_product_id,product.*,GROUP_CONCAT(product_gallery.img_url) as img_url,category.category_name,subcategory.sub_category_name,childcategory.child_category_name');
        $this->db->from('product_relative');      
        $this->db->join('product','product_relative.product_id=product.product_id','left');
        $this->db->join('product_gallery','product_gallery.product_id=product.product_id','left');
        $this->db->join('category','product.category_id=category.category_id','left');
        $this->db->join('subcategory','product.sub_category_id=subcategory.sub_category_id','left');
        $this->db->join('childcategory','childcategory.child_category_id=product.child_category_id','left');

        $this->db->where('product.product_id',$product_id);
        // $this->db->where('product.fk_lang_id',$fk_lang_id);
        $this->db->where('product.status','1');
        $this->db->group_by('product.product_id');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

    public function get_wishlist_data($user_id='')
    {
      $this->db->select('wishlist.user_id,wishlist.id,product.*,GROUP_CONCAT(product_gallery.img_url) as img_url');
        $this->db->from('wishlist');      
        $this->db->join('product','product.product_id=wishlist.product_id','left');
        $this->db->join('product_gallery','product_gallery.product_id=product.product_id','left');
        $this->db->where('wishlist.user_id',$user_id);
        $this->db->where('wishlist.status','1');
        $this->db->group_by('product_gallery.product_id');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

    public function get_search_product($fk_lang_id="")
    {
        $this->db->select('product.product_id,product.product_name,product.product_price,product.image_name,');
        $this->db->from('product');
        // $this->db->where('product.fk_lang_id',$fk_lang_id);  
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

    public function get_all_product_data($fk_lang_id="")
    {
        $this->db->select('product.*');
        $this->db->from('product');
        // $this->db->where('product.fk_lang_id',$fk_lang_id);        
        $this->db->where('product.status','1');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }
    public function get_all_popular_product_data($fk_lang_id="")
    {
        $this->db->select('product.*');
        $this->db->from('product');
        // $this->db->where('product.fk_lang_id',$fk_lang_id);  
        $this->db->where('product.popular','1'); 
         $this->db->where('product.status','1');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }
    public function get_all_featured_product_data($fk_lang_id="")
    {
        $this->db->select('product.*');
        $this->db->from('product');
        // $this->db->where('product.fk_lang_id',$fk_lang_id);  
        $this->db->where('product.featured','1');  
         $this->db->where('product.status','1');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }
     public function get_all_best_selling_product_data($fk_lang_id="")
    {
        $this->db->select('product.*');
        $this->db->from('product');
        // $this->db->where('product.fk_lang_id',$fk_lang_id);  
        $this->db->where('product.best_selling',1);  
         $this->db->where('product.status','1');
    
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

    public function order_history($user_id='')
    {
        $this->db->select('order_data.id,order_data.order_number,order_data.quantity,order_data.grand_total,product.product_name,product.image_name');
        $this->db->from('order_data');
        $this->db->join('product','order_data.fk_product_id=product.product_id','left');
        $this->db->where('order_data.fk_user_id',$user_id);     
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

    public function order_history_on_order_id($id='')
    {
        $this->db->select('order_data.id,order_data.order_number,order_data.quantity,order_data.grand_total,product.product_name,product.image_name,GROUP_CONCAT(tbl_order_status.status) as status,GROUP_CONCAT(tbl_order_status_master.order_status) as order_status');
        $this->db->from('order_data');
        $this->db->join('product','order_data.fk_product_id=product.product_id','left');
        $this->db->join('tbl_order_status','order_data.fk_product_id=tbl_order_status.fk_order_id','left');
        $this->db->join('tbl_order_status_master','tbl_order_status.status=tbl_order_status_master.id','left');
        $this->db->where('order_data.id',$id);   
        $this->db->group_by('tbl_order_status.status');  
        $query = $this->db->get();
        $result = $query->row_array();
        return $result;
    }
}
