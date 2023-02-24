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
             $this->db->where('category.active_inactive','1');
            //  $this->db->where('subcategory.status',1);
            //   $this->db->where('subcategory.active_inactive','1');
             $this->db->where('category.category_type !=', "Gift Card");
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

      public function verify_otp($table="", $mobile_no="",$otp="",$fk_lang_id="") {
        $response = array();
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where('contact_no', $mobile_no);
        $this->db->where('otp', $otp);
        $query = $this->db->get();
        $result = $query->row_array();   
        if($result['otp'] == $otp)
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
                $response['status'] = true;
                $response['message'] = "Otp Verified Successfully";
                $response['code'] = 200;
                $response['op_user_id'] = $result['op_user_id'];
                $response['data']=$result;
            // }
        } else {
            if($fk_lang_id==1){
                $response['message'] = "otp mismatch";
            }else{
                $response['message'] = "رمز التفعيل غير صحيح";
            }
            
            $response['status'] = false;
            $response['code']=201;
        }
        return $response;
    }

    public function get_cart_data($user_id="",$fk_lang_id="")
    {
        ini_set("memory_limit", "-1");

       $this->db->select('cart.*,cart.qty as cart_qty,product.*,inventory.qty as quantity');
       $this->db->from('cart');
       $this->db->join('product','cart.product_id=product.product_id','left');
       $this->db->join('inventory','inventory.product_id=product.product_id','left');
       $this->db->where('cart.user_id',$user_id);
      $this->db->where('inventory.used_status','1');  
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
       $this->db->where('inventory.used_status','1');  
        $this->db->order_by('cart.cart_id','DESC'); 
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

    public function product_details_on_id($product_id='')
    {
        $this->db->select('product.*,inventory.qty as quantity,category.category_name,category.category_name_ar,subcategory.sub_category_name,subcategory.sub_category_name_ar,childcategory.child_category_name,childcategory.child_category_name_ar');
        $this->db->from('product');      
        $this->db->join('product_gallery','product_gallery.product_id=product.product_id','left');
        $this->db->join('category','product.category_id=category.category_id','left');
        $this->db->join('subcategory','product.sub_category_id=subcategory.sub_category_id','left');
        $this->db->join('childcategory','childcategory.child_category_id=product.child_category_id','left');
        $this->db->join('inventory','inventory.product_id=product.product_id','left');
        $this->db->where('product.product_id',$product_id);
        // $this->db->where('product.fk_lang_id',$fk_lang_id);
        $this->db->where('product.status','1');
        $this->db->where('inventory.used_status','1');  
        $this->db->group_by('product_gallery.product_id');
        $query = $this->db->get();
        //echo $this->db->last_query();die();
        $result = $query->row_array();
        $result['img_url'] = $this->product_gallery_details_on_id($product_id);
        return $result;
    }
    
    public function product_gallery_details_on_id($product_id)
    {
        $this->db->select('img_url');
        $this->db->from('product_gallery');      
        $this->db->where('product_id',$product_id);
        $this->db->where('status','1');
        $query = $this->db->get();
        $result = $query->result_array();
        $img_urls ="";
        $count = count($result);
        foreach($result as $result_key => $result_row)
        {
            if($count==($result_key+1)){
                 $img_urls =  $img_urls.$result_row['img_url']."";
            }else{
                 $img_urls =  $img_urls.$result_row['img_url'].",";
            }
           
        }
       
        return $img_urls;
    }

    public function related_product_details_on_id($product_id='')
    {
        $this->db->select('product_relative.rel_product_id,product.*,category.category_name,subcategory.sub_category_name,childcategory.child_category_name,inventory.qty as quantity');
        $this->db->from('product');      
        $this->db->join('product_relative','product_relative.rel_product_id=product.product_id','left');
        // $this->db->join('product_gallery','product_gallery.product_id=product.product_id','left');
        $this->db->join('category','product.category_id=category.category_id','left');
        $this->db->join('subcategory','product.sub_category_id=subcategory.sub_category_id','left');
        $this->db->join('childcategory','childcategory.child_category_id=product.child_category_id','left');
        $this->db->join('inventory','inventory.product_id=product.product_id','left');
        $this->db->where('product_relative.product_id',$product_id);
        // $this->db->where('product.fk_lang_id',$fk_lang_id);
        $this->db->where('product.status',1);
        $this->db->where('product.product_status','1');  
        $this->db->where('inventory.used_status','1');  
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

    public function get_wishlist_data($user_id='')
    {
      $this->db->select('wishlist.user_id,wishlist.id,product.*,GROUP_CONCAT(product_gallery.img_url) as img_url,inventory.qty as quantity');
        $this->db->from('wishlist');      
        $this->db->join('product','product.product_id=wishlist.product_id','left');
        $this->db->join('product_gallery','product_gallery.product_id=product.product_id','left');
        $this->db->join('inventory','inventory.product_id=product.product_id','left');
        $this->db->where('wishlist.user_id',$user_id);
        $this->db->where('wishlist.status','1');
        $this->db->where('product.status','1');
        $this->db->where('product.product_status','1');  
        $this->db->where('inventory.used_status','1');  
        $this->db->group_by('product_gallery.product_id');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

    public function get_search_product($fk_lang_id="")
    {
        $this->db->select('product.product_id,product.product_name,product.product_offer_price,product.image_name,product.product_name_ar');
        $this->db->from('product');
        $this->db->where('product.status','1');
        $this->db->where('product.product_status','1');  
         $this->db->order_by('product.product_id','DESC');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

    public function get_all_product_data($fk_lang_id="",$limit="",$start="")
    {
        $this->db->select('product.*,inventory.qty as quantity');
        $this->db->from('product');
        $this->db->join('inventory','inventory.product_id=product.product_id','left');
        // $this->db->where('product.fk_lang_id',$fk_lang_id);        
         $this->db->where('product.status','1');
         $this->db->where('product.product_status','1');  
         $this->db->where('inventory.used_status','1');  
         $this->db->where('product.category_type !=','Gift Card');  
         $this->db->order_by('product.product_id','DESC');
          $this->db->limit($limit, $start);
        // $this->db->limit($limit);
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }
    public function get_all_popular_product_data($fk_lang_id="")
    {
        $this->db->select('product.*,inventory.qty as quantity,GROUP_CONCAT(product_gallery.img_url) as img_url');
        $this->db->from('product');
        $this->db->join('inventory','inventory.product_id=product.product_id','left');
         $this->db->join('product_gallery','product_gallery.product_id=product.product_id','left');
        // $this->db->where('product.fk_lang_id',$fk_lang_id);  
        $this->db->where('product.popular','1'); 
         $this->db->where('product.status','1');
         $this->db->where('product.product_status','1');
         $this->db->where('inventory.used_status','1');  
          $this->db->order_by('product.product_id','DESC');
           $this->db->group_by('product_gallery.product_id');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }
    public function get_all_featured_product_data($fk_lang_id="")
    {
        $this->db->select('product.*,inventory.qty as quantity,GROUP_CONCAT(product_gallery.img_url) as img_url');
        $this->db->from('product');
        $this->db->join('inventory','inventory.product_id=product.product_id','left');
        $this->db->join('product_gallery','product_gallery.product_id=product.product_id','left');
        // $this->db->where('product.fk_lang_id',$fk_lang_id);  
        $this->db->where('product.featured','1');  
        $this->db->where('product.status','1');
        $this->db->where('product.product_status','1');
        $this->db->where('inventory.used_status','1');  
        // $this->db->where('product.product_id',37);  
         $this->db->order_by('product.product_id','DESC');
          $this->db->group_by('product_gallery.product_id');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }
    public function get_all_best_selling_product_data($fk_lang_id="")
    {
        $this->db->select('product.*,inventory.qty as quantity,GROUP_CONCAT(product_gallery.img_url) as img_url');
        $this->db->from('product');
        $this->db->join('inventory','inventory.product_id=product.product_id','left');
        $this->db->join('product_gallery','product_gallery.product_id=product.product_id','left');
        // $this->db->where('product.fk_lang_id',$fk_lang_id);  
        $this->db->where('product.best_selling',1);  
        $this->db->where('product.status','1');
        $this->db->where('product.product_status','1');
        $this->db->where('inventory.used_status','1');  
        $this->db->order_by('product.product_id','DESC');
        $this->db->group_by('product_gallery.product_id');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

    public function order_history($user_id='')
    {
        $this->db->select('order_data.id,order_data.order_number,order_data.order_id,order_data.quantity,order_data.grand_total,product.product_name,product.image_name,product.product_name_ar,product.currency_in_english,product.currency_in_arabic,order_data.date,GROUP_CONCAT(DISTINCT(tbl_order_status.status)) as status,GROUP_CONCAT(DISTINCT(tbl_order_status_master.order_status)) as order_status,GROUP_CONCAT(DISTINCT(tbl_order_status_master.order_status_ar)) as order_status_ar,order_data.order_date_time');
        $this->db->from('order_data');
        $this->db->join('product','order_data.fk_product_id=product.product_id','left');
         $this->db->join('tbl_order_status','tbl_order_status.fk_order_id=order_data.id','left');
        $this->db->join('tbl_order_status_master','tbl_order_status.status=tbl_order_status_master.id','left');
        $this->db->where('order_data.fk_user_id',$user_id);     
        $this->db->where('tbl_order_status.used_status','1');     
        $this->db->group_by('order_data.order_id');
        $this->db->order_by('order_data.id',"DESC");
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }
    public function order_history_on_order_id($id='')
    {
        $this->db->select('order_data.*,product.product_name,product.product_name_ar,product.image_name,user_delivery_address.roomno,user_delivery_address.building,user_delivery_address.street,user_delivery_address.zone,op_user.user_name,tbl_payment.payment_type,product.currency_in_english,product.currency_in_arabic');
        $this->db->from('order_data');
        $this->db->join('product','order_data.fk_product_id=product.product_id','left');
        $this->db->join('tbl_payment','order_data.order_number=tbl_payment.order_no','left');
        $this->db->join('op_user','order_data.fk_user_id=op_user.op_user_id','left');
        // $this->db->join('tbl_order_status','order_data.fk_product_id=tbl_order_status.fk_order_id','left');
        // $this->db->join('tbl_order_status_master','tbl_order_status.status=tbl_order_status_master.id','left');      
        $this->db->join('user_delivery_address','order_data.fk_address_id=user_delivery_address.id','left');
        $this->db->where('order_data.order_id',$id);   
        // $this->db->group_by('tbl_order_status.status'); 
        // $this->db->order_by('order_data.id','DESC'); 
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

    public function get_rate($distance_calculation='')
    {
       $this->db->select('rate');
       $this->db->from('tbl_delivery_rate');
       // $this->db->where('from_km >=', $distance_calculation);
        $this->db->where((int) $distance_calculation.' BETWEEN from_km AND to_km');
        // $this->db->where('status','1');
       // $this->db->order_by('to_km',"ASC");
        $query = $this->db->get();
        $result = $query->row_array();
        return $result;
    }

    public function order_data($order_id='')
    {
        $this->db->select('order_data.*,product.product_name,product.product_name_ar,product.image_name,user_delivery_address.roomno,user_delivery_address.building,user_delivery_address.street,user_delivery_address.zone,op_user.user_name,op_user.contact_no,tbl_payment.payment_type');
        $this->db->from('order_data');
        $this->db->join('product','order_data.fk_product_id=product.product_id','left');
        $this->db->join('tbl_payment','order_data.order_number=tbl_payment.order_no','left');
        $this->db->join('op_user','order_data.fk_user_id=op_user.op_user_id','left');
        $this->db->join('user_delivery_address','order_data.fk_address_id=user_delivery_address.id','left');
        $this->db->where('order_data.order_id',$order_id);   
        $this->db->order_by('order_data.id','DESC'); 
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

    public function get_gift_card()
    {
        $this->db->select('product.*,tbl_gift_card_code.uniq_code');
        $this->db->from('product');
        $this->db->join('tbl_gift_card_code','tbl_gift_card_code.fk_product_id=product.product_id','left');
        // $this->db->where('gift_card_stock','1');
        $this->db->where('category_type','Gift Card');
        $this->db->where('product.product_status','1');
        $this->db->where('product.status','1');
        $this->db->where('tbl_gift_card_code.gift_card_stock','1');
        $this->db->group_by('product.product_name');
         $this->db->order_by('product.product_id','DESC');
        $query=$this->db->get();
        $result=$query->result_array();
        return $result;
    }
    public function get_gift_card_sum($product_id)
    {
        $this->db->select('count(uniq_code) as uniq_code');
        $this->db->from('tbl_gift_card_code');
        $this->db->where('gift_card_stock','1');
        $this->db->where('fk_product_id',$product_id);
        $query=$this->db->get();
        //echo $this->db->last_query();die();
        $result=$query->row_array();
        return $result;
    }
    public function get_brand_product($brand_id)
    {
        $this->db->select('product.*,inventory.qty as quantity');
        $this->db->from('product');
        $this->db->join('inventory','inventory.product_id=product.product_id','left');
        $this->db->where("find_in_set($brand_id, product.brand_id)");
        $this->db->where('product.status','1');
        $this->db->where('product.product_status','1');  
         $this->db->where('inventory.used_status','1'); 
        $this->db->where('product.category_type !=','Gift Card');
         $this->db->order_by('product.product_id','DESC');
        $query=$this->db->get();
        $result=$query->result_array();
        return $result;
    }
    public function get_product_on_category($category_id=""){
        $this->db->select('product.*,inventory.qty as quantity,GROUP_CONCAT(product_gallery.img_url) as img_url');
        $this->db->from('product');
        $this->db->join('inventory','inventory.product_id=product.product_id','left');
          $this->db->join('product_gallery','product_gallery.product_id=product.product_id','left');
        $this->db->where('product.category_id',$category_id);        
        $this->db->where('product.status','1');
        $this->db->where('product.product_status','1');  
         $this->db->where('inventory.used_status','1');  
          $this->db->order_by('product.product_id','DESC');
           $this->db->group_by('product_gallery.product_id');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }
    public function get_product_on_sub_category($sub_category_id=""){
        $this->db->select('product.*,inventory.qty as quantity,GROUP_CONCAT(product_gallery.img_url) as img_url');
        $this->db->from('product');
        $this->db->join('inventory','inventory.product_id=product.product_id','left');
          $this->db->join('product_gallery','product_gallery.product_id=product.product_id','left');
        $this->db->where('product.sub_category_id',$sub_category_id);        
        $this->db->where('product.status','1');
        $this->db->where('product.product_status','1');  
        $this->db->where('inventory.used_status','1');  
        $this->db->order_by('product.product_id','DESC');
         $this->db->group_by('product_gallery.product_id');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }


}
