<?php

/**
 * Permission_model
 * 
 * @package   
 * @author hx_wsm
 * @copyright 412
 * @version 2011
 * @access public
 */
class Permission_model extends CI_Model
{

	/**
	 * Permission_model::__construct()
	 * 
	 * @return
	 */
	function __construct()
	{
		parent::__construct();
		
	}
    
    /**
     * Permission_model::check_sql_permission()
	 * 检查用户组是否有操作指定数据表的权限
     * 
     * @param string $sql_table_name
     * @param string $sql_action
     * @param string $user_group
     * @return 返回bool
     */
    function check_sql_permission($sql_table_name = '', $sql_action = '', $user_group = ''){
        if(empty($sql_table_name) || empty($sql_action) || empty($user_group) || $user_group == false)
            return false;
        
        $PERMISSION_SQL_ARRAY = $this->config->item('PERMISSION_SQL_ARRAY');
        if(!isset($PERMISSION_SQL_ARRAY[$sql_table_name . $sql_action]))
            return false;
            
        return in_array($user_group, $PERMISSION_SQL_ARRAY[$sql_table_name . $sql_action]);
    
    }
    
    /**
     * Permission_model::check_visit_permission()
	 * 检查用户组是否有访问指定页面的权限
     * 
     * @param string $page_sql_table_name
     * @param string $user_group
     * @return 返回bool
     */
    function check_visit_permission($page_sql_table_name = '', $user_group = ''){
        if(empty($page_sql_table_name) || empty($user_group))
            return false;
            
        return $this->check_sql_permission($page_sql_table_name, SQL_ACTION_SELECT, $user_group);
    }
    
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */