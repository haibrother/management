<?php

/**
 * User_model
 * 
 * @package   
 * @author hx_wsm
 * @copyright 412
 * @version 2011
 * @access public
 */
class User_model extends CI_Model
{

	/**
	 * User_model::__construct()
	 * 
	 * @return
	 */
	function __construct()
	{
		parent::__construct();
		
	}
	
	/**
	 * User_model::create()
	 * 
	 * @param mixed $user_data
	 * @return
	 */
	public function create($user_data = array()){
	   //权限检查
        if(!$this->permission_model->check_sql_permission(TABLE_USERS, SQL_ACTION_INSERT, $this->session->userdata('user_group')))
			return false;
		
		$ud = $user_data;
		
		if(empty($ud))
			return false;
			
		if(!isset($ud['user_login']) || !isset($ud['user_pass']) || !isset($ud['user_department']) || !isset($ud['user_email']) || !isset($ud['user_status']))
			return false;
		
		if($this->session->userdata('user_group') != POWER_ADMIN && $ud['user_status'] < 3){
			return false;
		}
		
		$data = array(
           'user_login' => $ud['user_login'],
           'user_pass' => $this->get_password($ud['user_pass']),
           'user_nicename' => '',
           'user_department' => $ud['user_department'],
           'user_email' => $ud['user_email'],
           'user_registered' => date('Y-m-d H:i:s'),
           'user_status' => $ud['user_status'],
           'user_last_ip' => $this->input->server('REMOTE_ADDR'),
           'user_last_time' => date('Y-m-d H:i:s')
        );
        
		$this->db->insert(TABLE_USERS, $data);
		$result = $this->db->affected_rows();
        
        if($result == 1){
            $i_id = $this->db->insert_id();
            $new_operationlog_date = $this->get_user_form(array('user_id' => $i_id), '', 1, 0);
            $this->operationlog_model->write_operationlog('', $new_operationlog_date, TABLE_USERS, SQL_ACTION_INSERT, $data, 2);
        }
        
        return $result;
	}
	
	
	/**
	 * User_model::get_user_form()
	 * 412 20110326 获取用户列表
	 * 
	 * @param string $wherearr
	 * @param string $order
	 * @param string $num
	 * @param string $offset
	 * @return
	 */
	function get_user_form($wherearr = '', $order = '', $num = '', $offset = '')
	{
		
		if(!empty($wherearr))
			$this->db->where($wherearr);
			
		empty($order) ? $this->db->order_by("user_registered", "desc") : $this->db->order_by($order);
		
		$this->db->where('user_status >', 0);
        $this->db->from(TABLE_USERS);
        
        if(is_numeric($num) && is_numeric($offset)){ //412 20110326 判定如只传入的限制条件不是数字则返回记录条数
            $this->db->limit($num, $offset);
    		$query = $this->db->get();
    		return $query->result();
        } else {
    		return $this->db->count_all_results();
        }
	}
 /**
  * User_model::get_user_array()
  * 417 20120713 获取用户列表
  * @param void
  * @return array
  * */
  function get_user_array(){
     $sql = 'select user_login from '.TABLE_USERS.' where user_status = 3';
     $query = $this->db->query($sql);
     $result = $query->result();
    
     return $result;
  }
  
	
	
	/**
	 * User_model::delete_user()
	 * 412 20110326 删除用户
	 * 
	 * @param string $wherearr
	 * @return
	 */
	function delete_user($wherearr = '')
	{
	   //权限检查
        if(!$this->permission_model->check_sql_permission(TABLE_USERS, SQL_ACTION_DELETE, $this->session->userdata('user_group')))
			return false;
		
		if(!empty($wherearr)){
            
            //数据表更改前数据
            $old_operationlog_date = $this->get_user_form($wherearr, '', 1, 0);
            
            $this->db->where($wherearr);
			$this->db->update(TABLE_USERS, array('user_status' => 0));
            $result = $this->db->affected_rows();
            
            $this->operationlog_model->write_operationlog($old_operationlog_date, '', TABLE_USERS, SQL_ACTION_DELETE, '', 2);
            
            return $result;
		}else{
			return false;
		}
	}
	
	
	
	/**
	 * User_model::update_user()
	 * 
	 * @param string $user_id
	 * @param mixed $update_data
	 * @return
	 */
	function update_user($user_id = '', $update_data = array())
	{
		if(!is_numeric($user_id) || empty($update_data))
			return false;
            
	   //权限检查
        if($user_id != $this->session->userdata('user_id') && !$this->permission_model->check_sql_permission(TABLE_USERS, SQL_ACTION_UPDATE, $this->session->userdata('user_group')))
			return false;
		
        $wherearr = array('user_id' => $user_id);
        //数据表更改前数据
        $old_operationlog_date = $this->get_user_form($wherearr, '', 1, 0);
        
		$this->db->where($wherearr);
		$this->db->update(TABLE_USERS, $update_data);
        
		$result = $this->db->affected_rows();
        
        $this->operationlog_model->write_operationlog($old_operationlog_date, '', TABLE_USERS, SQL_ACTION_UPDATE, $update_data, 3);
        
        return $result;
	}
	
	
	/**
	 * User_model::get_id_user()
	 * 
	 * @param integer $user_id
	 * @return
	 */
	function get_id_user($user_id = 0){
	   //权限检查
        if(!$this->permission_model->check_sql_permission(TABLE_USERS, SQL_ACTION_SELECT, $this->session->userdata('user_group')))
			return false;
		
		if(!is_numeric($user_id) || $user_id <= 0)
			return false;
		
		$this->db->where('user_id', $user_id);
		$query = $this->db->get(TABLE_USERS, 1);
		
		if ($query->num_rows() == 0)
			return false;
		
		$row = $query->first_row();
		$result = $row->user_login;
		if($row->user_status == 0)
			$result .= '(已删除)';
		return $result;
	}
	
	
	/**
	 * User_model::get_id_user()
	 * 
	 * @param integer $user_id
	 * @return
	 */
	function get_login_user($user_login = ''){
	   //权限检查
        if(!$this->permission_model->check_sql_permission(TABLE_USERS, SQL_ACTION_SELECT, $this->session->userdata('user_group')))
			return false;
		
		if($user_login == '')
			return false;
		
		$this->db->where('user_login', $user_login);
		$query = $this->db->get(TABLE_USERS, 1);
		
		if ($query->num_rows() == 0)
			return false;
		
		$row = $query->first_row();
		$result = $row->user_id;
		
		return $result;
	}
	
	
	/**
	 * User_model::is_admin()
	 * 
	 * @param integer $user_id
	 * @return
	 */
	function is_admin($user_id = 0){
	   //权限检查
        if(!$this->permission_model->check_sql_permission(TABLE_USERS, SQL_ACTION_SELECT, $this->session->userdata('user_group')))
			return false;
		
		if(!is_numeric($user_id) || $user_id <= 0)
			return false;
		
		$this->db->where('user_id', $user_id);
		$query = $this->db->get(TABLE_USERS, 1);
		
		if ($query->num_rows() == 0)
			return false;
		
		$row = $query->first_row();
		$result = $row->user_status;
		return $result == 2 ? true : false;
	}
    
	
    /**
     * User_model::get_password()
	 * 412 20110408 返回加密的密码
     * 
     * @param string $original
     * @return
     */
    function get_password($original = ''){
        if(empty($original))
            return false;
        
        $orig = $original;
        $resutl = md5(md5($orig)) . md5($orig);
        return $resutl;
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
		$USER_GROUP_INFO = $this->config->item('USER_GROUP_INFO');
		
		foreach ($query->result() as $key1 => $row){
			foreach($key_header as $key2 => $value){
				if($key2 == 'user_status'){
					$result['resultarr'][$key1][$key2] = $USER_GROUP_INFO[$row->$key2]['name'];
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