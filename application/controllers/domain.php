<?php
/**
 * domain.php
 *
 * @version domain.php 53 2011-4-22 15:48:13
 * @author 412
 * @access public
 */

class Domain extends CI_Controller
{

	/**
	 * Domain::__construct()
	 * 
	 * @return
	 */
	function __construct()
	{
		parent::__construct();
		
		include APPPATH.'language/server_language.php';
		
		include APPPATH.'language/domain_language.php';
		
		$this->load->library('pagination');
		$this->load->model('domain_model');
        if(!$this->permission_model->check_visit_permission(TABLE_DOMAINS, $this->session->userdata('user_group')))
            show_404();
	}


	/**
	 * Domain::index()
	 * 
	 * @return
	 */
	public function index()
	{
		$this->domain_list();
	}
	
	/**
	 * Domain::domain_list()
	 * 
	 * @return 通过view返回数据
	 */
	public function domain_list(){
		//配置分页类
		$config['base_url'] = 'domain/domain_list/';
		$config['per_page'] = PAGE_PER_MAX;
		$config['uri_segment'] = 3;
		$data['page_total'] = $config['total_rows'] = $this->domain_model->get_domain_form();
		$config['cur_tag_open'] = '<a class="current">';
		$config['cur_tag_close'] = '</a>';
		$config['first_link'] = '&laquo; ';
		$config['last_link'] = 'Last &raquo;';
		//获取分页
		$this->pagination->initialize($config);
		$data['page'] = $this->pagination->create_links();
        
        //状态
		$DOMAIN_STATUS = $this->config->item('DOMAIN_STATUS');
       
		
		//获取idc url数据
		$didcurl_result = $this->domain_model->get_didcurl_to_domain();
		$didcurl_value = $this->domain_model->option_didcurl_to_domain($didcurl_result,DEFAULT_IDCURL_ID);
		
		//页数存入seesion已供excel导出
		$this->session->set_userdata('now_page', intval($this->uri->segment(3)));
		$this->session->set_userdata('excel_where', '');
		
		//获取列表
		$result = $this->domain_model->get_domain_form('', '', PAGE_PER_MAX, intval($this->uri->segment(3)));
		
		$data['result'] = '';
		foreach ($result as $key => $value){
			foreach($value as $key2 => $value2){
				if($key2 == 'domain_idc_url'){
					$data['result']->$key->$key2 = $this->domain_model->get_id_didcurl($value2);
				}elseif($key2 == 'domain_status'){
				    $data['result']->$key->$key2 = '<span class="order-examine-status order-examine-' . ($value2 == DOMAIN_STATUS_ENABLE ? 'yes' : 'no') . '"> ' . $DOMAIN_STATUS[$value2] . ' </span>' .
					(($this->session->userdata('user_group') == ADMIN || $this->session->userdata('user_group') == POWER_ADMIN) ? (($value2 == DOMAIN_STATUS_ENABLE ? ('[<a href="domain/domain_status_transform/' . $data['result']->$key->domain_id . '/' . DOMAIN_STATUS_STOP . '" onClick="operate_affirm(\'domain\/domain_status_transform\/' . $data['result']->$key->domain_id . '\/' . DOMAIN_STATUS_STOP . '\/\');return false;" title="停用域名">停用</a>]') : ('[<a href="domain/domain_status_transform/' . $data['result']->$key->domain_id . '/' . DOMAIN_STATUS_ENABLE . '" onClick="operate_affirm(\'domain\/domain_status_transform\/' . $data['result']->$key->domain_id . '\/' . DOMAIN_STATUS_ENABLE . '\/\');return false;" title="启用域名">启用</a>]'))) : '');
				}elseif($key2 == 'domain_due_time'){
					$data['result']->$key->$key2 = strtotime($value2) != 0 ? ((strtotime($value2) < strtotime (date('Y-m-d 00:00:00', strtotime ("+8 day"))) &&  strtotime($value2) >= strtotime (date('Y-m-d 00:00:00'))) ? ('<span class="order-pay-status order-pay-no">' . date('Y-m-d', strtotime($value2)) . '</span>') : date('Y-m-d', strtotime($value2))) : ('0');
				}elseif($key2 == 'domain_pass'){
					$data['result']->$key->$key2 = $this->encrypt->decode($value2);
				}else{
					$data['result']->$key->$key2 = $value2;
				}
			}
		}
		
		
		//显示数据数组
		$data['form_title'] = array(
								'编号',
								'域名',
								'域名管理登录网址',
                                '用途',
								'管理账号',
								'管理密码',
                                '服务器所在IP',
                                '创建时间',
								'截止时间',
                                
);
		
		$data['data_key'] = array(
								'domain_id',
								'domain_name',
								'domain_idc_url',
                                'domain_purpose',
								'domain_user',
								'domain_pass',
								'domain_web_ip',
								'domain_time',
                                'domain_due_time',
								'domain_status',
                                );
		
		
		//检测是否具有创建权限
        if($this->permission_model->check_sql_permission(TABLE_DOMAINS, SQL_ACTION_INSERT, $this->session->userdata('user_group')))
		$data['data_create'] = array(
								'name' => array(
									'element'=> 'input',
									'type' => 'text',
									'name' => '域名',
									'value' => '',
									'maxlength' => '100',
									'need' => true,
									'checkrepeat' => true),
									
								'idc_url' => array(
									'element'=> 'select',
									'type' => '',
									'name' => '域名管理登录网址',
									'value' => $didcurl_value,
									'maxlength' => '',
									'need' => true),
                                    
                                'purpose' => array(
									'element'=> 'textarea',
									'type' => 'text',
									'name' => '作用',
									'value' => '',
									'maxlength' => '500',
									'need' => true,
									'maxlength' => '200'),
									
								'user' => array(
									'element'=> 'input',
									'type' => 'text',
									'name' => '管理账号',
									'value' => '',
									'maxlength' => '60',
									'need' => true),
									
								'pass' => array(
									'element'=> 'input',
									'type' => 'text',
									'name' => '管理密码',
									'value' => '',
									'maxlength' => '60',
									'need' => true),
									
								'web_ip' => array(
									'element'=> 'input',
									'type' => 'text',
									'name' => '服务器所在IP',
									'value' => '',
									'maxlength' => '100',
									'need' => true),
								);
		$data['data_create_url'] = 'domain/create';
		
		$data['top_item'] = 'domain';
		$data['item'] = 'domain';
		$data['item_id_field'] = 'domain_id';
		$data['item_name_field'] = 'domain_name';
		$data['edit_able'] = $this->permission_model->check_sql_permission(TABLE_DOMAINS, SQL_ACTION_UPDATE, $this->session->userdata('user_group'));
		$data['data_edit_url'] = 'domain/edit/';
		
		$data['delete_able'] = $this->permission_model->check_sql_permission(TABLE_DOMAINS, SQL_ACTION_DELETE, $this->session->userdata('user_group'));
		$data['data_delete_url'] = 'domain/delete_domain_single/';
		
		$this->load->view('index', $data);
		
	}
    
    /**
	 * domain_::domain_status_transform()
	 *
	 * @param mixed $domain_id
	 * @param mixed $status
	 * @return
	 */
	public function domain_status_transform($domain_id, $status){
        if($this->session->userdata('user_group') != ADMIN && $this->session->userdata('user_group') != POWER_ADMIN)
            show_404();

		if(!is_numeric($domain_id)){
            show_404();
		}else{
			$server_data = array();
			$server_data['domain_status'] = $status;

			$result = $this->domain_model->update_domain($domain_id, $server_data);

			echo $result;
		}
	}
	
	/**
	 * Domain::create()
	 * 
	 * @return
	 */
	public function create(){
	   //权限检查
        if(!$this->permission_model->check_sql_permission(TABLE_DOMAINS, SQL_ACTION_INSERT, $this->session->userdata('user_group')))
            show_404();
        
        //检测是否进入ajax检测重复值
		if(!empty($_POST['check_repeat']) && $this->input->post('check_repeat') == 'true'){
			$domain_data = array();
			$domain_data['domain_name'] = $this->input->post('name');
			$result = $this->domain_model->get_domain_form($domain_data);
			echo $result;
		//检测各变量是否为空
		}elseif(empty($_POST['name']) || empty($_POST['purpose']) || empty($_POST['idc_url']) || empty($_POST['user']) || empty($_POST['pass']) || empty($_POST['web_ip'])){
			echo 'false';
		
		}else{
			$domain_data = array();
			$domain_data['domain_name'] = $this->input->post('name');
			$check_repeat_result = $this->domain_model->get_domain_form($domain_data);
			if($check_repeat_result != 0){
				echo 2;
			}else{
				$domain_data['domain_idc_url'] = $this->input->post('idc_url');
                $domain_data['domain_purpose'] = $this->input->post('purpose');
				$domain_data['domain_user'] = $this->input->post('user');
				$domain_data['domain_pass'] = $this->input->post('pass');
				$domain_data['domain_web_ip'] = $this->input->post('web_ip');
				$result = $this->domain_model->create($domain_data);
				echo $result;
			}
		}
	}
	
	/**
	 * Domain::delete_domain_single()
	 * 
	 * @param mixed $domain_id
	 * @return
	 */
	public function delete_domain_single($domain_id){
        
		if(!is_numeric($domain_id) || !$this->permission_model->check_sql_permission(TABLE_DOMAINS, SQL_ACTION_DELETE, $this->session->userdata('user_group'))){
			echo 'false';
		}else{
			$domain_data = array('domain_id' => $domain_id);
			$result = $this->domain_model->delete_domain($domain_data);
			echo $result;
		}
	}
	
	/**
	 * Domain::edit()
	 * 
	 * @param mixed $domain_id
	 * @return
	 */
	public function edit($domain_id){
	   //权限检查
        if(!$this->permission_model->check_sql_permission(TABLE_DOMAINS, SQL_ACTION_UPDATE, $this->session->userdata('user_group')))
            show_404();
        
		if(!is_numeric($domain_id)){
			echo 'false';
		}elseif(empty($_POST['name']) || empty($_POST['idc_url']) || empty($_POST['user']) || empty($_POST['pass']) || empty($_POST['web_ip'])){
			$domain_data = array('domain_id' => $domain_id);
			$result = $this->domain_model->get_domain_form($domain_data, '', 1, 0);

            $ordinary = $this->user_model->get_user_array();
            
			foreach ($result as $row){$data['result'] = $row;}
			
			$didcurl_result = $this->domain_model->get_didcurl_to_domain();
			$didcurl_value = $this->domain_model->option_didcurl_to_domain($didcurl_result,$data['result']->domain_idc_url);
		
			$data['data_edit'] = array(								
									'name' => array(
										'element'=> 'input',
										'type' => 'text',
										'name' => '域名',
										'value' => $data['result']->domain_name,
										'maxlength' => '100',
										'need' => true,
										'checkrepeat' => true,
										'need' => true),
										
									'idc_url' => array(
										'element'=> 'select',
										'type' => '',
										'name' => '域名管理登录网址（IDC）',
										'value' => $didcurl_value,
										'maxlength' => '',
										'need' => true),
                                        
                                        'purpose' => array(
										'element'=> 'textarea',
										'type' => 'text',
										'name' => '用途',
										'value' => $data['result']->domain_purpose,
										'maxlength' => '200',
										'need' => true),
										
									'user' => array(
										'element'=> 'input',
										'type' => 'text',
										'name' => '管理账号',
										'value' => $data['result']->domain_user,
										'maxlength' => '60',
										'need' => true),
										
									'pass' => array(
										'element'=> 'input',
										'type' => 'text',
										'name' => '管理密码',
										'value' => $this->encrypt->decode($data['result']->domain_pass),
										'maxlength' => '60',
										'need' => true),
										
                                        'check_permissions'=>array(
                                        'element'=>'input',
                                        'type'=>'checkbox',
                                        'name'=>'拥有查看用户名和密码权限的普通用户',
                                        'maxlength' => '64',                                        
                                        'ordinary'=>$ordinary
                                    ),
                                        
									'web_ip' => array(
										'element'=> 'input',
										'type' => 'text',
										'name' => '服务器所在IP',
										'value' => $data['result']->domain_web_ip,
										'maxlength' => '100',
										'need' => true),
                                        
                                        'time' => array(
										'element'=> 'input',
										'type' => 'text',
										'name' => '创建时间',
										'value' => date('Y-m-d', strtotime($data['result']->domain_time)),
										'maxlength' => '50'),

									'due_time' => array(
										'element'=> 'input',
										'type' => 'text',
										'name' => '截止时间',
										'value' => date('Y-m-d', strtotime($data['result']->domain_due_time)),
										'maxlength' => '50')
									);
			$data['data_edit_url'] = 'domain/edit/' . $domain_id;
			$data['data_edit_backurl'] = 'domain';
			
			$data['top_item'] = 'domain';
			$data['item'] = 'domain';
			$this->load->view('edit', $data);
		}else{
			$domain_data = array();
			$domain_data['domain_name'] = $this->input->post('name');
			$domain_data['domain_idc_url'] = $this->input->post('idc_url');
            $domain_data['domain_purpose'] = $this->input->post('purpose');
			$domain_data['domain_user'] = $this->input->post('user');
			$domain_data['domain_pass'] = $this->input->post('pass');
			$domain_data['domain_web_ip'] = $this->input->post('web_ip');
            
            $domain_data['domain_time'] = $this->input->post('time');
			$domain_data['domain_due_time'] = $this->input->post('due_time');
            $domain_data['check_permissions'] = $this->input->post('check_permission');
			
			$result = $this->domain_model->update_domain($domain_id, $domain_data);
			echo $result;
		}
	}
	
	/**
	 * Domain::didcurl()
	 * 
	 * @return
	 */
	public function didcurl(){
	   //权限检查
        if(!$this->permission_model->check_sql_permission(TABLE_DIDCURLS, SQL_ACTION_SELECT, $this->session->userdata('user_group')))
            show_404();
        
		//配置分页类
		$config['base_url'] = 'domain/didcurl/';
		$config['per_page'] = PAGE_PER_MAX;
		$config['uri_segment'] = 3;
		$data['page_total'] = $config['total_rows'] = $this->domain_model->get_didcurl_form();
		$config['cur_tag_open'] = '<a class="current">';
		$config['cur_tag_close'] = '</a>';
		$config['first_link'] = '&laquo;';
		$config['last_link'] = '&raquo;';
		//获取分页
		$this->pagination->initialize($config);
		$data['page'] = $this->pagination->create_links();
		
		//页数存入seesion已供excel导出
		$this->session->set_userdata('now_page', intval($this->uri->segment(3)));
		$this->session->set_userdata('excel_where', '');
		
		//获取列表
		$data['result'] = $this->domain_model->get_didcurl_form('', '', PAGE_PER_MAX, intval($this->uri->segment(3)));
		
		$data['form_title'] = array(
								'编号',
								'域名管理登录网址',
								'创建时间');
		
		$data['data_key'] = array(
								'didcurl_id',
								'didcurl_value',
								'didcurl_time');
		
        if($this->permission_model->check_sql_permission(TABLE_DIDCURLS, SQL_ACTION_INSERT, $this->session->userdata('user_group')))
		$data['data_create'] = array(									
								'value' => array(
									'element'=> 'input',
									'type' => 'text',
									'name' => '域名管理登录网址',
									'value' => '',
									'maxlength' => '255',
									'need' => true)
								);
		$data['data_create_url'] = 'domain/didcurl_create';
		
		$data['top_item'] = 'domain';
		$data['item'] = 'didcurl';
		$data['item_id_field'] = 'didcurl_id';
		$data['edit_able'] = $this->permission_model->check_sql_permission(TABLE_DIDCURLS, SQL_ACTION_UPDATE, $this->session->userdata('user_group'));
		$data['data_edit_url'] = 'domain/didcurl_edit/';
		
		$data['delete_able'] = $this->permission_model->check_sql_permission(TABLE_DIDCURLS, SQL_ACTION_DELETE, $this->session->userdata('user_group'));
		$data['data_delete_url'] = 'domain/delete_didcurl_single/';
		
		$this->load->view('index', $data);
		
	}
	
	/**
	 * Domain::didcurl_create()
	 * 
	 * @return
	 */
	public function didcurl_create(){
		
		if(!empty($_POST['check_repeat']) && $this->input->post('check_repeat') == 'true'){
			$didcurl_data = array();
			$didcurl_data['didcurl_value'] = $this->input->post('value');
			$result = $this->domain_model->get_didcurl_form($didcurl_data);
			echo $result;
		}elseif(empty($_POST['value']) || !$this->permission_model->check_sql_permission(TABLE_DIDCURLS, SQL_ACTION_INSERT, $this->session->userdata('user_group'))){
			echo 0;
		}else{
			$didcurl_data = array();
			$didcurl_data['didcurl_value'] = $this->input->post('value');
			$check_repeat_result = $this->domain_model->get_didcurl_form($didcurl_data);
			if($check_repeat_result != 0){
				echo 2;
			}else{
				$result = $this->domain_model->didcurl_create($didcurl_data);
				echo $result == 1 ? '1' : '0';
			}
		}
	}
	
	
	/**
	 * Domain::didcurl_edit()
	 * 
	 * @param mixed $didcurl_id
	 * @return
	 */
	public function didcurl_edit($didcurl_id){
	   //权限检查
        if(!$this->permission_model->check_sql_permission(TABLE_DIDCURLS, SQL_ACTION_UPDATE, $this->session->userdata('user_group')))
            show_404();
        
		if(!is_numeric($didcurl_id)){
			echo 'false';
		}elseif(empty($_POST['value'])){
			$didcurl_data = array('didcurl_id' => $didcurl_id);
			$result = $this->domain_model->get_didcurl_form($didcurl_data, '', 1, 0);
			foreach ($result as $row){$data['result'] = $row;}
		
			$data['data_edit'] = array(										
									'value' => array(
										'element'=> 'input',
										'type' => 'text',
										'name' => '域名管理登录网址',
										'value' => $data['result']->didcurl_value,
										'need' => true,
										'maxlength' => '255')
									);
			$data['data_edit_url'] = 'domain/didcurl_edit/' . $didcurl_id;
			$data['data_edit_backurl'] = 'domain/didcurl';
			
			$data['top_item'] = 'domain';
			$data['item'] = 'didcurl';
			$this->load->view('edit', $data);
		}else{
			$didcurl_data = array();
			$didcurl_data['didcurl_value'] = $this->input->post('value');
			
			$result = $this->domain_model->update_didcurl($didcurl_id, $didcurl_data);
			echo $result;
		}
	}
	
	
	/**
	 * Domain::delete_didcurl_single()
	 * 
	 * @param mixed $didcurl_id
	 * @return
	 */
	public function delete_didcurl_single($didcurl_id){
        
		if(!is_numeric($didcurl_id) || !$this->permission_model->check_sql_permission(TABLE_DIDCURLS, SQL_ACTION_DELETE, $this->session->userdata('user_group'))){
			echo 'false';
		}else{
			$this->load->model('server_model');
			$server_result = $this->server_model->get_server_form(array('server_idc_url' => $didcurl_id));
			$domain_result = $this->domain_model->get_domain_form(array('domain_idc_url' => $didcurl_id));
			if($server_result > 0){
				echo 6;
			}elseif($domain_result > 0){
				echo 5;
			}else{
				$didcurl_data = array('didcurl_id' => $didcurl_id);
				$result = $this->domain_model->delete_didcurl($didcurl_data);
				echo $result;
			}
		}
	}
	
	/**
	 * Domain::domain_search()
	 * 
	 * @return
	 */
	public function domain_search(){
        
        $domain_search_where = '';
        $data['search_where'] = true;
        
        if(!empty($_POST['submit'])){
            $domain_search_data['domain_search_input'] = trim($this->input->post('search_input'));
            
            $this->session->set_userdata($domain_search_data);
            
            if($domain_search_data['domain_search_input'] == ''){
                $domain_search_where = '';
                $data['search_where'] = true;
            }else{
            	$domain_search_where = array('domain_name' => $domain_search_data['domain_search_input']);
            }
            
        }elseif($this->session->userdata('domain_search_input') != false){
            $domain_search_where = $this->domain_model->domain_transition_search_where($this->session->userdata('domain_search_input'));
        }
        
		//配置分页类
		$config['base_url'] = 'domain/domain_search/';
		$config['per_page'] = PAGE_PER_MAX;
		$config['uri_segment'] = 3;
		$data['page_total'] = $config['total_rows'] = $this->domain_model->get_domain_form($domain_search_where);
		$config['cur_tag_open'] = '<a class="current">';
		$config['cur_tag_close'] = '</a>';
		$config['first_link'] = '&laquo;';
		$config['last_link'] = '&raquo;';
		//获取分页
		$this->pagination->initialize($config);
		$data['page'] = $this->pagination->create_links();
		
        //状态
		$DOMAIN_STATUS = $this->config->item('DOMAIN_STATUS');
        
		//获取idc url数据
		$didcurl_result = $this->domain_model->get_didcurl_to_domain();
		$didcurl_value = $this->domain_model->option_didcurl_to_domain($didcurl_result,DEFAULT_IDCURL_ID);
		
		//椤页数存入seesion已供excel导出
		$this->session->set_userdata('now_page', intval($this->uri->segment(3)));
		$this->session->set_userdata('excel_where', $domain_search_where);
		
		//获取列表
		$result = $this->domain_model->get_domain_form($domain_search_where, '', PAGE_PER_MAX, intval($this->uri->segment(3)));
		
		$data['result'] = '';
		foreach ($result as $key => $value){
			foreach($value as $key2 => $value2){
				if($key2 == 'domain_idc_url'){
					$data['result']->$key->$key2 = $this->domain_model->get_id_didcurl($value2);
				}elseif($key2 == 'domain_status'){
				    $data['result']->$key->$key2 = '<span class="order-examine-status order-examine-' . ($value2 == DOMAIN_STATUS_ENABLE ? 'yes' : 'no') . '"> ' . $DOMAIN_STATUS[$value2] . ' </span>' .
					(($this->session->userdata('user_group') == ADMIN || $this->session->userdata('user_group') == POWER_ADMIN) ? (($value2 == DOMAIN_STATUS_ENABLE ? ('[<a href="domain/domain_status_transform/' . $data['result']->$key->domain_id . '/' . DOMAIN_STATUS_STOP . '" onClick="operate_affirm(\'domain\/domain_status_transform\/' . $data['result']->$key->domain_id . '\/' . DOMAIN_STATUS_STOP . '\/\');return false;" title="停用域名">停用</a>]') : ('[<a href="domain/domain_status_transform/' . $data['result']->$key->domain_id . '/' . DOMAIN_STATUS_ENABLE . '" onClick="operate_affirm(\'domain\/domain_status_transform\/' . $data['result']->$key->domain_id . '\/' . DOMAIN_STATUS_ENABLE . '\/\');return false;" title="启用域名">启用</a>]'))) : '');
				}elseif($key2 == 'domain_due_time'){
					$data['result']->$key->$key2 = strtotime($value2) != 0 ? ((strtotime($value2) < strtotime (date('Y-m-d 00:00:00', strtotime ("+8 day"))) &&  strtotime($value2) >= strtotime (date('Y-m-d 00:00:00'))) ? ('<span class="order-pay-status order-pay-no">' . date('Y-m-d', strtotime($value2)) . '</span>') : date('Y-m-d', strtotime($value2))) : ('0');
				}elseif($key2 == 'domain_pass'){
					$data['result']->$key->$key2 = $this->encrypt->decode($value2);
				}else{
					$data['result']->$key->$key2 = $value2;
				}
			}
		}
		
		$data['form_title'] = array(
								'编号',
								'域名',
								'域名管理登录网址（IDC）',
                                '用途',
								'管理账号',
								'管理密码',
                                '服务器所在IP',
                                '创建时间',
								'截止时间',
                                '状态'
                                );
		
		$data['data_key'] = array(
								'domain_id',
								'domain_name',
								'domain_idc_url',
                                'domain_purpose',
								'domain_user',
								'domain_pass',
								'domain_web_ip',
								'domain_time',
                                'domain_due_time',
								'domain_status',
                                );
		
		
		$data['top_item'] = 'domain';
		$data['item'] = 'domain_search';
		$data['item_id_field'] = 'domain_id';
		$data['item_name_field'] = 'domain_name';
		//鎼滅储鍔熻兘
		$data['data_search'] = true;
		$data['data_search_url'] = 'domain/domain_search/';
		
		$data['edit_able'] = $this->permission_model->check_sql_permission(TABLE_DOMAINS, SQL_ACTION_UPDATE, $this->session->userdata('user_group'));
		$data['data_edit_url'] = 'domain/edit/';
		
		$data['delete_able'] = $this->permission_model->check_sql_permission(TABLE_DOMAINS, SQL_ACTION_DELETE, $this->session->userdata('user_group'));
		$data['data_delete_url'] = 'domain/delete_domain_single/';
		
		$this->load->view('index', $data);
	}
	
	
	
	/**
	 * Domain::excel_export()
	 * 
	 * @param string $idc_domain
	 * @param string $all_page
	 * @param bool $condition
	 * @return
	 */
	public function excel_export($idc_domain = 'domain', $all_page = 'page', $condition = true){
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
			$this->db->where(($idc_domain == 'didcurl' ? 'didcurl_status' : 'domain_status'), 1);
		}
		
		$this->db->order_by(($idc_domain == 'didcurl' ? 'didcurl_time' : 'domain_time'), "desc");
		
		$FIELD_DISPLAY = '';
		$query = '';
		
		$FIELD_DISPLAY = $this->config->item($idc_domain == 'didcurl' ? 'DIDCURL_FIELD_DISPLAY' : 'DOMAIN_FIELD_DISPLAY');
		$query = $this->db->get(($idc_domain == 'didcurl' ? TABLE_DIDCURLS : TABLE_DOMAINS), $limit, $now_page);
		
		$result_array = $this->domain_model->excel_sql_result_convert_domain($query, $FIELD_DISPLAY);
		
		$this->excel_model->array_to_excel($result_array['headerarr'], $result_array['resultarr'], 'excel数据');
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */