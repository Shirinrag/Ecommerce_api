<?php
	class Inventory {
		public function manage_inventory($user_id='',$org_user_id='',$fk_org_id='',$org_branch_id='',$sample_id='',$sample_type=''){
			$CI = get_instance();
	       	$previous_user_inventory_info = $CI->model->selectWhereData('tbl_inventory',array('user_id'=>$user_id,'used_status'=>'Active','del_status'=>'Active'),array('id','quantity'));
            $update_user_inventory_array = array(
                'used_status'=>'Inactive',
            );
            $CI->model->updateData('tbl_inventory',$update_user_inventory_array,array('id'=>$previous_user_inventory_info['id']));
            // $CI->model->updateData('tbl_inventory',$update_user_inventory_array,array('id'=>$previous_user_inventory_info['id']));
            $user_insert_array = array(
                'user_id'=>$user_id,
                'org_user_id'=>$org_user_id,
                'fk_order_number'=>$sample_id,
                'fk_org_id'=>$fk_org_id,
                'from_user_id'=>$org_user_id,
                'to_user_id'=>$user_id,
                'sample_type'=>$sample_type,
                'fk_branch_id'=>$org_branch_id,
                'status'=>3,
                'quantity'=>$previous_user_inventory_info['quantity']+1,
                'add_quantity'=>1,
                'date'=>get_date(),
            );
            $CI->model->insertData('tbl_inventory',$user_insert_array);

            $order_type_info = $CI->model->selectWhereData('tbl_organization',array('id'=>$fk_org_id,'del_status'=>'Active'),array('order_type'));

            $order_type_info_1= explode(",",$order_type_info);
            if (sizeof($order_type_info_1)==1) {
                $previous_org_physical_inventory_info = $CI->model->selectWhereData('tbl_inventory',array('user_id'=>$org_user_id,'order_type'=>$order_type_info_1[0],'used_status'=>'Active','del_status'=>'Active'),'*');
                $CI->model->updateData('tbl_inventory',$update_user_inventory_array,array('id'=>$previous_org_physical_inventory_info['id']));
                $org_physical_insert_array = array(
                    'user_id'=>$org_user_id,
                    'org_user_id'=>$org_user_id,
                    'fk_order_number'=>$sample_id,
                    'fk_org_id'=>$fk_org_id,
                    'order_type'=>$order_type_info_1[0],
                    'from_user_id'=>$org_user_id,
                    'fk_branch_id'=>$org_branch_id,
                    'to_user_id'=>$user_id,
                    'sample_type'=>$sample_type,
                    'status'=>3,
                    'quantity'=>$previous_org_physical_inventory_info['quantity']-1,
                    'deduct_quantity'=>1,
                    'date'=>get_date(),
                );
                $CI->model->insertData('tbl_inventory',$org_physical_insert_array);

            } else {
                $org_remaining_virtual_kit = $CI->model->selectWhereData('tbl_inventory',array('user_id'=>$org_user_id,'order_type'=>2,'used_status'=>'Active'),array('quantity'));
                $previous_org_virtual_inventory_info = $CI->model->selectWhereData('tbl_inventory',array('user_id'=>$org_user_id,'order_type'=>2,'used_status'=>'Active','del_status'=>'Active'),'*');
                $CI->model->updateData('tbl_inventory',$update_user_inventory_array,array('id'=>$previous_org_virtual_inventory_info['id']));
                if ($org_remaining_virtual_kit['quantity'] >= 1) {
                    $org_virtual_insert_array = array(
                        'user_id'=>$org_user_id,
                        'org_user_id'=>$org_user_id,
                        'fk_order_number'=>$sample_id,
                        'fk_org_id'=>$fk_org_id,
                        'order_type'=>2,
                        'from_user_id'=>$org_user_id,
                        'fk_branch_id'=>$org_branch_id,
                        'to_user_id'=>$user_id,
                        'sample_type'=>$sample_type,
                        'status'=>3,
                        'quantity'=>$previous_org_virtual_inventory_info['quantity']-1,
                        'deduct_quantity'=>1,
                        'date'=>get_date(),
                    );
                    $CI->model->insertData('tbl_inventory',$org_virtual_insert_array);
                } else {
                    $org_remaining_physical_kit = $CI->model->selectWhereData('tbl_inventory',array('user_id'=>$org_user_id,'order_type'=>1,'used_status'=>'Active'),array('quantity'));
                    $previous_org_physical_inventory_info = $CI->model->selectWhereData('tbl_inventory',array('user_id'=>$org_user_id,'order_type'=>1,'used_status'=>'Active','del_status'=>'Active'),'*');
                    $CI->model->updateData('tbl_inventory',$update_user_inventory_array,array('id'=>$previous_org_physical_inventory_info['id']));
                    if ($org_remaining_physical_kit['quantity'] >= 1) {
                        $org_physical_insert_array = array(
                            'user_id'=>$org_user_id,
                            'org_user_id'=>$org_user_id,
                            'fk_order_number'=>$sample_id,
                            'fk_org_id'=>$fk_org_id,
                            'order_type'=>1,
                            'from_user_id'=>$org_user_id,
                            'fk_branch_id'=>$org_branch_id,
                            'to_user_id'=>$user_id,
                            'sample_type'=>$sample_type,
                            'status'=>3,
                            'quantity'=>$previous_org_physical_inventory_info['quantity']-1,
                            'deduct_quantity'=>1,
                            'date'=>get_date(),
                        );
                        $CI->model->insertData('tbl_inventory',$org_physical_insert_array);
                    }
                }
		    }

		    return true;
	    }
	}