<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login_model extends CI_Model {

		public function get_login_info($email='',$password='')
		{
			$this->db->select('tbl_users.*,tbl_organization.org_name,tbl_states.name as state_name');
	        $this->db->from('tbl_users');
	        $this->db->join('tbl_organization','tbl_organization.fk_user_id=tbl_users.id','left');
	        $this->db->join('tbl_states','tbl_states.id=tbl_users.state','left');
	        $this->db->where('tbl_users.email',$email);
	        $this->db->where('tbl_users.password',$password);
	        $this->db->where('tbl_users.del_status','Active');
	        $query=$this->db->get();
	        return $query->row_array();
		}

		public function get_user_login_info($email='',$password='')
		{
			$this->db->select('tbl_users.*,tbl_organization.org_name,tbl_organization.org_logo,tbl_states.name as state_name,tbl_gender.gender as gender_name');
	        $this->db->from('tbl_users');
	        $this->db->join('tbl_organization','tbl_organization.id=tbl_users.fk_org_id','Left');
	        $this->db->join('tbl_states','tbl_states.id=tbl_users.state','Left');
	        // $this->db->join('tbl_org_users','tbl_org_users.fk_org_id=tbl_users.fk_org_id','Left');
	        $this->db->join('tbl_gender','tbl_gender.id=tbl_users.gender','Left');
	        $this->db->where('tbl_users.email',$email);
	        $this->db->where('tbl_users.password',$password);
	        $this->db->where('tbl_users.del_status','Active');
	        $query=$this->db->get();
	        return $query->row_array();
		}

		public function get_lab_login_info($email='',$password='')
		{
			$this->db->select('tbl_lab.*,tbl_users.id,tbl_users.fk_user_type_id,tbl_users.fk_lab_id,tbl_users.fk_sp_id,tbl_users.email,tbl_users.contact_number,tbl_users.address_one,tbl_users.address_one,tbl_users.address_two,tbl_users.city,tbl_users.state,tbl_users.zipcode,tbl_states.name as state_name');
	        $this->db->from('tbl_users');
	        $this->db->join('tbl_lab','tbl_lab.id=tbl_users.fk_lab_id');
	        $this->db->join('tbl_states','tbl_states.id=tbl_users.state');
	        $this->db->where('tbl_users.email',$email);
	        $this->db->where('tbl_users.password',$password);
	        $this->db->where('tbl_users.del_status','Active');
	        $query=$this->db->get();
	        return $query->row_array();
		}

		public function get_naviagtor_org_login_info($email='',$password='')
		{
			$this->db->select('*');
	        $this->db->from('tbl_users');
	        // $this->db->join('tbl_navigator','tbl_navigator.id=tbl_users.fk_navigator_id');
	        // $this->db->join('tbl_states','tbl_states.id=tbl_users.state');
	        $this->db->where('tbl_users.email',$email);
	        $this->db->where('tbl_users.password',$password);
	        $this->db->where('tbl_users.del_status','Active');
	        $query=$this->db->get();
	        return $query->row_array();
		}

		public function get_naviagtor_login_info($email='',$password='')
		{
			$this->db->select('tbl_users.*,tbl_practice_provider.user_status');
	        $this->db->from('tbl_users');
	        $this->db->join('tbl_practice_provider','tbl_practice_provider.fk_user_id=tbl_users.id');
	        // $this->db->join('tbl_states','tbl_states.id=tbl_users.state');
	        $this->db->where('tbl_users.email',$email);
	        $this->db->where('tbl_users.password',$password);
	        $this->db->where('tbl_users.del_status','Active');
	        $this->db->where('tbl_practice_provider.user_status','1');
	        $query=$this->db->get();
	        return $query->row_array();
		}

		public function get_naviagtor_org_admin_login_info($email='',$password='')
		{
			$this->db->select('*');
	        $this->db->from('tbl_users');
	        $this->db->where('tbl_users.email',$email);
	        $this->db->where('tbl_users.password',$password);
	        $this->db->where('tbl_users.del_status','Active');
	        $query=$this->db->get();
	        return $query->row_array();
		}

		public function get_org_admin_login_info($email='',$password='')
		{
			$this->db->select('*');
	        $this->db->from('tbl_users');
	        $this->db->where('tbl_users.email',$email);
	        $this->db->where('tbl_users.password',$password);
	        $this->db->where('tbl_users.del_status','Active');
	        $query=$this->db->get();
	        return $query->row_array();
		}

		public function get_edit_info_roles_access($update_id='')
		{
			$this->db->select('tbl_roles_access.*,tbl_user_type.type,GROUP_CONCAT(tbl_appointment_type.appointment_type) as appointment_type_name');
        	$this->db->from('tbl_roles_access');
        	$this->db->join('tbl_user_type','tbl_user_type.id=tbl_roles_access.fk_user_type_id','Left');
        	$this->db->join('tbl_appointment_type', 'FIND_IN_SET(tbl_appointment_type.id ,tbl_roles_access.fk_appointment_type_ids)', 'left');
        	$this->db->where('tbl_roles_access.id',$update_id);
        	$query=$this->db->get();
	        return $query->row_array();
	        //$this->db->where('tbl_roles_access.del_status','Active');
        	//$this->db->group_by('tbl_roles_access.id');
			//$this->db->select('tbl_roles_access.*,tbl_user_type.type');
	  		//$this->db->from('tbl_roles_access');
	  		//$this->db->join('tbl_user_type','tbl_user_type.id=tbl_roles_access.fk_user_type_id','Left');
	  		//$this->db->where('tbl_roles_access.id',$update_id);
	  		//$this->db->where('tbl_roles_access.del_status','Active');
	  		//$query=$this->db->get();
		}

		public function get_like_email_count($string='')
		{
			$this->db->select('*');
			$this->db->from('tbl_users');
			$this->db->where("email LIKE '%$string%'");
			$query=$this->db->get();
			return $query->num_rows();
		}

		public function get_org_local_login_info($email='',$password='')
		{
			$this->db->select('tbl_branch.*,tbl_branch.id as branch_id,tbl_users.*,tbl_states.name as state_name');
	        $this->db->from('tbl_users');
	        $this->db->join('tbl_branch','tbl_branch.fk_login_id=tbl_users.id');
	        $this->db->join('tbl_states','tbl_states.id=tbl_users.state');
	        $this->db->where('tbl_users.email',$email);
	        $this->db->where('tbl_users.password',$password);
	        $this->db->where('tbl_users.del_status','Active');
	        $query=$this->db->get();
	        return $query->row_array();
		}
}