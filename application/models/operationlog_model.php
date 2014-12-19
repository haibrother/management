<?php

/**
 * Operationlog_model
 * 
 * @package   
 * @author hx_wsm
 * @copyright 412
 * @version 2011
 * @access public
 */
class Operationlog_model extends CI_Model
{

	/**
	 * Operationlog_model::__construct()
	 * 
	 * @return
	 */
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		
	}
	
	/**
	 * Operationlog_model::create()
	 * 
	 * @param mixed $operationlog_data
	 * @return
	 */
	public function create($operationlog_data = array()){
		
		$ud = $operationlog_data;
		return true;
		if(empty($ud))
			return false;
			
		if(!isset($ud['operationlog_table_name']))
			return false;
		
		$data = array(
				'operationlog_table_name' => $ud['operationlog_table_name'],
				'operationlog_table_nicename' => $ud['operationlog_table_nicename'],
				'operationlog_user_id' => $ud['operationlog_user_id'],
				'operationlog_method' => $ud['operationlog_method'],
				'operationlog_time' => date('Y-m-d H:i:s'),
				'operationlog_row_id' => $ud['operationlog_row_id'],
				'operationlog_key_field' => $ud['operationlog_key_field'],
				'operationlog_field' => $ud['operationlog_field'] ? $ud['operationlog_field'] : '',
				'operationlog_field_nicename' => $ud['operationlog_field_nicename'],
				'operationlog_old_value' => $ud['operationlog_old_value'] ? $ud['operationlog_old_value'] : '',
				'operationlog_new_value' => $ud['operationlog_new_value'] ? $ud['operationlog_new_value'] : '',
				'operationlog_client_ip' => $this->input->server('REMOTE_ADDR'),
				'operationlog_client_msg' => $this->input->server('HTTP_USER_AGENT') . "\n" .$this->input->server('REQUEST_URI'),
				'operationlog_level' => $ud['operationlog_level']);
        
		$this->db->insert(TABLE_OPERATIONLOGS, $data);
		
		return $this->db->affected_rows();
	}
	
	
	/**
	 * Operationlog_model::get_operationlog_form()
	 * 412 20110326 获取用户列表
	 * 
	 * @param string $wherearr
	 * @param string $order
	 * @param string $num
	 * @param string $offset
	 * @return
	 */
	function get_operationlog_form($wherearr = '', $order = '', $num = '', $offset = '')
	{
	   //权限检查
        if(!$this->permission_model->check_sql_permission(TABLE_OPERATIONLOGS, SQL_ACTION_SELECT, $this->session->userdata('user_group')))
            return false;
        
		if(!empty($wherearr))
			$this->db->where($wherearr);
			
		empty($order) ? $this->db->order_by("operationlog_time", "desc") : $this->db->order_by($order);
		
		$this->db->where('operationlog_level !=', 0);
        $this->db->from(TABLE_OPERATIONLOGS);
        
        if(is_numeric($num) && is_numeric($offset)){ //412 20110326 判定如只传入的限制条件不是数字则返回记录条数
            $this->db->limit($num, $offset);
    		$query = $this->db->get();
    		return $query->result();
        } else {
    		return $this->db->count_all_results();
        }
	}
	
	
	/**
	 * Operationlog_model::delete_operationlog()
	 * 
	 * @param string $wherearr
	 * @return
	 */
	function delete_operationlog($wherearr = '')
	{
	   //权限检查
        if(!$this->permission_model->check_sql_permission(TABLE_OPERATIONLOGS, SQL_ACTION_DELETE, $this->session->userdata('user_group')))
            return false;
        
		if(!empty($wherearr)){
            
            //数据表更改前数据
            $old_operationlog_date = $this->get_operationlog_form($wherearr, '', 1, 0);
            
            $this->db->where($wherearr);
			$this->db->update(TABLE_OPERATIONLOGS, array('operationlog_level' => 0));
            $result = $this->db->affected_rows();
            
            $this->write_operationlog($old_operationlog_date, '', TABLE_OPERATIONLOGS, SQL_ACTION_DELETE, '', 1);
            
            return $result;
		}else{
			return false;
		}
	}
	
	
	/**
	 * Operationlog_model::write_operationlog()
	 * 
	 * @param string $old_date
	 * @param string $new_date
	 * @param string $table_name
	 * @param string $sql_method
	 * @param string $write_arr
	 * @param integer $level
	 * @return
	 */
	function write_operationlog($old_date = '', $new_date = '', $table_name = '', $sql_method = '', $write_arr = '', $level = 0)
	{
        if(empty($table_name) || empty($sql_method) || $level == 0)
            return 0;
        
        $TABLES_NICENAME = $this->config->item('TABLES_NICENAME');
        
        $TABLE_FIELD_NICENAME = '';
        
        switch($table_name){
        	case TABLE_DOMAINS:
        		$TABLE_FIELD_NICENAME = $this->config->item('DOMAIN_FIELD_DISPLAY');
        		break;
        	case TABLE_ORDERS:
        		$TABLE_FIELD_NICENAME = $this->config->item('ORDER_FIELD_DISPLAY');
        		break;
          	case TABLE_DOMAINORDERS:
        		$TABLE_FIELD_NICENAME = $this->config->item('DOMAINORDER_FIELD_DISPLAY');
        		break;
        	case TABLE_SERVERS:
        		$TABLE_FIELD_NICENAME = $this->config->item('SERVER_FIELD_DISPLAY');
        		break;
        	case TABLE_USERS:
        		$TABLE_FIELD_NICENAME = $this->config->item('USER_FIELD_DISPLAY');
        		break;
        	case TABLE_DIDCURLS:
        		$TABLE_FIELD_NICENAME = $this->config->item('DIDCURL_FIELD_DISPLAY');
        		break;
        }
        
        $od = '';
        if(!empty($old_date))
            foreach($old_date as $row){ $od = $row;}
        $nd = '';
        if(!empty($new_date))
            foreach($new_date as $row){ $nd = $row;}
        
        $wr = $write_arr;
        
        //去除密码显示
        if(isset($wr['user_pass']))
            unset($wr['user_pass']);
        
        $old_value = '';
        $new_value = '';
        $field = '';
        $key_field = '';
        $row_id = '';
        
        foreach($TABLES_NICENAME[$table_name]['key_field'] as $value){
            $field_value = '';
            
            if(!empty($od)){
                $field_value = $od->$value;
            }
            
            if(!empty($nd))
                $field_value = $nd->$value;
            
            if(isset($wr[$value])){
                $field_value = $wr[$value];
            }
            
            $key_field .= $field_value . ', ';
        }
        if(!empty($wr)){
            foreach($wr as $key => $value){
                $oldd = '';
                $newd = '';
                
                if(!empty($od))
                    $oldd = $od->$key;
                
                $newd = (!empty($nd)) ? $nd->$key : $value;
                
                if($oldd != $newd){
                    $old_value .=  !empty($oldd) ? ($oldd . ', ') : '';
                    $new_value .=  $newd . ', ';
                    
                    $field .= $key . ', ';
                }
            }
        }
        
        
 		//row_id 赋值
        if(!empty($od)){
            $row_id = $od->$TABLES_NICENAME[$table_name]['id_field'];
        }
        
        if(!empty($nd))
            $row_id = $nd->$TABLES_NICENAME[$table_name]['id_field'];
        
        if(isset($wr[$value])){
            $row_id = $wr[$TABLES_NICENAME[$table_name]['id_field']];
        }
        
        $operationlog_data['operationlog_table_name'] = $table_name;
        $operationlog_data['operationlog_table_nicename'] = isset($TABLES_NICENAME[$table_name]['name']) ? $TABLES_NICENAME[$table_name]['name'] : '';
        $operationlog_data['operationlog_user_id'] = $this->session->userdata('user_id');
        $operationlog_data['operationlog_method'] = $sql_method;
        $operationlog_data['operationlog_row_id'] = $row_id;
        $operationlog_data['operationlog_key_field'] = substr($key_field, 0, -2);
        $operationlog_data['operationlog_field'] = substr($field, 0, -2);
        $operationlog_data['operationlog_field_nicename'] = '';
        $operationlog_data['operationlog_old_value'] = substr($old_value, 0, -2);
        $operationlog_data['operationlog_new_value'] = substr($new_value, 0, -2);
        $operationlog_data['operationlog_level'] = (int)$level;
        
        return $this->create($operationlog_data);
	}
	
	
	/**
	 * Operationlog_model::excel_sql_result_convert()
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
		$TABLES_SQL_METHOD_NICENAME = $this->config->item('TABLES_SQL_METHOD_NICENAME');
		
		foreach ($query->result() as $key1 => $row){
			foreach($key_header as $key2 => $value){
				if($key2 == 'operationlog_user_id'){
					$result['resultarr'][$key1][$key2] = $this->user_model->get_id_user($row->$key2);
				}elseif($key2 == 'operationlog_method'){
					$result['resultarr'][$key1][$key2] = $TABLES_SQL_METHOD_NICENAME[$row->$key2];
				}elseif($key2 == 'operationlog_nicemethod'){
					$result['resultarr'][$key1][$key2] = str_replace('<br />', "", $this->sql_date_transform($row->operationlog_method, $row->operationlog_table_name, $row->operationlog_key_field, $row->operationlog_field, $row->operationlog_old_value, $row->operationlog_new_value));
				}else{
					$result['resultarr'][$key1][$key2] = $row->$key2;
				}
			}
		}
		
		return $result;

	}
	
	
	/**
	 * Operationlog_model::sql_date_transform()
	 * 转换操作记录值
	 * 不过灵活，需改进
	 * 
	 * @param mixed $query
	 * @param mixed $key_header
	 * @return
	 */
	public function sql_date_transform($sql_method = '', $table_name = '', $operationlog_key_field = '', $operationlog_field = '', $operationlog_old_value = '', $operationlog_new_value = ''){
		if (empty($sql_method) || empty($table_name) || empty($operationlog_key_field))
			return false;
		
		$result = '';
        $TABLES_NICENAME = $this->config->item('TABLES_NICENAME');
        $FIELD_DISPLAY = $this->config->item($table_name.'_FIELD_DISPLAY');
		
		$key_field_arr = preg_split("/, /", $operationlog_key_field);
		$field_arr = preg_split("/, /", $operationlog_field);
		$old_value_arr = preg_split("/, /", $operationlog_old_value);
		$new_value_arr = preg_split("/, /", $operationlog_new_value);
		
		$key_field_display = '';
		
        foreach($TABLES_NICENAME[$table_name]['key_field'] as $key => $value){
            $key_field_display .= (isset($FIELD_DISPLAY[$value])? $FIELD_DISPLAY[$value] : $value) . ':' . (isset($key_field_arr[$key])? $key_field_arr[$key] : '') . ' ';
        }
		$key_field_display .= '<br />';
		
		if($sql_method == SQL_ACTION_INSERT){
			if($table_name == TABLE_ORDERS){
        		$TABLES_NICENAME = $this->config->item('TABLES_NICENAME');
				$result .= '创建申请单 ' . '<br />' . $key_field_display;
			}elseif($table_name == TABLE_SERVERS){
				$result .= '创建服务器 ' . '<br />' . $key_field_display;
			}elseif($table_name == TABLE_DOMAINS){
				$result .= '创建域名 ' . '<br />' . $key_field_display;
			}elseif($table_name == TABLE_DIDCURLS){
				$result .= '创建IDC ' . '<br />' . $key_field_display;
			}elseif($table_name == TABLE_USERS){
				$result .= '创建用户 ' . '<br />' . $key_field_display;
			}else{
				return false;
			}
			
		}elseif($sql_method == SQL_ACTION_UPDATE){
		
			$update_field_display = '更改内容:<br />';
	        foreach($field_arr as $key => $value){
	        	if($value == 'server_pass' || $value == 'domain_pass'){
	        		if(!((isset($old_value_arr[$key]) && isset($new_value_arr[$key])) && ($this->encrypt->decode($old_value_arr[$key]) == $this->encrypt->decode($new_value_arr[$key]))))
	            	$update_field_display .= (isset($FIELD_DISPLAY[$value])? $FIELD_DISPLAY[$value] : $value) . ':\'' . (isset($old_value_arr[$key])? $this->encrypt->decode($old_value_arr[$key]) : '') . '\' -> \'' . (isset($new_value_arr[$key])? $this->encrypt->decode($new_value_arr[$key]) : '') . '\'<br />';
	        	}else{
       			 $update_field_display .= (isset($FIELD_DISPLAY[$value])? $FIELD_DISPLAY[$value] : $value) . ':\'' . (isset($old_value_arr[$key])? $old_value_arr[$key] : '') . '\' -> \'' . (isset($new_value_arr[$key])? $new_value_arr[$key] : '') . '\'<br />';
	        	}
	        }
	        
			if($table_name == TABLE_ORDERS){
				$result .= '更新申请单资料 ' . '<br />' . $key_field_display . $update_field_display;
			}elseif($table_name == TABLE_SERVERS){
				$result .= '更新服务器资料 ' . '<br />' . $key_field_display . $update_field_display;
			}elseif($table_name == TABLE_DOMAINS){
				$result .= '更新域名资料 ' . '<br />' . $key_field_display . $update_field_display;
			}elseif($table_name == TABLE_DIDCURLS){
				$result .= '更新IDC资料 ' . '<br />' . $key_field_display . $update_field_display;
			}elseif($table_name == TABLE_USERS){
				$result .= '更新用户资料 ' . '<br />' . $key_field_display . $update_field_display;
			}else{
				return false;
			}
			
		}elseif($sql_method == SQL_ACTION_DELETE){
			if($table_name == TABLE_ORDERS){
				$result .= '删除申请单 ' . '<br />' . $key_field_display;
			}elseif($table_name == TABLE_SERVERS){
				$result .= '删除服务器 ' . '<br />' . $key_field_display;
			}elseif($table_name == TABLE_DOMAINS){
				$result .= '删除域名 ' . '<br />' . $key_field_display;
			}elseif($table_name == TABLE_DIDCURLS){
				$result .= '删除IDC ' . '<br />' . $key_field_display;
			}elseif($table_name == TABLE_USERS){
				$result .= '删除用户 ' . '<br />' . $key_field_display;
			}elseif($table_name == TABLE_OPERATIONLOGS){
				$result .= '删除操作记录 ' . '<br />' . $key_field_display;
			}else{
				return false;
			}
			
		}else{
			return false;
		}
		
		return $result;
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */