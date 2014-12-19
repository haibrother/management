<?php

/**
 * Email_model
 *
 * @package
 * @author hx_wsm
 * @copyright 412
 * @version 2011
 * @access public
 */
class Email_model extends CI_Model
{

	/**
	 * Email_model::__construct()
	 *
	 * @return
	 */
	function __construct()
	{
		parent::__construct();

	}


	/**
	 * Email_model::send_email_user()
	 *
	 * @param string $to_email
	 * @param string $subject
	 * @param string $message
	 * @return
	 */
	function send_email_user($to_email = '', $subject = '', $message = ''){
		if($to_email == '' || $subject == '')
			return false;

    	preg_match_all("/(\w*\-*\.*)+@[a-zA-Z0-9\-]+\.[a-zA-Z]{2,4}(\.[a-zA-Z]{2,4})?/", implode(", ", $to_email), $emails);

		$this->load->library('email');

		$config['protocol'] = 'smtp';
		$config['smtp_host'] = 'smtp.163.com';
		$config['smtp_port'] = '25';
		$config['smtp_user'] = 'hx_wsm@163.com';
		$config['smtp_pass'] = 'hxwsm9999';
		$config['mailtype'] = 'html';
		$config['smtp_timeout'] = '5';

		$this->email->initialize($config);

		$this->email->from('hx_wsm@163.com', 'HX Web Server Manager');

		$this->email->to(array_values($emails[0]));

		$this->email->subject($subject);
		$this->email->message($message);

		$result = $this->email->send();

		$this->db->insert(TABLE_EMAILS, array('email_to' => implode(',', $to_email), 'email_time' => date('Y-m-d H:i:s'), 'email_debugger' => $this->email->print_debugger()));

        return $result;
	}

	/**
	 * Email_model::email_get_order_info()
	 * 获取申请单信息，转换成email内容
	 *
	 * @param integer $order_id
	 * @param string $process
	 * @return
	 */
	function email_get_order_info($order_id = 0, $process = '', $server_id_get = false){
		if($order_id == 0 || empty($process) || empty($order_id))
			return false;

		//获取列表
        if(!$server_id_get){
            $this->db->where(array('order_id' => $order_id));
        }else{
            $this->db->where(array('order_server_id' => $order_id));
            $this->db->where('order_pay_status !=', PAY_STATUS_RENEWED);
            $this->db->limit(1);
        }
		$this->db->where('order_status', 'open');
		$this->db->order_by("order_start_time", "desc");
		$query = $this->db->get(TABLE_ORDERS);

        if(!($query->num_rows() > 0))
            return false;

		$order_result = $query->result();

		if(empty($order_result))
			return false;

		//获取server数据
		$server_result = $this->server_model->get_server_to_order();
		$server_value = $this->server_model->option_server_to_order($server_result,DEFAULT_SERVER_ID);

		//获取支付审核信息数组
		$PAY_STATUS = $this->config->item('PAY_STATUS');
		$EXAMINE_STATUS = $this->config->item('EXAMINE_STATUS');
		$USER_GROUP_INFO = $this->config->item('USER_GROUP_INFO');
		//获取货币单位
		$ORDER_PAY_UNIT = $this->config->item('ORDER_PAY_UNIT');

		$result['msg'] = '';
		$result['subject'] = '';
		$ip_server_agent = '';
		$data = '';

		foreach ($order_result as $key => $value){
			foreach($value as $key2 => $value2){
				if($key2 == 'order_server_id'){
					$server_info = $this->server_model->get_id_server($value2);
					$data->$key2 = $this->server_model->order_server_info($server_info, false, false, true);
					$data->server_ip = $server_info['server_ip'];
				}elseif($key2 == 'order_user_id'){
					$user_result = $this->user_model->get_user_form(array('user_id' => $value2), '', 1, 0);
					foreach($user_result as $row){ $data->$key2 = $row; }
				}elseif($key2 == 'order_pay_status'){
					$data->$key2 = $PAY_STATUS[$value2];
				}elseif($key2 == 'order_examine_status'){
					$data->$key2 = $EXAMINE_STATUS[$value2];
				}else{
					if($key2 == 'order_alipay_id' && ((int)$value2 != -1))
						$data->pay_method .= '支付宝支付<br />支付宝帐号：' . $value2 . '<br />';
					if($key2 == 'order_bank_id' && (int)$value2 != -1)
						$data->pay_method .= '银行支付<br />银行帐号：' . $value2 . '<br />';
					if($key2 == 'order_bank_name' && ((int)$value2 != -1))
						$data->pay_method .= '支付银行：' . $value2 . '<br />';
					$data->$key2 = $value2;
				}
			}
			if(!isset($data->pay_method)) $data->pay_method = 'false';
		}

		if(!isset($data->order_id))
			return false;

		switch($process){
			case 'create':
				$result['subject'] = '新服务器申请:编号 ' . $data->order_id . ', 服务器IP ' . $data->server_ip . ', 用户 ' . $data->order_user_id->user_login;
				$ip_server_agent = 'IP ' . $this->input->server('REMOTE_ADDR') . '<br />浏览器信息 ' . $this->input->server('HTTP_USER_AGENT') . '<br />';
				break;
			case 'renew_create':
				$result['subject'] = '服务器续费申请:编号 ' . $data->order_id . ', 服务器IP ' . $data->server_ip . ', 用户 ' . $data->order_user_id->user_login;
				$ip_server_agent = 'IP ' . $this->input->server('REMOTE_ADDR') . '<br />浏览器信息 ' . $this->input->server('HTTP_USER_AGENT') . '<br />';
				break;
			case 'create_remind':
				$result['subject'] = '服务器续费提醒:请及时付费！编号 ' . $data->order_id . ', 服务器IP ' . $data->server_ip . ', 用户 ' . $data->order_user_id->user_login;
				$ip_server_agent = 'IP ' . $this->input->server('REMOTE_ADDR') . '<br />浏览器信息 ' . $this->input->server('HTTP_USER_AGENT') . '<br />';
				break;
			case 'edit':
				$result['subject'] = '服务器申请单修改:编号 ' . $data->order_id . ', 服务器IP ' . $data->server_ip . ', 操作用户 ' . $this->session->userdata('user_login');
				$ip_server_agent = '操作人员：<br />工号：' . $this->session->userdata('user_login') . '<br />时间：' . date('Y-m-d H:i:s') . '<br />';
				break;
			case 'pay_order_yes':
				$result['subject'] = '服务器申请单付款确认:编号 ' . $data->order_id . ', 服务器IP ' . $data->server_ip . ', 操作用户 ' . $this->session->userdata('user_login');
				$ip_server_agent = '操作人员：<br />工号：' . $this->session->userdata('user_login') . '<br />时间：' . date('Y-m-d H:i:s') . '<br />';
				break;
			case 'examine_order':
				$result['subject'] = '服务器申请单审核:编号 ' . $data->order_id . ', 服务器IP ' . $data->server_ip . ', 审核结果:' . $data->order_examine_status . ', 操作用户 ' . $this->session->userdata('user_login');
				$ip_server_agent = '操作人员：<br />工号：' . $this->session->userdata('user_login') . '<br />时间：' . date('Y-m-d H:i:s') . '<br />';
				break;
			case 'due':
				//$result['subject'] = '即将到期申请单:编号 ' . $data->order_id . ', 服务器IP ' . $data->server_ip . ', 用户 ' . $data->order_user_id->user_login . ', 截至时间 ' . $data->order_due_time . ', 还剩余 ' . (int)((strtotime($data->order_due_time) - time())/(24*3600)) . '天';

				//更新申请单状态为待付款，发送邮件给申请用户
				if($data->order_pay_status == $PAY_STATUS[PAY_STATUS_YES])
                    if(isset($data->order_id))
					   $this->order_model->pay_order_change($data->order_id, $data->order_user_id->user_id, PAY_STATUS_WAITRENEW);
				break;
			case 'userdue':
				$result['subject'] = '服务器过期提醒:编号 ' . $data->order_id . ', 服务器IP ' . $data->server_ip . ', 用户 ' . $data->order_user_id->user_login;
				break;
			default:
				return false;
				break;
		}


		$result['msg'] = '<strong>申请单信息：</strong>编号：' . $data->order_id . '<br /><strong>服务器信息：</strong>' . $data->order_server_id . '<strong>申请单金额</strong> ' . $ORDER_PAY_UNIT[$data->order_unit]['display'] . $data->order_pay_amount . '，申请单时间 ' . $data->order_start_time . '，<strong>截止日期</strong> <span style="color:red;"><strong>' . date('Y-m-d', strtotime($data->order_due_time)) . '</strong></span>，备注 ' . $data->order_remark . '<br /><strong>付款状态</strong> <span style="color:red;"><strong>' . $data->order_pay_status . '</strong></span>，<strong>审核状态</strong> <span style="color:red;"><strong>' . $data->order_examine_status . '</strong></span><br /><strong>付款方式：</strong>' . $data->pay_method .
		(isset($data->order_user_id->user_id) ? ('<strong>用户信息：</strong> ID ' . $data->order_user_id->user_id . ', 工号 ' . $data->order_user_id->user_login . '，部门 ' . $data->order_user_id->user_department . '，E-mail ' . $data->order_user_id->user_email . '，用户类型 ' . $USER_GROUP_INFO[$data->order_user_id->user_status]['name'] . '<br />') : '') . '<br />';

        $result['msg'] = str_replace(array("http://", "https://"), '', $result['msg']);

		$result['order_id'] = $data->order_id;

		return $result;
	}


	/**
	 * Email_model::email_dueorder_date()
	 * 生成即将过期的申请单提醒email内容
	 *
	 * @return
	 */
	function email_dueorder_date(){

		if(!$this->email_dueorder_date_check())
			return false;

		$this->db->select('server_id, server_ip');
		$this->db->where('server_due_time >=', date('Y-m-d 00:00:00'));
		$this->db->where('server_due_time <', date('Y-m-d 00:00:00', strtotime ("+8 day")));
		$this->db->where('server_status', '1');
		$query = $this->db->get(TABLE_SERVERS);

		if(!($query->num_rows() > 0)){
			$this->email_dueorder_date_update();
			return false;
		}

		$email_subject = '服务器过期提醒：申请单ID：';
		$email_msg = '';
        $email_address = $this->get_admin_email();

		foreach($query->result() as $row){
			$msg = $this->email_get_order_info($row->server_id, 'due', true);
			$email_subject .= isset($msg['order_id']) ? ($msg['order_id'] . ', ') : '';
			$email_msg .= isset($msg['msg']) ? '<strong>即将过期申请单：</strong>' : '';
			$email_msg .= isset($msg['msg']) ? $msg['msg'] : '';
		}

		$email_subject .= '请及时处理！';

        if(isset($email_msg))
            $this->send_email_user($email_address, $email_subject, $email_msg);

		$this->email_dueorder_date_update();

		return true;
	}


	/**
	 * Email_model::email_dueorder_date_check()
	 * 查询数据库内数据，确认是否已经发送过过期申请单
	 *
	 * @return
	 */
	function email_dueorder_date_check(){
		$this->db->select('option_value');
		$this->db->where('option_name', 'email_send_day');
		$query = $this->db->get(TABLE_OPTIONS);

		if(!($query->num_rows() > 0))
			return false;

		foreach($query->result() as $row){
			if($row->option_value != date('Y-m-d'))
			return true;
		}
		return false;

	}


	/**
	 * Email_model::email_dueorder_date_update()
	 *
	 * @return
	 */
	function email_dueorder_date_update(){
		$this->db->where('option_name', 'email_send_day');
		$this->db->update(TABLE_OPTIONS, array('option_value' => date('Y-m-d')));
		return true;
	}


	/**
	 * Email_model::get_admin_email()
	 *
	 * @return
	 */
	public function get_admin_email(){
		$this->db->select('user_email');
		$this->db->where('user_status', ADMIN);
		$this->db->or_where('user_status', POWER_ADMIN);
		$query = $this->db->get(TABLE_USERS);

		if(!($query->num_rows() > 0))
			return false;

		$result = array();
		foreach($query->result() as $row){
			$result[] = $row->user_email;
		}
		return $result;
	}


	/**
	 * Email_model::get_user_email($user_id)
	 *
	 * @return
	 */
	public function get_user_email($user_id){
		if(!is_numeric($user_id) && $user_id <= 0)
			return false;

		$this->db->select('user_email');
		$this->db->where('user_id', $user_id);
		$query = $this->db->get(TABLE_USERS);

		if(!($query->num_rows() > 0))
			return false;

		$result = array();
		foreach($query->result() as $row){
			$result[] = $row->user_email;
		}
		return $result;
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */