<?php

/**
 * Server
 *
 * @package
 * @author hx_wsm
 * @copyright 412
 * @version 2011
 * @access public
 */
class Server extends CI_Controller
{

	/**
	 * Server::__construct()
	 *
	 * @return
	 */
	function __construct()
	{
		parent::__construct();

		$this->load->library('pagination');
		$this->load->model('domain_model');
		$this->load->model('server_model');
        if(!$this->permission_model->check_visit_permission(TABLE_SERVERS, $this->session->userdata('user_group')))
            show_404();
	}


	/**
	 * Server::index()
	 *
	 * @return
	 */
	public function index()
	{
		$this->server_list();
	}

	/**
	 * Server::server_list()
	 *
	 * @return
	 */
	public function server_list(){
		//配置分页类
		$config['base_url'] = 'server/server_list/';
		$config['per_page'] = PAGE_PER_MAX;
		$config['uri_segment'] = 3;
		$data['page_total'] = $config['total_rows'] = $this->server_model->get_server_form();
		$config['cur_tag_open'] = '<a class="current">';
		$config['cur_tag_close'] = '</a>';
		$config['first_link'] = '&laquo;';
		$config['last_link'] = '&raquo;';
		//获取分页
		$this->pagination->initialize($config);
		$data['page'] = $this->pagination->create_links();

		//获得服务器状态数组
		$SERVER_STATUS = $this->config->item('SERVER_STATUS');

		//获取idc url数据
		$didcurl_result = $this->server_model->get_didcurl_to_domain();
		$didcurl_value = $this->server_model->option_didcurl_to_domain($didcurl_result,DEFAULT_IDCURL_ID);

		//页数存入seesion已供excel导出
		$this->session->set_userdata('now_page', intval($this->uri->segment(3)));
		$this->session->set_userdata('excel_where', '');

		//获取列表
		$result = $this->server_model->get_server_form('', '', PAGE_PER_MAX, intval($this->uri->segment(3)));

		$data['result'] = '';
        $login = $this->session->userdata('user_login');
		foreach ($result as $key => $value){
			foreach($value as $key2 => $value2){
				if($key2 == 'server_status'){
					$data['result']->$key->$key2 = '<span class="order-examine-status order-examine-' . ($value2 == SERVER_STATUS_ENABLE ? 'yes' : 'no') . '"> ' . $SERVER_STATUS[$value2] . ' </span>' .
					(($this->session->userdata('user_group') == ADMIN || $this->session->userdata('user_group') == POWER_ADMIN) ? (($value2 == SERVER_STATUS_ENABLE ? ('[<a href="server/sever_status_transform/' . $data['result']->$key->server_id . '/' . SERVER_STATUS_STOP . '" onClick="operate_affirm(\'server\/sever_status_transform\/' . $data['result']->$key->server_id . '\/' . SERVER_STATUS_STOP . '\/\');return false;" title="停用服务器！">停用</a>]') : ('[<a href="server/sever_status_transform/' . $data['result']->$key->server_id . '/' . SERVER_STATUS_ENABLE . '" onClick="operate_affirm(\'server\/sever_status_transform\/' . $data['result']->$key->server_id . '\/' . SERVER_STATUS_ENABLE . '\/\');return false;" title="启用服务器！">启用</a>]'))) : '');
				}elseif($key2 == 'server_idc_url'){
					$data['result']->$key->$key2 = $this->server_model->get_id_didcurl($value2);
				}elseif($key2 == 'server_ip'){
					$data['result']->$key->$key2 = $value2 . '<br /><a href="order/order_search_serverid/' . $data['result']->$key->server_id . '" title="查询该服务器所有申请单">[申请单查看]</a>';
				}elseif($key2 == 'server_due_time'){
					$data['result']->$key->$key2 = strtotime($value2) != 0 ? ((strtotime($value2) < strtotime (date('Y-m-d 00:00:00', strtotime ("+8 day"))) &&  strtotime($value2) >= strtotime (date('Y-m-d 00:00:00'))) ? ('<span class="order-pay-status order-pay-no">' . date('Y-m-d', strtotime($value2)) . '</span>') : date('Y-m-d', strtotime($value2))) : ('0');
				}elseif($key2 == 'server_user'){
				    $data['result']->$key->$key2 = $value2;
				    if($this->session->userdata('user_group') == USER){
				        if($value2!=''){
				           if(!preg_match("/$login/",$value->check_permissions)){
				                $data['result']->$key->$key2 = '';
				           }
				        }
				    }
				}elseif($key2 == 'server_pass'){
					$data['result']->$key->$key2 = $this->encrypt->decode($value2);
				    if($this->session->userdata('user_group') == USER){
				        if($value2!=''){
				           if(!preg_match("/$login/",$value->check_permissions)){
				                $data['result']->$key->$key2 = '';
				           }
				        }
				    }
				}elseif($key2 == 'server_time'){
					$data['result']->$key->$key2 = date('Y-m-d', strtotime($value2));
				}else{
					$data['result']->$key->$key2 = $value2;
				}
			}
		}

        
		$data['form_title'] = array(
								'编号',
								'IP',
								'所在机房',
								'IDC',
								'作用',
								'管理帐号',
								'管理密码',
								'联系信息',
								'创建日期',
								'截止日期',
								'状态');

		$data['data_key'] = array(
								'server_id',
								'server_ip',
								'server_address_name',
								'server_idc_url',
								'server_purpose',
								'server_user',
								'server_pass',
								'server_contact',
								'server_time',
								'server_due_time',
								'server_status');
        //如果是普通用户，则不显示用户名和密码
        if($this->session->userdata('user_group') == USER){
            /*
            unset($data['form_title'][5]);
            unset($data['form_title'][6]);
            unset($data['data_key'][5]);
            unset($data['data_key'][6]);
            */
            $data['is_show_add'] = "1";
            
        }

        if($this->permission_model->check_sql_permission(TABLE_SERVERS, SQL_ACTION_INSERT, $this->session->userdata('user_group')))
		$data['data_create'] = array(
								'ip' => array(
									'element'=> 'input',
									'type' => 'text',
									'name' => 'IP',
									'value' => '',
									'maxlength' => '50',
									'need' => true,
									'checkrepeat' => true),

								'address_name' => array(
									'element'=> 'input',
									'type' => 'text',
									'name' => '机房',
									'value' => '',
									'maxlength' => '100',
									'need' => true),

								'purpose' => array(
									'element'=> 'textarea',
									'type' => 'text',
									'name' => '作用(100个汉字以内)',
									'value' => '',
									'maxlength' => '500',
									'need' => true,
									'maxlength' => '200'),

								'idc_url' => array(
									'element'=> 'select',
									'type' => '',
									'name' => 'IDC',
									'value' => $didcurl_value,
									'maxlength' => '',
									'need' => true,
									'note' => ' 如需添加新IDC，请先进入 <a href="domain/didcurl">IDC管理页面</a> 添加'),

								'user' => array(
									'element'=> 'input',
									'type' => 'text',
									'name' => '管理帐号',
									'value' => '',
									'maxlength' => '60',
									'need' => true),

								'pass' => array(
									'element'=> 'input',
									'type' => 'text',
									'name' => '管理密码',
									'value' => '',
									'maxlength' => '64',
									'need' => true),

								'contact' => array(
									'element'=> 'input',
									'type' => 'text',
									'name' => '联系信息',
									'value' => '',
									'maxlength' => '255')
								);
		$data['data_create_url'] = 'server/create';

		$data['top_item'] = 'server';
		$data['item'] = 'server';
		$data['item_id_field'] = 'server_id';
		$data['edit_able'] = $this->permission_model->check_sql_permission(TABLE_SERVERS, SQL_ACTION_UPDATE, $this->session->userdata('user_group'));
		$data['data_edit_url'] = 'server/edit/';

		//提示信息
		$data['data_title_note'] = ($this->session->userdata('user_group') == ADMIN || $this->session->userdata('user_group') == POWER_ADMIN) ? '' : '如需修改服务器状态，请联系管理员！';

		$data['delete_able'] = $this->permission_model->check_sql_permission(TABLE_SERVERS, SQL_ACTION_DELETE, $this->session->userdata('user_group'));
		$data['data_delete_url'] = 'server/delete_server_single/';

		$this->load->view('index', $data);

	}

	/**
	 * Server::create()
	 *
	 * @return
	 */
	public function create(){

		if(!empty($_POST['check_repeat']) && $this->input->post('check_repeat') == 'true'){
			$server_data = array();
			$server_data['server_ip'] = $this->input->post('ip');
			$result = $this->server_model->get_server_form($server_data);
			echo $result;
		}elseif(!$this->permission_model->check_sql_permission(TABLE_SERVERS, SQL_ACTION_INSERT, $this->session->userdata('user_group')) || empty($_POST['ip']) || empty($_POST['address_name']) || empty($_POST['idc_url']) || empty($_POST['purpose'])){
			echo 0;
		}else{

			$server_data = array();
			$server_data['server_ip'] = $this->input->post('ip');
			//验证ip地址
			$check_repeat_result = $this->server_model->get_server_form($server_data);
			if($check_repeat_result != 0){
				echo 2;
			}else{
				$server_data['server_address_name'] = $this->input->post('address_name');
				$server_data['server_idc_url'] = $this->input->post('idc_url');
				$server_data['server_purpose'] = $this->input->post('purpose');
				$server_data['server_user'] = $this->input->post('user');
				$server_data['server_pass'] = $this->input->post('pass');
				$server_data['server_contact'] = $this->input->post('contact');
				$result = $this->server_model->create($server_data);
				echo $result;
			}
		}
	}

	/**
	 * Server::sever_status_transform()
	 *
	 * @param mixed $server_id
	 * @param mixed $status
	 * @return
	 */
	public function sever_status_transform($server_id, $status){
	   //权限检查
        if($this->session->userdata('user_group') != ADMIN && $this->session->userdata('user_group') != POWER_ADMIN)
            show_404();

		if(!is_numeric($server_id)){
            show_404();
		}else{
			$server_data = array();
			$server_data['server_status'] = $status;

			$result = $this->server_model->update_server($server_id, $server_data);

			echo $result;
		}
	}

	/**
	 * Server::delete_server_single()
	 *
	 * @param mixed $server_id
	 * @return
	 */
	public function delete_server_single($server_id){

		if(!is_numeric($server_id) || !$this->permission_model->check_sql_permission(TABLE_SERVERS, SQL_ACTION_DELETE, $this->session->userdata('user_group'))){
			echo 'false';
		}else{
			$this->load->model('order_model');
			$order_result = $this->order_model->get_order_form(array('order_server_id' => $server_id));
			if($order_result > 0){
				echo 7;
			}else{
				$server_data = array('server_id' => $server_id);
				$result = $this->server_model->delete_server($server_data);
				echo $result;
			}
		}
	}

	/**
	 * Server::edit()
	 *
	 * @param mixed $server_id
	 * @return
	 */
	public function edit($server_id){
	   //权限检查
        if(!$this->permission_model->check_sql_permission(TABLE_SERVERS, SQL_ACTION_UPDATE, $this->session->userdata('user_group')))
            show_404();

		if(!is_numeric($server_id)){
            show_404();
		}elseif(empty($_POST['ip']) || empty($_POST['address_name']) || empty($_POST['idc_url']) || empty($_POST['purpose'])){
			$server_data = array('server_id' => $server_id);
			$result = $this->server_model->get_server_form($server_data, '', 1, 0);
			foreach ($result as $row){
			     $data['result'] = $row;
             }
            $check_permissions = $result[0]->check_permissions;
			$didcurl_result = $this->server_model->get_didcurl_to_domain();
			$didcurl_value = $this->server_model->option_didcurl_to_domain($didcurl_result,$data['result']->server_idc_url);
            //获取普通用户
            $ordinary = $this->user_model->get_user_array();

            if(isset($check_permissions) && $check_permissions!=''){
                $permissions = explode('|',$check_permissions);
                if(isset($ordinary) && !empty($ordinary)){
                    foreach($ordinary as $k=>$v){
                        foreach($permissions as $v1){
                            if($v->user_login == $v1){
                                $ordinary[$k]->checked = 1;
                                continue;
                            } 
                        }
                    }
                }
            }
            $data['data_d'] = 1;
			$data['data_edit'] = array(
									'ip' => array(
										'element'=> 'input',
										'type' => 'text',
										'name' => 'IP',
										'value' => $data['result']->server_ip,
										'maxlength' => '50',
										'need' => true,
										'checkrepeat' => true),

									'address_name' => array(
										'element'=> 'input',
										'type' => 'text',
										'name' => '机房',
										'value' => $data['result']->server_address_name,
										'maxlength' => '100',
										'need' => true),

									'purpose' => array(
										'element'=> 'textarea',
										'type' => 'text',
										'name' => '作用',
										'value' => $data['result']->server_purpose,
										'maxlength' => '200',
										'need' => true),

									'idc_url' => array(
										'element'=> 'select',
										'type' => '',
										'name' => 'IDC ',
										'value' => $didcurl_value,
										'maxlength' => '',
										'need' => true),

									'user' => array(
										'element'=> 'input',
										'type' => 'text',
										'name' => '管理帐号',
										'value' => $data['result']->server_user,
										'maxlength' => '60',
										'need' => true),

									'pass' => array(
										'element'=> 'input',
										'type' => 'text',
										'name' => '管理密码',
										'value' => $this->encrypt->decode($data['result']->server_pass),
										'maxlength' => '64',
										'need' => true),
                                    'check_permissions'=>array(
                                        'element'=>'input',
                                        'type'=>'checkbox',
                                        'name'=>'拥有查看用户名和密码权限的普通用户',
                                        'maxlength' => '64',                                        
                                        'ordinary'=>$ordinary
                                    ),

									'contact' => array(
										'element'=> 'input',
										'type' => 'text',
										'name' => '联系方式',
										'value' => $data['result']->server_contact,
										'maxlength' => '255'),

									'time' => array(
										'element'=> 'input',
										'type' => 'text',
										'name' => '创建日期',
										'value' => date('Y-m-d', strtotime($data['result']->server_time)),
										'maxlength' => '50'),

									'due_time' => array(
										'element'=> 'input',
										'type' => 'text',
										'name' => '截止日期',
										'value' => date('Y-m-d', strtotime($data['result']->server_due_time)),
										'maxlength' => '50')
									);
			$data['data_edit_url'] = 'server/edit/' . $server_id;
			$data['data_edit_backurl'] = 'server';

			$data['top_item'] = 'server';
			$data['item'] = 'server';
			$this->load->view('edit', $data);
		}elseif(!(strtotime($this->input->post('due_time')) > 0) || !(strtotime($this->input->post('time')) > 0)){
            echo 3;
        }else{
			$server_data = array();
			$server_data['server_ip'] = $this->input->post('ip');
			$server_data['server_address_name'] = $this->input->post('address_name');
			$server_data['server_idc_url'] = $this->input->post('idc_url');
			$server_data['server_purpose'] = $this->input->post('purpose');
			$server_data['server_user'] = $this->input->post('user');
			$server_data['server_pass'] = $this->input->post('pass');
			$server_data['server_contact'] = $this->input->post('contact');
			$server_data['server_time'] = $this->input->post('time');
			$server_data['server_due_time'] = $this->input->post('due_time');

            $server_data['check_permissions'] = $this->input->post('check_permission');
			$result = $this->server_model->update_server($server_id, $server_data);
			echo $result;
		}
	}

	/**
	 * Server::server_search()
	 *
	 * @return
	 */
	public function server_search(){

        $server_search_like = '';
        $data['search_like'] = true;

        if(!empty($_POST['submit'])){
            $server_search_data['server_search_select'] = trim($this->input->post('search_select'));
            $server_search_data['server_search_select_in'] = trim($this->input->post('search_select_in'));
            $server_search_data['server_search_input'] = trim($this->input->post('search_input'));
            $this->session->set_userdata($server_search_data);

            $server_search_like = $this->server_model->server_transition_search_like($server_search_data);

            if($server_search_like == false){
                $server_search_like = '';
                $data['search_like'] = false;
            }

        }elseif($this->session->userdata('server_search_input') != false){
            $server_search_data['server_search_select'] = $this->session->userdata('server_search_select');
            $server_search_data['server_search_select_in'] =$this->session->userdata('server_search_select_in');
            $server_search_data['server_search_input'] = $this->session->userdata('server_search_input');
            $server_search_like = $this->server_model->server_transition_search_like($server_search_data);
        }
		//配置分页类
		$config['base_url'] = 'server/server_search/';
		$config['per_page'] = PAGE_PER_MAX;
		$config['uri_segment'] = 3;
		$data['page_total'] = $config['total_rows'] = $this->server_model->get_server_form('', '', '', '', '', $server_search_like);

		$config['cur_tag_open'] = '<a class="current">';
		$config['cur_tag_close'] = '</a>';
		$config['first_link'] = '&laquo;';
		$config['last_link'] = '&raquo;';

		$this->pagination->initialize($config);
		$data['page'] = $this->pagination->create_links();

		//获得服务器状态数组
		$SERVER_STATUS = $this->config->item('SERVER_STATUS');


		$didcurl_result = $this->server_model->get_didcurl_to_domain();
		$didcurl_value = $this->server_model->option_didcurl_to_domain($didcurl_result,DEFAULT_IDCURL_ID);

		//页数存入seesion已供excel导出
		$this->session->set_userdata('now_page', intval($this->uri->segment(3)));
		$this->session->set_userdata('excel_like', $server_search_like);


		$result = $this->server_model->get_server_form('', '', PAGE_PER_MAX, intval($this->uri->segment(3)), '', $server_search_like);

		$data['result'] = '';
        $login = $this->session->userdata('user_login');
		foreach ($result as $key => $value){
			foreach($value as $key2 => $value2){
				if($key2 == 'server_status'){
					$data['result']->$key->$key2 = '<span class="order-examine-status order-examine-' . ($value2 == SERVER_STATUS_ENABLE ? 'yes' : 'no') . '"> ' . $SERVER_STATUS[$value2] . ' </span>' .
					(($this->session->userdata('user_group') == ADMIN || $this->session->userdata('user_group') == POWER_ADMIN) ? (($value2 == SERVER_STATUS_ENABLE ? ('[<a href="server/sever_status_transform/' . $data['result']->$key->server_id . '/' . SERVER_STATUS_STOP . '" onClick="operate_affirm(\'server\/sever_status_transform\/' . $data['result']->$key->server_id . '\/' . SERVER_STATUS_STOP . '\/\');return false;" title="停用服务器">停用</a>]') : ('[<a href="server/sever_status_transform/' . $data['result']->$key->server_id . '/' . SERVER_STATUS_ENABLE . '" onClick="operate_affirm(\'server\/sever_status_transform\/' . $data['result']->$key->server_id . '\/' . SERVER_STATUS_ENABLE . '\/\');return false;" title="启用服务器">启用</a>]'))) : '');
				}elseif($key2 == 'server_idc_url'){
					$data['result']->$key->$key2 = $this->server_model->get_id_didcurl($value2);
				}elseif($key2 == 'server_due_time'){
					$data['result']->$key->$key2 = strtotime($value2) != 0 ? ((strtotime($value2) < strtotime (date('Y-m-d 00:00:00', strtotime ("+8 day"))) &&  strtotime($value2) >= strtotime (date('Y-m-d 00:00:00'))) ? ('<span class="order-pay-status order-pay-no">' . date('Y-m-d', strtotime($value2)) . '</span>') : date('Y-m-d', strtotime($value2))) : ('0');
				}elseif($key2 == 'server_user'){
				    $data['result']->$key->$key2 = $value2;
				    if($this->session->userdata('user_group') == USER){
				        if($value2!=''){
				           if(!preg_match("/$login/",$value->check_permissions)){
				                $data['result']->$key->$key2 = '';
				           }
				        }
				    }
				}elseif($key2 == 'server_pass'){
				    $data['result']->$key->$key2 = $this->encrypt->decode($value2);
				    if($this->session->userdata('user_group') == USER){
				        if($value2!=''){
    				           if(!preg_match("/$login/",$value->check_permissions)){
    				                $data['result']->$key->$key2 = '';
    				           }
    				        }
    				    }
					
				}elseif($key2 == 'server_due_time'){
					$data['result']->$key->$key2 = $value2;
				}else{
					$data['result']->$key->$key2 = $value2;
				}
                
			}
		}

		$data['form_title'] = array(
								'编号',
								'IP',
								'所在机房',
								'IDC',
								'作用',
								'管理帐号',
								'管理密码',
								'联系信息',
								'创建时间',
								'截止日期',
								'状态');

		$data['data_key'] = array(
								'server_id',
								'server_ip',
								'server_address_name',
								'server_idc_url',
								'server_purpose',
								'server_user',
								'server_pass',
								'server_contact',
								'server_time',
								'server_due_time',
								'server_status');

        //如果是普通用户，则不显示用户名和密码

        if($this->session->userdata('user_group') == USER){
           /*
            unset($data['form_title'][5]);
            unset($data['form_title'][6]);
            unset($data['data_key'][5]);
            unset($data['data_key'][6]);
            */
        }
		$data['top_item'] = 'server';
		$data['item'] = 'server_search';
		$data['item_id_field'] = 'server_id';
		//搜索功能
		$data['data_search'] = true;
		$data['data_search_url'] = 'server/server_search/';

		$data['edit_able'] = $this->permission_model->check_sql_permission(TABLE_SERVERS, SQL_ACTION_UPDATE, $this->session->userdata('user_group'));
		$data['data_edit_url'] = 'server/edit/';

		$data['delete_able'] = $this->permission_model->check_sql_permission(TABLE_SERVERS, SQL_ACTION_DELETE, $this->session->userdata('user_group'));
		$data['data_delete_url'] = 'server/delete_server_single/';

		$this->load->view('index', $data);
	}
    
    /**
	 * idc::didcurl()
	 * 
	 * @return
	 */
	public function didcurl(){
	   //权限检查
        if(!$this->permission_model->check_sql_permission(TABLE_DIDCURLS, SQL_ACTION_SELECT, $this->session->userdata('user_group')))
            show_404();
        
		//配置分页类
		$config['base_url'] = 'server/didcurl/';
		$config['per_page'] = PAGE_PER_MAX;
		$config['uri_segment'] = 3;
		$data['page_total'] = $config['total_rows'] = $this->server_model->get_didcurl_form();
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
		$data['result'] = $this->server_model->get_didcurl_form('', '', PAGE_PER_MAX, intval($this->uri->segment(3)));
		
		$data['form_title'] = array(
								'编号',
								'IDC',
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
									'name' => 'IDC',
									'value' => '',
									'maxlength' => '255',
									'need' => true)
								);
		$data['data_create_url'] = 'server/didcurl_create';
		
		$data['top_item'] = 'server';
		$data['item'] = 'didcurl';
		$data['item_id_field'] = 'didcurl_id';
		$data['edit_able'] = $this->permission_model->check_sql_permission(TABLE_DIDCURLS, SQL_ACTION_UPDATE, $this->session->userdata('user_group'));
		$data['data_edit_url'] = 'server/didcurl_edit/';
		
		$data['delete_able'] = $this->permission_model->check_sql_permission(TABLE_DIDCURLS, SQL_ACTION_DELETE, $this->session->userdata('user_group'));
		$data['data_delete_url'] = 'server/delete_didcurl_single/';
		
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
			$result = $this->server_model->get_didcurl_form($didcurl_data);
			echo $result;
		}elseif(empty($_POST['value']) || !$this->permission_model->check_sql_permission(TABLE_DIDCURLS, SQL_ACTION_INSERT, $this->session->userdata('user_group'))){
			echo 0;
		}else{
			$didcurl_data = array();
			$didcurl_data['didcurl_value'] = $this->input->post('value');
			$check_repeat_result = $this->server_model->get_didcurl_form($didcurl_data);
			if($check_repeat_result != 0){
				echo 2;
			}else{
				$result = $this->server_model->didcurl_create($didcurl_data);
				echo $result == 1 ? '1' : '0';
			}
		}
	}
	
	
	/**
	 * domain::didcurl_edit()
	 * 
	 * @param mixed $didcurl_id
	 * @return
	 */
	public function didcurl_edit($didcurl_id){
        if(!$this->permission_model->check_sql_permission(TABLE_DIDCURLS, SQL_ACTION_UPDATE, $this->session->userdata('user_group')))
            show_404();
        
		if(!is_numeric($didcurl_id)){
			echo 'false';
		}elseif(empty($_POST['value'])){
			$didcurl_data = array('didcurl_id' => $didcurl_id);
			$result = $this->server_model->get_didcurl_form($didcurl_data, '', 1, 0);
			foreach ($result as $row){$data['result'] = $row;}
		
			$data['data_edit'] = array(										
									'value' => array(
										'element'=> 'input',
										'type' => 'text',
										'name' => 'IDC',
										'value' => $data['result']->didcurl_value,
										'need' => true,
										'maxlength' => '255')
									);
			$data['data_edit_url'] = 'server/didcurl_edit/' . $didcurl_id;
			$data['data_edit_backurl'] = 'server/didcurl';
			
			$data['top_item'] = 'server';
			$data['item'] = 'didcurl';
			$this->load->view('edit', $data);
		}else{
			$didcurl_data = array();
			$didcurl_data['didcurl_value'] = $this->input->post('value');
			
			$result = $this->server_model->update_didcurl($didcurl_id, $didcurl_data);
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
			$domain_result = $this->server_model->get_order_form(array('domain_idc_url' => $didcurl_id));
			if($server_result > 0){
				echo 6;
			}elseif($domain_result > 0){
				echo 5;
			}else{
				$didcurl_data = array('didcurl_id' => $didcurl_id);
				$result = $this->server_model->delete_didcurl($didcurl_data);
				echo $result;
			}
		}
	}
    
	/**
	 * Server::excel_export()
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

			$excel_like = $this->session->userdata('excel_like');

			if(!empty($excel_where)){
				//$this->db->where($excel_where);
			}
			if(!empty($excel_like)){
				if(isset($excel_like['and']))
					$this->db->like($excel_like['and']);
			}
			$this->db->where('server_status >', 0);
		}
       
		$this->db->order_by("server_ip", "asc");
         $query = $this->db->get(TABLE_SERVERS, $limit, $now_page);
        
		$FIELD_DISPLAY = $this->config->item('SERVER_FIELD_DISPLAY');
         //如果是普通用户，则不显示用户名和密码
        if($this->session->userdata('user_group') == USER){
            unset($FIELD_DISPLAY['server_user']);
            unset($FIELD_DISPLAY['server_pass']);
        }
        
		$result_array = $this->server_model->excel_sql_result_convert($query, $FIELD_DISPLAY, 'server_status');
		$this->excel_model->array_to_excel($result_array['headerarr'], $result_array['resultarr'], 'excel数据');
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */