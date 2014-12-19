<?php

/**
 * Server_model
 *
 * @package
 * @author hx_wsm
 * @copyright 412
 * @version 2011
 * @access public
 */
class Server_model extends CI_Model
{

	/**
	 * Server_model::__construct()
	 *
	 * @return
	 */
	function __construct()
	{
		parent::__construct();
		$this->load->database();

	}


	/**
	 * Server_model::get_server_form()
	 * 412 20110326 获取服务器列表
	 *
	 * @param string $wherearr
	 * @param string $order
	 * @param string $num
	 * @param string $offset
	 * @param string $status
	 * @return
	 */
	function get_server_form($wherearr = '', $order = '', $num = '', $offset = '', $status = '', $likearr = '')
	{
		if(!empty($wherearr))
			$this->db->where($wherearr);

		if(!empty($likearr)){
			if(isset($likearr['and']))
				$this->db->like($likearr['and']);
		}

		empty($order) ? $this->db->order_by("server_ip", "asc") : $this->db->order_by($order);

        $this->db->from(TABLE_SERVERS);

        if(!empty($status)){
			$this->db->where('server_status', $status);
        }else{
			$this->db->where('server_status >', 0);
        }

        if(is_numeric($num) && is_numeric($offset)){ //412 20110326 判定如只传入的限制条件不是数字则返回记录条数
        	if($num >0){
            	$this->db->limit($num, $offset);
        	}

    		$query = $this->db->get();
    		return $query->result();
        } else {
    		return $this->db->count_all_results();
        }
	}

	/**
	 * Server_model::create()
	 *
	 * @param mixed $server_data
	 * @return
	 */
	public function create($server_data = array()){
	   //权限检查
        if(!$this->permission_model->check_sql_permission(TABLE_SERVERS, SQL_ACTION_INSERT, $this->session->userdata('user_group')))
            return false;

		$ud = $server_data;

		if(empty($ud))
			return false;

		if(!isset($ud['server_ip']) || !isset($ud['server_address_name']) || !isset($ud['server_idc_url']) || !isset($ud['server_purpose']) || !isset($ud['server_pass']) || !isset($ud['server_user']))
			return false;

		$data = array(
           'server_ip' => $ud['server_ip'],
           'server_address_name' => $ud['server_address_name'],
           'server_idc_url' => $ud['server_idc_url'],
           'server_purpose' => $ud['server_purpose'],
           'server_user' => $ud['server_user'],
           'server_pass' => $this->encrypt->encode($ud['server_pass']),
           'server_contact' => $ud['server_contact'],
           'server_nicename' => '',
           'server_time' => date('Y-m-d H:i:s'),
           'server_status' => 1
        );

		$this->db->insert(TABLE_SERVERS, $data);
		$result = $this->db->affected_rows();

        if($result == 1){
            $i_id = $this->db->insert_id();
            $new_operationlog_date = $this->get_server_form(array('server_id' => $i_id), '', 1, 0);
            $this->operationlog_model->write_operationlog('', $new_operationlog_date, TABLE_SERVERS, SQL_ACTION_INSERT, $data, 2);
        }

        return $result;
	}


	/**
	 * Server_model::delete_server()
	 * 412 20110326 删除服务器
	 *
	 * @param string $wherearr
	 * @return
	 */
	function delete_server($wherearr = '')
	{
	   //权限检查
        if(!$this->permission_model->check_sql_permission(TABLE_SERVERS, SQL_ACTION_DELETE, $this->session->userdata('user_group')))
            return false;

		if(!empty($wherearr)){
            //数据表更改前数据
            $old_operationlog_date = $this->get_server_form($wherearr, '', 1, 0);

            $this->db->where($wherearr);
			$this->db->update(TABLE_SERVERS, array('server_status' => 0));
            $result = $this->db->affected_rows();

            $this->operationlog_model->write_operationlog($old_operationlog_date, '', TABLE_SERVERS, SQL_ACTION_DELETE, '', 2);

            return $result;
		}else{
			return false;
		}
	}

	/**
	 * Server_model::update_server()
	 *
	 * @param string $server_id
	 * @param mixed $update_data
	 * @return
	 */
	function update_server($server_id = '', $update_data = array())
	{
	   //权限检查
        if(!$this->permission_model->check_sql_permission(TABLE_SERVERS, SQL_ACTION_UPDATE, $this->session->userdata('user_group')))
            return false;

		if(!is_numeric($server_id) || empty($update_data))
			return false;

        $wherearr = array('server_id' => $server_id);
        //数据表更改前数据
        $old_operationlog_date = $this->get_server_form($wherearr, '', 1, 0);

		$this->db->where($wherearr);

		//加密密码
		if(isset($update_data['server_pass']))
			$update_data['server_pass'] = $this->encrypt->encode($update_data['server_pass']);

		$this->db->update(TABLE_SERVERS, $update_data);

		$result = $this->db->affected_rows();

        $this->operationlog_model->write_operationlog($old_operationlog_date, '', TABLE_SERVERS, SQL_ACTION_UPDATE, $update_data, 2);

        return $result;
	}

	/**
	 * Server_model::get_server_to_order()
	 *
	 * @param bool $all
	 * @return
	 */
	function get_server_to_order($all = false){
		if($all == false){
			$query = $this->get_server_form('', '', 0, 0);
		}else{
			$this->db->order_by("server_ip", "asc");
			$this->db->where('server_status', 1);
        	$this->db->from(TABLE_SERVERS);
        	//$this->db->join(TABLE_ORDERS, TABLE_ORDERS . '.order_server_id != ' . TABLE_SERVERS . '.server_id');
        	//$this->db->select(TABLE_SERVERS . '.server_ip', TABLE_SERVERS . '.server_address_name', TABLE_SERVERS . '.server_idc_url', TABLE_SERVERS . '.server_purpose');

    		$query = $this->db->get();
    		$query = $query->result();
		}

		$result = array();

		foreach ($query as $row){
			if($all == false || $this->order_model->check_server_order($row->server_id) == false)
				$result[$row->server_id] = array(
		  								'server_ip' => $row->server_ip,
		  								'server_address_name' => $row->server_address_name,
		  								'server_idc_url' =>$row->server_idc_url,
		  								'server_purpose' =>$row->server_purpose);
		}
		return $result;
	}

	/**
	 * Server_model::option_server_to_order()
	 *
	 * @param string $server_result
	 * @param integer $default_server_id
	 * @return
	 */
	function option_server_to_order($server_result = '',$default_server_id = 0){
		if(empty($server_result))
			return false;

		$result = array();
		$result['server_ip'] = '';
		foreach ($server_result as $key => $value){
 			$result['server_ip'] .= '<option ' . ($key == $default_server_id ? 'selected="selected"' : '') .  ' value="' . $key . '">' . $value['server_ip'] . '</option>';
		}
		return $result;
	}


	/**
	 * Server_model::get_id_server()
	 *
	 * @param integer $server_id
	 * @return
	 */
	function get_id_server($server_id = 0){
	   //权限检查
        if(!$this->permission_model->check_sql_permission(TABLE_SERVERS, SQL_ACTION_SELECT, $this->session->userdata('user_group')))
            return false;

		if(!is_numeric($server_id) || $server_id <= 0)
			return false;

		$this->db->where('server_id', $server_id);
		$query = $this->db->get(TABLE_SERVERS, 1);

		if ($query->num_rows() == 0)
			return false;

		$row = $query->first_row();
		$result = '';
		$result = array(
                    'server_id' => $row->server_id,
					'server_ip' => $row->server_ip,
					'server_address_name' => $row->server_address_name,
					'server_idc_url' => $row->server_idc_url,
					'server_purpose' => $row->server_purpose,
					'server_status' => $row->server_status,
					'server_due_time' => $row->server_due_time);

		return $result;
	}

	/**
	 * Server_model::order_server_info()
	 *
	 * @param mixed $server_info
	 * @param bool $all
	 * @return
	 */
	function order_server_info($server_info, $all = true, $link = false, $due_time = false){
		if(empty($server_info))
			return false;

		$result = '';
		if($all == true){
			$result .= ((isset($server_info['server_address_name'])) ? '<strong>所在机房：</strong>' . $server_info['server_address_name'] . '<br />' : '') . ((isset($server_info['server_idc_url'])) ? '<strong>IDC：</strong>' . $this->get_id_didcurl($server_info['server_idc_url']) . '<br />' : '') . ((isset($server_info['server_purpose'])) ? '<strong>作用：</strong>' . $server_info['server_purpose'] . '<br />' : '') . ((isset($server_info['server_status']) && $server_info['server_status'] == 0) ? '(已被删除！)<br />' : ((isset($server_info['server_status']) && $server_info['server_status'] == 2) ? '<span class="order-pay-no"><strong>(已停用！)</strong></span><br />' : ''));
		}else{
			$result .= ((isset($server_info['server_ip'])) ? (($link) ? ('<a href="order/order_search_serverid/' . $server_info['server_id'] . '" title="查询该服务器所有申请单">' . $server_info['server_ip'] . '</a><br />') : ($server_info['server_ip'] . '<br />')) : '') . '<span class="server_ip_info">' . ((isset($server_info['server_address_name'])) ? '<strong>机房：</strong>' . $server_info['server_address_name'] . '<br />' : '') . ((isset($server_info['server_idc_url'])) ? '<strong>IDC：</strong>' . $this->get_id_didcurl($server_info['server_idc_url']) . '<br />' : '') . ((isset($server_info['server_purpose'])) ? '<strong>作用：</strong>' . $server_info['server_purpose'] . '<br />' : '') . ((isset($server_info['server_status']) && $server_info['server_status'] == 0) ? '(已被删除！)<br />' : ((isset($server_info['server_status']) && $server_info['server_status'] == 2) ? '<span class="order-pay-no"><strong>(已停用！)</strong></span><br />' : '')) . '</span>' . (($due_time == true && $server_info['server_due_time'] != 0) ? '<span style="color:red"><strong>服务器截止日期：' . date('Y-m-d', strtotime ($server_info['server_due_time'])) . '</strong></span><br />' : '');
		}
		return $result;
	}


	/**
	 * Server_model::order_server_info_excel()
	 *
	 * @param mixed $server_info
	 * @return
	 */
	function order_server_info_excel($server_info){
		if(empty($server_info))
			return false;

		$result = '';
			$result .= ((isset($server_info['server_ip'])) ? $server_info['server_ip'] . ', ' : '') . ((isset($server_info['server_address_name'])) ? '机房：' . $server_info['server_address_name'] . ', ' : '') . ((isset($server_info['server_idc_url'])) ? 'IDC：' . $this->get_id_didcurl($server_info['server_idc_url']) . ', ' : '') . ((isset($server_info['server_purpose'])) ? '作用：' . $server_info['server_purpose'] . ', ' : '') . ((isset($server_info['server_status']) && $server_info['server_status'] == 0) ? '(已被删除！)' : '');
		return $result;
	}


	/**
	 * Server_model::server_transition_search_where()
	 *
	 * @param string $search_select
	 * @param string $search_input
	 * @param string $search_ip_select
	 * @return
	 */
	function server_transition_search_like($search_select){
		if(!isset($search_select['server_search_select']) || !isset($search_select['server_search_select_in']) || !isset($search_select['server_search_input']))
			return false;

		switch($search_select['server_search_select']){
            case 1:
                if($search_select['server_search_select_in'] == 'in'){
                    return array('and' => array('server_ip' => $search_select['server_search_input']));
                }
                break;

            case 2:
                if($search_select['server_search_select_in'] == 'in'){
                    return array('and' => array('server_purpose' => $search_select['server_search_input']));
                }
                break;
            case 3:
                if($search_select['server_search_select_in'] == 'in'){
                    return array('and' => array('server_contact' => $search_select['server_search_input']));
                }
                break;
             case 4:
                if($search_select['server_search_select_in'] == 'in'){
                    return array('and' => array('server_status' => $search_select['server_search_input']));
                }
                break;
             case 5:
                if($search_select['server_search_select_in'] == 'in'){
                    return array('and' => array('check_permissions' => $search_select['server_search_input']));
                }
                break;
	    default:
                return false;
                break;
        }
        return false;


	}
    	/**
	 * Domain_model::get_didcurl_form()
	 * 412 20110326 获取用户列表
	 * 
	 * @param string $wherearr
	 * @param string $order
	 * @param string $num
	 * @param string $offset
	 * @param bool $all
	 * @return
	 */
	function get_didcurl_form($wherearr = '', $order = '', $num = '', $offset = '', $all = false)
	{
	   //权限检查
        if(!$this->permission_model->check_sql_permission(TABLE_DNLCURLS, SQL_ACTION_SELECT, $this->session->userdata('user_group')))
			return false;
        
		if(!empty($wherearr))
			$this->db->where($wherearr);
		
		if($all == false)
			$this->db->where('didcurl_status', 1);
			
		empty($order) ? $this->db->order_by("didcurl_time", "desc") : $this->db->order_by($order);
		
        $this->db->from(TABLE_DNLCURLS);
        
        if(is_numeric($num) && is_numeric($offset)){ //412 20110326 判定如只传入的限制条件不是数字则返回记录条数
        	if($num >0){
            	$this->db->limit($num, $offset);
        	}
        	
    		$query = $this->db->get();
    		return $query->result();
        } else {
    		return $this->db->count_all_results();
        }
	}
	
	/**
	 * Domain_model::didcurl_create()
	 * 
	 * @param mixed $didcurl_data
	 * @return
	 */
	public function didcurl_create($didcurl_data = array()){
	   //权限检查
        if(!$this->permission_model->check_sql_permission(TABLE_DNLCURLS, SQL_ACTION_INSERT, $this->session->userdata('user_group')))
			return false;
        
		
		$ud = $didcurl_data;
		
		if(empty($ud))
			return false;
			
		if(!isset($ud['didcurl_value']))
			return false;
		
		$data = array(
           'didcurl_value' => $ud['didcurl_value'],
           'didcurl_time' => date('Y-m-d H:i:s'),
           'didcurl_status' => 1
        );
        
		$this->db->insert(TABLE_DNLCURLS, $data);
		$result = $this->db->affected_rows();
        if($result == 1){
            $i_id = $this->db->insert_id();
            $new_operationlog_date = $this->get_didcurl_form(array('didcurl_id' => $i_id), '', 1, 0);
            $this->operationlog_model->write_operationlog('', $new_operationlog_date, TABLE_DNLCURLS, SQL_ACTION_INSERT, $data, 2);
        }
        
        return $result;
	}
	
	
	/**
	 * Domain_model::update_didcurl()
	 * 
	 * @param string $didcurl_id
	 * @param mixed $update_data
	 * @return
	 */
	function update_didcurl($didcurl_id = '', $update_data = array())
	{
	   //权限检查
        if(!$this->permission_model->check_sql_permission(TABLE_DNLCURLS, SQL_ACTION_UPDATE, $this->session->userdata('user_group')))
			return false;
        
		if(!is_numeric($didcurl_id) || empty($update_data))
			return false;
        
        $wherearr = array('didcurl_id' => $didcurl_id);
        //数据表更改前数据
        $old_operationlog_date = $this->get_didcurl_form($wherearr, '', 1, 0);
        
		$this->db->where($wherearr);
		$this->db->update(TABLE_DNLCURLS, $update_data);
        
		$result = $this->db->affected_rows();
        
        $this->operationlog_model->write_operationlog($old_operationlog_date, '', TABLE_DNLCURLS, SQL_ACTION_UPDATE, $update_data, 2);

        return $result;
	}
	
	
	/**
	 * Domain_model::delete_didcurl()
	 * 
	 * @param string $wherearr
	 * @return
	 */
	function delete_didcurl($wherearr = '')
	{
	   //权限检查
        if(!$this->permission_model->check_sql_permission(TABLE_DNLCURLS, SQL_ACTION_DELETE, $this->session->userdata('user_group')))
			return false;
        
		if(!empty($wherearr)){
            
            //数据表更改前数据
            $old_operationlog_date = $this->get_didcurl_form($wherearr, '', 1, 0);
            
            $this->db->where($wherearr);
			$this->db->update(TABLE_DNLCURLS, array('didcurl_status' => 0));
            $result = $this->db->affected_rows();
            
            $this->operationlog_model->write_operationlog($old_operationlog_date, '', TABLE_DNLCURLS, SQL_ACTION_DELETE, '', 2);
            
            return $result;
		}else{
			return false;
		}
	}
	
	/**
	 * Domain_model::get_didcurl_to_domain()
	 * 412 20110326 获取与domain关联的idc url数组
	 * 返回以didcurl_id 为键名的数组
	 * 
	 * @param bool $all
	 * @return
	 */
	function get_didcurl_to_domain($all = false){
        
		if($all == false){
			$query = $this->get_didcurl_form('', '', 0, 0);
		}else{
			$query = $this->get_didcurl_form('', '', 0, 0, true);
		}
		
		$result = array();
		
		foreach ($query as $row){
		  $result[$row->didcurl_id] = $row->didcurl_value;
		}
		return $result;
	}
	
	/**
	 * Domain_model::option_didcurl_to_domain()
	 * 412 20110326 获取与domain关联的idc url数组
	 * 返回以didcurl_id 为键名的数组
	 * 
	 * @param string $didcurl_result
	 * @param integer $default_idcurl_id
	 * @return
	 */
	function option_didcurl_to_domain($didcurl_result = '',$default_idcurl_id = 0){
        
		if(empty($didcurl_result))
			return false;
		
		$result = '';
		foreach ($didcurl_result as $key => $value){
			
 			$result .= '<option ' . ($key == $default_idcurl_id ? 'selected="selected"' : '') .  ' value="' . $key . '">' . $value . '</option>';
		}
		return $result;
	}
	
	/**
	 * Domain_model::get_id_didcurl()
	 * 
	 * @param integer $didcurl_id
	 * @return
	 */
	function get_id_didcurl($didcurl_id = 0){
	   //权限检查
        if(!$this->permission_model->check_sql_permission(TABLE_DNLCURLS, SQL_ACTION_SELECT, $this->session->userdata('user_group')))
			return false;
        
		if(!is_numeric($didcurl_id) || $didcurl_id <= 0)
			return false;
		
		$this->db->where('didcurl_id', $didcurl_id);
		$query = $this->db->get(TABLE_DNLCURLS, 1);
		
		if ($query->num_rows() == 0)
			return false;
		
		$row = $query->first_row();
		$result = $row->didcurl_value;
		if($row->didcurl_status == 0)
			$result .= '(已删除)';
		return $result;
	}
	

	/**
	 * Server_model::excel_sql_result_convert()
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

		//获得全局状态数组
		$GLOBAL_SERVER_STATUS = $this->config->item('GLOBAL_SERVER_STATUS');

		foreach ($query->result() as $key1 => $row){
			foreach($key_header as $key2 => $value){
				if($key2 == 'server_idc_url'){
					$result['resultarr'][$key1][$key2] = $this->get_id_didcurl($row->$key2);
				}elseif($key2 == 'server_status' || $key2 == 'didcurl_status'){
					$result['resultarr'][$key1][$key2] = $GLOBAL_SERVER_STATUS[$row->$key2];
				}elseif($key2 == 'server_pass'){
					$result['resultarr'][$key1][$key2] = $this->encrypt->decode($row->$key2);
				}else{
					$result['resultarr'][$key1][$key2] = $row->$key2;
				}
			}
		}

		return $result;

	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
