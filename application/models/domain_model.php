<?php

/**
 * Domain_model
 * 
 * @package   
 * @author hx_wsm
 * @copyright 412
 * @version 2011
 * @access public
 */
class Domain_model extends CI_Model
{

	/**
	 * Domain_model::__construct()
	 * 
	 * @return
	 */
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		
		date_default_timezone_set('PRC');
	}
	
	/**
	 * Domain_model::create()
	 * 
	 * @param mixed $domain_data
	 * @return
	 */
	public function create($domain_data = array()){
	   //权限检查
        if(!$this->permission_model->check_sql_permission(TABLE_DOMAINS, SQL_ACTION_INSERT, $this->session->userdata('user_group')))
            return false;
        
		
		$ud = $domain_data;
		
		if(empty($ud))
			return false;
			
		if(!isset($ud['domain_name']) || !isset($ud['domain_idc_url']) || !isset($ud['domain_user']) || !isset($ud['domain_pass']) || !isset($ud['domain_web_ip']))
			return false;
		
		$data = array(
           'domain_name' => $ud['domain_name'],
           'domain_idc_url' => $ud['domain_idc_url'],
           'domain_purpose' => $ud['domain_purpose'],
           'domain_user' => $ud['domain_user'],
           'domain_pass' => $this->encrypt->encode($ud['domain_pass']),
           'domain_web_ip' => $ud['domain_web_ip'],
           'domain_nicename' => '',
           'domain_dns' => '',
           'domain_time' => date('Y-m-d H:i:s'),
           'domain_status' => 1
        );
        
		$this->db->insert(TABLE_DOMAINS, $data);
		$result = $this->db->affected_rows();
        
        if($result == 1){
            $i_id = $this->db->insert_id();
            $new_operationlog_date = $this->get_domain_form(array('domain_id' => $i_id), '', 1, 0);
            $this->operationlog_model->write_operationlog('', $new_operationlog_date, TABLE_DOMAINS, SQL_ACTION_INSERT, $data, 3);
        }
        
        return $result;
	}
	
	/**
	 * Domain_model::get_domain_form()
	 * 412 20110326 获取用户列表
	 * 
	 * @param string $wherearr
	 * @param string $order
	 * @param string $num
	 * @param string $offset
	 * @return
	 */
	function get_domain_form($wherearr = '', $order = '', $num = '', $offset = '')
	{
	   //权限检查
        if(!$this->permission_model->check_sql_permission(TABLE_DOMAINS, SQL_ACTION_SELECT, $this->session->userdata('user_group')))
            return false;
        
		if(!empty($wherearr))
			$this->db->where($wherearr);
		
        if(!empty($status)){
			$this->db->where('domain_status', $status);
        }else{
			$this->db->where('domain_status >', 0);
        }
			
		empty($order) ? $this->db->order_by("domain_time", "desc") : $this->db->order_by($order);
		
        $this->db->from(TABLE_DOMAINS);
        
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
	 * Domain_model::delete_domain()
	 * 412 20110326 删除用户
	 * 
	 * @param string $wherearr
	 * @return
	 */
	function delete_domain($wherearr = '')
	{
	   //权限检查
        if(!$this->permission_model->check_sql_permission(TABLE_DOMAINS, SQL_ACTION_DELETE, $this->session->userdata('user_group')))
            return false;
        
		if(!empty($wherearr)){
            //数据表更改前数据
            $old_operationlog_date = $this->get_domain_form($wherearr, '', 1, 0);
            
            $this->db->where($wherearr);
			$this->db->update(TABLE_DOMAINS, array('domain_status' => 0));
            $result = $this->db->affected_rows();
            
            $this->operationlog_model->write_operationlog($old_operationlog_date, '', TABLE_DOMAINS, SQL_ACTION_DELETE, '', 3);
            
            return $result;
		}else{
			return false;
		}
	}
	
	
	/**
	 * Domain_model::update_domain()
	 * 
	 * @param string $domain_id
	 * @param mixed $update_data
	 * @return
	 */
	function update_domain($domain_id = '', $update_data = array())
	{
	   //权限检查
        if(!$this->permission_model->check_sql_permission(TABLE_DOMAINS, SQL_ACTION_UPDATE, $this->session->userdata('user_group')))
            return false;
        
		if(!is_numeric($domain_id) || empty($update_data))
			return false;
        
        $wherearr = array('domain_id' => $domain_id);
        //数据表更改前数据
        $old_operationlog_date = $this->get_domain_form($wherearr, '', 1, 0);
        
		$this->db->where($wherearr);
		
		//加密密码
		if(isset($update_data['domain_pass']))
		$update_data['domain_pass'] = $this->encrypt->encode($update_data['domain_pass']);
		
		$this->db->update(TABLE_DOMAINS, $update_data);
        
		$result = $this->db->affected_rows();
        
        $this->operationlog_model->write_operationlog($old_operationlog_date, '', TABLE_DOMAINS, SQL_ACTION_UPDATE, $update_data, 3);

        return $result;
	}
    
   	/**
	 * Server_model::option_server_to_order()
	 *417 20121112
	 * @param string $domain_result
	 * @param integer $default_domain_id
	 * @return
	 */
	function option_domain_to_order($domain_result = '',$default_domain_id = 0){
		if(empty($domain_result))
			return false;

		$result = array();
		$result['domain_name'] = '';
		foreach ($domain_result as $key => $value){
 			$result['domain_name'] .= '<option ' . ($key == $default_domain_id ? 'selected="selected"' : '') .  ' value="' . $key . '">' . $value['domain_name'] . '</option>';
		}
		return $result;
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
        if(!$this->permission_model->check_sql_permission(TABLE_DIDCURLS, SQL_ACTION_SELECT, $this->session->userdata('user_group')))
			return false;
        
		if(!empty($wherearr))
			$this->db->where($wherearr);
		
		if($all == false)
			$this->db->where('didcurl_status', 1);
			
		empty($order) ? $this->db->order_by("didcurl_time", "desc") : $this->db->order_by($order);
		
        $this->db->from(TABLE_DIDCURLS);
        
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
        if(!$this->permission_model->check_sql_permission(TABLE_DIDCURLS, SQL_ACTION_INSERT, $this->session->userdata('user_group')))
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
        
		$this->db->insert(TABLE_DIDCURLS, $data);
		$result = $this->db->affected_rows();
        
        if($result == 1){
            $i_id = $this->db->insert_id();
            $new_operationlog_date = $this->get_didcurl_form(array('didcurl_id' => $i_id), '', 1, 0);
            $this->operationlog_model->write_operationlog('', $new_operationlog_date, TABLE_DIDCURLS, SQL_ACTION_INSERT, $data, 2);
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
        if(!$this->permission_model->check_sql_permission(TABLE_DIDCURLS, SQL_ACTION_UPDATE, $this->session->userdata('user_group')))
			return false;
        
		if(!is_numeric($didcurl_id) || empty($update_data))
			return false;
        
        $wherearr = array('didcurl_id' => $didcurl_id);
        //数据表更改前数据
        $old_operationlog_date = $this->get_didcurl_form($wherearr, '', 1, 0);
        
		$this->db->where($wherearr);
		$this->db->update(TABLE_DIDCURLS, $update_data);
        
		$result = $this->db->affected_rows();
        
        $this->operationlog_model->write_operationlog($old_operationlog_date, '', TABLE_DIDCURLS, SQL_ACTION_UPDATE, $update_data, 2);

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
	   //鏉冮檺妫€鏌?
        if(!$this->permission_model->check_sql_permission(TABLE_DIDCURLS, SQL_ACTION_DELETE, $this->session->userdata('user_group')))
			return false;
        
		if(!empty($wherearr)){
            
            //数据表更改前数据
            $old_operationlog_date = $this->get_didcurl_form($wherearr, '', 1, 0);
            
            $this->db->where($wherearr);
			$this->db->update(TABLE_DIDCURLS, array('didcurl_status' => 0));
            $result = $this->db->affected_rows();
            
            $this->operationlog_model->write_operationlog($old_operationlog_date, '', TABLE_DIDCURLS, SQL_ACTION_DELETE, '', 2);
            
            return $result;
		}else{
			return false;
		}
	}
	
	/**
	 * Domain_model::get_didcurl_to_domain()
	 * 412 20110326 获取与domain关联的idc url数组
	 * 杩斿洖浠idcurl_id 涓洪敭鍚嶇殑鏁扮粍
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
        if(!$this->permission_model->check_sql_permission(TABLE_DIDCURLS, SQL_ACTION_SELECT, $this->session->userdata('user_group')))
			return false;
        
		if(!is_numeric($didcurl_id) || $didcurl_id <= 0)
			return false;
		
		$this->db->where('didcurl_id', $didcurl_id);
		$query = $this->db->get(TABLE_DIDCURLS, 1);
		
		if ($query->num_rows() == 0)
			return false;
		
		$row = $query->first_row();
		$result = $row->didcurl_value;
		if($row->didcurl_status == 0)
			$result .= '(已删除)';
		return $result;
	}
    
    /**
	 * Server_model::get_domain_to_order()
	 *
	 * @param bool $all
	 * @return
	 */
	function get_domain_to_order($all = false){
		if($all == false){
			$query = $this->get_domain_form('', '', 0, 0);
		}else{
			$this->db->order_by("domain_name", "asc");
			$this->db->where('domain_status', 1);
        	$this->db->from(TABLE_DOMAINS);
    		$query = $this->db->get();
    		$query = $query->result();
		}

		$result = array();

		foreach ($query as $row){
			if($all == false || $this->domainorder_model->check_domain_order($row->domain_id) == false)
				$result[$row->domain_id] = array(
		  								'domain_id' => $row->domain_id,
		  								'domain_name' => $row->domain_name,
		  								'domain_idc_url' =>$row->domain_idc_url,
		  								'domain_purpose' =>$row->domain_purpose);
		}
		return $result;
	}
    
    /**
	 * domain_model::get_id_domain()
	 *
	 * @param integer $domain_id
	 * @return
	 */
	function get_id_domain($domain_id = 0){
	   //权限检查
        if(!$this->permission_model->check_sql_permission(TABLE_DOMAINS, SQL_ACTION_SELECT, $this->session->userdata('user_group')))
            return false;

		if(!is_numeric($domain_id) || $domain_id <= 0)
			return false;

		$this->db->where('domain_id', $domain_id);
		$query = $this->db->get(TABLE_DOMAINS, 1);

		if ($query->num_rows() == 0)
			return false;

		$row = $query->first_row();
		$result = '';
		$result = array(
                    'domain_id' => $row->domain_id,
					'domain_name' => $row->domain_name,
					'domain_web_ip' => $row->domain_web_ip,
					'domain_idc_url' => $row->domain_idc_url,
					'domain_purpose' => $row->domain_purpose,
					'domain_status' => $row->domain_status,
					'domain_due_time' => $row->domain_due_time);

		return $result;
	}

	/**
	 * Server_model::order_domain_info()
	 *
	 * @param mixed $domain_info
	 * @param bool $all
	 * @return
	 */
	function order_domain_info($domain_info, $all = true, $link = false, $due_time = false){
		if(empty($domain_info))
			return false;

		$result = '';
		if($all == true){
			$result .=  ((isset($domain_info['domain_idc_url'])) ? '<strong>IDC：</strong>' . $this->get_id_didcurl($domain_info['domain_idc_url']) . '<br />' : '') . ((isset($domain_info['domain_purpose'])) ? '<strong>作用：</strong>' . $domain_info['domain_purpose'] . '<br />' : '') . ((isset($domain_info['domain_status']) && $domain_info['domain_status'] == 0) ? '(已被删除！)<br />' : ((isset($domain_info['domain_status']) && $domain_info['domain_status'] == 2) ? '<span class="order-pay-no"><strong>(已停用！)</strong></span><br />' : ''));
		}else{
			$result .= ((isset($domain_info['domain_web_ip'])) ? (($link) ? ('<a href="domainorder/order_search_serverid/' . $domain_info['domain_id'] . '" title="查询该服务器所有申请单">' . $domain_info['domain_web_ip'] . '</a><br />') : ($domain_info['domain_web_ip'] . '<br />')) : '') . '<span class="server_ip_info">' . ((isset($domain_info['domain_idc_url'])) ? '<strong>IDC：</strong>' . $this->get_id_didcurl($domain_info['domain_idc_url']) . '<br />' : '') . ((isset($domain_info['domain_purpose'])) ? '<strong>作用：</strong>' . $domain_info['domain_purpose'] . '<br />' : '') . ((isset($domain_info['domain_status']) && $domain_info['domain_status'] == 0) ? '(已被删除！)<br />' : ((isset($domain_info['domain_status']) && $domain_info['domain_status'] == 2) ? '<span class="order-pay-no"><strong>(已停用！)</strong></span><br />' : '')) . '</span>' . (($due_time == true && $domain_info['domain_due_time'] != 0) ? '<span style="color:red"><strong>域名截止日期：' . date('Y-m-d', strtotime ($domain_info['domain_due_time'])) . '</strong></span><br />' : '');
		}
		return $result;
	}
	
	
	/**
	 * Domain_model::domain_transition_search_where()
	 * 获取符合条件的domain id对应数组，会导致结果只有一个。函数需要改造成关键词搜索。
	 * 
	 * @param string $search_input
	 * @return
	 */
	function domain_transition_search_where($search_input = ''){
		if(empty($search_input))
	       return false;
           
        $search_select_date = $this->get_domain_form(array('domain_name' => $search_input), '', 1, 0);
            if($search_select_date){
                $domain_id = 0;
                foreach($search_select_date as $row){
                    $domain_id = $row->domain_id;
                }
                if($domain_id == 0)
                    return false;
                return array('domain_id' => $domain_id);
            }
        return false;
	}
	
	
	/**
	 * Domain_model::excel_sql_result_convert_domain()
	 * 
	 * @param mixed $query
	 * @param mixed $key_header
	 * @return
	 */
	public function excel_sql_result_convert_domain($query, $key_header){
		if ($query->num_rows() <= 0)
			return false;
		
		$result['headerarr'] = $key_header;
		$result['resultarr'] = array();
		
		//获得全局状态数组
		$GLOBAL_SERVER_STATUS = $this->config->item('GLOBAL_SERVER_STATUS');
		
		foreach ($query->result() as $key1 => $row){
			foreach($key_header as $key2 => $value){
				if($key2 == 'domain_idc_url'){
					$result['resultarr'][$key1][$key2] = $this->domain_model->get_id_didcurl($row->$key2);
				}elseif($key2 == 'domain_status' || $key2 == 'didcurl_status'){
					$result['resultarr'][$key1][$key2] = $GLOBAL_SERVER_STATUS[$row->$key2];
				}elseif($key2 == 'domain_pass'){
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