<?php
/**
 * order.php
 *
 * @version order.php 57 2011-4-22 18:48:13
 * @author 412
 * @access public
 */

class Order extends CI_Controller
{

	/**
	 * Order::__construct()
	 *
	 * @return
	 */
	function __construct()
	{
		parent::__construct();


		$this->load->library('pagination');
		$this->load->model('order_model');
		$this->load->model('server_model');
		$this->load->model('email_model');
		$this->load->model('domain_model');
        if(!$this->permission_model->check_visit_permission(TABLE_ORDERS, $this->session->userdata('user_group')))
            show_404();

	}
	/**
	function test(){
		echo 'aaaaa';
	}
 	*/
	/**
	 * Order::index()
	 *
	 * @return
	 */
	public function index()
	{
        $this->email_model->email_dueorder_date();
		$this->order_list();
	}

	/**
	 * Order::order_list()
	 *
	 * @return
	 */
	public function order_list(){
		//配置分页类
		$config['base_url'] = 'order/order_list/';
		$config['per_page'] = PAGE_PER_MAX;
		$config['uri_segment'] = 3;
		$data['page_total'] = $config['total_rows'] = $this->order_model->get_order_form('','','','','','payment');
		$config['cur_tag_open'] = '<a class="current">';
		$config['cur_tag_close'] = '</a>';
		$config['first_link'] = '&laquo;';
		$config['last_link'] = '&raquo;';
		//获取分页
		$this->pagination->initialize($config);
		$data['page'] = $this->pagination->create_links();

		//获取server数据
		$server_result = $this->server_model->get_server_to_order('false');
		$server_value = $this->server_model->option_server_to_order($server_result,DEFAULT_SERVER_ID);

		//获取货币单位
		$pay_unit_value = $this->order_model->option_unit_to_order(DEFAULT_UNIT);
		$ORDER_PAY_UNIT = $this->config->item('ORDER_PAY_UNIT');

		//获取支付审核信息数组
		$PAY_STATUS = $this->config->item('PAY_STATUS');
		$EXAMINE_STATUS = $this->config->item('EXAMINE_STATUS');

		//页数存入seesion已供excel导出
		$this->session->set_userdata('now_page', intval($this->uri->segment(3)));
		$this->session->set_userdata('excel_where', '');

		//获取列表
		$result = $this->order_model->get_order_form('', '', PAGE_PER_MAX, intval($this->uri->segment(3)),'','payment');


		$data['result'] = '';

		foreach ($result as $key => $value){
				$data['result']->$key->pay_method = '';
			foreach($value as $key2 => $value2){
				if($key2 == 'order_server_id'){
					$server_info = $this->server_model->get_id_server($value2);
					$data['result']->$key->$key2 = $this->server_model->order_server_info($server_info, false, true);
				}elseif($key2 == 'order_user_id'){
					$data['result']->$key->$key2 = $this->user_model->get_id_user($value2);
				}elseif($key2 == 'order_due_time'){
				    $expiration_time = $value->order_expiration_time + 1;
					$data['result']->$key->$key2 = strtotime($value2) != 0 ? ((strtotime($value2) < strtotime (date('Y-m-d 00:00:00', strtotime ("+$expiration_time day"))) &&  strtotime($value2) >= strtotime (date('Y-m-d 00:00:00'))) ? ('<span class="order-pay-status order-pay-no">' . date('Y-m-d', strtotime($value2)) . '</span>') : date('Y-m-d', strtotime($value2))) : ('0');
				}elseif($key2 == 'order_pay_status'){
					$pay_status =  '<span class="order-pay-status order-pay-' . $value2 . '">' . $PAY_STATUS[$value2] . '</span>';

                    if($this->session->userdata('user_group') < USER && $value2 == PAY_STATUS_NO){
						$pay_status .= '<br />[<a href="order/pay_order_yes/' . $data['result']->$key->order_id . '" onClick="operate_affirm(\'order\/pay_order_yes\/' . $data['result']->$key->order_id . '\/\');return false;" title="确认已付款！">确认</a>]';
					}elseif($value2 == PAY_STATUS_WAITRENEW){
						$pay_status .= '<br />[<a href="order/reneworder/' . $data['result']->$key->order_id . '" title="服务器续费！">续费</a>]';
					}

                    if($value2 == PAY_STATUS_NO && $data['result']->$key->order_user_id == $this->session->userdata('user_login')){
						$pay_status .= '[<a href="order/order_email_remind/' . $data['result']->$key->order_id . '" onClick="operate_affirm(\'order\/order_email_remind\/' . $data['result']->$key->order_id . '\/\');return false;" title="发送email提醒管理员！">email!</a>]';
					}

					$data['result']->$key->$key2 = $pay_status;

				}elseif($key2 == 'order_examine_status'){
					$data['result']->$key->$key2 = '<span class="order-examine-status order-examine-' . $value2 . '"> ' . $EXAMINE_STATUS[$value2] . ' </span>' . ($this->session->userdata('user_group') < USER ? ($value2 == EXAMINE_STATUS_WAIT ? ('<br />[<a href="order/examine_order/' . $data['result']->$key->order_id . '/' . EXAMINE_STATUS_YES . '" onClick="operate_affirm(\'order\/examine_order\/' . $data['result']->$key->order_id . '\/' . EXAMINE_STATUS_YES . '\/\');return false;" title="审核通过！">通过</a>]<br />[<a href="order/examine_order/' . $data['result']->$key->order_id . '/' . EXAMINE_STATUS_NO . '" onClick="operate_affirm(\'order\/examine_order\/' . $data['result']->$key->order_id . '\/' . EXAMINE_STATUS_NO . '\/\');return false;" title="审核不通过！">未通过</a>]') : '') : '');
				}elseif($key2 == 'order_pay_amount'){
					$data['result']->$key->$key2 = $ORDER_PAY_UNIT[$value->order_unit]['display'] . ' ' . $value2;
				}else{
					if($key2 == 'order_alipay_id' && ((int)$value2 != -1))
						$data['result']->$key->pay_method .= '<span class="pay_method_info">支付宝支付<br /><strong>支付宝帐号：</strong>' . $value2 . '</span>';
					if($key2 == 'order_bank_id' && ((int)$value2 != -1))
						$data['result']->$key->pay_method .= '<span class="pay_method_info">银行支付<br /><strong>银行帐号：</strong>' . $value2;
					if($key2 == 'order_bank_name' && ((int)$value2 != -1))
						$data['result']->$key->pay_method .= '<br /><strong>支付银行：</strong>' . $value2 . '</span>';
					$data['result']->$key->$key2 = $value2;
				}
			}
		}

		$data['form_title'] = array(
								'编号',
								'服务器IP',
								'用户',
								'支付方式',
								'申请单金额',
								'创建时间',
								'服务器截止日期',
								'备注',
								'付款状态',
								'审核状态');

		$data['data_key'] = array(
								'order_id',
								'order_server_id',
								'order_user_id',
								'pay_method',
								'order_pay_amount',
								'order_start_time',
								'order_due_time',
								'order_remark',
								'order_pay_status',
								'order_examine_status');

        if($this->permission_model->check_sql_permission(TABLE_ORDERS, SQL_ACTION_INSERT, $this->session->userdata('user_group')))
		$data['data_create'] = array(
								'server_id' => array(
									'element'=> 'select',
									'type' => '',
									'name' => '服务器IP',
									'value' => $server_value['server_ip'],
									'maxlength' => '',
									'need' => true,
									'note' => ' 如无您所需要的服务器，请先进入 <a href="server">服务器管理页面</a> 添加服务器！'),

								'alipay_id' => array(
									'element'=> 'input',
									'type' => 'text',
									'name' => '支付宝帐号',
									'value' => '',
									'maxlength' => '64',
									'need' => true),

								'bank_id' => array(
									'element'=> 'input',
									'type' => 'text',
									'name' => '银行帐号',
									'value' => '',
									'maxlength' => '64',
									'need' => true),

								'bank_name' => array(
									'element'=> 'input',
									'type' => 'text',
									'name' => '开户银行名称',
									'value' => '',
									'maxlength' => '60',
									'need' => true),

								'pay_unit' => array(
									'element'=> 'select',
									'type' => 'text',
									'name' => '金额单位',
									'value' => $pay_unit_value,
									'maxlength' => '',
									'need' => true),

								'pay_amount' => array(
									'element'=> 'input',
									'type' => 'text',
									'name' => '申请单金额(精确至小数点后两位小数)',
									'value' => '',
									'maxlength' => '12',
									'need' => true),

								'due_time' => array(
									'element'=> 'input',
									'type' => 'text',
									'name' => '截止日期 请填写类似格式(“2011-04-07”)',
									'value' => '',
									'maxlength' => '50',
									'need' => true),
                                    
                                    'expiration_time' => array(
									'element'=> 'input',
									'type' => 'text',
									'name' => '过期天数',
									'value' => '7',
									'maxlength' => '3',
									'need' => true),

								'remark' => array(
									'element'=> 'textarea',
									'type' => 'text',
									'name' => '备注(100个汉字以内)',
									'value' => '',
									'maxlength' => '200'),
								);
		$data['data_create_url'] = 'order/create';

		$data['top_item'] = 'order';
		$data['item'] = 'order';
		$data['item_id_field'] = 'order_id';
		$data['edit_able'] = $this->permission_model->check_sql_permission(TABLE_ORDERS, SQL_ACTION_UPDATE, $this->session->userdata('user_group'));
		$data['data_edit_url'] = 'order/edit/';
        

		$data['delete_able'] = $this->permission_model->check_sql_permission(TABLE_ORDERS, SQL_ACTION_DELETE, $this->session->userdata('user_group'));
		$data['data_delete_url'] = 'order/delete_order_single/';

		$this->load->view('index', $data);

	}

	/**
	 * Order::create()
	 *
	 * @return
	 */
	public function create($renew_old_id = 0){
		sleep(10);
	   //权限检查
        if(!$this->permission_model->check_sql_permission(TABLE_ORDERS, SQL_ACTION_INSERT, $this->session->userdata('user_group')))
            show_404();

		if(empty($_POST['server_id']) || empty($_POST['alipay_id']) || empty($_POST['bank_id']) || empty($_POST['bank_name']) || empty($_POST['pay_amount']) || empty($_POST['due_time'])){
			echo 'false';
		}elseif(!(strtotime($this->input->post('due_time')) > 0) || !(strtotime($this->input->post('due_time')) > time())){
			echo 3;
		}else{
			$order_data = array();
			$order_data['order_server_id'] = $this->input->post('server_id');
			$order_data['order_alipay_id'] = $this->input->post('alipay_id');
			$order_data['order_bank_id'] = $this->input->post('bank_id');
			$order_data['order_bank_name'] = $this->input->post('bank_name');
			$order_data['order_unit'] = $this->input->post('pay_unit');
			$order_data['order_pay_amount'] = $this->input->post('pay_amount');
			$order_data['order_due_time'] = date('Y-m-d H:i:s', strtotime($this->input->post('due_time')));
            $order_data['order_expiration_time'] = $this->input->post('expiration_time');
			$order_data['order_remark'] = $this->input->post('remark');

			$check_repeat_result = $this->order_model->get_order_form($order_data);
			if($check_repeat_result != 0){
				echo 2;
			}else{
				$result = $this->order_model->create($order_data, (is_numeric($renew_old_id) && $renew_old_id != 0) ? true : false);
				if(is_numeric($renew_old_id) && $renew_old_id != 0){
					$old_order_data = array();
					$old_order_data['order_pay_status'] = PAY_STATUS_RENEWED;

					$old_order_result = $this->order_model->update_order($renew_old_id, $old_order_data);

				}

				echo $result;
			}
		}
	}

	/**
	 * Order::delete_order_single()
	 *
	 * @param mixed $order_id
	 * @return
	 */
	public function delete_order_single($order_id){

		if(!is_numeric($order_id) || !$this->permission_model->check_sql_permission(TABLE_ORDERS, SQL_ACTION_DELETE, $this->session->userdata('user_group'))){
			echo 'false';
		}else{
			$order_data = array('order_id' => $order_id);
			$result = $this->order_model->delete_order($order_data);
			echo $result;
		}
	}

	/**
	 * Order::edit()
	 *
	 * @param mixed $order_id
	 * @return
	 */
	public function edit($order_id){
	   //权限检查
        if(!$this->permission_model->check_sql_permission(TABLE_ORDERS, SQL_ACTION_UPDATE, $this->session->userdata('user_group')))
            show_404();

		if(!is_numeric($order_id)){
            show_404();
		}elseif(empty($_POST['server_id']) || empty($_POST['pay_amount']) || empty($_POST['due_time'])){
			$order_data = array('order_id' => $order_id);

			$result = $this->order_model->get_order_form($order_data, '', 1, 0);
			//获取server数据
			$server_result = $this->server_model->get_server_to_order();

			foreach ($result as $row){$data['result'] = $row;}

			//获取货币单位
			$pay_unit_value = $this->order_model->option_unit_to_order($data['result']->order_unit);

			//获取货币单位
			$paystatus_value = $this->order_model->option_paystatus_to_order($data['result']->order_pay_status);
			$examinestatus_value = $this->order_model->option_examinestatus_to_order($data['result']->order_examine_status);

			$server_value = $this->server_model->option_server_to_order($server_result,$data['result']->order_server_id);

			$data['data_edit'] = array(
									'server_id' => array(
										'element'=> 'select',
										'type' => '',
										'name' => '服务器IP',
										'value' => $server_value['server_ip'],
										'maxlength' => '',
										'need' => true),

									'user_id' => array(
										'element'=> 'input',
										'type' => 'text',
										'name' => '用户',
										'value' => $this->user_model->get_id_user($data['result']->order_user_id),
										'maxlength' => '10'),

    								'alipay_id' => array(
    									'element'=> 'input',
    									'type' => 'text',
    									'name' => '支付宝帐号(-1代表不存在)',
    									'value' => $data['result']->order_alipay_id,
    									'maxlength' => '64',
    									'need' => true),

    								'bank_id' => array(
    									'element'=> 'input',
    									'type' => 'text',
    									'name' => '银行帐号(-1代表不存在)',
    									'value' => $data['result']->order_bank_id,
    									'maxlength' => '64',
    									'need' => true),

    								'bank_name' => array(
    									'element'=> 'input',
    									'type' => 'text',
    									'name' => '开户银行名称(-1代表不存在)',
    									'value' => $data['result']->order_bank_name,
    									'maxlength' => '60',
    									'need' => true),

									'pay_unit' => array(
										'element'=> 'select',
										'type' => 'text',
										'name' => '金额单位',
										'value' => $pay_unit_value,
										'maxlength' => '',
										'need' => true),

									'pay_amount' => array(
										'element'=> 'input',
										'type' => 'text',
										'name' => '申请单金额(精确至小数点后两位小数)',
										'value' => $data['result']->order_pay_amount,
										'maxlength' => '12',
										'need' => true),

									'due_time' => array(
										'element'=> 'input',
										'type' => 'text',
										'name' => '截止日期(请用“年-月-日”格式)',
										'value' => date('Y-m-d', strtotime($data['result']->order_due_time)),
										'maxlength' => '50',
										'need' => true),
                                        
                                    'expiration_time' => array(
										'element'=> 'input',
										'type' => 'text',
										'name' => '过期天数',
										'value' => $data['result']->order_expiration_time,
										'maxlength' => '3',
										'need' => true),

									'remark' => array(
										'element'=> 'textarea',
										'type' => 'text',
										'name' => '备注',
										'value' => $data['result']->order_remark,
										'maxlength' => '200'),

									'pay_status' => array(
										'element'=> 'select',
										'type' => 'text',
										'name' => '支付状态',
										'value' => $paystatus_value,
										'maxlength' => '',
										'need' => true,
										'note' => '注意:只有状态为 已支付 的申请单才会触发过期提醒功能及服务器截止日期更新!'),

									'examine_status' => array(
										'element'=> 'select',
										'type' => 'text',
										'name' => '审核状态',
										'value' => $examinestatus_value,
										'maxlength' => '',
										'need' => true),
									);
			$data['data_edit_url'] = 'order/edit/' . $order_id;
			$data['data_edit_backurl'] = 'order';

			$data['top_item'] = 'order';
			$data['item'] = 'order';
			$this->load->view('edit', $data);
		}else{
			$order_data = array();
			$order_data['order_user_id'] = $this->input->post('user_id');
			if($this->user_model->get_login_user($order_data['order_user_id']) == false){
				echo 8;
			}else{
				$order_data['order_user_id'] = $this->user_model->get_login_user($order_data['order_user_id']);
				$order_data['order_server_id'] = $this->input->post('server_id');
				$order_data['order_unit'] = $this->input->post('pay_unit');
    			$order_data['order_alipay_id'] = $this->input->post('alipay_id');
    			$order_data['order_bank_id'] = $this->input->post('bank_id');
    			$order_data['order_bank_name'] = $this->input->post('bank_name');
				$order_data['order_pay_amount'] = $this->input->post('pay_amount');
				$order_data['order_due_time'] = date('Y-m-d H:i:s', strtotime($this->input->post('due_time')));
                $order_data['order_expiration_time'] = $this->input->post('expiration_time');
				$order_data['order_remark'] = $this->input->post('remark');
				$order_data['order_pay_status'] = $this->input->post('pay_status');
				$order_data['order_examine_status'] = $this->input->post('examine_status');

				$result = $this->order_model->update_order($order_id, $order_data);

				if($order_data['order_pay_status'] == PAY_STATUS_YES){
					$server_data = array();
					$server_data['server_due_time'] = $order_data['order_due_time'];

					$server_result = $this->server_model->update_server($order_data['order_server_id'], $server_data);
				}

				if($result == 1){
		            $email_msg = $this->email_model->email_get_order_info($order_id, 'edit');
		            $email_address = $this->email_model->get_admin_email();
		            $this->email_model->send_email_user($email_address, $email_msg['subject'], $email_msg['msg']);
				}
				echo $result;
			}
		}
	}

	/**
	 * Order::pay_order_yes()
	 *
	 * @param mixed $order_id
	 * @return
	 */
	public function pay_order_yes($order_id){
	   //权限检查
        if($this->session->userdata('user_group') != ADMIN && $this->session->userdata('user_group') != POWER_ADMIN)
            show_404();

		if(!is_numeric($order_id)){
            show_404();
		}else{
			if($this->order_model->check_order_examine($order_id) == false){
				echo '7';
			}else{
				$order_data = array();
				$order_data['order_pay_status'] = PAY_STATUS_YES;

				$result = $this->order_model->update_order($order_id, $order_data);

                if($result == 1){
    				$order_info = $this->order_model->get_order_duetime($order_id);
    				$server_data = array();
    				$server_data['server_due_time'] = $order_info['order_due_time'];

    				$server_result = $this->server_model->update_server($order_info['order_server_id'], $server_data);

    	            $email_msg = $this->email_model->email_get_order_info($order_id, 'pay_order_yes');
    	            $email_address = $this->email_model->get_admin_email();
    	            $this->email_model->send_email_user($email_address, $email_msg['subject'], $email_msg['msg']);

                    //如果过期时间在未来7天内，更新申请单状态为待付款，发送邮件给申请用户
                    if(strtotime($order_info['order_due_time']) < strtotime (date('Y-m-d 00:00:00', strtotime ("+8 day"))))
                        $this->order_model->pay_order_change($order_id, $order_info['order_user_id'], PAY_STATUS_WAITRENEW);

                    echo '1';
                }else{
                    echo '0';
                }

			}
		}
	}

	/**
	 * Order::examine_order()
	 *
	 * @param mixed $order_id
	 * @param string $examine_order
	 * @return
	 */
	public function examine_order($order_id, $examine_order = ''){
		//权限检查
        if($this->session->userdata('user_group') != ADMIN && $this->session->userdata('user_group') != POWER_ADMIN)
            show_404();

		if(!is_numeric($order_id) || ($examine_order != EXAMINE_STATUS_YES && $examine_order != EXAMINE_STATUS_NO)){
            show_404();
		}else{
			$order_data = array();
			$order_data['order_examine_status'] = $examine_order;

			$result = $this->order_model->update_order($order_id, $order_data);

            $email_msg = $this->email_model->email_get_order_info($order_id, 'examine_order');
            $email_address = $this->email_model->get_admin_email();
            $this->email_model->send_email_user($email_address, $email_msg['subject'], $email_msg['msg']);

			echo $result;
		}
	}

	/**
	 * Order::get_server_info()
	 *
	 * @param mixed $server_id
	 * @return
	 */
	public function get_server_info($server_id){
		if(!is_numeric($server_id)){
			echo 'false';
		}else{
			$server_info = $this->server_model->get_id_server($server_id);
			$result = $this->server_model->order_server_info($server_info);
			echo $result;
		}
	}

    /**
     * Order::order_search()
     *
     * @return
     */
    public function order_search_serverid($server_id = ''){
        if(!is_numeric($server_id) || $server_id <= 0)
            show_404();

        $order_search_data['order_search_select'] = 4;
        $order_search_data['order_search_input'] = '';
        $order_search_data['order_search_ip_select'] = $server_id;

        $this->session->set_userdata($order_search_data);

        redirect('/order/order_search/', 'refresh');

    }

    /**
     * Order::order_search()
     *
     * @return
     */
    public function order_email_remind($order_id = ''){
        if(!is_numeric($order_id) || $order_id <= 0)
            show_404();

        $email_msg = $this->email_model->email_get_order_info($order_id, 'create_remind');
        $email_address = $this->email_model->get_admin_email();
        $result = $this->email_model->send_email_user($email_address, $email_msg['subject'], $email_msg['msg']);

        echo $result ? '2' : '3';

    }

    /**
     * Order::order_search()
     *
     * @return
     */
    public function order_search(){

        $order_search_where = '';
        $data['search_where'] = true;

        if(!empty($_POST['submit'])){

            $order_search_data['order_search_select'] = trim($this->input->post('search_select'));
            $order_search_data['order_search_input'] = trim($this->input->post('search_input'));
            $order_search_data['order_search_ip_select'] = trim($this->input->post('search_ip_select'));

            $this->session->set_userdata($order_search_data);


            $order_search_where = $this->order_model->order_transition_search_where($order_search_data['order_search_select'], $order_search_data['order_search_input'], $order_search_data['order_search_ip_select']);

            if($order_search_where == false){
                $order_search_where = '';
                $data['search_where'] = false;
            }

        }elseif($this->session->userdata('order_search_select') != false){
            $order_search_where = $this->order_model->order_transition_search_where($this->session->userdata('order_search_select'), $this->session->userdata('order_search_input'), $this->session->userdata('order_search_ip_select'));
        }

		//配置分页类
		$config['base_url'] = 'order/order_search/';
		$config['per_page'] = PAGE_PER_MAX;
		$config['uri_segment'] = 3;
		$data['page_total'] = $config['total_rows'] = $this->order_model->get_order_form($order_search_where, '');
		$config['cur_tag_open'] = '<a class="current">';
		$config['cur_tag_close'] = '</a>';
		$config['first_link'] = '&laquo;';
		$config['last_link'] = '&raquo;';
		//获取分页
		$this->pagination->initialize($config);
		$data['page'] = $this->pagination->create_links();

		//获取server数据
		$server_result = $this->server_model->get_server_to_order();
		$server_value = $this->server_model->option_server_to_order($server_result,$this->session->userdata('order_search_ip_select'));

		//获取货币单位
		$ORDER_PAY_UNIT = $this->config->item('ORDER_PAY_UNIT');

		//获取支付审核信息数组
		$PAY_STATUS = $this->config->item('PAY_STATUS');
		$EXAMINE_STATUS = $this->config->item('EXAMINE_STATUS');

        $data['search_server_ip'] = $server_value['server_ip'];

		//获取列表
		$data['result'] = '';
        if($data['search_where']){

			//页数存入seesion已供excel导出
			$this->session->set_userdata('now_page', intval($this->uri->segment(3)));
			$this->session->set_userdata('excel_where', $order_search_where);

    		$result = $this->order_model->get_order_form($order_search_where, '', PAGE_PER_MAX, intval($this->uri->segment(3)));

        	foreach ($result as $key => $value){
        		foreach($value as $key2 => $value2){
        			if($key2 == 'order_server_id'){
        				$server_info = $this->server_model->get_id_server($value2);
        				$data['result']->$key->$key2 = $this->server_model->order_server_info($server_info, false, true);
        			}elseif($key2 == 'order_user_id'){
        				$data['result']->$key->$key2 = $this->user_model->get_id_user($value2);
        			}elseif($key2 == 'order_due_time'){
        			     $expiration_time = $value->order_expiration_time + 1;
						$data['result']->$key->$key2 = strtotime($value2) != 0 ? ((strtotime($value2) < strtotime (date('Y-m-d 00:00:00', strtotime ("+$expiration_time day"))) &&  strtotime($value2) >= strtotime (date('Y-m-d 00:00:00'))) ? ('<span class="order-pay-status order-pay-no">' . date('Y-m-d', strtotime($value2)) . '</span>') : date('Y-m-d', strtotime($value2))) : ('0');
					}elseif($key2 == 'order_pay_status'){
					   $pay_status =  '<span class="order-pay-status order-pay-' . $value2 . '">' . $PAY_STATUS[$value2] . '</span>';
	                   if($this->session->userdata('user_group') < USER && $value2 == PAY_STATUS_NO){
						  $pay_status .= '<br />[<a href="order/pay_order_yes/' . $data['result']->$key->order_id . '" onClick="operate_affirm(\'order\/pay_order_yes\/' . $data['result']->$key->order_id . '\/\');return false;" title="确认已付款！">确认</a>]';
					   }elseif($value2 == PAY_STATUS_WAITRENEW){
						  $pay_status .= '<br />[<a href="order/reneworder/' . $data['result']->$key->order_id . '" title="服务器续费！">续费</a>]';
					   }

                       if($value2 == PAY_STATUS_NO && $data['result']->$key->order_user_id == $this->session->userdata('user_login')){
						$pay_status .= '[<a href="order/order_email_remind/' . $data['result']->$key->order_id . '" onClick="operate_affirm(\'order\/order_email_remind\/' . $data['result']->$key->order_id . '\/\');return false;" title="发送email提醒管理员！">email!</a>]';
                        }

					   $data['result']->$key->$key2 = $pay_status;

				    }elseif($key2 == 'order_examine_status'){
						$data['result']->$key->$key2 = '<span class="order-examine-status order-examine-' . $value2 . '"> ' . $EXAMINE_STATUS[$value2] . ' </span>' . ($this->session->userdata('user_group') < USER ? ($value2 == EXAMINE_STATUS_WAIT ? ('<br />[<a href="order/examine_order/' . $data['result']->$key->order_id . '/' . EXAMINE_STATUS_YES . '" onClick="operate_affirm(\'order\/examine_order\/' . $data['result']->$key->order_id . '\/' . EXAMINE_STATUS_YES . '\/\');return false;" title="审核通过！">通过</a>]<br />[<a href="order/examine_order/' . $data['result']->$key->order_id . '/' . EXAMINE_STATUS_NO . '" onClick="operate_affirm(\'order\/examine_order\/' . $data['result']->$key->order_id . '\/' . EXAMINE_STATUS_NO . '\/\');return false;" title="审核不通过！">未通过</a>]') : '') : '');
					}elseif($key2 == 'order_pay_amount'){
						$data['result']->$key->$key2 = $ORDER_PAY_UNIT[$value->order_unit]['display'] . ' ' . $value2;
					}else{
        				if($key2 == 'order_alipay_id' && (int)$value2 != -1)
        					$data['result']->$key->pay_method .= '<span class="pay_method_info">支付宝支付<br /><strong>支付宝帐号：</strong>' . $value2 . '</span>';
        				if($key2 == 'order_bank_id' && (int)$value2 != -1)
        					$data['result']->$key->pay_method .= '<span class="pay_method_info">银行支付<br /><strong>银行帐号：</strong>' . $value2;
        				if($key2 == 'order_bank_name' && (int)$value2 != -1)
        					$data['result']->$key->pay_method .= '<br /><strong>支付银行：</strong>' . $value2 . '</span>';
        				$data['result']->$key->$key2 = $value2;
        			}
        		}
        		if(!isset($data['result']->$key->pay_method)) $data['result']->$key->pay_method = 'false';
        	}
        }

		$data['form_title'] = array(
								'编号',
								'服务器IP',
								'用户',
								'支付方式',
								'申请单金额',
								'创建时间',
								'截止日期',
								'备注',
								'付款状态',
								'审核状态');

		$data['data_key'] = array(
								'order_id',
								'order_server_id',
								'order_user_id',
								'pay_method',
								'order_pay_amount',
								'order_start_time',
								'order_due_time',
								'order_remark',
								'order_pay_status',
								'order_examine_status');

		$data['top_item'] = 'order';
		$data['item'] = 'order_search';
		$data['item_id_field'] = 'order_id';
		//搜索功能
		$data['data_search'] = true;
		$data['data_search_url'] = 'order/order_search/';

		$data['edit_able'] = $this->permission_model->check_sql_permission(TABLE_ORDERS, SQL_ACTION_UPDATE, $this->session->userdata('user_group'));
		$data['data_edit_url'] = 'order/edit/';

		$data['delete_able'] = $this->permission_model->check_sql_permission(TABLE_ORDERS, SQL_ACTION_DELETE, $this->session->userdata('user_group'));
		$data['data_delete_url'] = 'order/delete_order_single/';

		$this->load->view('index', $data);

    }



	/**
	 * Order::reneworder()
	 *
	 * @return
	 */
	public function reneworder($order_id){
    	if(!$this->permission_model->check_sql_permission(TABLE_ORDERS, SQL_ACTION_INSERT, $this->session->userdata('user_group')))
            show_404();

		if(!is_numeric($order_id))
            show_404();

		$order_data = array('order_id' => $order_id);

		$result = $this->order_model->get_order_form($order_data, '', 1, 0);
		//获取server数据
		$server_result = $this->server_model->get_server_to_order();

		foreach ($result as $row){$data['result'] = $row;}

		//获取货币单位
		$pay_unit_value = $this->order_model->option_unit_to_order($data['result']->order_unit);

		$server_value = $this->server_model->option_server_to_order($server_result,$data['result']->order_server_id);


		$data['data_create'] = array(
								'server_id' => array(
									'element'=> 'select',
									'type' => '',
									'name' => '服务器IP(不可编辑)',
									'value' => $server_value['server_ip'],
									'maxlength' => '',
									'need' => true,
									'disabled' => true),

								'alipay_id' => array(
									'element'=> 'input',
									'type' => 'text',
									'name' => '支付宝帐号',
									'value' => $data['result']->order_alipay_id,
									'maxlength' => '64',
									'need' => true),

								'bank_id' => array(
									'element'=> 'input',
									'type' => 'text',
									'name' => '银行帐号',
									'value' => $data['result']->order_bank_id,
									'maxlength' => '64',
									'need' => true),

								'bank_name' => array(
									'element'=> 'input',
									'type' => 'text',
									'name' => '开户银行名称',
									'value' => $data['result']->order_bank_name,
									'maxlength' => '60',
									'need' => true),

								'pay_unit' => array(
									'element'=> 'select',
									'type' => 'text',
									'name' => '金额单位',
									'value' => $pay_unit_value,
									'maxlength' => '',
									'need' => true),

								'pay_amount' => array(
									'element'=> 'input',
									'type' => 'text',
									'name' => '申请单金额(精确至小数点后两位小数)',
									'value' => $data['result']->order_pay_amount,
									'maxlength' => '12',
									'need' => true),

								'due_time' => array(
									'element'=> 'input',
									'type' => 'text',
									'name' => '截止日期(上次截止日期：' . date('Y-m-d', strtotime ($data['result']->order_due_time)) . ' )',
									'value' => date('Y-m-d', strtotime ("+1 month", strtotime ($data['result']->order_due_time))),
									'maxlength' => '50',
									'need' => true),

								'remark' => array(
									'element'=> 'textarea',
									'type' => 'text',
									'name' => '备注',
									'value' => $data['result']->order_remark,
									'maxlength' => '200'),
								);
		$data['data_create_url'] = 'order/create/' . $order_id;

		$data['top_item'] = 'order';
		$data['item'] = 'order';

		$this->load->view('reneworder', $data);
	}



	/**
	 * Order::multiorder()
	 *
	 * @return
	 */
	public function multiorder(){

		//获取server数据
		$server_result = $this->server_model->get_server_to_order('false');
		$server_value = $this->server_model->option_server_to_order($server_result,DEFAULT_SERVER_ID);

		$pay_unit_value = $this->order_model->option_unit_to_order(DEFAULT_UNIT);

        if($this->permission_model->check_sql_permission(TABLE_ORDERS, SQL_ACTION_INSERT, $this->session->userdata('user_group')))
		$data['data_create'] = array(
								'server_id' => array(
									'element'=> 'select',
									'type' => '',
									'name' => '服务器IP',
									'value' => $server_value['server_ip'],
									'maxlength' => '',
									'need' => true),

								'alipay_id' => array(
									'element'=> 'input',
									'type' => 'text',
									'name' => '支付宝帐号',
									'value' => '',
									'maxlength' => '64',
									'need' => true),

								'bank_id' => array(
									'element'=> 'input',
									'type' => 'text',
									'name' => '银行帐号',
									'value' => '',
									'maxlength' => '64',
									'need' => true),

								'bank_name' => array(
									'element'=> 'input',
									'type' => 'text',
									'name' => '开户银行名称',
									'value' => '',
									'maxlength' => '60',
									'need' => true,
									'newline' => true),

								'pay_unit' => array(
									'element'=> 'select',
									'type' => 'text',
									'name' => '金额单位',
									'value' => $pay_unit_value,
									'maxlength' => '',
									'need' => true),

								'pay_amount' => array(
									'element'=> 'input',
									'type' => 'text',
									'name' => '申请单金额(精确至小数点后两位)',
									'value' => '',
									'maxlength' => '12',
									'need' => true),

								'due_time' => array(
									'element'=> 'input',
									'type' => 'text',
									'name' => '截止日期 填写格式(2011-04-07)',
									'value' => '',
									'maxlength' => '50',
									'need' => true),

								'remark' => array(
									'element'=> 'textarea',
									'type' => 'text',
									'name' => '备注(100个汉字以内)',
									'value' => '',
									'maxlength' => '200',
									'newline' => true),
								);
		$data['data_create_url'] = 'order/create';

		$data['top_item'] = 'order';
		$data['item'] = 'multiorder';

		$this->load->view('multiorder', $data);
	}



	/**
	 * Order::multicreate()
	 *
	 * @return
	 */
	public function multicreate(){
	   //权限检查
        if(!$this->permission_model->check_sql_permission(TABLE_ORDERS, SQL_ACTION_INSERT, $this->session->userdata('user_group')))
            show_404();

        $result = '';

        for($i = 0; $i < 5; $i ++){

			$order_data = array();
			$order_data['order_server_id'] = trim($this->input->post('server_id'.$i));
			$order_data['order_alipay_id'] = trim($this->input->post('alipay_id'.$i));
			$order_data['order_bank_id'] = trim($this->input->post('bank_id'.$i));
			$order_data['order_bank_name'] = trim($this->input->post('bank_name'.$i));
			$order_data['order_unit'] = trim($this->input->post('pay_unit'.$i));
			$order_data['order_pay_amount'] = trim($this->input->post('pay_amount'.$i));
			$order_data['order_due_time'] = date('Y-m-d H:i:s', strtotime($this->input->post('due_time'.$i)));
			$order_data['order_remark'] = trim($this->input->post('remark'.$i));

			if(empty($_POST['pay_amount'.$i]) && empty($_POST['due_time'.$i]))
				continue;

        	$result .= '表单 ' . ($i+1) . ' : ';

			if(empty($_POST['due_time'.$i])){
				$result .= '表单信息不完整';
			}elseif(!(strtotime($this->input->post('due_time'.$i)) > 0) || !(strtotime($this->input->post('due_time'.$i)) > time())){
				$result .= '表单日期格式错误或小于当前日期！';
			}else{
				$check_repeat_result = $this->order_model->get_order_form($order_data);
				if($check_repeat_result != 0){
					$result .= '存在重复申请单！';
				}else{
					$query_result = $this->order_model->create($order_data);
					$result .= $query_result == 1 ? '创建成功！' : '创建失败！';
				}
			}
			$result .= '<br />';
        }

		$data['multiorder_result'] = $result;
		$data['top_item'] = 'order';
		$data['item'] = 'multiorder';
		$data['submit_back'] = 'multiorder';

		$this->load->view('multiorder', $data);

	}


	/**
	 * Order::excel_export()
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
			$this->db->where('order_status', 'open');
		}

		$this->db->order_by("order_start_time", "desc");

		$query = $this->db->get(TABLE_ORDERS, $limit, $now_page);

		$FIELD_DISPLAY = $this->config->item('ORDER_FIELD_DISPLAY');

		$result_array = $this->order_model->excel_sql_result_convert($query, $FIELD_DISPLAY);

		$this->excel_model->array_to_excel($result_array['headerarr'], $result_array['resultarr'], 'excel数据');
	}




	/**
	 * Order::order_list()
	 *
	 * @return
	 */
	public function order_needrenew(){

        $order_search_where = array(
                             //   'order_due_time >=' => date('Y-m-d 00:00:00'),
                               // 'order_due_time <' => date('Y-m-d 00:00:00', strtotime ("+8 day"))
                                );

		//配置分页类
		$config['base_url'] = 'order/order_needrenew/';
        $page_per_max = isset($_GET['page_per_max']) && is_numeric($_GET['page_per_max']) ?  $_GET['page_per_max']:PAGE_PER_MAX;

		$config['per_page'] = $page_per_max;
		$config['uri_segment'] = 3;
		$data['page_total'] = $config['total_rows'] = $this->order_model->get_order_form($order_search_where,'','','','','renew');
		$config['cur_tag_open'] = '<a class="current">';
		$config['cur_tag_close'] = '</a>';
		$config['first_link'] = '&laquo;';
		$config['last_link'] = '&raquo;';
		//获取分页
		$this->pagination->initialize($config);
		$data['page'] = $this->pagination->create_links();
        if(preg_match("/order_needrenew\/\d/",$data['page'])){
            $data['page'] = preg_replace("/order_needrenew\/(\d)/","order_needrenew/\$1".'?page_per_max='.$page_per_max,$data['page']);
        }

		//获取server数据
		$server_result = $this->server_model->get_server_to_order('false');
		$server_value = $this->server_model->option_server_to_order($server_result,DEFAULT_SERVER_ID);

		//获取货币单位
		$pay_unit_value = $this->order_model->option_unit_to_order(DEFAULT_UNIT);
		$ORDER_PAY_UNIT = $this->config->item('ORDER_PAY_UNIT');

		//获取支付审核信息数组
		$PAY_STATUS = $this->config->item('PAY_STATUS');
		$EXAMINE_STATUS = $this->config->item('EXAMINE_STATUS');

		//页数存入seesion已供excel导出
		$this->session->set_userdata('now_page', intval($this->uri->segment(3)));
		$this->session->set_userdata('excel_where', $order_search_where);

		//获取列表
		$result = $this->order_model->get_order_form($order_search_where, '', $page_per_max, intval($this->uri->segment(3)),'','renew');
      
		$data['result'] = '';

		foreach ($result as $key => $value){
				$data['result']->$key->pay_method = '';
			foreach($value as $key2 => $value2){
				if($key2 == 'order_server_id'){
					$server_info = $this->server_model->get_id_server($value2);
					$data['result']->$key->$key2 = $this->server_model->order_server_info($server_info, false, true);
				}elseif($key2 == 'order_user_id'){
					$data['result']->$key->$key2 = $this->user_model->get_id_user($value2);
				}elseif($key2 == 'order_due_time'){
				    $expiration_time = $value->order_expiration_time + 1;
					$data['result']->$key->$key2 = strtotime($value2) != 0 ? ((strtotime($value2) < strtotime (date('Y-m-d 00:00:00', strtotime ("+$expiration_time day"))) &&  strtotime($value2) >= strtotime (date('Y-m-d 00:00:00'))) ? ('<span class="order-pay-status order-pay-no">' . date('Y-m-d', strtotime($value2)) . '</span>') : date('Y-m-d', strtotime($value2))) : ('0');
				}elseif($key2 == 'order_pay_status'){
					$pay_status =  '<span class="order-pay-status order-pay-' . $value2 . '">' . $PAY_STATUS[$value2] . '</span>';
					if($this->session->userdata('user_group') < USER && $value2 == PAY_STATUS_NO){
						$pay_status .= '<br />[<a href="order/pay_order_yes/' . $data['result']->$key->order_id . '" onClick="operate_affirm(\'order\/pay_order_yes\/' . $data['result']->$key->order_id . '\/\');return false;" title="确认已付款！">确认</a>]';
					}elseif($value2 == PAY_STATUS_WAITRENEW){
						$pay_status .= '<br />[<a href="order/reneworder/' . $data['result']->$key->order_id . '" title="服务器续费！">续费</a>]';
					}

                    if($value2 == PAY_STATUS_NO && $data['result']->$key->order_user_id == $this->session->userdata('user_login')){
						$pay_status .= '[<a href="order/order_email_remind/' . $data['result']->$key->order_id . '" onClick="operate_affirm(\'order\/order_email_remind\/' . $data['result']->$key->order_id . '\/\');return false;" title="发送email提醒管理员！">email!</a>]';
					}

					$data['result']->$key->$key2 = $pay_status;

				}elseif($key2 == 'order_examine_status'){
					$data['result']->$key->$key2 = '<span class="order-examine-status order-examine-' . $value2 . '"> ' . $EXAMINE_STATUS[$value2] . ' </span>' . ($this->session->userdata('user_group') < USER ? ($value2 == EXAMINE_STATUS_WAIT ? ('<br />[<a href="order/examine_order/' . $data['result']->$key->order_id . '/' . EXAMINE_STATUS_YES . '" onClick="operate_affirm(\'order\/examine_order\/' . $data['result']->$key->order_id . '\/' . EXAMINE_STATUS_YES . '\/\');return false;" title="审核通过！">通过</a>]<br />[<a href="order/examine_order/' . $data['result']->$key->order_id . '/' . EXAMINE_STATUS_NO . '" onClick="operate_affirm(\'order\/examine_order\/' . $data['result']->$key->order_id . '\/' . EXAMINE_STATUS_NO . '\/\');return false;" title="审核不通过！">未通过</a>]') : '') : '');
				}elseif($key2 == 'order_pay_amount'){
					$data['result']->$key->$key2 = $ORDER_PAY_UNIT[$value->order_unit]['display'] . ' ' . $value2;
				}else{
					if($key2 == 'order_alipay_id' && ((int)$value2 != -1))
						$data['result']->$key->pay_method .= '<span class="pay_method_info">支付宝支付<br /><strong>支付宝帐号：</strong>' . $value2 . '</span>';
					if($key2 == 'order_bank_id' && ((int)$value2 != -1))
						$data['result']->$key->pay_method .= '<span class="pay_method_info">银行支付<br /><strong>银行帐号：</strong>' . $value2;
					if($key2 == 'order_bank_name' && ((int)$value2 != -1))
						$data['result']->$key->pay_method .= '<br /><strong>支付银行：</strong>' . $value2 . '</span>';
					$data['result']->$key->$key2 = $value2;
				}
			}
		}

		$data['form_title'] = array(
								'编号',
								'服务器IP',
								'用户',
								'支付方式',
								'申请单金额',
								'创建时间',
								'服务器截止日期',
                                '有效天数',
								'备注',
								'付款状态',
								'审核状态');

		$data['data_key'] = array(
								'order_id',
								'order_server_id',
								'order_user_id',
								'pay_method',
								'order_pay_amount',
								'order_start_time',
								'order_due_time',
                                'order_expiration_time',
								'order_remark',
								'order_pay_status',
								'order_examine_status');


		$data['top_item'] = 'order';
		$data['item'] = 'order_needrenew';
		$data['item_id_field'] = 'order_id';
		$data['edit_able'] = $this->permission_model->check_sql_permission(TABLE_ORDERS, SQL_ACTION_UPDATE, $this->session->userdata('user_group'));
		$data['data_edit_url'] = 'order/edit/';
        $data['dedicacated'] = 1; //标识特殊的分页
        $data['page_per_max'] = $page_per_max;
        $data['expiration_time_url'] = 'order/expiration_time';
		$data['delete_able'] = $this->permission_model->check_sql_permission(TABLE_ORDERS, SQL_ACTION_DELETE, $this->session->userdata('user_group'));
		$data['data_delete_url'] = 'order/delete_order_single/';

		$this->load->view('index', $data);

	}
    
    /**
     *@autor $Id$
     * 批量修改过期天数
     * */
     function expiration_time(){
        if(empty($_GET['order_id']) || empty($_GET['times']) || !is_numeric($_GET['times'])){
            echo 0;
        }
        $order_id = explode(',',substr($_GET['order_id'],0,-1));
        $order_data = array('order_expiration_time'=>$_GET['times']);
        
        $result = $this->order_model->update_expiration_time($order_id, $order_data);
        echo $result;
     }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
