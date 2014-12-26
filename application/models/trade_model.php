<?php

/**
 */
class Trade_model extends CI_Model
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
	public function create($data = array(),$trade_type=''){
	   //权限检查
        if(!$data)return '';
        
        $count = count($data);
        $n = 1000;
        //切割成多个数组
        $arr = array_chunk($data,$n);
        if(!is_array($arr) || !$arr)
        {
            return '';
        }
        //需求不允许过滤数据
      //  $arr = $this->filtration_data($arr,$trade_type);
        foreach($arr as $k=>$v)
        {
            $value='';
            $i = 0;
            foreach($v as $k1=>$v1)
            {
                if(empty($v))return '';
                $key = "(`".implode("`,`",array_keys($v1))."`)";
                if($i==0)
                {
                    $value .= "('".implode("','",array_values($v1))."')";
                }else{
                    $value .= ",('".implode("','",array_values($v1))."')";
                }
                $i++;
            }
            $sql = "insert into trade {$key} values {$value}";
            $this->db->query($sql);
        }
        
	}
    
    /*
     *过滤掉重复数据 deal为唯一
     **/
     function filtration_data($arr,$trade_type)
     {
        $dealArr = array();
        foreach($arr as $k1=>$v1)
        {
            foreach($v1 as $k2=>$v2)
            {
                $dealArr[] = $v2['deal'];
            }
        }
        
        if(!empty($dealArr))
        {
            $this->db->where('trade_type',$trade_type);
            $this->db->where_in('deal',$dealArr);
            $this->db->delete(TABLE_TRADE); 
        }    
        
        return $arr;
     }
     
     /*
      *获取最大版本
      **/
      function get_version($wherearr = '')
      {
        $this->db->select('version');
        $this->db->from(TABLE_TRADE);
        $this->db->where($wherearr);
        $this->db->order_by('`version`','desc');
        $this->db->limit(1);
        $query = $this->db->get();
        $result = $query->result();
        if(!empty($result))return $result[0]->version+1;
        return 1;
      }
      
      /*
       *获取所有版本
       **/
       function get_all_version($wherearr)
       {
            $this->db->select('version');
            $this->db->from(TABLE_TRADE);
            $this->db->where($wherearr);
            $this->db->order_by('`version`','desc');
            $this->db->group_by('`version`');
            $query = $this->db->get();
            $result = $query->result();

            $return = array('version'=>'','last_version'=>'');
            if(!empty($result))
            {
                foreach($result as $k1=>$v1)
                {
                    foreach($v1 as $k2=>$v2)
                    {
                        $return['version'][] = $v2;
                    }
                }
                $return['last_version'] = $result[0]->version;
            }
            
            return $return;
       }
	
	
	/**
	 */
	function get_list($wherearr = '', $order = '', $num = '', $offset = '',$group_by='',$select='')
	{
		if(!empty($wherearr))
			$this->db->where($wherearr);

        empty($select) ? '':$this->db->select($select);
		empty($order) ? $this->db->order_by("`order`", "desc") : $this->db->order_by($order);
		empty($group_by) ? '' : $this->db->group_by($group_by);
        $this->db->from(TABLE_TRADE);
        
        if(is_numeric($num) && is_numeric($offset)){
            $this->db->limit($num, $offset);
    		$query = $this->db->get();
    		return $query->result();
            #var_dump($this->db->last_query());exit;
        } else {
    		 return $this->db->count_all_results();
             #var_dump($this->db->last_query());exit;
        }
	}
    
    /*
     *获取小计和总计
     **/
     function get_sum($wherearr = '', $num = '', $offset = '',$group_by='',$select='')
     {
        if(!empty($wherearr))
			$this->db->where($wherearr);

        empty($select) ? '':$this->db->select($select);
		empty($order) ? $this->db->order_by("`order`", "desc") : $this->db->order_by($order);
		empty($group_by) ? '' : $this->db->group_by($group_by);
        $this->db->from(TABLE_TRADE);
        
     }
     
     /*
      *小计
      **/
      function get_subtotal($wherearr = '', $num = '', $offset = '',$group_by='',$select='')
      {
            if(!empty($wherearr))
                $this->db->where($wherearr);

            empty($select) ? '':$this->db->select($select);
            empty($order) ? $this->db->order_by("`order`", "desc") : $this->db->order_by($order);
            empty($group_by) ? '' : $this->db->group_by($group_by);
            $this->db->from(TABLE_TRADE);
            $this->db->limit($num, $offset);
            $query = $this->db->get();
            return $query->result();
            #var_dump($this->db->last_query());exit;
      }
      
      /*
       *总计
       **/
       function get_total($wherearr = '', $num = '', $offset = '',$group_by='',$select='')
       {
            
       }
    
    
	
	/*
     *获取总数
     **/
     function get_count($wherearr = '',$select='')
     {
        if(!empty($wherearr))
			$this->db->where($wherearr);
         empty($select) ? '':$this->db->select($select);
        $this->db->from(TABLE_TRADE);
        $query = $this->db->get();
        $result =  $query->result();
        if(isset($result[0]->num) && $result[0]->num)return $result[0]->num;
        return 0;

     }
     
     /*
      *删除版本数据
      **/
      function delete_version($where)
      {
        $this->db->where($where);
        $this->db->delete(TABLE_TRADE);
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
	 * Server_model::excel_sql_result_convert()
	 * 
	 * @param mixed $query
	 * @param mixed $key_header
	 * @return
	 */
	public function excel_sql_result_convert($query, $key_header){
        $result['headerarr'] = $key_header;
		$result['resultarr'] = array();
        
		if ($query->num_rows() <= 0)
			return $result;
		
		

		foreach ($query->result() as $key1 => $row){
			foreach($key_header as $key2 => $value){
				$result['resultarr'][$key1][$key2] = $row->$key2;
			}
		}
		
		return $result;

	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */