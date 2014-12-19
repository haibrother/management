<?php

/**
 * Order_model
 *
 * @package
 * @author hx_wsm
 * @copyright 417
 * @version 2011
 * @access public
 */
class Domainorder_model extends CI_Model
{

	/**
	 * Order_model::__construct()
	 *
	 * @return
	 */
	function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	/**
	 * Order_model::create()
	 *
	 * @param mixed $order_data
	 * @return
	 */
	public function create($order_data = array(), $renew = false){
        if(!$this->permission_model->check_sql_permission(TABLE_DOMAINORDERS, SQL_ACTION_INSERT, $this->session->userdata('user_group')))
            return false;


		$ud = $order_data;

		if(empty($ud))
			return false;

		if(!isset($ud['order_domain_id']) || !isset($ud['order_alipay_id']) || !isset($ud['order_bank_id']) || !isset($ud['order_bank_name']) || !isset($ud['order_unit'])  || !isset($ud['order_pay_amount']) || !isset($ud['order_due_time']) || !isset($ud['order_remark']))
			return false;

		$data = array(
			'order_domain_id' => $ud['order_domain_id'],
			'order_user_id' => $this->session->userdata('user_id'),
			'order_alipay_id' => $ud['order_alipay_id'],
			'order_bank_id' => $ud['order_bank_id'],
			'order_bank_name' => $ud['order_bank_name'],
			'order_unit' => $ud['order_unit'],
			'order_pay_amount' => $ud['order_pay_amount'],
			'order_start_time' => date('Y-m-d H:i:s'),
			'order_due_time' => $ud['order_due_time'],
			'order_remark' => $ud['order_remark'],
			'order_pay_status' => 'no',
			'order_examine_status' => 'wait',
			'order_status' => 'open'
        );

		$this->db->insert(TABLE_DOMAINORDERS, $data);
		$result = $this->db->affected_rows();

        if($result == 1){
            $i_id = $this->db->insert_id();
            $new_operationlog_date = $this->get_order_form(array('order_id' => $i_id), '', 1, 0);
            $this->operationlog_model->write_operationlog('', $new_operationlog_date, TABLE_DOMAINORDERS, SQL_ACTION_INSERT, $data, 3);

			$email_msg = $this->email_model->email_get_order_info($i_id, $renew == false ? 'create' : 'renew_create');
            $email_address = $this->email_model->get_admin_email();
            $this->email_model->send_email_user($email_address, $email_msg['subject'], $email_msg['msg']);
        }

        return $result;

	}


	/**
	 * Order_model::get_order_form()
	 *
	 * @param string $wherearr
	 * @param string $order
	 * @param string $num
	 * @param string $offset
	 * @param string $like
	 * @return
	 */
	function get_order_form($wherearr = '', $order = '', $num = '', $offset = '', $like = '',$type='')
	{

        if(!$this->permission_model->check_sql_permission(TABLE_DOMAINORDERS, SQL_ACTION_SELECT, $this->session->userdata('user_group')))
            return false;
		if(!empty($wherearr))
			$this->db->where($wherearr);
    
		//if($this->session->userdata('user_group') == USER)
          //  $this->db->where(array('order_user_id' => $this->session->userdata('user_id')));

        empty($order) ? $this->db->order_by("order_start_time", "desc") : $this->db->order_by($order);
        $this->db->where('order_status', 'open');
        if(!empty($type)){
            if($type == 'payment'){
                $this->db->where_in('order_pay_status',array('yes','no'));
            }else{
                $this->db->where_in('order_pay_status',array('renewed','waitrenew'));
            }
        }
		
        $this->db->from(TABLE_DOMAINORDERS);

        if(is_numeric($num) && is_numeric($offset)){ //412 20110326 
            $this->db->limit($num, $offset);
    		$query = $this->db->get();
    		return $query->result();
        } else {
           // $this->db->count_all_results();
            //var_dump($this->db->count_all_results());
    		return $this->db->count_all_results();
        }
	}


	/**
	 * Order_model::delete_order()
	 *
	 * @param string $wherearr
	 * @return
	 */
	function delete_order($wherearr = '')
	{
        if(!$this->permission_model->check_sql_permission(TABLE_DOMAINORDERS, SQL_ACTION_DELETE, $this->session->userdata('user_group')))
            return false;

		if(!empty($wherearr)){
            $old_operationlog_date = $this->get_order_form($wherearr, '', 1, 0);

            $this->db->where($wherearr);
			$this->db->update(TABLE_DOMAINORDERS, array('order_status' => 'close'));
            $result = $this->db->affected_rows();

            $this->operationlog_model->write_operationlog($old_operationlog_date, '', TABLE_DOMAINORDERS, SQL_ACTION_DELETE, '', 3);

            return $result;

		}else{
			return false;
		}
	}


	/**
	 * Order_model::update_order()
	 *
	 * @param string $order_id
	 * @param mixed $update_data
	 * @return
	 */
	function update_order($order_id = '', $update_data = array())
	{
        //if(!$this->permission_model->check_sql_permission(TABLE_DOMAINORDERS, SQL_ACTION_UPDATE, $this->session->userdata('user_group')))
            //return false;

		if(!is_numeric($order_id) || empty($update_data))
			return false;

        $wherearr = array('order_id' => $order_id);
        $old_operationlog_date = $this->get_order_form($wherearr, '', 1, 0);

		$this->db->where($wherearr);
		$this->db->update(TABLE_DOMAINORDERS, $update_data);

		$result = $this->db->affected_rows();

        $this->operationlog_model->write_operationlog($old_operationlog_date, '', TABLE_DOMAINORDERS, SQL_ACTION_UPDATE, $update_data, 3);

		return $result;
	}
    
    function update_expiration_time($order_id = '', $update_data = array())
	{
	   //权限检查
        //if(!$this->permission_model->check_sql_permission(TABLE_ORDERS, SQL_ACTION_UPDATE, $this->session->userdata('user_group')))
            //return false;

		if(!is_array($order_id) || empty($update_data))
			return false;

        $wherearr = $this->db->where_in('order_id',$order_id);
        //数据表更改前数据
       // $old_operationlog_date = $this->get_order_form($wherearr, '', 1, 0);
        
		$this->db->update(TABLE_DOMAINORDERS, $update_data);
		$result = $this->db->affected_rows();

       // $this->operationlog_model->write_operationlog($old_operationlog_date, '', TABLE_ORDERS, SQL_ACTION_UPDATE, $update_data, 3);

		return $result;
	}


	/**
	 * Order_model::order_transition_search_where()
	 *
	 * @param string $search_select
	 * @param string $search_input
	 * @param string $search_ip_select
	 * @return
	 */
	function order_transition_search_where($search_select = '', $search_input = '', $search_ip_select = ''){
		if(empty($search_select) || !is_numeric($search_select))
	       return false;

        switch($search_select){
            case 1:
                $search_select_date = $this->user_model->get_user_form(array('user_login' => $search_input), '', 1, 0);
                if($search_select_date){
                    $user_id = 0;
                    foreach($search_select_date as $row){
                        $user_id = $row->user_id;
                    }
                    if($user_id == 0)
                        return false;
                    return array('order_user_id' => $user_id);
                }
                break;

            case 2:
                $timestamp = strtotime($search_input);
                if ($timestamp === -1)
                    return false;

                if(strlen($search_input) == 10){
                    $daystamp = date("Y-m-d", $timestamp);
                    $day_start = strtotime($daystamp);
                    $day_end = strtotime ("+1 day", $day_start);
                    return array('order_start_time >=' => date('Y-m-d H:i:s', $day_start), 'order_start_time <' => date('Y-m-d H:i:s', $day_end));
                }elseif(strlen($search_input) == 7){
                    $daystamp = date("Y-m-01", $timestamp);
                    $day_start = strtotime($daystamp);
                    $day_end = strtotime ("+1 month", $day_start);
                    return array('order_start_time >=' => date('Y-m-d H:i:s', $day_start), 'order_start_time <' => date('Y-m-d H:i:s', $day_end));
                }elseif(strlen($search_input) == 4){
                    $daystamp = date("Y-01-01", $timestamp);
                    $day_start = strtotime($daystamp);
                    $day_end = strtotime ("+1 year", $day_start);
                    return array('order_start_time >=' => date('Y-m-d H:i:s', $day_start), 'order_start_time <' => date('Y-m-d H:i:s', $day_end));

                }else{
                    return false;
                }
                break;

            case 3:
                $timestamp = strtotime($search_input);
                if ($timestamp === -1)
                    return false;

                if(strlen($search_input) == 10){
                    $daystamp = date("Y-m-d", $timestamp);
                    $day_start = strtotime($daystamp);
                    $day_end = strtotime ("+1 day", $day_start);
                    return array('order_due_time >=' => date('Y-m-d H:i:s', $day_start), 'order_due_time <' => date('Y-m-d H:i:s', $day_end));
                }elseif(strlen($search_input) == 7){
                    $daystamp = date("Y-m-01", $timestamp);
                    $day_start = strtotime($daystamp);
                    $day_end = strtotime ("+1 month", $day_start);
                    return array('order_due_time >=' => date('Y-m-d H:i:s', $day_start), 'order_due_time <' => date('Y-m-d H:i:s', $day_end));
                }elseif(strlen($search_input) == 4){
                    $daystamp = date("Y-01-01", $timestamp);
                    $day_start = strtotime($daystamp);
                    $day_end = strtotime ("+1 year", $day_start);
                    return array('order_due_time >=' => date('Y-m-d H:i:s', $day_start), 'order_due_time <' => date('Y-m-d H:i:s', $day_end));

                }else{
                    return false;
                }
                break;

            case 4:
                if(empty($search_ip_select))
                    return false;
                return array('order_domain_id' => $search_ip_select);
                break;

            default:
                return false;
                break;
        }
        return false;
	}


	/**
	 * Order_model::excel_sql_result_convert()
	 *
	 * @param mixed $query
	 * @param mixed $key_header
	 * @return
	 */
	public function excel_sql_result_convert($query, $key_header){
		if ($query->num_rows() <= 0)
			return false;

		$result['headerarr'] = $key_header;
		$result['resultarr'] = array();
		$ORDER_STATUS = $this->config->item('ORDER_STATUS');

		$ORDER_PAY_UNIT = $this->config->item('ORDER_PAY_UNIT');


		$PAY_STATUS = $this->config->item('PAY_STATUS');
		$EXAMINE_STATUS = $this->config->item('EXAMINE_STATUS');

		foreach ($query->result() as $key1 => $row){
			foreach($key_header as $key2 => $value){
				if($key2 == 'order_domain_id'){
					$server_info = $this->server_model->get_id_server($row->$key2);
					$result['resultarr'][$key1][$key2] = $this->server_model->order_server_info_excel($server_info);
				}elseif($key2 == 'order_user_id'){
					$result['resultarr'][$key1][$key2] = $this->user_model->get_id_user($row->$key2);
				}elseif($key2 == 'order_pay_status'){
					$result['resultarr'][$key1][$key2] = $PAY_STATUS[$row->$key2];
				}elseif($key2 == 'order_examine_status'){
					$result['resultarr'][$key1][$key2] = $EXAMINE_STATUS[$row->$key2];
				}elseif($key2 == 'order_status'){
					$result['resultarr'][$key1][$key2] = $ORDER_STATUS[$row->$key2];
				}elseif($key2 == 'pay_method'){
						$result['resultarr'][$key1][$key2] = '';

						if((int)$row->order_alipay_id != -1)
							$result['resultarr'][$key1][$key2] .= '支付宝支付, 支付宝帐号：' . $row->order_alipay_id . '';

						if((int)$row->order_bank_id != -1)
							$result['resultarr'][$key1][$key2] .= '银行支付, 银行帐号：'. $row->order_bank_id;

						if((int)$row->order_bank_name != -1)
							$result['resultarr'][$key1][$key2] .= ', 支付银行：' . $row->order_bank_name . '';

				}elseif($key2 == 'order_pay_amount'){
					$result['resultarr'][$key1][$key2] = $ORDER_PAY_UNIT[$row->order_unit]['display'] . ' ' . $row->order_pay_amount;
				}else{
					$result['resultarr'][$key1][$key2] = $row->$key2;
				}

			}
		}

		return $result;

	}
    
    public function check_domain_order($domain_id){
		if(!is_numeric($domain_id) || $domain_id <= 0)
			return false;

		$this->db->where('order_domain_id', $domain_id);
		$this->db->where('order_status', 'open');
		$query = $this->db->get(TABLE_DOMAINORDERS, 1);

		if ($query->num_rows() != 0)
			return true;

		return false;

	}

	/**
	 * Order_model::option_unit_to_order()
	 *
	 * @param string $default_order_unit
	 * @return
	 */
	function option_unit_to_order($default_order_unit = ''){
		$ORDER_PAY_UNIT = $this->config->item('ORDER_PAY_UNIT');
		$result = '';

		if(empty($ORDER_PAY_UNIT))
			return $result;

		foreach ($ORDER_PAY_UNIT as $key => $value){
 			$result .= '<option ' . ($key == $default_order_unit ? 'selected="selected"' : '') .  ' value="' . $key . '">' . $value['name'] . ' ' . $key . '</option>';
		}
		return $result;
	}

	/**
	 * Order_model::option_unit_to_order()
	 *
	 * @param string $default_order_unit
	 * @return
	 */
	function option_paystatus_to_order($default_paystatus = ''){
		$PAY_STATUS = $this->config->item('PAY_STATUS');
		$result = '';

		if(empty($PAY_STATUS))
			return $result;

		foreach ($PAY_STATUS as $key => $value){
 			$result .= '<option ' . ($key == $default_paystatus ? 'selected="selected"' : '') .  ' value="' . $key . '">' . $value . '</option>';
		}
		return $result;
	}

	/**
	 * Order_model::option_unit_to_order()
	 *
	 * @param string $default_order_unit
	 * @return
	 */
	function option_examinestatus_to_order($default_examinestatus = ''){
		$EXAMINE_STATUS = $this->config->item('EXAMINE_STATUS');
		$result = '';

		if(empty($EXAMINE_STATUS))
			return $result;

		foreach ($EXAMINE_STATUS as $key => $value){
 			$result .= '<option ' . ($key == $default_examinestatus ? 'selected="selected"' : '') .  ' value="' . $key . '">' . $value . '</option>';
		}
		return $result;
	}

	/**
	 * Order_model::get_order_duetime()
	 *
	 * @param string $order_id
	 * @return
	 */
	function get_order_duetime($order_id = 0){

		if(!is_numeric($order_id) || $order_id <= 0)
			return false;

		$this->db->where('order_id', $order_id);
		$query = $this->db->get(TABLE_DOMAINORDERS, 1);

		if ($query->num_rows() == 0)
			return false;
		$result = array();
		$row = $query->first_row();
		$result['order_due_time'] = $row->order_due_time;
		$result['order_domain_id'] = $row->order_domain_id;
		$result['order_user_id'] = $row->order_user_id;

		return $result;
	}


	/**
	 * Order_model::pay_order_change()
	 *
	 * @param mixed $order_id
	 * @param mixed $user_id
	 * @param mixed $order_pay_status
	 * @return void
	 */
	public function pay_order_change($order_id, $user_id, $order_pay_status){

		$order_data = array();
		$order_data['order_pay_status'] = $order_pay_status;

		$result = $this->update_order($order_id, $order_data);

		if($order_pay_status == PAY_STATUS_WAITRENEW){
			$email_msg = $this->email_model->email_get_order_info($order_id, 'userdue');
			$email_address = $this->email_model->get_user_email($user_id);
			$this->update_order($order_id, $order_data);
			$this->email_model->send_email_user($email_address, $email_msg['subject'], $email_msg['msg']);
		}

		return $result;
	}


	public function check_order_examine($order_id){
		if(!is_numeric($order_id) || $order_id <= 0)
			return false;

		$this->db->where('order_id', $order_id);
		$query = $this->db->get(TABLE_DOMAINORDERS, 1);

		if(!($query->num_rows() > 0))
			return false;

		foreach($query->result() as $row){
			return ($row->order_examine_status == EXAMINE_STATUS_YES) ? true : false;
		}
		return false;

	}


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */