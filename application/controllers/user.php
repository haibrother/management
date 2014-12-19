<?php

/**
 * User
 * 
 * @package   
 * @author hx_wsm
 * @copyright 412
 * @version 2011
 * @access public
 */
class User extends CI_Controller
{

	/**
	 * User::__construct()
	 * 
	 * @return
	 */
	function __construct()
	{
		parent::__construct();
		
		$this->load->library('pagination');
	}


	/**
	 * User::index()
	 * 
	 * @return
	 */
	public function index()
	{
		$this->user_list();
	}
	
	/**
	 * User::user_list()
	 * 
	 * @return
	 */
	public function user_list(){
        if(!$this->permission_model->check_visit_permission(TABLE_USERS, $this->session->userdata('user_group')) || ($this->session->userdata('user_group') != ADMIN && $this->session->userdata('user_group') != POWER_ADMIN))
            show_404();
        
		//配置分页类
		$config['base_url'] = 'user/user_list/';
		$config['per_page'] = PAGE_PER_MAX;
		$config['uri_segment'] = 3;
		$data['page_total'] = $config['total_rows'] = $this->user_model->get_user_form(array('user_status' => '3'));
		$config['cur_tag_open'] = '<a class="current">';
		$config['cur_tag_close'] = '</a>';
		$config['first_link'] = '&laquo;';
		$config['last_link'] = '&raquo;';
		//获取分页
		$this->pagination->initialize($config);
		$data['page'] = $this->pagination->create_links();
		
		//页数存入seesion已供excel导出
		$this->session->set_userdata('now_page', intval($this->uri->segment(3)));
		$this->session->set_userdata('excel_where', array('user_status' => '3'));
		
		//获取列表
		$result = $this->user_model->get_user_form(array('user_status' => '3'), '', PAGE_PER_MAX, intval($this->uri->segment(3)));
		
		$USER_GROUP_INFO = $this->config->item('USER_GROUP_INFO');
		
		$data['result'] = '';
		foreach ($result as $key => $value){
			foreach($value as $key2 => $value2){
				if($key2 == 'user_status'){
					@$data['result']->$key->$key2 = $USER_GROUP_INFO[$value2]['name'];
				}else{
					@$data['result']->$key->$key2 = $value2;
				}
			}
		}
		
		$data['form_title'] = array(
								'id',
								'工号',
								'部门',
								'E-mail',
								'创建时间',
								'用户类型',
								'最后登录ip',
								'最后登录时间');
		
		$data['data_key'] = array(
								'user_id',
								'user_login',
								'user_department',
								'user_email',
								'user_registered',
								'user_status',
								'user_last_ip',
								'user_last_time');
		
        if($this->permission_model->check_sql_permission(TABLE_USERS, SQL_ACTION_INSERT, $this->session->userdata('user_group')))
		$data['data_create'] = array(
								'user_status' => array(
									'element'=> 'select',
									'type' => '',
									'name' => '用户类型',
									'value' => '<option selected="selected" value="3">用户</option>',
									'maxlength' => '',
									'need' => true,
									'disabled' => true),
									
								'user_number' => array(
									'element'=> 'input',
									'type' => 'text',
									'name' => '用户工号(数字组成，创建后不可修改) ',
									'value' => '',
									'maxlength' => '50',
									'need' => true,
									'checkrepeat' => true),
									
								'user_psw' => array(
									'element'=> 'input',
									'type' => 'text',
									'name' => '登录密码',
									'value' => '',
									'maxlength' => '50',
									'need' => true),
									
								'user_department' => array(
									'element'=> 'input',
									'type' => 'text',
									'name' => '用户所在部门',
									'value' => '',
									'maxlength' => '50',
									'need' => true),
									
								'user_email' => array(
									'element'=> 'input',
									'type' => 'text',
									'name' => 'E-mail',
									'value' => '',
									'maxlength' => '100',
									'need' => true),
								);
        
		$data['data_create_url'] = 'user/create';
		
		$data['top_item'] = 'user';
		$data['item'] = 'user';
		$data['item_id_field'] = 'user_id';
		$data['edit_able'] = $this->permission_model->check_sql_permission(TABLE_USERS, SQL_ACTION_UPDATE, $this->session->userdata('user_group'));
		$data['data_edit_url'] = 'user/edit/';
		
		$data['delete_able'] = $this->permission_model->check_sql_permission(TABLE_USERS, SQL_ACTION_DELETE, $this->session->userdata('user_group'));
		$data['data_delete_url'] = 'user/delete_user_single/';
		
		$this->load->view('index', $data);
		
	}
	
	/**
	 * User::create()
	 * 
	 * @return
	 */
	public function create(){
		
		if(!empty($_POST['check_repeat']) && $this->input->post('check_repeat') == 'true'){
			$user_data = array();
			$user_data['user_login'] = $this->input->post('user_number');
			$result = $this->user_model->get_user_form($user_data);
			echo $result;
		}elseif(!$this->permission_model->check_sql_permission(TABLE_USERS, SQL_ACTION_INSERT, $this->session->userdata('user_group')) || empty($_POST['user_number']) || empty($_POST['user_psw']) || empty($_POST['user_department'])){
			echo 0;
		}else{
        	//检查管理员个数
        	$admin_num = $this->user_model->get_user_form(array('user_status' => '4'));
        	//如管理员个数超过1个则不允许添加
        	if($admin_num >= 3 && $this->input->post('user_status') != USER){
				echo 0;
        	}else{
				$user_data = array();
				$user_data['user_login'] = $this->input->post('user_number');
				$user_data['user_pass'] = $this->input->post('user_psw');
				$user_data['user_department'] = $this->input->post('user_department');
				$user_data['user_email'] = $this->input->post('user_email');
				$user_data['user_status'] = $this->input->post('user_status');
				$result = $this->user_model->create($user_data);
				echo $result;
        	}
		}
	}
	
	/**
	 * User::delete_user_single()
	 * 
	 * @param mixed $user_id
	 * @return
	 */
	public function delete_user_single($user_id){
        
		if(!$this->permission_model->check_sql_permission(TABLE_USERS, SQL_ACTION_DELETE, $this->session->userdata('user_group')) || !is_numeric($user_id)){
			echo 'false';
		}else{
			$user_data = array('user_id' => $user_id);
			$result = $this->user_model->delete_user($user_data);
			echo $result;
		}
	}
	
	/**
	 * User::edit()
	 * 
	 * @param mixed $user_id
	 * @return
	 */
	public function edit($user_id){
		//更新自己资料时应该刷新seesion，未做好
		
		if(!is_numeric($user_id))
			show_404();

	   //权限检查
        if($user_id != $this->session->userdata('user_id') && !$this->permission_model->check_sql_permission(TABLE_USERS, SQL_ACTION_UPDATE, $this->session->userdata('user_group')))
            show_404();
            
        //检查管理员个数
        $admin_num = $this->user_model->get_user_form(array('user_status' => '4'));
        //检查当前操作用户是否是管理员
        $is_admin = $this->user_model->is_admin($user_id);
        
		if(($this->session->userdata('user_group') != USER && empty($_POST['user_status'])) || empty($_POST['user_email']) || empty($_POST['user_department'])){
			$user_data = array('user_id' => $user_id);
			$data['result'] = '';
			$result = $this->user_model->get_user_form($user_data, '', 1, 0);
			foreach ($result as $row){$data['result'] = $row;}
			
			$data['data_edit'] = array(
						'user_number' => array(
									'element'=> 'input',
									'type' => 'text',
									'name' => '用户工号',
									'value' => $data['result']->user_login,
									'maxlength' => '50'),
									
						'user_status' => array(
							'element'=> 'select',
							'type' => '',
							'name' => '用户类型',
							'value' => '<option ' . ($data['result']->user_status == 3 ? 'selected="selected"':'') . ' value="3">用户</option><option ' . ($data['result']->user_status == 2 ? 'selected="selected"':'') . ' value="2">管理员</option>' . (($this->session->userdata('user_group') == POWER_ADMIN && $user_id == $this->session->userdata('user_id')) ? '<option ' . ($data['result']->user_status == 1 ? 'selected="selected"':'') . 'value="1">超级管理员</option>' : ''),
							'maxlength' => '',
							'need' => true),
							
						'user_department' => array(
							'element'=> 'input',
							'type' => 'text',
							'name' => '用户所在部门',
							'value' => $data['result']->user_department,
							'maxlength' => '50',
							'need' => true),
							
						'user_email' => array(
							'element'=> 'input',
							'type' => 'text',
							'name' => 'E-mail',
							'value' => $data['result']->user_email,
							'maxlength' => '100',
							'need' => true),
						);
            if($this->session->userdata('user_group') != POWER_ADMIN || $user_id == $this->session->userdata('user_id')){
                $data['data_edit']['user_status']['disabled'] =  true;
                $data['data_edit']['user_number']['disabled'] =  true;
                $data['data_edit']['user_number']['name'] =  '用户工号(不可编辑)';
            }
            
            if($admin_num > 0 && $is_admin == false){
                $data['data_edit']['user_status']['disabled'] =  true;
            }
            
            $data['data_pass_url'] = 'user/reset_password/' . $user_id;
            $data['data_edit_url'] = 'user/edit/' . $user_id;
			$data['data_edit_backurl'] = $this->session->userdata('user_group') == USER ? 'order' : ($data['result']->user_status == USER ? 'user' : 'user/admin_manage');
			
			$data['top_item'] = $user_id == $this->session->userdata('user_id') ? 'profile_edit' : 'user';
			$data['item'] = $user_id == $this->session->userdata('user_id') ? 'profile_edit' : 'user';
			
			$this->load->view('edit', $data);
		}else{
			$user_data = array();
			$user_data['user_department'] = $this->input->post('user_department');
			$user_data['user_email'] = $this->input->post('user_email');
            
            if($this->session->userdata('user_group') == POWER_ADMIN && $user_id != $this->session->userdata('user_id') && ($admin_num == 0 || $is_admin == true))
                $user_data['user_status'] = $this->input->post('user_status');
            
            if($this->session->userdata('user_group') != USER && $user_id != $this->session->userdata('user_id')){
				$user_data['user_login'] = $this->input->post('user_number');
            }
			
			$result = $this->user_model->update_user($user_id, $user_data);
			echo $result;
		}
	}
	
	
	/**
	 * User::reset_password()
	 * 
	 * @param mixed $user_id
	 * @return
	 */
	public function reset_password($user_id){
	   //权限检查
        if($user_id != $this->session->userdata('user_id') && !$this->permission_model->check_sql_permission(TABLE_USERS, SQL_ACTION_UPDATE, $this->session->userdata('user_group')))
            show_404();
        
        if(empty($_POST['user_psw_old']) && $user_id == $this->session->userdata('user_id'))
            show_404();
            
        if(empty($_POST['user_psw_new1']) || empty($_POST['user_psw_new2']))
            show_404();
        
        $result_old = 1;
        
        if($user_id == $this->session->userdata('user_id')){
			$user_data_old = array();
			$user_data_old['user_id'] = $this->session->userdata('user_id');
			$user_data_old['user_pass'] =  $this->user_model->get_password(trim($this->input->post('user_psw_old')));
			$result_old = $this->user_model->get_user_form($user_data_old);
		}
		
		if($result_old != 1){
			echo 2;
		}else{
			
			$user_data = array();
			$user_pass_new1 = trim($this->input->post('user_psw_new1'));
			$user_pass_new2 = trim($this->input->post('user_psw_new2'));
		
			if($user_pass_new1 == $user_pass_new2){
				$user_data['user_pass'] =  $this->user_model->get_password($user_pass_new1);
		
				$result = $this->user_model->update_user($user_id, $user_data);
				echo $result;
				
			}else{
				echo 0;
			}
		}
	}
	
	
	/**
	 * User::admin_manage()
	 * 
	 * @return
	 */
	public function admin_manage(){
        if($this->session->userdata('user_group') != POWER_ADMIN)
            show_404();
        
		//配置分页类
		$config['base_url'] = 'user/admin_manage/';
		$config['per_page'] = PAGE_PER_MAX;
		$config['uri_segment'] = 3;
		$data['page_total'] = $config['total_rows'] = $this->user_model->get_user_form(array('user_status !=' => '3'));
		$config['cur_tag_open'] = '<a class="current">';
		$config['cur_tag_close'] = '</a>';
		$config['first_link'] = '&laquo;';
		$config['last_link'] = '&raquo;';
		//获取分页
		$this->pagination->initialize($config);
		$data['page'] = $this->pagination->create_links();
		
		//页数存入seesion已供excel导出
		$this->session->set_userdata('now_page', intval($this->uri->segment(3)));
		$this->session->set_userdata('excel_where', array('user_status !=' => '3'));
		
		//获取列表
		$result = $this->user_model->get_user_form(array('user_status !=' => '3'), '', PAGE_PER_MAX, intval($this->uri->segment(3)));
		
		
		$USER_GROUP_INFO = $this->config->item('USER_GROUP_INFO');
		
		$data['result'] = '';
		foreach ($result as $key => $value){
			foreach($value as $key2 => $value2){
				if($key2 == 'user_status'){
					@$data['result']->$key->$key2 = $USER_GROUP_INFO[$value2]['name'];
				}else{
					@$data['result']->$key->$key2 = $value2;
				}
			}
		}
		
		$data['form_title'] = array(
								'id',
								'工号',
								'部门',
								'E-mail',
								'创建时间',
								'用户类型',
								'最后登录IP',
								'最后登录时间');
		
		$data['data_key'] = array(
								'user_id',
								'user_login',
								'user_department',
								'user_email',
								'user_registered',
								'user_status',
								'user_last_ip',
								'user_last_time');
		
 		//检查管理员个数
 		$admin_num = $this->user_model->get_user_form(array('user_status' => '4'));
 		
 		//如管理员个数超过1个则不允许添加
        if($this->permission_model->check_sql_permission(TABLE_USERS, SQL_ACTION_INSERT, $this->session->userdata('user_group')) && $admin_num < 3)
		$data['data_create'] = array(
								'user_status' => array(
									'element'=> 'select',
									'type' => '',
									'name' => '用户类型',
									'value' => '<option selected="selected" value="2">管理员</option>',
									'maxlength' => '',
									'need' => true),
									
								'user_number' => array(
									'element'=> 'input',
									'type' => 'text',
									'name' => '用户工号(数字组成，创建后不可修改)',
									'value' => '',
									'maxlength' => '50',
									'need' => true,
									'checkrepeat' => true),
									
								'user_psw' => array(
									'element'=> 'input',
									'type' => 'text',
									'name' => '登录密码',
									'value' => '',
									'maxlength' => '50',
									'need' => true),
									
								'user_department' => array(
									'element'=> 'input',
									'type' => 'text',
									'name' => '用户所在部门',
									'value' => '',
									'maxlength' => '50',
									'need' => true),
									
								'user_email' => array(
									'element'=> 'input',
									'type' => 'text',
									'name' => 'E-mail',
									'value' => '',
									'maxlength' => '100',
									'need' => true),
								);
        
		$data['data_create_url'] = 'user/create';
		
		$data['top_item'] = 'user';
		$data['item'] = 'admin_manage';
		$data['item_id_field'] = 'user_id';
		$data['edit_able'] = $this->permission_model->check_sql_permission(TABLE_USERS, SQL_ACTION_UPDATE, $this->session->userdata('user_group'));
		$data['data_edit_url'] = 'user/edit/';
		
		$data['delete_able'] = $this->permission_model->check_sql_permission(TABLE_USERS, SQL_ACTION_DELETE, $this->session->userdata('user_group'));
		$data['data_delete_url'] = 'user/delete_user_single/';
		
		$this->load->view('index', $data);
		
	}
	
	
	/**
	 * User::excel_export()
	 * 
	 * @param string $all_page
	 * @param bool $condition
	 * @return
	 */
	public function excel_export($all_page = 'page', $condition = true){
		if($all_page == 'page'){
			$limit = PAGE_PER_MAX;
			$now_page = $this->session->userdata('now_page');
		}elseif($all_page == 'all'){
			$limit = '';
			$now_page = '';
		}else{
			return false;
		}
		
		if($condition != false || $this->session->userdata('user_group') != POWER_ADMIN){
			$excel_where = $this->session->userdata('excel_where');
			if(!empty($excel_where))
				$this->db->where($excel_where);
			if($this->session->userdata('user_group') != POWER_ADMIN)
				$this->db->where('user_status >', 1);
		}
		
		$this->db->order_by("user_registered", "desc");
		
		$query = $this->db->get(TABLE_USERS, $limit, $now_page);
			
		$FIELD_DISPLAY = $this->config->item('USER_FIELD_DISPLAY');
		
		$result_array = $this->user_model->excel_sql_result_convert($query, $FIELD_DISPLAY);
		
		$this->excel_model->array_to_excel($result_array['headerarr'], $result_array['resultarr'], 'excel数据');
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
