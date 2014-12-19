<?php
/**
 * operationlog.php
 *
 * @version operationlog.php 57 2011-4-22 18:48:13
 * @author 412
 * @access public
 */

class Operationlog extends CI_Controller
{

	/**
	 * Operationlog::__construct()
	 * 
	 * @return
	 */
	function __construct()
	{
		parent::__construct();
		
		
		$this->load->library('pagination');
		$this->load->model('operationlog_model');
        if(!$this->permission_model->check_visit_permission(TABLE_OPERATIONLOGS, $this->session->userdata('user_group')))
            show_404();
	}


	/**
	 * Operationlog::index()
	 * 
	 * @return
	 */
	public function index()
	{
		$this->operationlog_list();
	}
	
	/**
	 * Operationlog::operationlog_list()
	 * 
	 * @return
	 */
	public function operationlog_list(){
		
		$where_arr = array('operationlog_level >=' => $this->session->userdata('user_group'));
		if($this->session->userdata('user_group') == USER)
			$where_arr['operationlog_user_id'] = $this->session->userdata('user_id');
		
		//配置分页类
		$config['base_url'] = 'operationlog/operationlog_list/';
		$config['per_page'] = PAGE_PER_MAX;
		$config['uri_segment'] = 3;
		$data['page_total'] = $config['total_rows'] = $this->operationlog_model->get_operationlog_form($where_arr);
		$config['cur_tag_open'] = '<a class="current">';
		$config['cur_tag_close'] = '</a>';
		$config['first_link'] = '&laquo;';
		$config['last_link'] = '&raquo;';
		//获取分页
		$this->pagination->initialize($config);
		$data['page'] = $this->pagination->create_links();
		
		//页数存入seesion已供excel导出
		$this->session->set_userdata('now_page', intval($this->uri->segment(3)));
		$this->session->set_userdata('excel_where', $where_arr);
		
		//获取列表
		$result = $this->operationlog_model->get_operationlog_form($where_arr, '', PAGE_PER_MAX, intval($this->uri->segment(3)));
		
		$TABLES_SQL_METHOD_NICENAME = $this->config->item('TABLES_SQL_METHOD_NICENAME');
		
		$data['result'] = '';
		foreach ($result as $key => $value){
			foreach($value as $key2 => $value2){
				if($key2 == 'operationlog_user_id'){
					$data['result']->$key->$key2 = $this->user_model->get_id_user($value2);
				}elseif($key2 == 'operationlog_method'){
					$data['result']->$key->$key2 = $TABLES_SQL_METHOD_NICENAME[$value2];
				}elseif($key2 == 'operationlog_field' || $key2 == 'operationlog_client_msg'){
					$data['result']->$key->$key2 = nl2br($value2);
				}else{
					$data['result']->$key->$key2 = $value2;
				}
			}
			$data['result']->$key->operationlog_nicemethod = $this->operationlog_model->sql_date_transform($value->operationlog_method, $value->operationlog_table_name, $value->operationlog_key_field, $value->operationlog_field, $value->operationlog_old_value, $value->operationlog_new_value);
		}
		
		$data['form_title'] = array(
								'编号',
								'数据表名称',
								'用户',
								'操作方式',
								'时间',
								'操作说明',
								'客户端IP');
		
		$data['data_key'] = array(
								'operationlog_id',
								'operationlog_table_nicename',
								'operationlog_user_id',
								'operationlog_method',
								'operationlog_time',
								'operationlog_nicemethod',
								'operationlog_client_ip');
		
		$data['top_item'] = 'operationlog';
		$data['item'] = '';
		$data['item_id_field'] = 'operationlog_id';
		$data['edit_able'] = $this->permission_model->check_sql_permission(TABLE_OPERATIONLOGS, SQL_ACTION_UPDATE, $this->session->userdata('user_group'));
		$data['data_edit_url'] = '';
		
		$data['delete_able'] = $this->permission_model->check_sql_permission(TABLE_OPERATIONLOGS, SQL_ACTION_DELETE, $this->session->userdata('user_group'));
		$data['data_delete_url'] = 'operationlog/delete_operationlog_single/';
		
		$this->load->view('index', $data);
		
	}
	
	/**
	 * Operationlog::delete_operationlog_single()
	 * 
	 * @param mixed $operationlog_id
	 * @return
	 */
	public function delete_operationlog_single($operationlog_id){
	   
		if(!$this->permission_model->check_sql_permission(TABLE_OPERATIONLOGS, SQL_ACTION_DELETE, $this->session->userdata('user_group')) || !is_numeric($operationlog_id)){
			echo 0;
		}else{
			$operationlog_data = array('operationlog_id' => $operationlog_id);
			$result = $this->operationlog_model->delete_operationlog($operationlog_data);
			echo $result;
		}
	}
	
	
	/**
	 * Operationlog::excel_export()
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
			$this->db->where('operationlog_level !=', 0);
		}
		
		$this->db->order_by("operationlog_time", "desc");
		
		$query = $this->db->get(TABLE_OPERATIONLOGS, $limit, $now_page);
			
		$FIELD_DISPLAY = $this->config->item('OPERATIONLOG_FIELD_DISPLAY');
		
		$result_array = $this->operationlog_model->excel_sql_result_convert($query, $FIELD_DISPLAY);
		
		$this->excel_model->array_to_excel($result_array['headerarr'], $result_array['resultarr'], 'excek数据');
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */